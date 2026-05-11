<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OutsourceSalesBill extends Model
{
    protected $table = 'outsource_sales_bills';

    protected $fillable = [
        'client_name',
        'client_address',
        'client_gstin',
        'client_state',
        'client_state_code',
        'place_of_supply',
        'invoice_number',
        'invoice_date',
        'invoice_type',
        'particulars',
        'description',
        'hsn',
        'qty',
        'rate',
        'gst_percent',
        'amount_before_tax',
        'sgst_percent',
        'sgst_amount',
        'cgst_percent',
        'cgst_amount',
        'igst_percent',
        'igst_amount',
        'round_off',
        'total_amount',
        'original_filename',
        'pdf_path',
    ];

    protected $casts = [
        'invoice_date'      => 'date',
        'qty'               => 'decimal:3',
        'rate'              => 'decimal:2',
        'amount_before_tax' => 'decimal:2',
        'sgst_amount'       => 'decimal:2',
        'cgst_amount'       => 'decimal:2',
        'igst_amount'       => 'decimal:2',
        'total_amount'      => 'decimal:2',
    ];
}
