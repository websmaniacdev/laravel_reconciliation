<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class YourHostingerBill extends Model
{
    protected $table = 'your_hostinger_bills';

    protected $fillable = [
        'client_name',
        'client_domain',
        'client_address',
        'client_gstin',
        'client_state',
        'client_state_code',
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

    /**
     * Strip .com/.in/.org etc. from domain to get display name
     */
    public static function cleanClientName($domain)
    {
        if (!$domain) {
            return null;
        }

        // remove protocol
        $domain = preg_replace('#^https?://#', '', $domain);

        // remove www
        $domain = preg_replace('/^www\./', '', $domain);

        // remove extension
        $domain = preg_replace('/\.(com|in|org|net|co\.in|info|biz|io)$/i', '', $domain);

        // remove anything after /
        $domain = explode('/', $domain)[0];

        // lowercase + trim
        return strtolower(trim($domain));
    }
}
