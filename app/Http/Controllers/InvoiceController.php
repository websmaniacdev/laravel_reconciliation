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
use App\Models\YourSalesBill;
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
        // ── Invoice Records (Meta Ads) ────────────────────────────────
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

        $filteredTotal       = (clone $query)->sum('price');
        $filteredGst         = round($filteredTotal * 0.18, 2);
        $filteredGrandTotal  = round($filteredTotal + $filteredGst, 2);
        $filteredCount       = (clone $query)->count();
        $filteredImpressions = (clone $query)->sum('impressions');

        $records = $query->orderBy('client_name', 'asc')
            ->orderBy('document_date', 'desc')
            ->paginate(1500)
            ->withQueryString();

        $subtotals = InvoiceSubtotal::orderBy('document_date', 'desc')->get();

        $pendingPdfs = PendingInvoicePdf::whereIn('status', ['pending', 'processing', 'failed'])
            ->orderBy('created_at', 'desc')
            ->get();

        // ── Your Sales Bills ──────────────────────────────────────────
        $yourBillQuery = YourSalesBill::query();

        if ($request->filled('client_name')) {
            $yourBillQuery->where('client_name', 'like', '%' . $request->client_name . '%');
        }
        if ($request->filled('from_date')) {
            $yourBillQuery->whereDate('invoice_date', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $yourBillQuery->whereDate('invoice_date', '<=', $request->to_date);
        }

        $yourBills      = $yourBillQuery->orderBy('invoice_date', 'desc')->get();
        $yourBillsTotal = $yourBills->sum('total_amount');

        // ── Month + Client wise Grouped Data ──────────────────────────
        // Key: YYYY-MM → client_name → ['meta_ads' => [...], 'your_bills' => [...]]
        // ── Month + Client wise Grouped Data ──────────────────────────────────
        $groupedData = [];

        // Step 1: Add all meta ads records (use their name as canonical key)
        foreach ($records as $record) {
            $monthKey  = $record->document_date
                ? \Carbon\Carbon::make($record->document_date)->format('Y-m')
                : '0000-00';
            $clientKey = strtoupper(trim($record->client_name ?? 'Unknown'));

            $groupedData[$monthKey][$clientKey]['meta_ads'][]   = $record;
            $groupedData[$monthKey][$clientKey]['your_bills'] ??= [];
        }

        // Step 2: Add your bills — fuzzy-match to existing meta ads keys
        foreach ($yourBills as $bill) {
            $monthKey      = $bill->invoice_date
                ? $bill->invoice_date->format('Y-m')
                : '0000-00';
            $billClientKey = strtoupper(trim($bill->client_name ?? 'Unknown'));
            $billNorm      = $this->normalizeClientName($billClientKey);

            // Try to find a matching meta ads client in the same month
            $bestMatch = null;
            if (isset($groupedData[$monthKey])) {
                foreach (array_keys($groupedData[$monthKey]) as $metaKey) {
                    $metaNorm = $this->normalizeClientName($metaKey);
                    if ($this->clientNamesMatch($billNorm, $metaNorm)) {
                        $bestMatch = $metaKey;
                        break;
                    }
                }
            }

            if ($bestMatch !== null) {
                // Merge under the matched meta ads client key
                $groupedData[$monthKey][$bestMatch]['your_bills'][] = $bill;
            } else {
                // No match found — standalone entry
                $groupedData[$monthKey][$billClientKey]['your_bills'][] = $bill;
                $groupedData[$monthKey][$billClientKey]['meta_ads']    ??= [];
            }
        }

        krsort($groupedData);

        // Count matched clients (have both sides)
        $matchedCount = 0;
        foreach ($groupedData as $clients) {
            foreach ($clients as $data) {
                if (!empty($data['meta_ads']) && !empty($data['your_bills'])) {
                    $matchedCount++;
                }
            }
        }

        return view('invoices.index', compact(
            'records',
            'subtotals',
            'pendingPdfs',
            'filteredTotal',
            'filteredGst',
            'filteredGrandTotal',
            'filteredCount',
            'filteredImpressions',
            'yourBills',
            'yourBillsTotal',
            'groupedData',
            'matchedCount'
        ));
    }


    private function normalizeClientName(string $name): string
    {
        $name = strtoupper(trim($name));

        // Remove date suffixes: "- APRIL 2026", "APRIL 2026", etc.
        $months = 'JANUARY|FEBRUARY|MARCH|APRIL|MAY|JUNE|JULY|AUGUST|SEPTEMBER|OCTOBER|NOVEMBER|DECEMBER';
        $name = preg_replace('/[\s\-]+(?:' . $months . ')\s+\d{4}.*$/i', '', $name);

        // Remove legal suffixes common in Indian company names
        $name = preg_replace(
            '/\b(?:PRIVATE\s+LIMITED|PVT\.?\s*LTD\.?|LIMITED|LTD\.?|PVT\.?)\b/i',
            '',
            $name
        );

        // Normalize punctuation and spaces
        $name = preg_replace('/[^\w\s]/', ' ', $name);
        $name = preg_replace('/\s+/', ' ', $name);

        return trim($name);
    }

    private function clientNamesMatch(string $a, string $b): bool
    {
        if ($a === $b) return true;

        // One is a prefix of the other
        if (str_starts_with($a, $b) || str_starts_with($b, $a)) return true;

        // One is fully contained in the other
        if (str_contains($a, $b) || str_contains($b, $a)) return true;

        // Fallback: percentage similarity
        similar_text($a, $b, $percent);
        return $percent >= 75;
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
