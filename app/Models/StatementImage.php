<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StatementImage extends Model
{
    use HasFactory;

    protected $table = 'statement_images';

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
}
