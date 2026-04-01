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

        // ── Summary totals ────────────────────────────────────────────
        $filteredSubtotal   = (clone $query)->sum('subtotal');
        $filteredIcann      = (clone $query)->sum('icann_fee');
        $filteredTax        = (clone $query)->sum('tax_amount');
        $filteredOrderTotal = (clone $query)->sum('order_total');
        $filteredCount      = (clone $query)->count();

        $records = $query
            ->orderBy('order_date', 'desc')
            ->paginate(500)
            ->withQueryString();

        $paymentCategories = GodaddyReceipt::select('payment_category')
            ->distinct()
            ->whereNotNull('payment_category')
            ->pluck('payment_category');

        $pendingFiles = PendingGodaddyFile::whereIn('status', ['pending', 'processing', 'failed'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('godaddy.index', compact(
            'records',
            'pendingFiles',
            'paymentCategories',
            'filteredSubtotal',
            'filteredIcann',
            'filteredTax',
            'filteredOrderTotal',
            'filteredCount'
        ));
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
