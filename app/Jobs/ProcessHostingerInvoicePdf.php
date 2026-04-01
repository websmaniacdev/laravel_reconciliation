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
                throw new \RuntimeException("Parser returned 0 line items.");
            }

            DB::transaction(function () use ($data, $pending) {

                $header     = $data['header'];
                $billedTo   = $data['billed_to'];
                $totals     = $data['totals'];
                $lineItems  = $data['line_items'];

                // Delete old records
                HostingerInvoiceRecord::where('pdf_filename', $pending->original_filename)->delete();

                // ====================== COMBINE ALL ITEMS ======================
                $combinedDescriptions = [];
                $totalUnitPrice       = 0.0;
                $totalDiscount        = 0.0;
                $totalExclGst         = 0.0;
                $totalGst             = 0.0;
                $totalLineTotal       = 0.0;

                $clientName = null;
                $type       = 'Hosting';   // Default

                foreach ($lineItems as $item) {
                    $desc = trim($item['description'] ?? '');

                    if (!empty($desc)) {
                        $combinedDescriptions[] = $desc;

                        // === NEW LOGIC: Detect Domain and extract Client Name ===
                        $detectedDomain = $this->extractDomainAndClientName($desc);

                        if ($detectedDomain) {
                            $clientName = $detectedDomain['client_name'];
                            $type       = 'Domain';
                        }
                    }

                    // Sum amounts
                    $totalUnitPrice += $item['unit_price']     ?? 0;
                    $totalDiscount  += $item['discount']       ?? 0;
                    $totalExclGst   += $item['total_excl_gst'] ?? 0;
                    $totalGst       += $item['gst_amount']     ?? 0;
                    $totalLineTotal += $item['line_total']     ?? 0;
                }

                $finalDescription = implode(" |\n ", $combinedDescriptions);
                $billingPeriod    = $lineItems[0]['billing_period'] ?? null;

                // Create SINGLE record with new fields
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
                    'description'        => $finalDescription,
                    'client_name'        => $clientName,
                    'type'               => $type,
                    'billing_period'     => $billingPeriod,
                    'unit_price'         => round($totalUnitPrice, 2),
                    'discount'           => round($totalDiscount, 2),
                    'total_excl_gst'     => round($totalExclGst, 2),
                    'gst_amount'         => round($totalGst, 2),
                    'line_total'         => round($totalLineTotal, 2),
                    'currency'           => $totals['currency'] ?? $header['currency'],
                ]);

                // Update Summary
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
                        'total_records'     => 1,
                    ]
                );

                $pending->update([
                    'status'           => 'done',
                    'records_inserted' => 1,
                    'processed_at'     => now(),
                    'error_message'    => null,
                ]);
            });

            Log::info("[HostingerPDF] ✅ Done: {$pending->original_filename} | Client: {} | Type: {}");
        } catch (\Throwable $e) {
            $pending->update([
                'status'        => 'failed',
                'error_message' => substr($e->getMessage(), 0, 1000),
            ]);

            Log::error("[HostingerPDF] ❌ Failed: {$pending->original_filename} | Error: {$e->getMessage()}");
            throw $e;
        }
    }

    private function extractDomainAndClientName(string $description): ?array
    {
        $desc = trim($description);

        // Improved regex: properly handles .co.in, .com, .in, .net etc.
        // It looks for word characters followed by valid domain extension
        if (preg_match('/([a-z0-9\-]+)\.(co\.in|com|in|net|org|co|io|biz|info)/i', $desc, $matches)) {
            $clientName = $matches[1];
            $fullDomain = $matches[0];

            // Extra safety: Agar "co" domain name ke end mein aa jaye toh ignore kare
            if (strtolower($clientName) === 'co') {
                // Try to find better match before ".co.in"
                if (preg_match('/([a-z0-9\-]+)\.co\.in/i', $desc, $betterMatch)) {
                    $clientName = $betterMatch[1];
                    $fullDomain = $betterMatch[0];
                }
            }

            return [
                'client_name' => $clientName,
                'domain'      => strtolower($fullDomain),
            ];
        }

        return null; // Not a domain → Hosting
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
