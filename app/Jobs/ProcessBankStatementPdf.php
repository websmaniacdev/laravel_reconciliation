<?php

namespace App\Jobs;

use App\Models\BankStatementPdf;
use App\Models\BankTransaction;
use App\Services\SbiStatementPdfParser;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ProcessBankStatementPdf implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 300;
    public int $tries   = 3;
    public int $backoff = 10;

    public function __construct(
        private readonly int $pendingId
    ) {}

    public function handle(): void
    {
        /** @var BankStatementPdf $pdf */
        $pdf = BankStatementPdf::findOrFail($this->pendingId);

        if ($pdf->status === 'done') {
            return;
        }

        $pdf->update(['status' => 'processing']);

        try {
            $fullPath = Storage::disk('local')->path($pdf->stored_path);

            if (!file_exists($fullPath)) {
                throw new \RuntimeException("PDF file not found on disk: {$fullPath}");
            }

            $parser   = new SbiStatementPdfParser();
            $records  = $parser->parse(
                $fullPath,
                $pdf->original_filename,
                $pdf->password ?: null
            );


            if (empty($records)) {
                echo $parser->getRawText($fullPath);
                // throw new \RuntimeException(
                //     "Parser returned 0 records. PDF format unsupported or no transactions found."
                // );
            }

            DB::transaction(function () use ($records, $pdf) {
                $inserted = 0;

                foreach ($records as $rec) {
                    BankTransaction::create([
                        'pdf_filename'        => $rec['pdf_filename'],
                        'transaction_date'    => $rec['transaction_date'],
                        'statement_date'      => $rec['statement_date'],
                        'transaction_details' => $rec['transaction_details'],
                        'amount'              => $rec['amount'],
                        'type'                => $rec['type'],
                        'is_merged'           => false,
                    ]);
                    $inserted++;
                }

                $pdf->update([
                    'status'           => 'done',
                    'records_inserted' => $inserted,
                    'processed_at'     => now(),
                    'error_message'    => null,
                ]);
            });

            Log::info("[BankStmt] ✅ Done: {$pdf->original_filename} | Records: {$pdf->fresh()->records_inserted}");
        } catch (\Throwable $e) {
            $pdf->update([
                'status'        => 'failed',
                'error_message' => substr($e->getMessage(), 0, 1000),
            ]);

            Log::error("[BankStmt] ❌ Failed: {$pdf->original_filename} | {$e->getMessage()}");

            throw $e;
        }
    }

    public function failed(\Throwable $exception): void
    {
        $pdf = BankStatementPdf::find($this->pendingId);
        if ($pdf) {
            $pdf->update([
                'status'        => 'failed',
                'error_message' => 'Max retries exceeded: ' . substr($exception->getMessage(), 0, 900),
            ]);
        }

        Log::critical("[BankStmt] 🔴 Max retries exceeded for id={$this->pendingId}: {$exception->getMessage()}");
    }
}
