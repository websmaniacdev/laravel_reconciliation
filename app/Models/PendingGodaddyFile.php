<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PendingGodaddyFile extends Model
{
    use HasFactory;

    protected $table = 'pending_godaddy_files';

    protected $fillable = [
        'original_filename',
        'stored_path',
        'file_type',
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
