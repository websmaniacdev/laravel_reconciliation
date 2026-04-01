<?php

namespace App\Exports;

use App\Models\HostingerInvoiceRecord;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class HostingerInvoiceRecordsExport implements FromQuery, WithHeadings, WithMapping, WithStyles
{
    public function __construct(private readonly array $filters = []) {}

    public function query()
    {
        $query = HostingerInvoiceRecord::query();

        if (!empty($this->filters['invoice_number'])) {
            $query->where('invoice_number', 'like', '%' . $this->filters['invoice_number'] . '%');
        }

        if (!empty($this->filters['billed_to'])) {
            $query->where(function ($q) {
                $q->where('billed_to_name', 'like', '%' . $this->filters['billed_to'] . '%')
                    ->orWhere('billed_to_company', 'like', '%' . $this->filters['billed_to'] . '%');
            });
        }

        if (!empty($this->filters['description'])) {
            $query->where('description', 'like', '%' . $this->filters['description'] . '%');
        }

        if (!empty($this->filters['from_date'])) {
            $query->whereDate('invoice_date', '>=', $this->filters['from_date']);
        }

        if (!empty($this->filters['to_date'])) {
            $query->whereDate('invoice_date', '<=', $this->filters['to_date']);
        }

        return $query->orderBy('invoice_date', 'desc')->orderBy('invoice_number');
    }

    public function headings(): array
    {
        return [
            'Invoice #',
            'Invoice Date',
            'Next Billing Date',
            'Order #',
            'Billed To (Name)',
            'Billed To (Company)',
            'GSTIN',
            'Email',
            'Country',
            'Description',
            'Billing Period',
            'Unit Price',
            'Discount',
            'Total Excl. GST',
            'GST Amount',
            'Line Total',
            'Currency',
            'PDF File',
        ];
    }

    public function map($record): array
    {
        return [
            $record->invoice_number,
            $record->invoice_date?->format('d M Y'),
            $record->next_billing_date?->format('d M Y'),
            $record->order_number,
            $record->billed_to_name,
            $record->billed_to_company,
            $record->billed_to_gstin,
            $record->billed_to_email,
            $record->billed_to_country,
            $record->description,
            $record->billing_period,
            $record->unit_price,
            $record->discount,
            $record->total_excl_gst,
            $record->gst_amount,
            $record->line_total,
            $record->currency,
            $record->pdf_filename,
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
