<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessHostingerInvoicePdf;
use App\Models\HostingerInvoiceRecord;
use App\Models\HostingerInvoiceSummary;
use App\Models\HostingerPendingPdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\HostingerInvoiceRecordsExport;
use App\Models\YourHostingerBill;
use Illuminate\Support\Facades\Artisan;

class HostingerInvoiceController extends Controller
{
    public function runCommand()
    {
        Artisan::call('hostinger:process-pending', [
            '--sync' => true
        ]);

        return back()->with('success', 'Command executed successfully!');
    }
    // ══════════════════════════════════════════════════════════════════
    // LIST
    // ══════════════════════════════════════════════════════════════════

    public function index(Request $request)
    {
        // ==================== HOSTINGER RECORDS QUERY ====================
        $query = HostingerInvoiceRecord::query();

        if ($request->filled('invoice_number')) {
            $query->where('invoice_number', 'like', '%' . $request->invoice_number . '%');
        }

        if ($request->filled('billed_to')) {
            $query->where(function ($q) use ($request) {
                $q->where('billed_to_name', 'like', '%' . $request->billed_to . '%')
                    ->orWhere('billed_to_company', 'like', '%' . $request->billed_to . '%')
                    ->orWhere('client_name', 'like', '%' . $request->billed_to . '%');
            });
        }

        if ($request->filled('description')) {
            $query->where('description', 'like', '%' . $request->description . '%');
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('from_date')) {
            $query->whereDate('invoice_date', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('invoice_date', '<=', $request->to_date);
        }

        // TOTALS FOR HOSTINGER
        $inrQuery = (clone $query)->where('currency', 'INR');
        $usdQuery = (clone $query)->where('currency', '!=', 'INR');

        $inrSubtotal   = (clone $inrQuery)->sum('total_excl_gst');
        $inrDiscount   = (clone $inrQuery)->sum('discount');
        $inrGst        = (clone $inrQuery)->sum('gst_amount');
        $inrGrandTotal = (clone $inrQuery)->sum('line_total');
        $inrCount      = (clone $inrQuery)->count();

        $usdSubtotal   = (clone $usdQuery)->sum('total_excl_gst');
        $usdDiscount   = (clone $usdQuery)->sum('discount');
        $usdGst        = (clone $usdQuery)->sum('gst_amount');
        $usdGrandTotal = (clone $usdQuery)->sum('line_total');
        $usdCount      = (clone $usdQuery)->count();

        // Paginated Records
        $records = $query->orderBy('invoice_date', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(100)
            ->withQueryString();

        // YOUR BILLS QUERY
        $yourBillQuery = YourHostingerBill::query();

        if ($request->filled('billed_to')) {
            $yourBillQuery->where('client_name', 'like', '%' . $request->billed_to . '%');
        }

        if ($request->filled('invoice_number')) {
            $yourBillQuery->where('invoice_number', 'like', '%' . $request->invoice_number . '%');
        }

        if ($request->filled('from_date')) {
            $yourBillQuery->whereDate('invoice_date', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $yourBillQuery->whereDate('invoice_date', '<=', $request->to_date);
        }

        $yourBills = $yourBillQuery->orderBy('invoice_date', 'desc')->get();
        $grandTotal = $yourBills->sum('total_amount');

        // GROUPED DATA (Month + Client Wise)
        $groupedData = [];

        foreach ($records as $record) {
            $monthKey = $record->invoice_date ? $record->invoice_date->format('Y-m') : '0000-00';
            $clientKey = strtoupper(trim($record->client_name ?? $record->billed_to_name ?? $record->billed_to_company ?? 'Unknown'));

            if (!isset($groupedData[$monthKey])) {
                $groupedData[$monthKey] = [];
            }
            if (!isset($groupedData[$monthKey][$clientKey])) {
                $groupedData[$monthKey][$clientKey] = ['hostinger' => [], 'your_bills' => []];
            }

            $groupedData[$monthKey][$clientKey]['hostinger'][] = $record;
        }

        foreach ($yourBills as $bill) {
            $monthKey = $bill->invoice_date ? $bill->invoice_date->format('Y-m') : '0000-00';
            $clientKey = strtoupper(trim($bill->client_name ?? 'Unknown'));

            if (!isset($groupedData[$monthKey])) {
                $groupedData[$monthKey] = [];
            }
            if (!isset($groupedData[$monthKey][$clientKey])) {
                $groupedData[$monthKey][$clientKey] = ['hostinger' => [], 'your_bills' => []];
            }

            $groupedData[$monthKey][$clientKey]['your_bills'][] = $bill;
        }

        krsort($groupedData);

        // Count Matched Clients
        $matchedCount = 0;
        foreach ($groupedData as $clients) {
            foreach ($clients as $data) {
                if (!empty($data['hostinger']) && !empty($data['your_bills'])) {
                    $matchedCount++;
                }
            }
        }

        // Pending PDFs
        $pendingPdfs = HostingerPendingPdf::whereIn('status', ['pending', 'processing', 'failed'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('hostinger.index', compact(
            'records',
            'groupedData',
            'pendingPdfs',
            'inrGrandTotal',
            'usdGrandTotal',
            'inrCount',
            'usdCount',
            'inrSubtotal',
            'inrDiscount',
            'inrGst',
            'usdSubtotal',
            'usdDiscount',
            'usdGst',
            'grandTotal',
            'matchedCount'
        ));
    }

    // ══════════════════════════════════════════════════════════════════
    // UPLOAD
    // ══════════════════════════════════════════════════════════════════

    public function upload(Request $request)
    {
        $request->validate([
            'pdfs'   => 'required|array|min:1|max:100',
            'pdfs.*' => 'required|file|mimes:pdf|max:10240',
        ]);

        $queued = 0;
        $errors = [];

        foreach ($request->file('pdfs') as $file) {
            try {
                $originalName = $file->getClientOriginalName();
                $stored       = $file->store('hostinger-invoices', 'local');

                $pending = HostingerPendingPdf::create([
                    'original_filename' => $originalName,
                    'stored_path'       => $stored,
                    'status'            => 'pending',
                ]);

                ProcessHostingerInvoicePdf::dispatch($pending->id);
                $queued++;
            } catch (\Exception $e) {
                $errors[] = $file->getClientOriginalName() . ': ' . $e->getMessage();
            }
        }

        $message = $queued > 0
            ? "{$queued} PDF(s) queued. If not processed automatically, run: php artisan hostinger:process-pending --sync"
            : 'No PDFs were queued.';

        return redirect()->route('hostinger.invoices.index')
            ->with('success', $message)
            ->with('upload_errors', $errors);
    }

    // ══════════════════════════════════════════════════════════════════
    // EXPORT
    // ══════════════════════════════════════════════════════════════════

    public function export(Request $request)
    {
        $filters = [
            'invoice_number' => $request->get('invoice_number'),
            'billed_to'      => $request->get('billed_to'),
            'description'    => $request->get('description'),
            'type'           => $request->get('type'),          // ← was missing
            'from_date'      => $request->get('from_date'),
            'to_date'        => $request->get('to_date'),
        ];

        return Excel::download(
            new HostingerInvoiceRecordsExport($filters),
            'hostinger-invoices-' . now()->format('Y-m-d') . '.xlsx'
        );
    }

    // ══════════════════════════════════════════════════════════════════
    // DELETE
    // ══════════════════════════════════════════════════════════════════

    public function destroy(HostingerInvoiceRecord $record)
    {
        $record->delete();
        return response()->json(['success' => true]);
    }

    public function destroyPending(HostingerPendingPdf $pending)
    {
        if (Storage::disk('local')->exists($pending->stored_path)) {
            Storage::disk('local')->delete($pending->stored_path);
        }
        $pending->delete();
        return response()->json(['success' => true]);
    }

    public function retryPending(HostingerPendingPdf $pending)
    {
        if (!in_array($pending->status, ['failed', 'pending'])) {
            return response()->json(['success' => false, 'message' => 'Only failed/pending PDFs can be retried.'], 422);
        }

        $pending->update(['status' => 'pending', 'error_message' => null]);
        ProcessHostingerInvoicePdf::dispatch($pending->id);

        return response()->json(['success' => true]);
    }
    public function updateClientName(Request $request, $id)
    {
        $record = HostingerInvoiceRecord::findOrFail($id);

        $validated = $request->validate([
            'client_name' => 'nullable|string|max:255'
        ]);

        $record->update([
            'client_name' => $validated['client_name'] ?? null
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Client name updated successfully'
        ]);
    }
}
