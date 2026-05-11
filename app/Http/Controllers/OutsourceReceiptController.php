<?php

namespace App\Http\Controllers;

use App\Exports\OutsourceReceiptsExport;
use App\Jobs\ProcessOutsourcePdf;
use App\Models\OutsourceReceipt;
use App\Models\OutsourceSalesBill;
use App\Models\PendingOutsourcePdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Artisan;

class OutsourceReceiptController extends Controller
{
    public function runCommand()
    {
        Artisan::call('outsource:process-pending', [
            '--sync' => true
        ]);

        return back()->with('success', 'Command executed successfully!');
    }
    // ══════════════════════════════════════════════════════════════════
    // LIST
    // ══════════════════════════════════════════════════════════════════

    public function index(Request $request)
    {
        // ── OUTSOURCE RECEIPTS QUERY ──────────────────────────────────
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

        $filteredSubtotal   = (clone $query)->sum('subtotal');
        $filteredGst        = (clone $query)->sum('gst_amount');
        $filteredGrandTotal = (clone $query)->sum('grand_total');
        $filteredCount      = (clone $query)->count();

        $records = $query
            ->orderBy('client_name', 'asc')
            ->orderBy('invoice_date', 'desc')
            ->paginate(500)
            ->withQueryString();

        // ── SALES BILLS QUERY ─────────────────────────────────────────
        $salesBillQuery = OutsourceSalesBill::query();

        if ($request->filled('client_name')) {
            $salesBillQuery->where('client_name', 'like', '%' . $request->client_name . '%');
        }
        if ($request->filled('from_date')) {
            $salesBillQuery->whereDate('invoice_date', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $salesBillQuery->whereDate('invoice_date', '<=', $request->to_date);
        }

        $salesBills      = $salesBillQuery->orderBy('invoice_date', 'desc')->get();
        $salesBillsTotal = $salesBills->sum('total_amount');

        // ══════════════════════════════════════════════════════════════
        // GROUPED DATA — 6-Month Period + Client wise
        //
        // Period key for receipts:
        //   We detect the 6-month slot from the receipt's invoice_date.
        //   e.g. invoice_date = 2026-04-30 → period "2026-04|2026-09"
        //        invoice_date = 2025-10-15 → period "2025-10|2026-03"
        //
        // Period key for sales bills:
        //   Comes directly from period_start / period_end parsed from PDF.
        //   e.g. "Apr 2026 – Sep 2026" → "2026-04|2026-09"
        //
        // Helper: given a date, return the 6-month period key
        // Periods: Apr–Sep (H1) and Oct–Mar (H2) of a financial year
        // ══════════════════════════════════════════════════════════════

        $getPeriodKey = function ($date) {
            if (!$date) return 'unknown';
            $d     = $date instanceof \Carbon\Carbon ? $date : \Carbon\Carbon::parse($date);
            $month = (int) $d->format('n');
            $year  = (int) $d->format('Y');

            // Apr(4)–Sep(9) → "YYYY-04|YYYY-09"
            if ($month >= 4 && $month <= 9) {
                return $year . '-04|' . $year . '-09';
            }
            // Oct(10)–Dec(12) → "YYYY-10|(YYYY+1)-03"
            if ($month >= 10) {
                return $year . '-10|' . ($year + 1) . '-03';
            }
            // Jan(1)–Mar(3) → "(YYYY-1)-10|YYYY-03"
            return ($year - 1) . '-10|' . $year . '-03';
        };

        $getPeriodLabel = function ($periodKey) {
            if ($periodKey === 'unknown') return 'Unknown Period';
            [$start, $end] = explode('|', $periodKey);
            try {
                $s = \Carbon\Carbon::createFromFormat('Y-m', $start);
                $e = \Carbon\Carbon::createFromFormat('Y-m', $end);
                return $s->format('M Y') . ' – ' . $e->format('M Y');
            } catch (\Throwable $th) {
                return $periodKey;
            }
        };

        $groupedData = [];

        // Group Gsuite receipts by 6-month period + client
        foreach ($records as $record) {
            $periodKey = $getPeriodKey($record->invoice_date);
            $clientKey = strtoupper(trim($record->client_name ?? 'Unknown'));

            if (!isset($groupedData[$periodKey][$clientKey])) {
                $groupedData[$periodKey][$clientKey] = [
                    'receipts'    => [],
                    'sales_bills' => [],
                    'period_label' => $getPeriodLabel($periodKey),
                ];
            }
            $groupedData[$periodKey][$clientKey]['receipts'][] = $record;
        }

        // Group Sales bills by their parsed 6-month period + client
        foreach ($salesBills as $bill) {
            // Use period_start/period_end if available, else fall back to invoice_date
            if ($bill->period_start && $bill->period_end) {
                $periodKey = $bill->period_start->format('Y-m') . '|' . $bill->period_end->format('Y-m');
            } else {
                $periodKey = $getPeriodKey($bill->invoice_date);
            }

            $clientKey = strtoupper(trim($bill->client_name ?? 'Unknown'));

            if (!isset($groupedData[$periodKey][$clientKey])) {
                $groupedData[$periodKey][$clientKey] = [
                    'receipts'     => [],
                    'sales_bills'  => [],
                    'period_label' => $bill->period_label ?? $getPeriodLabel($periodKey),
                ];
            }
            $groupedData[$periodKey][$clientKey]['sales_bills'][] = $bill;
        }

        // Sort: newest period first
        krsort($groupedData);

        // Count matched clients (has both receipt AND sales bill in same period)
        $matchedCount = 0;
        foreach ($groupedData as $clients) {
            foreach ($clients as $clientKey => $data) {
                if ($clientKey === 'period_label') continue;
                if (!empty($data['receipts']) && !empty($data['sales_bills'])) {
                    $matchedCount++;
                }
            }
        }

        // ── PENDING PDFs ──────────────────────────────────────────────
        $pendingPdfs = PendingOutsourcePdf::whereIn('status', ['pending', 'processing', 'failed'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('outsource.index', compact(
            'records',
            'pendingPdfs',
            'filteredSubtotal',
            'filteredGst',
            'filteredGrandTotal',
            'filteredCount',
            'salesBills',
            'salesBillsTotal',
            'groupedData',
            'matchedCount'
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
