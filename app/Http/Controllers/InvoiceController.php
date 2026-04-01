<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessInvoicePdf;
use App\Models\InvoiceRecord;
use App\Models\InvoiceSubtotal;
use App\Models\PendingInvoicePdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\InvoiceRecordsExport;
use Illuminate\Support\Facades\Artisan;

class InvoiceController extends Controller
{

    public function runCommand()
    {
        Artisan::call('invoice:process-pending', [
            '--sync' => true
        ]);

        return back()->with('success', 'Command executed successfully!');
    }
    // ══════════════════════════════════════════════════════════════════
    // LIST
    // ══════════════════════════════════════════════════════════════════

    public function index(Request $request)
    {
        $query = InvoiceRecord::query();

        if ($request->filled('client_name')) {
            $query->where('client_name', 'like', '%' . $request->client_name . '%');
        }

        if ($request->filled('from_date')) {
            $query->whereDate('document_date', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('document_date', '<=', $request->to_date);
        }

        // ── Filter-wise totals (before pagination) ────────────────────
        $filteredTotal       = (clone $query)->sum('price');
        $filteredGst         = round($filteredTotal * 0.18, 2);
        $filteredGrandTotal  = round($filteredTotal + $filteredGst, 2);
        $filteredCount       = (clone $query)->count();
        $filteredImpressions = (clone $query)->sum('impressions');

        $records = $query->orderBy('client_name', 'asc')
            ->orderBy('document_date', 'desc')
            ->paginate(1500)
            ->withQueryString();

        // PDF subtotals
        $subtotals = InvoiceSubtotal::orderBy('document_date', 'desc')->get();

        // Pending/failed PDFs
        $pendingPdfs = PendingInvoicePdf::whereIn('status', ['pending', 'processing', 'failed'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('invoices.index', compact(
            'records',
            'subtotals',
            'pendingPdfs',
            'filteredTotal',
            'filteredGst',
            'filteredGrandTotal',
            'filteredCount',
            'filteredImpressions'
        ));
    }

    // ══════════════════════════════════════════════════════════════════
    // UPLOAD
    // ══════════════════════════════════════════════════════════════════

    public function upload(Request $request)
    {
        $request->validate([
            'pdfs'   => 'required|array|min:1|max:200',
            'pdfs.*' => 'required|file|mimes:pdf|max:10240',
        ]);

        $queued = 0;
        $errors = [];

        foreach ($request->file('pdfs') as $file) {
            try {
                $originalName = $file->getClientOriginalName();
                $stored       = $file->store('invoices', 'local');

                $pending = PendingInvoicePdf::create([
                    'original_filename' => $originalName,
                    'stored_path'       => $stored,
                    'status'            => 'pending',
                ]);

                ProcessInvoicePdf::dispatch($pending->id);
                $queued++;
            } catch (\Exception $e) {
                $errors[] = $file->getClientOriginalName() . ': ' . $e->getMessage();
            }
        }

        $message = $queued > 0
            ? "{$queued} PDF(s) queued. If not processed automatically, run: php artisan invoice:process-pending --sync"
            : 'No PDFs were queued.';

        return redirect()->route('invoices.index')
            ->with('success', $message)
            ->with('upload_errors', $errors);
    }

    // ══════════════════════════════════════════════════════════════════
    // EXPORT
    // ══════════════════════════════════════════════════════════════════

    public function export(Request $request)
    {
        $filters = [
            'client_name' => $request->get('client_name'),
            'from_date'   => $request->get('from_date'),
            'to_date'     => $request->get('to_date'),
        ];

        return Excel::download(
            new InvoiceRecordsExport($filters),
            'invoice-records-' . now()->format('Y-m-d') . '.xlsx'
        );
    }

    // ══════════════════════════════════════════════════════════════════
    // MERGE
    // ══════════════════════════════════════════════════════════════════

    public function merge(Request $request)
    {
        $request->validate([
            'record_ids'   => 'required|array|min:2',
            'record_ids.*' => 'integer|exists:invoice_records,id',
            'merged_name'  => 'required|string|max:255',
        ]);

        $ids     = $request->record_ids;
        $name    = $request->merged_name;
        $groupId = (int) DB::table('invoice_records')->max('merged_group_id') + 1;

        DB::transaction(function () use ($ids, $name, $groupId) {
            $records = InvoiceRecord::whereIn('id', $ids)->get();

            InvoiceRecord::create([
                'client_name'     => $name,
                'price'           => $records->sum('price'),
                'document_date'   => $records->min('document_date'),
                'impressions'     => $records->sum('impressions'),
                'campaign_type'   => 'Merged Record',
                'is_merged'       => true,
                'merged_name'     => $name,
                'merged_group_id' => $groupId,
            ]);

            InvoiceRecord::whereIn('id', $ids)->delete();
        });

        return response()->json(['success' => true]);
    }

    // ══════════════════════════════════════════════════════════════════
    // MERGE BY MONTH
    // ══════════════════════════════════════════════════════════════════

    public function mergeByMonth(Request $request)
    {
        $request->validate([
            'record_ids'   => 'required|array|min:2',
            'record_ids.*' => 'integer|exists:invoice_records,id',
            'merged_name'  => 'required|string|max:255',
        ]);

        $ids      = $request->record_ids;
        $baseName = trim($request->merged_name);

        $records = InvoiceRecord::whereIn('id', $ids)
            ->orderBy('document_date')
            ->get();

        // ── Group by Y-m using Carbon's format directly on the cast date ──
        // Cast already Carbon instance he, direct format() call karo — parse() mat karo
        $groups = $records->groupBy(function ($rec) {
            if (!$rec->document_date) return 'unknown';
            // document_date Eloquent cast se already Carbon hai
            return \Carbon\Carbon::make($rec->document_date)->format('Y-m');
        });

        if ($groups->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'No records to merge.'], 422);
        }

        $mergedCount = 0;

        DB::transaction(function () use ($groups, $baseName, &$mergedCount) {
            $maxGroupId = (int) DB::table('invoice_records')->max('merged_group_id');

            foreach ($groups as $yearMonth => $groupRecords) {

                // ── Month label: group ke min document_date se nikalo ──
                // yearMonth string se mat banao — actual date se banao
                // Isse "March 2026" ke bajay sahi "February 2026" aayega
                if ($yearMonth === 'unknown') {
                    $monthLabel = 'Unknown Date';
                } else {
                    // Group ke sabse purani date se month+year lo
                    $minDate = $groupRecords
                        ->filter(fn($r) => $r->document_date !== null)
                        ->sortBy('document_date')
                        ->first()
                        ?->document_date;

                    $monthLabel = $minDate
                        ? \Carbon\Carbon::make($minDate)->format('F Y')   // "February 2026"
                        : \Carbon\Carbon::createFromFormat('Y-m', $yearMonth)->format('F Y');
                }

                $mergedName = $baseName . ' - ' . $monthLabel;
                $maxGroupId++;

                InvoiceRecord::create([
                    'client_name'     => $mergedName,
                    'price'           => $groupRecords->sum('price'),
                    'document_date'   => $groupRecords->min('document_date'),
                    'impressions'     => $groupRecords->sum('impressions'),
                    'campaign_type'   => 'Merged Record',
                    'is_merged'       => true,
                    'merged_name'     => $mergedName,
                    'merged_group_id' => $maxGroupId,
                ]);

                InvoiceRecord::whereIn('id', $groupRecords->pluck('id')->toArray())->delete();
                $mergedCount++;
            }
        });

        return response()->json([
            'success'       => true,
            'merged_groups' => $mergedCount,
        ]);
    }

    // ══════════════════════════════════════════════════════════════════
    // DELETE
    // ══════════════════════════════════════════════════════════════════

    public function destroy(InvoiceRecord $invoice)
    {
        $invoice->delete();
        return response()->json(['success' => true]);
    }

    public function destroyPending(PendingInvoicePdf $pending)
    {
        if (Storage::disk('local')->exists($pending->stored_path)) {
            Storage::disk('local')->delete($pending->stored_path);
        }
        $pending->delete();
        return response()->json(['success' => true]);
    }

    public function retryPending(PendingInvoicePdf $pending)
    {
        if (!in_array($pending->status, ['failed', 'pending'])) {
            return response()->json(['success' => false, 'message' => 'Only failed/pending PDFs can be retried.'], 422);
        }

        $pending->update(['status' => 'pending', 'error_message' => null]);
        ProcessInvoicePdf::dispatch($pending->id);

        return response()->json(['success' => true]);
    }
}
