<?php

namespace App\Jobs;

use App\Models\InvoiceRecord;
use App\Models\InvoiceSubtotal;
use App\Models\PendingInvoicePdf;
use App\Services\MetaInvoicePdfParser;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ProcessInvoicePdf implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Max execution time per PDF (seconds)
     */
    public int $timeout = 300;

    /**
     * Retry attempts on failure
     */
    public int $tries = 3;

    /**
     * Delay between retries (seconds)
     */
    public int $backoff = 10;

    public function __construct(
        private readonly int $pendingId
    ) {}

    public function handle(): void
    {
        /** @var PendingInvoicePdf $pending */
        $pending = PendingInvoicePdf::findOrFail($this->pendingId);

        // Already processed successfully? Skip silently
        if ($pending->status === 'done') {
            return;
        }

        // Mark as processing
        $pending->update(['status' => 'processing']);

        try {
            $fullPath = Storage::disk('local')->path($pending->stored_path);

            if (!file_exists($fullPath)) {
                throw new \RuntimeException(
                    "PDF file not found on disk: {$fullPath}"
                );
            }

            $parser  = new MetaInvoicePdfParser();
            $records = $parser->parse($fullPath, $pending->original_filename);

            if (empty($records)) {
                throw new \RuntimeException(
                    "Parser returned 0 records. PDF may be unsupported or empty."
                );
            }

            // ── Wrap everything in a transaction ──────────────────────
            DB::transaction(function () use ($records, $pending) {

                $inserted         = 0;
                $totalSubtotal    = 0.0;
                $totalImpressions = 0;
                $taxInvoiceId     = null;
                $documentDate     = null;

                foreach ($records as $rec) {
                    InvoiceRecord::create([
                        'client_name'    => $rec['client_name'],
                        'price'          => $rec['price'],
                        'document_date'  => $rec['document_date'],
                        'tax_invoice_id' => $rec['tax_invoice_id'],
                        'impressions'    => $rec['impressions'] ?? 0,
                        'campaign_type'  => $rec['campaign_type'] ?? 'Unknown',
                        'pdf_filename'   => $rec['pdf_filename'],
                        'is_merged'      => false,
                    ]);

                    $totalSubtotal    += (float) $rec['price'];
                    $totalImpressions += (int) ($rec['impressions'] ?? 0);

                    // First non-null values ko capture karo
                    $taxInvoiceId = $taxInvoiceId ?? ($rec['tax_invoice_id'] ?? null);
                    $documentDate = $documentDate ?? ($rec['document_date'] ?? null);

                    $inserted++;
                }

                // ── PDF-wise subtotal upsert ───────────────────────────
                $subtotal   = round($totalSubtotal, 2);
                $gst        = round($totalSubtotal * 0.18, 2);
                $grandTotal = round($subtotal + $gst, 2);

                InvoiceSubtotal::updateOrCreate(
                    ['pdf_filename' => $pending->original_filename],
                    [
                        'tax_invoice_id'    => $taxInvoiceId,
                        'document_date'     => $documentDate,
                        'subtotal'          => $subtotal,
                        'gst_amount'        => $gst,
                        'grand_total'       => $grandTotal,
                        'total_records'     => $inserted,
                        'total_impressions' => $totalImpressions,
                    ]
                );

                // ── Mark pending record as done ───────────────────────
                $pending->update([
                    'status'           => 'done',
                    'records_inserted' => $inserted,
                    'processed_at'     => now(),
                    'error_message'    => null,
                ]);
            });

            Log::info(
                "[InvoicePDF] ✅ Done: {$pending->original_filename} | Records: {$pending->fresh()->records_inserted}"
            );
        } catch (\Throwable $e) {
            // Mark as failed with error message
            $pending->update([
                'status'        => 'failed',
                'error_message' => substr($e->getMessage(), 0, 1000),
            ]);

            Log::error(
                "[InvoicePDF] ❌ Failed: {$pending->original_filename} | Error: {$e->getMessage()}"
            );

            // Re-throw so queue can handle retries
            throw $e;
        }
    }

    /**
     * Called when all retry attempts exhausted
     */
    public function failed(\Throwable $exception): void
    {
        $pending = PendingInvoicePdf::find($this->pendingId);
        if ($pending) {
            $pending->update([
                'status'        => 'failed',
                'error_message' => 'Max retries exceeded: ' . substr($exception->getMessage(), 0, 900),
            ]);
        }

        Log::critical(
            "[InvoicePDF] 🔴 Max retries exceeded for pendingId={$this->pendingId}: {$exception->getMessage()}"
        );
    }
}