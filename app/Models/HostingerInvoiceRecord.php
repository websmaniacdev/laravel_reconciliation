<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class HostingerInvoiceRecord extends Model
{
    use HasFactory;

    protected $table = 'hostinger_invoice_records';

    protected $fillable = [
        'pdf_filename',
        'invoice_number',
        'invoice_date',
        'next_billing_date',
        'order_number',
        'billed_to_name',
        'billed_to_company',
        'billed_to_gstin',
        'billed_to_email',
        'billed_to_country',
        'description',
        'billing_period',
        'unit_price',
        'discount',
        'total_excl_gst',
        'gst_amount',
        'line_total',
        'currency',
    ];

    protected $casts = [
        'invoice_date'      => 'date',
        'next_billing_date' => 'date',
        'unit_price'        => 'decimal:2',
        'discount'          => 'decimal:2',
        'total_excl_gst'    => 'decimal:2',
        'gst_amount'        => 'decimal:2',
        'line_total'        => 'decimal:2',
    ];
}
