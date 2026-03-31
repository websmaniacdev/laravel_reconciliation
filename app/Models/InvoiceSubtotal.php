<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class InvoiceSubtotal extends Model
{
    use HasFactory;

    protected $table = 'invoice_subtotals';

    protected $fillable = [
        'pdf_filename',
        'tax_invoice_id',
        'document_date',
        'subtotal',
        'gst_amount',
        'grand_total',
        'total_records',
        'total_impressions',
    ];

    protected $casts = [
        'document_date'     => 'date',
        'subtotal'          => 'decimal:2',
        'gst_amount'        => 'decimal:2',
        'grand_total'       => 'decimal:2',
        'total_records'     => 'integer',
        'total_impressions' => 'integer',
    ];
}