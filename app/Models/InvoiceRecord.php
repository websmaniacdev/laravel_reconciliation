<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class InvoiceRecord extends Model
{
    use HasFactory;

    protected $table = 'invoice_records';

    protected $fillable = [
        'client_name',
        'price',
        'document_date',
        'tax_invoice_id',
        'pdf_filename',
        'impressions',
        'campaign_type',
        'is_merged',
        'merged_name',
        'merged_group_id',
    ];

    protected $casts = [
        'document_date' => 'date',
        'is_merged'     => 'boolean',
        'price'         => 'decimal:4',
        'impressions'   => 'integer',
    ];
}