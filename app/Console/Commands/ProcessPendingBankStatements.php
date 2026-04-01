<?php

namespace App\Console\Commands;

use App\Jobs\ProcessBankStatementPdf;
use App\Models\BankStatementPdf;
use Illuminate\Console\Command;

class ProcessPendingBankStatements extends Command
{
    protected $signature = 'bankstmt:process-pending
                                {--sync         : Process synchronously (no queue worker needed)}
                                {--retry-failed : Also retry previously failed PDFs}
                                {--id=          : Process only a specific PDF by ID}';

    protected $description = 'Process all pending SBI Card bank statement PDFs';

    public function handle(): int
    {
        $this->info('');
        $this->info('╔══════════════════════════════════════════╗');
        $this->info('║   SBI Bank Statement PDF Processor       ║');
        $this->info('╚══════════════════════════════════════════╝');
        $this->info('');

        $query = BankStatementPdf::query();

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
                try {
                    (new ProcessBankStatementPdf($pdf->id))->handle();
                    $fresh = $pdf->fresh();
                    $success++;
                    $bar->setMessage("✅ {$pdf->original_filename} ({$fresh->records_inserted} records)");
                } catch (\Throwable $e) {
                    $failed++;
                    $bar->setMessage("❌ {$pdf->original_filename}");
                }
            } else {
                ProcessBankStatementPdf::dispatch($pdf->id);
                $success++;
            }

            $bar->advance();
        }

        $bar->setMessage('Done!');
        $bar->finish();
        $this->info('');
        $this->info('');

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
                $this->warn("⚠  {$failed} PDF(s) failed. Run with --retry-failed to retry.");

                $failedPdfs = BankStatementPdf::whereIn('id', $pending->pluck('id'))
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
            $this->line('  <fg=green>php artisan bankstmt:process-pending --sync</>');
        }

        $this->info('');
        return self::SUCCESS;
    }
}
