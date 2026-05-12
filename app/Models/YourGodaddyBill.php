<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class YourGodaddyBill extends Model
{
    protected $fillable = [
        'invoice_number',
        'invoice_date',
        'invoice_type',
        'client_name',
        'client_address',
        'client_gstin',
        'client_place_of_supply',
        'domain_name',
        'service_period',
        'description',
        'amount_before_tax',
        'sgst_amount',
        'cgst_amount',
        'igst_amount',
        'round_off',
        'total_amount',
        'pdf_path',
        'original_filename',
    ];

    protected $casts = [
        'invoice_date'    => 'date',
        'amount_before_tax' => 'float',
        'sgst_amount'     => 'float',
        'cgst_amount'     => 'float',
        'igst_amount'     => 'float',
        'round_off'       => 'float',
        'total_amount'    => 'float',
    ];
}
