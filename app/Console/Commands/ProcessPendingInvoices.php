<?php

namespace App\Console\Commands;

use App\Jobs\ProcessInvoicePdf;
use App\Models\PendingInvoicePdf;
use Illuminate\Console\Command;

class ProcessPendingInvoices extends Command
{
    protected $signature = 'invoice:process-pending
                                {--sync      : Process synchronously (no queue worker needed)}
                                {--retry-failed : Also retry previously failed PDFs}
                                {--id=       : Process only a specific pending PDF by ID}';

    protected $description = 'Process all pending Meta Ads invoice PDFs';

    public function handle(): int
    {
        $this->info('');
        $this->info('╔══════════════════════════════════════════╗');
        $this->info('║   Meta Ads Invoice PDF Processor         ║');
        $this->info('╚══════════════════════════════════════════╝');
        $this->info('');

        // ── Build Query ──────────────────────────────────────────────
        $query = PendingInvoicePdf::query();

        if ($this->option('id')) {
            $query->where('id', (int) $this->option('id'));
        } elseif ($this->option('retry-failed')) {
            $query->whereIn('status', ['pending', 'failed']);
        } else {
            $query->where('status', 'pending');
        }

        $pending = $query->orderBy('id')->get();

        if ($pending->isEmpty()) {
            $this->warn('⚠  No pending PDFs found.');
            if (!$this->option('retry-failed')) {
                $this->line('   Tip: Use --retry-failed to also retry failed PDFs.');
            }
            $this->info('');
            return self::SUCCESS;
        }

        $total   = $pending->count();
        $success = 0;
        $failed  = 0;

        $this->info("📄 Found {$total} PDF(s) to process...");
        $this->info('');

        $bar = $this->output->createProgressBar($total);
        $bar->setFormat(' %current%/%max% [%bar%] %percent:3s%% | %message%');
        $bar->setMessage('Starting...');
        $bar->start();

        foreach ($pending as $pdf) {
            $bar->setMessage($pdf->original_filename);

            if ($this->option('sync')) {
                // ── Synchronous — direct process, no queue ───────────
                try {
                    (new ProcessInvoicePdf($pdf->id))->handle();
                    $fresh = $pdf->fresh();
                    $success++;
                    $bar->setMessage("✅ {$pdf->original_filename} ({$fresh->records_inserted} records)");
                } catch (\Throwable $e) {
                    $failed++;
                    $bar->setMessage("❌ {$pdf->original_filename}");
                }
            } else {
                // ── Async — dispatch to queue ────────────────────────
                ProcessInvoicePdf::dispatch($pdf->id);
                $success++;
            }

            $bar->advance();
        }

        $bar->setMessage('Done!');
        $bar->finish();
        $this->info('');
        $this->info('');

        // ── Summary Table ────────────────────────────────────────────
        if ($this->option('sync')) {
            $this->table(
                ['Status', 'Count'],
                [
                    ['✅ Processed Successfully', $success],
                    ['❌ Failed',                 $failed],
                    ['📄 Total',                  $total],
                ]
            );

            if ($failed > 0) {
                $this->warn("⚠  {$failed} PDF(s) failed. Run with --retry-failed to retry them.");
            }

            // Show any failed ones with their errors
            $failedPdfs = PendingInvoicePdf::whereIn('id', $pending->pluck('id'))
                ->where('status', 'failed')
                ->get();

            if ($failedPdfs->isNotEmpty()) {
                $this->info('');
                $this->error('Failed PDFs:');
                foreach ($failedPdfs as $f) {
                    $this->line("  • {$f->original_filename}");
                    $this->line("    Error: {$f->error_message}");
                }
            }

            $this->info('');
            $this->info('🎉 Processing complete!');
        } else {
            $this->info("🚀 {$total} PDF(s) dispatched to queue!");
            $this->info('');
            $this->line('Now run your queue worker:');
            $this->line('  <fg=green>php artisan queue:work --queue=default</>');
            $this->info('');
            $this->line('Or process without a queue (sync mode):');
            $this->line('  <fg=green>php artisan invoice:process-pending --sync</>');
        }

        $this->info('');
        return self::SUCCESS;
    }
}