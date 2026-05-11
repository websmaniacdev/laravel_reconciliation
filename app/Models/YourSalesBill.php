<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class YourSalesBill extends Model
{
    protected $table = 'your_sales_bills';

    protected $fillable = [
        'client_name',
        'client_address',
        'client_gstin',
        'client_place_of_supply',
        'client_state',
        'client_state_code',
        'invoice_number',
        'invoice_date',
        'invoice_type',
        'line_items',
        'description',
        'amount_before_tax',
        'sgst_amount',
        'cgst_amount',
        'igst_amount',
        'round_off',
        'total_amount',
        'original_filename',
        'pdf_path',
    ];

    protected $casts = [
        'invoice_date'      => 'date',
        'line_items'        => 'array',
        'amount_before_tax' => 'decimal:2',
        'sgst_amount'       => 'decimal:2',
        'cgst_amount'       => 'decimal:2',
        'igst_amount'       => 'decimal:2',
        'round_off'         => 'decimal:2',
        'total_amount'      => 'decimal:2',
    ];
}
