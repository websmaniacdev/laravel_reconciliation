<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BankTransaction extends Model
{
    use HasFactory;

    protected $table = 'bank_transactions';

    protected $fillable = [
        'pdf_filename',
        'transaction_date',
        'statement_date',
        'transaction_details',
        'amount',
        'type',
        'is_merged',
        'merged_name',
        'merged_group_id',
    ];

    protected $casts = [
        'transaction_date' => 'date',
        'statement_date'   => 'date',
        'amount'           => 'decimal:2',
        'is_merged'        => 'boolean',
    ];
}
