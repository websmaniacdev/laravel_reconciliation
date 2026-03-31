<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PendingInvoicePdf extends Model
{
    use HasFactory;

    protected $table = 'pending_invoice_pdfs';

    protected $fillable = [
        'original_filename',
        'stored_path',
        'status',
        'error_message',
        'records_inserted',
        'processed_at',
    ];

    protected $casts = [
        'processed_at' => 'datetime',
    ];

    // ── Scopes ──────────────────────────────────────────────

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    public function scopePendingOrFailed($query)
    {
        return $query->whereIn('status', ['pending', 'failed']);
    }
}