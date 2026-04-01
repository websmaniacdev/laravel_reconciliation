<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BankStatementPdf extends Model
{
    use HasFactory;

    protected $table = 'bank_statement_pdfs';

    protected $fillable = [
        'original_filename',
        'stored_path',
        'password',
        'status',
        'error_message',
        'records_inserted',
        'processed_at',
    ];

    protected $casts = [
        'processed_at' => 'datetime',
    ];

    // ── Scopes ────────────────────────────────────────────────────────

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

    // ── Relations ─────────────────────────────────────────────────────

    public function transactions()
    {
        return $this->hasMany(BankTransaction::class, 'pdf_filename', 'original_filename');
    }
}
