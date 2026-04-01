<?php

namespace App\Jobs;

use App\Models\GodaddyReceipt;
use App\Models\PendingGodaddyFile;
use App\Services\GodaddyFileParser;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ProcessGodaddyFile implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 300;
    public int $tries   = 3;
    public int $backoff = 10;

    public function __construct(private readonly int $pendingId) {}

    public function handle(): void
    {
        /** @var PendingGodaddyFile $pending */
        $pending = PendingGodaddyFile::findOrFail($this->pendingId);

        if ($pending->status === 'done') {
            return;
        }

        $pending->update(['status' => 'processing']);

        try {
            $fullPath = Storage::disk('local')->path($pending->stored_path);

            if (!file_exists($fullPath)) {
                throw new \RuntimeException("File not found on disk: {$fullPath}");
            }

            $parser  = new GodaddyFileParser();
            $records = $parser->parse($fullPath, $pending->original_filename);

            if (empty($records)) {
                throw new \RuntimeException("Parser returned 0 records.");
            }

            DB::transaction(function () use ($records, $pending) {
                $inserted = 0;

                foreach ($records as $rec) {
                    GodaddyReceipt::create($rec);
                    $inserted++;
                }

                $pending->update([
                    'status'           => 'done',
                    'records_inserted' => $inserted,
                    'processed_at'     => now(),
                    'error_message'    => null,
                ]);
            });

            Log::info("[GodaddyFile] ✅ Done: {$pending->original_filename} | Records: {$pending->fresh()->records_inserted}");
        } catch (\Throwable $e) {
            $pending->update([
                'status'        => 'failed',
                'error_message' => substr($e->getMessage(), 0, 1000),
            ]);

            Log::error("[GodaddyFile] ❌ Failed: {$pending->original_filename} | {$e->getMessage()}");

            throw $e;
        }
    }

    public function failed(\Throwable $exception): void
    {
        $pending = PendingGodaddyFile::find($this->pendingId);
        if ($pending) {
            $pending->update([
                'status'        => 'failed',
                'error_message' => 'Max retries exceeded: ' . substr($exception->getMessage(), 0, 900),
            ]);
        }

        Log::critical("[GodaddyFile] 🔴 Max retries exceeded for pendingId={$this->pendingId}");
    }
}
