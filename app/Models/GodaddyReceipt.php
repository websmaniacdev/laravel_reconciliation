<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class GodaddyReceipt extends Model
{
    use HasFactory;

    protected $table = 'godaddy_receipts';

    protected $fillable = [
        'receipt_number',
        'order_date',
        'product_name',
        'domain_name',
        'icann_fee',
        'length',
        'subtotal',
        'tax_amount',
        'order_total',
        'currency',
        'payment_category',
        'payment_sub_category',
        'source_filename',
    ];

    protected $casts = [
        'order_date'   => 'date',
        'icann_fee'    => 'decimal:2',
        'subtotal'     => 'decimal:2',
        'tax_amount'   => 'decimal:2',
        'order_total'  => 'decimal:2',
    ];
}
