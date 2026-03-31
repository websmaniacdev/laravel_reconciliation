<?php

namespace App\Jobs;

use App\Models\OutsourceReceipt;
use App\Models\PendingOutsourcePdf;
use App\Services\GoogleWorkspaceReceiptParser;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ProcessOutsourcePdf implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 300;
    public int $tries   = 3;
    public int $backoff = 10;

    public function __construct(private readonly int $pendingId) {}

    public function handle(): void
    {
        /** @var PendingOutsourcePdf $pending */
        $pending = PendingOutsourcePdf::findOrFail($this->pendingId);

        if ($pending->status === 'done') {
            return;
        }

        $pending->update(['status' => 'processing']);

        try {
            $fullPath = Storage::disk('local')->path($pending->stored_path);

            if (!file_exists($fullPath)) {
                throw new \RuntimeException("PDF file not found on disk: {$fullPath}");
            }

            $parser = new GoogleWorkspaceReceiptParser();
            $data   = $parser->parse($fullPath, $pending->original_filename, $pending->client_name);

            DB::transaction(function () use ($data, $pending) {
                OutsourceReceipt::create($data);

                $pending->update([
                    'status'           => 'done',
                    'records_inserted' => 1,
                    'processed_at'     => now(),
                    'error_message'    => null,
                ]);
            });

            Log::info("[OutsourcePDF] ✅ Done: {$pending->original_filename}");
        } catch (\Throwable $e) {
            $pending->update([
                'status'        => 'failed',
                'error_message' => substr($e->getMessage(), 0, 1000),
            ]);

            Log::error("[OutsourcePDF] ❌ Failed: {$pending->original_filename} | {$e->getMessage()}");

            throw $e;
        }
    }

    public function failed(\Throwable $exception): void
    {
        $pending = PendingOutsourcePdf::find($this->pendingId);
        if ($pending) {
            $pending->update([
                'status'        => 'failed',
                'error_message' => 'Max retries exceeded: ' . substr($exception->getMessage(), 0, 900),
            ]);
        }
        Log::critical("[OutsourcePDF] 🔴 Max retries exceeded for pendingId={$this->pendingId}");
    }
}