<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class HostingerInvoiceSummary extends Model
{
    use HasFactory;

    protected $table = 'hostinger_invoice_summaries';

    protected $fillable = [
        'pdf_filename',
        'invoice_number',
        'invoice_date',
        'next_billing_date',
        'order_number',
        'billed_to_name',
        'billed_to_company',
        'billed_to_gstin',
        'subtotal',
        'total_discount',
        'gst_amount',
        'grand_total',
        'amount_paid',
        'amount_due',
        'currency',
        'total_records',
    ];

    protected $casts = [
        'invoice_date'      => 'date',
        'next_billing_date' => 'date',
        'subtotal'          => 'decimal:2',
        'total_discount'    => 'decimal:2',
        'gst_amount'        => 'decimal:2',
        'grand_total'       => 'decimal:2',
        'amount_paid'       => 'decimal:2',
        'amount_due'        => 'decimal:2',
    ];
}
