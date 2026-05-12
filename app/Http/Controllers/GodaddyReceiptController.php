<?php

namespace App\Http\Controllers;

use App\Exports\GodaddyReceiptsExport;
use App\Jobs\ProcessGodaddyFile;
use App\Models\GodaddyReceipt;
use App\Models\PendingGodaddyFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Artisan;

class GodaddyReceiptController extends Controller
{
    public function runCommand()
    {
        Artisan::call('godaddy:process-pending', [
            '--sync' => true
        ]);

        return back()->with('success', 'Command executed successfully!');
    }
    // ══════════════════════════════════════════════════════════════════
    // LIST
    // ══════════════════════════════════════════════════════════════════

    public function index(Request $request)
    {
        // ── GoDaddy Receipts (what YOU paid GoDaddy) ──────────────────
        $query = GodaddyReceipt::query();

        if ($request->filled('domain_name')) {
            $query->where('domain_name', 'like', '%' . $request->domain_name . '%');
        }
        if ($request->filled('product_name')) {
            $query->where('product_name', 'like', '%' . $request->product_name . '%');
        }
        if ($request->filled('from_date')) {
            $query->whereDate('order_date', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('order_date', '<=', $request->to_date);
        }
        if ($request->filled('payment_category')) {
            $query->where('payment_category', $request->payment_category);
        }

        $filteredSubtotal   = (clone $query)->sum('subtotal');
        $filteredIcann      = (clone $query)->sum('icann_fee');
        $filteredTax        = (clone $query)->sum('tax_amount');
        $filteredOrderTotal = (clone $query)->sum('order_total');
        $filteredCount      = (clone $query)->count();

        $records = $query->orderBy('order_date', 'desc')->paginate(500)->withQueryString();

        $paymentCategories = GodaddyReceipt::select('payment_category')
            ->distinct()->whereNotNull('payment_category')->pluck('payment_category');

        $pendingFiles = PendingGodaddyFile::whereIn('status', ['pending', 'processing', 'failed'])
            ->orderBy('created_at', 'desc')->get();

        // ── Your GoDaddy Bills (what you CHARGED clients) ─────────────
        $yourBillQuery = \App\Models\YourGodaddyBill::query();

        if ($request->filled('domain_name')) {
            $yourBillQuery->where('domain_name', 'like', '%' . $request->domain_name . '%');
        }
        if ($request->filled('from_date')) {
            $yourBillQuery->whereDate('invoice_date', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $yourBillQuery->whereDate('invoice_date', '<=', $request->to_date);
        }

        $yourBills      = $yourBillQuery->orderBy('invoice_date', 'desc')->get();
        $yourBillsTotal = $yourBills->sum('total_amount');

        // ── Domain + Month wise Grouped Comparison ────────────────────
        // Key: YYYY-MM → domain → ['receipts' => [...], 'your_bills' => [...]]
        $groupedData = [];

        // Step 1: GoDaddy receipts keyed by month → domain
        foreach ($records as $record) {
            $monthKey  = $record->order_date
                ? $record->order_date->format('Y-m')
                : '0000-00';
            $domainKey = strtolower(trim($record->domain_name ?? 'unknown'));

            $groupedData[$monthKey][$domainKey]['receipts'][]   = $record;
            $groupedData[$monthKey][$domainKey]['your_bills'] ??= [];
        }

        // Step 2: Your bills — match by domain name (fuzzy)
        foreach ($yourBills as $bill) {
            $monthKey  = $bill->invoice_date
                ? $bill->invoice_date->format('Y-m')
                : '0000-00';
            $billDomain = strtolower(trim($bill->domain_name ?? 'unknown'));

            // Try to find matching domain in receipts for this month
            $bestMatch = null;
            if (isset($groupedData[$monthKey])) {
                foreach (array_keys($groupedData[$monthKey]) as $receiptDomain) {
                    if ($this->domainsMatch($billDomain, $receiptDomain)) {
                        $bestMatch = $receiptDomain;
                        break;
                    }
                }
            }

            if ($bestMatch !== null) {
                $groupedData[$monthKey][$bestMatch]['your_bills'][] = $bill;
            } else {
                $groupedData[$monthKey][$billDomain]['your_bills'][] = $bill;
                $groupedData[$monthKey][$billDomain]['receipts']    ??= [];
            }
        }

        krsort($groupedData);

        $matchedCount = 0;
        foreach ($groupedData as $domains) {
            foreach ($domains as $data) {
                if (!empty($data['receipts']) && !empty($data['your_bills'])) {
                    $matchedCount++;
                }
            }
        }

        return view('godaddy.index', compact(
            'records',
            'pendingFiles',
            'paymentCategories',
            'filteredSubtotal',
            'filteredIcann',
            'filteredTax',
            'filteredOrderTotal',
            'filteredCount',
            'yourBills',
            'yourBillsTotal',
            'groupedData',
            'matchedCount'
        ));
    }

    // ── Domain fuzzy matcher ───────────────────────────────────────────
    private function domainsMatch(string $a, string $b): bool
    {
        // Strip www prefix
        $a = preg_replace('/^www\./', '', $a);
        $b = preg_replace('/^www\./', '', $b);

        if ($a === $b) return true;

        // One contains the other (handles subdomain cases)
        if (str_contains($a, $b) || str_contains($b, $a)) return true;

        // Compare base domain without TLD as last resort
        $baseA = explode('.', $a)[0];
        $baseB = explode('.', $b)[0];
        if (strlen($baseA) > 3 && strlen($baseB) > 3 && $baseA === $baseB) return true;

        return false;
    }

    // ══════════════════════════════════════════════════════════════════
    // UPLOAD
    // ══════════════════════════════════════════════════════════════════

    public function upload(Request $request)
    {
        // NOTE: mimes validation CSV ke liye unreliable hai Windows/Linux dono pe
        // kyunki CSV ka MIME type vary karta hai (text/csv, application/csv, text/plain etc.)
        // Isliye sirf extension-based validation use karte hain
        $request->validate([
            'files'   => 'required|array|min:1|max:50',
            'files.*' => 'required|file|max:20480',
        ]);

        $allowedExtensions = ['xlsx', 'xls', 'csv'];
        $queued = 0;
        $errors = [];

        foreach ($request->file('files') as $file) {
            $originalName = $file->getClientOriginalName();
            $ext          = strtolower($file->getClientOriginalExtension());

            // Manual extension check (MIME ke bajay)
            if (!in_array($ext, $allowedExtensions)) {
                $errors[] = "{$originalName}: Only .xlsx, .xls, .csv files allowed. Got .{$ext}";
                continue;
            }

            try {
                $stored = $file->store('godaddy_files', 'local');

                $pending = PendingGodaddyFile::create([
                    'original_filename' => $originalName,
                    'stored_path'       => $stored,
                    'file_type'         => $ext,
                    'status'            => 'pending',
                ]);

                ProcessGodaddyFile::dispatch($pending->id);
                $queued++;
            } catch (\Exception $e) {
                $errors[] = "{$originalName}: " . $e->getMessage();
            }
        }

        $message = $queued > 0
            ? "{$queued} file(s) queued for processing."
            : 'No files were queued.';

        return redirect()->route('godaddy.index')
            ->with('success', $message)
            ->with('upload_errors', $errors);
    }

    // ══════════════════════════════════════════════════════════════════
    // EXPORT
    // ══════════════════════════════════════════════════════════════════

    public function export(Request $request)
    {
        $filters = [
            'domain_name'      => $request->get('domain_name'),
            'product_name'     => $request->get('product_name'),
            'from_date'        => $request->get('from_date'),
            'to_date'          => $request->get('to_date'),
            'payment_category' => $request->get('payment_category'),
        ];

        return Excel::download(
            new GodaddyReceiptsExport($filters),
            'godaddy-receipts-' . now()->format('Y-m-d') . '.xlsx'
        );
    }

    // ══════════════════════════════════════════════════════════════════
    // DELETE
    // ══════════════════════════════════════════════════════════════════

    public function destroy(GodaddyReceipt $receipt)
    {
        $receipt->delete();
        return response()->json(['success' => true]);
    }

    public function destroyPending(PendingGodaddyFile $pending)
    {
        if (Storage::disk('local')->exists($pending->stored_path)) {
            Storage::disk('local')->delete($pending->stored_path);
        }
        $pending->delete();
        return response()->json(['success' => true]);
    }

    public function retryPending(PendingGodaddyFile $pending)
    {
        if (!in_array($pending->status, ['failed', 'pending'])) {
            return response()->json([
                'success' => false,
                'message' => 'Only failed/pending files can be retried.',
            ], 422);
        }

        $pending->update(['status' => 'pending', 'error_message' => null]);
        ProcessGodaddyFile::dispatch($pending->id);

        return response()->json(['success' => true]);
    }
}
