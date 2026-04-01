<?php

namespace App\Console\Commands;

use App\Jobs\ProcessGodaddyFile;
use App\Models\PendingGodaddyFile;
use Illuminate\Console\Command;

class ProcessPendingGodaddy extends Command
{
    protected $signature = 'godaddy:process-pending
                                {--sync         : Process synchronously (no queue worker needed)}
                                {--retry-failed : Also retry previously failed files}
                                {--id=          : Process only a specific pending file by ID}';

    protected $description = 'Process all pending Godaddy Excel/CSV files';

    public function handle(): int
    {
        $this->info('');
        $this->info('╔══════════════════════════════════════════╗');
        $this->info('║   Godaddy Receipt File Processor         ║');
        $this->info('╚══════════════════════════════════════════╝');
        $this->info('');

        $query = PendingGodaddyFile::query();

        if ($this->option('id')) {
            $query->where('id', (int) $this->option('id'));
        } elseif ($this->option('retry-failed')) {
            $query->whereIn('status', ['pending', 'failed']);
        } else {
            $query->where('status', 'pending');
        }

        $pending = $query->orderBy('id')->get();

        if ($pending->isEmpty()) {
            $this->warn('⚠  No pending files found.');
            if (!$this->option('retry-failed')) {
                $this->line('   Tip: Use --retry-failed to also retry failed files.');
            }
            $this->info('');
            return self::SUCCESS;
        }

        $total   = $pending->count();
        $success = 0;
        $failed  = 0;

        $this->info("📄 Found {$total} file(s) to process...");
        $this->info('');

        $bar = $this->output->createProgressBar($total);
        $bar->setFormat(' %current%/%max% [%bar%] %percent:3s%% | %message%');
        $bar->setMessage('Starting...');
        $bar->start();

        foreach ($pending as $file) {
            $bar->setMessage($file->original_filename);

            if ($this->option('sync')) {
                try {
                    (new ProcessGodaddyFile($file->id))->handle();
                    $fresh = $file->fresh();
                    $success++;
                    $bar->setMessage("✅ {$file->original_filename} ({$fresh->records_inserted} records)");
                } catch (\Throwable $e) {
                    $failed++;
                    $bar->setMessage("❌ {$file->original_filename}");
                }
            } else {
                ProcessGodaddyFile::dispatch($file->id);
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
                $this->warn("⚠  {$failed} file(s) failed. Run with --retry-failed to retry.");

                $failedFiles = PendingGodaddyFile::whereIn('id', $pending->pluck('id'))
                    ->where('status', 'failed')
                    ->get();

                if ($failedFiles->isNotEmpty()) {
                    $this->info('');
                    $this->error('Failed Files:');
                    foreach ($failedFiles as $f) {
                        $this->line("  • {$f->original_filename}");
                        $this->line("    Error: {$f->error_message}");
                    }
                }
            }

            $this->info('');
            $this->info('🎉 Processing complete!');
        } else {
            $this->info("🚀 {$total} file(s) dispatched to queue!");
            $this->info('');
            $this->line('Now run your queue worker:');
            $this->line('  <fg=green>php artisan queue:work --queue=default</>');
            $this->info('');
            $this->line('Or process without a queue:');
            $this->line('  <fg=green>php artisan godaddy:process-pending --sync</>');
        }

        $this->info('');
        return self::SUCCESS;
    }
}
