<?php

namespace App\Exports;

use App\Models\OutsourceReceipt;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class OutsourceReceiptsExport implements FromQuery, WithHeadings, WithMapping, WithStyles, WithEvents
{
    protected array $filters;
    protected float $totalSubtotal = 0;
    protected int   $rowIndex      = 0;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function query()
    {
        $query = OutsourceReceipt::query();

        if (!empty($this->filters['client_name'])) {
            $query->where('client_name', 'like', '%' . $this->filters['client_name'] . '%');
        }

        if (!empty($this->filters['from_date'])) {
            $query->whereDate('invoice_date', '>=', $this->filters['from_date']);
        }

        if (!empty($this->filters['to_date'])) {
            $query->whereDate('invoice_date', '<=', $this->filters['to_date']);
        }

        return $query->orderBy('invoice_date', 'desc');
    }

    public function headings(): array
    {
        return [
            '#',
            'Client Name',
            'Invoice Number',
            'Invoice Date',
            'Subscription',
            'Description',
            'Interval',
            'Subtotal (INR)',
            'GST 18% (INR)',
            'Grand Total (INR)',
            'PDF File',
        ];
    }

    public function map($record): array
    {
        $this->rowIndex++;
        $this->totalSubtotal += (float) $record->subtotal;

        return [
            $this->rowIndex,
            $record->client_name,
            $record->invoice_number ?? '',
            $record->invoice_date ? $record->invoice_date->format('d M Y') : '',
            $record->subscription ?? '',
            $record->description ?? '',
            $record->interval ?? '',
            number_format((float) $record->subtotal, 2),
            number_format((float) $record->gst_amount, 2),
            number_format((float) $record->grand_total, 2),
            $record->pdf_filename ?? '',
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font'      => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF2563EB']],
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

                foreach (range('A', 'K') as $col) {
                    $sheet->getColumnDimension($col)->setAutoSize(true);
                }

                $totalRow = $lastRow + 2;
                $gstRow   = $totalRow + 1;
                $grandRow = $totalRow + 2;

                $gst        = round($this->totalSubtotal * 0.18, 2);
                $grandTotal = round($this->totalSubtotal + $gst, 2);

                $sheet->setCellValue("H{$totalRow}", number_format($this->totalSubtotal, 2));
                $sheet->setCellValue("G{$totalRow}", 'Subtotal Total:');
                $sheet->setCellValue("I{$gstRow}", number_format($gst, 2));
                $sheet->setCellValue("G{$gstRow}", 'GST (18%):');
                $sheet->setCellValue("J{$grandRow}", number_format($grandTotal, 2));
                $sheet->setCellValue("G{$grandRow}", 'Grand Total:');

                foreach ([$totalRow, $gstRow, $grandRow] as $row) {
                    $sheet->getStyle("G{$row}:J{$row}")->getFont()->setBold(true);
                    $sheet->getStyle("G{$row}:J{$row}")->getBorders()
                        ->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
                }

                $sheet->getStyle("G{$grandRow}:J{$grandRow}")
                    ->getFill()->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('FFDBEAFE');
            },
        ];
    }
}