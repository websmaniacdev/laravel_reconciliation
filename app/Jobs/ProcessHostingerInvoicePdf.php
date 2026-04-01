<?php

namespace App\Jobs;

use App\Models\HostingerInvoiceRecord;
use App\Models\HostingerInvoiceSummary;
use App\Models\HostingerPendingPdf;
use App\Services\HostingerInvoicePdfParser;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ProcessHostingerInvoicePdf implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 120;
    public int $tries   = 3;
    public int $backoff = 10;

    public function __construct(
        private readonly int $pendingId
    ) {}

    public function handle(): void
    {
        /** @var HostingerPendingPdf $pending */
        $pending = HostingerPendingPdf::findOrFail($this->pendingId);

        if ($pending->status === 'done') {
            return;
        }

        $pending->update(['status' => 'processing']);

        try {
            $fullPath = Storage::disk('local')->path($pending->stored_path);

            if (!file_exists($fullPath)) {
                throw new \RuntimeException("PDF file not found on disk: {$fullPath}");
            }

            $parser = new HostingerInvoicePdfParser();
            $data   = $parser->parse($fullPath, $pending->original_filename);

            if (empty($data['line_items'])) {
                throw new \RuntimeException(
                    "Parser returned 0 line items. PDF may be unsupported or in an unexpected format."
                );
            }

            DB::transaction(function () use ($data, $pending) {

                $header   = $data['header'];
                $billedTo = $data['billed_to'];
                $totals   = $data['totals'];
                $inserted = 0;

                // ── Delete any existing records for this PDF (re-upload case) ──
                HostingerInvoiceRecord::where('pdf_filename', $pending->original_filename)->delete();

                foreach ($data['line_items'] as $item) {
                    HostingerInvoiceRecord::create([
                        'pdf_filename'       => $pending->original_filename,
                        'invoice_number'     => $header['invoice_number'],
                        'invoice_date'       => $header['invoice_date'],
                        'next_billing_date'  => $header['next_billing_date'],
                        'order_number'       => $header['order_number'],
                        'billed_to_name'     => $billedTo['name'],
                        'billed_to_company'  => $billedTo['company'],
                        'billed_to_gstin'    => $billedTo['gstin'],
                        'billed_to_email'    => $billedTo['email'],
                        'billed_to_country'  => $billedTo['country'],
                        'description'        => $item['description'],
                        'billing_period'     => $item['billing_period'],
                        'unit_price'         => $item['unit_price'],
                        'discount'           => $item['discount'],
                        'total_excl_gst'     => $item['total_excl_gst'],
                        'gst_amount'         => $item['gst_amount'],
                        'line_total'         => $item['line_total'],
                        'currency'           => $totals['currency'] ?? $header['currency'],
                    ]);
                    $inserted++;
                }

                // ── Upsert summary ─────────────────────────────────────
                HostingerInvoiceSummary::updateOrCreate(
                    ['pdf_filename' => $pending->original_filename],
                    [
                        'invoice_number'    => $header['invoice_number'],
                        'invoice_date'      => $header['invoice_date'],
                        'next_billing_date' => $header['next_billing_date'],
                        'order_number'      => $header['order_number'],
                        'billed_to_name'    => $billedTo['name'],
                        'billed_to_company' => $billedTo['company'],
                        'billed_to_gstin'   => $billedTo['gstin'],
                        'subtotal'          => $totals['subtotal'],
                        'total_discount'    => $totals['total_discount'],
                        'gst_amount'        => $totals['gst_amount'],
                        'grand_total'       => $totals['grand_total'],
                        'amount_paid'       => $totals['amount_paid'],
                        'amount_due'        => $totals['amount_due'],
                        'currency'          => $totals['currency'],
                        'total_records'     => $inserted,
                    ]
                );

                $pending->update([
                    'status'           => 'done',
                    'records_inserted' => $inserted,
                    'processed_at'     => now(),
                    'error_message'    => null,
                ]);
            });

            Log::info("[HostingerPDF] ✅ Done: {$pending->original_filename} | Records: {$pending->fresh()->records_inserted}");
        } catch (\Throwable $e) {
            $pending->update([
                'status'        => 'failed',
                'error_message' => substr($e->getMessage(), 0, 1000),
            ]);

            Log::error("[HostingerPDF] ❌ Failed: {$pending->original_filename} | Error: {$e->getMessage()}");

            throw $e;
        }
    }

    public function failed(\Throwable $exception): void
    {
        $pending = HostingerPendingPdf::find($this->pendingId);
        if ($pending) {
            $pending->update([
                'status'        => 'failed',
                'error_message' => 'Max retries exceeded: ' . substr($exception->getMessage(), 0, 900),
            ]);
        }

        Log::critical("[HostingerPDF] 🔴 Max retries exceeded for pendingId={$this->pendingId}: {$exception->getMessage()}");
    }
}
