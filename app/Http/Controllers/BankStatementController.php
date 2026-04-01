<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessBankStatementPdf;
use App\Models\BankStatementPdf;
use App\Models\BankTransaction;
use App\Exports\BankTransactionExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Artisan;

class BankStatementController extends Controller
{
    public function runCommand()
    {
        Artisan::call('bankstmt:process-pending', [
            '--sync' => true
        ]);

        return back()->with('success', 'Command executed successfully!');
    }
    // ══════════════════════════════════════════════════════════════════
    // LIST
    // ══════════════════════════════════════════════════════════════════

    public function index(Request $request)
    {
        $query = BankTransaction::query();

        if ($request->filled('search')) {
            $query->where('transaction_details', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('from_date')) {
            $query->whereDate('transaction_date', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('transaction_date', '<=', $request->to_date);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // ── Summary totals (before pagination) ───────────────────────
        $filteredCount  = (clone $query)->count();
        $filteredCredit = (clone $query)->where('type', 'credit')->sum('amount');
        $filteredDebit  = (clone $query)->where('type', 'debit')->sum('amount');
        $filteredNet    = round($filteredCredit - $filteredDebit, 2);

        $transactions = $query
            ->orderBy('transaction_date', 'desc')
            ->paginate(1500)
            ->withQueryString();

        // Pending/failed PDFs
        $pendingPdfs = BankStatementPdf::whereIn('status', ['pending', 'processing', 'failed'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('bankstatements.index', compact(
            'transactions',
            'pendingPdfs',
            'filteredCount',
            'filteredCredit',
            'filteredDebit',
            'filteredNet'
        ));
    }

    // ══════════════════════════════════════════════════════════════════
    // UPLOAD  (PDF + optional password per PDF)
    // ══════════════════════════════════════════════════════════════════

    public function upload(Request $request)
    {
        $request->validate([
            'pdfs'      => 'required|array|min:1|max:200',
            'pdfs.*'    => 'required|file|mimes:pdf|max:10240',
            'passwords' => 'nullable|array',        // passwords[filename] = "secret"
        ]);

        $queued = 0;
        $errors = [];

        foreach ($request->file('pdfs') as $file) {
            try {
                $originalName = $file->getClientOriginalName();
                $stored       = $file->store('bankstatements', 'local');

                // Password lookup: passwords array keyed by original filename
                $passwords = $request->input('passwords', []);
                $password  = $passwords[$originalName] ?? null;

                $pending = BankStatementPdf::create([
                    'original_filename' => $originalName,
                    'stored_path'       => $stored,
                    'password'          => $password ?: null,
                    'status'            => 'pending',
                ]);

                ProcessBankStatementPdf::dispatch($pending->id);
                $queued++;
            } catch (\Exception $e) {
                $errors[] = $file->getClientOriginalName() . ': ' . $e->getMessage();
            }
        }

        $message = $queued > 0
            ? "{$queued} PDF(s) queued. If not processed, run: php artisan bankstmt:process-pending --sync"
            : 'No PDFs were queued.';

        return redirect()->route('bankstatements.index')
            ->with('success', $message)
            ->with('upload_errors', $errors);
    }

    // ══════════════════════════════════════════════════════════════════
    // EXPORT
    // ══════════════════════════════════════════════════════════════════

    public function export(Request $request)
    {
        $filters = [
            'search'    => $request->get('search'),
            'from_date' => $request->get('from_date'),
            'to_date'   => $request->get('to_date'),
            'type'      => $request->get('type'),
        ];

        return Excel::download(
            new BankTransactionExport($filters),
            'bank-transactions-' . now()->format('Y-m-d') . '.xlsx'
        );
    }

    // ══════════════════════════════════════════════════════════════════
    // MERGE
    // ══════════════════════════════════════════════════════════════════

    public function merge(Request $request)
    {
        $request->validate([
            'record_ids'   => 'required|array|min:2',
            'record_ids.*' => 'integer|exists:bank_transactions,id',
            'merged_name'  => 'required|string|max:255',
        ]);

        $ids     = $request->record_ids;
        $name    = $request->merged_name;
        $groupId = (int) DB::table('bank_transactions')->max('merged_group_id') + 1;

        DB::transaction(function () use ($ids, $name, $groupId) {
            $records = BankTransaction::whereIn('id', $ids)->get();

            $totalCredit = $records->where('type', 'credit')->sum('amount');
            $totalDebit  = $records->where('type', 'debit')->sum('amount');
            $net         = $totalCredit - $totalDebit;
            $type        = $net >= 0 ? 'credit' : 'debit';

            BankTransaction::create([
                'pdf_filename'        => 'merged',
                'transaction_date'    => $records->min('transaction_date'),
                'statement_date'      => $records->first()?->statement_date,
                'transaction_details' => $name,
                'amount'              => abs($net),
                'type'                => $type,
                'is_merged'           => true,
                'merged_name'         => $name,
                'merged_group_id'     => $groupId,
            ]);

            BankTransaction::whereIn('id', $ids)->delete();
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
            'record_ids.*' => 'integer|exists:bank_transactions,id',
            'merged_name'  => 'required|string|max:255',
        ]);

        $ids      = $request->record_ids;
        $baseName = trim($request->merged_name);

        $records = BankTransaction::whereIn('id', $ids)
            ->orderBy('transaction_date')
            ->get();

        $groups = $records->groupBy(function ($rec) {
            if (!$rec->transaction_date) return 'unknown';
            return \Carbon\Carbon::make($rec->transaction_date)->format('Y-m');
        });

        if ($groups->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'No records to merge.'], 422);
        }

        $mergedCount = 0;

        DB::transaction(function () use ($groups, $baseName, &$mergedCount) {
            $maxGroupId = (int) DB::table('bank_transactions')->max('merged_group_id');

            foreach ($groups as $yearMonth => $groupRecords) {
                if ($yearMonth === 'unknown') {
                    $monthLabel = 'Unknown Date';
                } else {
                    $minDate = $groupRecords
                        ->filter(fn($r) => $r->transaction_date !== null)
                        ->sortBy('transaction_date')
                        ->first()
                        ?->transaction_date;

                    $monthLabel = $minDate
                        ? \Carbon\Carbon::make($minDate)->format('F Y')
                        : \Carbon\Carbon::createFromFormat('Y-m', $yearMonth)->format('F Y');
                }

                $mergedName  = $baseName . ' - ' . $monthLabel;
                $maxGroupId++;

                $totalCredit = $groupRecords->where('type', 'credit')->sum('amount');
                $totalDebit  = $groupRecords->where('type', 'debit')->sum('amount');
                $net         = $totalCredit - $totalDebit;
                $type        = $net >= 0 ? 'credit' : 'debit';

                BankTransaction::create([
                    'pdf_filename'        => 'merged',
                    'transaction_date'    => $groupRecords->min('transaction_date'),
                    'statement_date'      => $groupRecords->first()?->statement_date,
                    'transaction_details' => $mergedName,
                    'amount'              => abs($net),
                    'type'                => $type,
                    'is_merged'           => true,
                    'merged_name'         => $mergedName,
                    'merged_group_id'     => $maxGroupId,
                ]);

                BankTransaction::whereIn('id', $groupRecords->pluck('id')->toArray())->delete();
                $mergedCount++;
            }
        });

        return response()->json(['success' => true, 'merged_groups' => $mergedCount]);
    }

    // ══════════════════════════════════════════════════════════════════
    // DELETE
    // ══════════════════════════════════════════════════════════════════

    public function destroy(BankTransaction $transaction)
    {
        $transaction->delete();
        return response()->json(['success' => true]);
    }

    public function destroyPending(BankStatementPdf $pending)
    {
        if (Storage::disk('local')->exists($pending->stored_path)) {
            Storage::disk('local')->delete($pending->stored_path);
        }
        $pending->delete();
        return response()->json(['success' => true]);
    }

    public function retryPending(BankStatementPdf $pending)
    {
        if (!in_array($pending->status, ['failed', 'pending'])) {
            return response()->json(['success' => false, 'message' => 'Only failed/pending PDFs can be retried.'], 422);
        }

        $pending->update(['status' => 'pending', 'error_message' => null]);
        ProcessBankStatementPdf::dispatch($pending->id);

        return response()->json(['success' => true]);
    }
}
