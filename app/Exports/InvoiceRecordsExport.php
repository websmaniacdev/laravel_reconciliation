<?php

namespace App\Exports;

use App\Models\InvoiceRecord;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class InvoiceRecordsExport implements FromQuery, WithHeadings, WithMapping, WithStyles, WithEvents
{
    protected array $filters;
    protected float $totalPrice = 0;
    protected int   $rowIndex   = 0;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    // ── Query with filters ────────────────────────────────────────────

    public function query()
    {
        $query = InvoiceRecord::query();

        if (!empty($this->filters['client_name'])) {
            $query->where('client_name', 'like', '%' . $this->filters['client_name'] . '%');
        }

        if (!empty($this->filters['from_date'])) {
            $query->whereDate('document_date', '>=', $this->filters['from_date']);
        }

        if (!empty($this->filters['to_date'])) {
            $query->whereDate('document_date', '<=', $this->filters['to_date']);
        }

        return $query->orderBy('document_date', 'desc');
    }

    // ── Column headings ───────────────────────────────────────────────

    public function headings(): array
    {
        return [
            '#',
            'Client Name',
            'Price (INR)',
            'Document Date',
            'Is Merged',
            'Merged Name',
        ];
    }

    // ── Row mapping ───────────────────────────────────────────────────

    public function map($record): array
    {
        $this->rowIndex++;
        $this->totalPrice += (float) $record->price;

        return [
            $this->rowIndex,
            $record->client_name,
            number_format((float) $record->price, 2),
            $record->document_date ? $record->document_date->format('d M Y') : '',
            $record->is_merged ? 'Yes' : 'No',
            $record->merged_name ?? '',
        ];
    }

    // ── Header row styling ────────────────────────────────────────────

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font'      => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                'fill'      => [
                    'fillType'   => Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FF2563EB'],
                ],
                'alignment' => ['horizontal' => 'center'],
            ],
        ];
    }

    // ── Footer totals + GST ───────────────────────────────────────────

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet   = $event->sheet;
                $lastRow = $sheet->getHighestRow();

                // Auto-width for all columns
                foreach (range('A', 'F') as $col) {
                    $sheet->getColumnDimension($col)->setAutoSize(true);
                }

                // 2 blank rows after data
                $totalRow = $lastRow + 2;
                $gstRow   = $totalRow + 1;
                $grandRow = $totalRow + 2;

                $gst        = round($this->totalPrice * 0.18, 2);
                $grandTotal = round($this->totalPrice + $gst, 2);

                // ── Subtotal row ──────────────────────────────────────
                $sheet->setCellValue("B{$totalRow}", 'Subtotal (pre-GST)');
                $sheet->setCellValue("C{$totalRow}", number_format($this->totalPrice, 2));

                // ── GST row ───────────────────────────────────────────
                $sheet->setCellValue("B{$gstRow}", 'GST (18%)');
                $sheet->setCellValue("C{$gstRow}", number_format($gst, 2));

                // ── Grand total row ───────────────────────────────────
                $sheet->setCellValue("B{$grandRow}", 'Grand Total (incl. GST)');
                $sheet->setCellValue("C{$grandRow}", number_format($grandTotal, 2));

                // ── Styling for footer rows ───────────────────────────
                foreach ([$totalRow, $gstRow, $grandRow] as $row) {
                    $sheet->getStyle("B{$row}:C{$row}")
                        ->getFont()->setBold(true);

                    $sheet->getStyle("B{$row}:C{$row}")
                        ->getBorders()
                        ->getAllBorders()
                        ->setBorderStyle(Border::BORDER_THIN);
                }

                // Grand total — distinct background
                $sheet->getStyle("B{$grandRow}:C{$grandRow}")
                    ->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('FFDBEAFE'); // light blue
            },
        ];
    }
}