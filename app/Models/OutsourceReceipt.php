<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OutsourceReceipt extends Model
{
    use HasFactory;

    protected $table = 'outsource_receipts';

    protected $fillable = [
        'client_name',
        'invoice_number',
        'invoice_date',
        'subscription',
        'description',
        'interval',
        'subtotal',
        'gst_amount',
        'grand_total',
        'pdf_filename',
    ];

    protected $casts = [
        'invoice_date' => 'date',
        'subtotal'     => 'decimal:2',
        'gst_amount'   => 'decimal:2',
        'grand_total'  => 'decimal:2',
    ];
}