<?php

namespace App\Exports;

use App\Models\GodaddyReceipt;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class GodaddyReceiptsExport implements FromQuery, WithHeadings, WithMapping, WithStyles, WithEvents
{
    protected array $filters;
    protected float $totalOrderTotal = 0;
    protected float $totalSubtotal   = 0;
    protected int   $rowIndex        = 0;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function query()
    {
        $query = GodaddyReceipt::query();

        if (!empty($this->filters['domain_name'])) {
            $query->where('domain_name', 'like', '%' . $this->filters['domain_name'] . '%');
        }

        if (!empty($this->filters['product_name'])) {
            $query->where('product_name', 'like', '%' . $this->filters['product_name'] . '%');
        }

        if (!empty($this->filters['from_date'])) {
            $query->whereDate('order_date', '>=', $this->filters['from_date']);
        }

        if (!empty($this->filters['to_date'])) {
            $query->whereDate('order_date', '<=', $this->filters['to_date']);
        }

        if (!empty($this->filters['payment_category'])) {
            $query->where('payment_category', $this->filters['payment_category']);
        }

        return $query->orderBy('order_date', 'desc');
    }

    public function headings(): array
    {
        return [
            '#',
            'Receipt Number',
            'Order Date',
            'Product Name',
            'Domain Name',
            'ICANN Fee',
            'Length',
            'Subtotal',
            'Tax Amount',
            'Order Total',
            'Currency',
            'Payment Category',
            'Payment Sub-Category',
            'Source File',
        ];
    }

    public function map($record): array
    {
        $this->rowIndex++;
        $this->totalSubtotal   += (float) $record->subtotal;
        $this->totalOrderTotal += (float) $record->order_total;

        return [
            $this->rowIndex,
            $record->receipt_number ?? '',
            $record->order_date ? $record->order_date->format('d M Y') : '',
            $record->product_name ?? '',
            $record->domain_name ?? '',
            number_format((float) $record->icann_fee, 2),
            $record->length ?? '',
            number_format((float) $record->subtotal, 2),
            number_format((float) $record->tax_amount, 2),
            number_format((float) $record->order_total, 2),
            $record->currency ?? 'INR',
            $record->payment_category ?? '',
            $record->payment_sub_category ?? '',
            $record->source_filename ?? '',
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font'      => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF16A34A']],
                'alignment' => ['horizontal' => 'center'],
            ],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet   = $event->sheet;
                $lastRow = $sheet->getHighestRow();

                foreach (range('A', 'N') as $col) {
                    $sheet->getColumnDimension($col)->setAutoSize(true);
                }

                $subRow   = $lastRow + 2;
                $totalRow = $lastRow + 3;

                $sheet->setCellValue("G{$subRow}", 'Total Subtotal:');
                $sheet->setCellValue("H{$subRow}", number_format($this->totalSubtotal, 2));

                $sheet->setCellValue("I{$totalRow}", 'Grand Total (Order Total):');
                $sheet->setCellValue("J{$totalRow}", number_format($this->totalOrderTotal, 2));

                foreach ([$subRow, $totalRow] as $row) {
                    $sheet->getStyle("G{$row}:J{$row}")->getFont()->setBold(true);
                    $sheet->getStyle("G{$row}:J{$row}")->getBorders()
                        ->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
                }

                $sheet->getStyle("I{$totalRow}:J{$totalRow}")
                    ->getFill()->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('FFD1FAE5'); // light green
            },
        ];
    }
}
