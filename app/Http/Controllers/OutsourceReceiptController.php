<?php

namespace App\Http\Controllers;

use App\Exports\OutsourceReceiptsExport;
use App\Jobs\ProcessOutsourcePdf;
use App\Models\OutsourceReceipt;
use App\Models\PendingOutsourcePdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class OutsourceReceiptController extends Controller
{
    // ══════════════════════════════════════════════════════════════════
    // LIST
    // ══════════════════════════════════════════════════════════════════

    public function index(Request $request)
    {
        $query = OutsourceReceipt::query();

        if ($request->filled('client_name')) {
            $query->where('client_name', 'like', '%' . $request->client_name . '%');
        }

        if ($request->filled('from_date')) {
            $query->whereDate('invoice_date', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('invoice_date', '<=', $request->to_date);
        }

        // ── Summary totals (filter-wise) ──────────────────────────────
        $filteredSubtotal   = (clone $query)->sum('subtotal');
        $filteredGst        = (clone $query)->sum('gst_amount');
        $filteredGrandTotal = (clone $query)->sum('grand_total');
        $filteredCount      = (clone $query)->count();

        $records = $query
            ->orderBy('client_name', 'asc')
            ->orderBy('invoice_date', 'desc')
            ->paginate(500)
            ->withQueryString();

        // Pending / failed PDFs
        $pendingPdfs = PendingOutsourcePdf::whereIn('status', ['pending', 'processing', 'failed'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('outsource.index', compact(
            'records',
            'pendingPdfs',
            'filteredSubtotal',
            'filteredGst',
            'filteredGrandTotal',
            'filteredCount'
        ));
    }

    // ══════════════════════════════════════════════════════════════════
    // UPLOAD
    // ══════════════════════════════════════════════════════════════════

    public function upload(Request $request)
    {
        $request->validate([
            'client_name' => 'required|string|max:255',
            'pdfs'        => 'required|array|min:1|max:100',
            'pdfs.*'      => 'required|file|mimes:pdf|max:10240',
        ]);

        $clientName = trim($request->client_name);
        $queued     = 0;
        $errors     = [];

        foreach ($request->file('pdfs') as $file) {
            try {
                $originalName = $file->getClientOriginalName();
                $stored       = $file->store('outsource_receipts', 'local');

                $pending = PendingOutsourcePdf::create([
                    'client_name'       => $clientName,
                    'original_filename' => $originalName,
                    'stored_path'       => $stored,
                    'status'            => 'pending',
                ]);

                ProcessOutsourcePdf::dispatch($pending->id);
                $queued++;
            } catch (\Exception $e) {
                $errors[] = $file->getClientOriginalName() . ': ' . $e->getMessage();
            }
        }

        $message = $queued > 0
            ? "{$queued} PDF(s) queued for processing."
            : 'No PDFs were queued.';

        return redirect()->route('outsource.index')
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
            new OutsourceReceiptsExport($filters),
            'outsource-receipts-' . now()->format('Y-m-d') . '.xlsx'
        );
    }

    // ══════════════════════════════════════════════════════════════════
    // MERGE (Single Merge)
    // ══════════════════════════════════════════════════════════════════

    public function merge(Request $request)
    {
        $request->validate([
            'record_ids'   => 'required|array|min:2',
            'record_ids.*' => 'integer|exists:outsource_receipts,id',
            'merged_name'  => 'required|string|max:255',
        ]);

        $ids     = $request->record_ids;
        $name    = $request->merged_name;
        $groupId = (int) DB::table('outsource_receipts')->max('merged_group_id') + 1;

        DB::transaction(function () use ($ids, $name, $groupId) {
            $records = OutsourceReceipt::whereIn('id', $ids)->get();

            OutsourceReceipt::create([
                'client_name'      => $name,
                'invoice_number'   => 'Merged',
                'invoice_date'     => $records->min('invoice_date'),
                'subscription'     => 'Merged Record',
                'interval'         => null,
                'description'      => 'Manually merged records',
                'subtotal'         => $records->sum('subtotal'),
                'gst_amount'       => $records->sum('gst_amount'),
                'grand_total'      => $records->sum('grand_total'),
                'is_merged'        => true,
                'merged_name'      => $name,
                'merged_group_id'  => $groupId,
            ]);

            OutsourceReceipt::whereIn('id', $ids)->delete();
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
            'record_ids.*' => 'integer|exists:outsource_receipts,id',
            'merged_name'  => 'required|string|max:255',
        ]);

        $ids      = $request->record_ids;
        $baseName = trim($request->merged_name);

        $records = OutsourceReceipt::whereIn('id', $ids)
            ->orderBy('invoice_date')
            ->get();

        $groups = $records->groupBy(function ($rec) {
            if (!$rec->invoice_date) return 'unknown';
            return \Carbon\Carbon::make($rec->invoice_date)->format('Y-m');
        });

        if ($groups->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'No records to merge.'], 422);
        }

        $mergedCount = 0;

        DB::transaction(function () use ($groups, $baseName, &$mergedCount) {
            $maxGroupId = (int) DB::table('outsource_receipts')->max('merged_group_id');

            foreach ($groups as $yearMonth => $groupRecords) {

                if ($yearMonth === 'unknown') {
                    $monthLabel = 'Unknown Date';
                } else {
                    $minDate = $groupRecords
                        ->filter(fn($r) => $r->invoice_date !== null)
                        ->sortBy('invoice_date')
                        ->first()
                        ?->invoice_date;

                    $monthLabel = $minDate
                        ? \Carbon\Carbon::make($minDate)->format('F Y')
                        : \Carbon\Carbon::createFromFormat('Y-m', $yearMonth)->format('F Y');
                }

                $mergedName = $baseName . ' - ' . $monthLabel;
                $maxGroupId++;

                OutsourceReceipt::create([
                    'client_name'     => $mergedName,
                    'invoice_number'  => 'Merged',
                    'invoice_date'    => $groupRecords->min('invoice_date'),
                    'subscription'    => 'Merged Record',
                    'interval'        => null,
                    'description'     => 'Month-wise merged records',
                    'subtotal'        => $groupRecords->sum('subtotal'),
                    'gst_amount'      => $groupRecords->sum('gst_amount'),
                    'grand_total'     => $groupRecords->sum('grand_total'),
                    'is_merged'       => true,
                    'merged_name'     => $mergedName,
                    'merged_group_id' => $maxGroupId,
                ]);

                OutsourceReceipt::whereIn('id', $groupRecords->pluck('id')->toArray())->delete();
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

    public function destroy(OutsourceReceipt $receipt)
    {
        $receipt->delete();
        return response()->json(['success' => true]);
    }

    public function destroyPending(PendingOutsourcePdf $pending)
    {
        if (Storage::disk('local')->exists($pending->stored_path)) {
            Storage::disk('local')->delete($pending->stored_path);
        }
        $pending->delete();
        return response()->json(['success' => true]);
    }

    public function retryPending(PendingOutsourcePdf $pending)
    {
        if (!in_array($pending->status, ['failed', 'pending'])) {
            return response()->json([
                'success' => false,
                'message' => 'Only failed/pending PDFs can be retried.',
            ], 422);
        }

        $pending->update(['status' => 'pending', 'error_message' => null]);
        ProcessOutsourcePdf::dispatch($pending->id);

        return response()->json(['success' => true]);
    }
}