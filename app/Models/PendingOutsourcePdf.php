<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PendingOutsourcePdf extends Model
{
    use HasFactory;

    protected $table = 'pending_outsource_pdfs';

    protected $fillable = [
        'client_name',
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

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }
}