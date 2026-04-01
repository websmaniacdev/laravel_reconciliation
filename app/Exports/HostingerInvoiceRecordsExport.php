<?php

namespace App\Exports;

use App\Models\HostingerInvoiceRecord;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Illuminate\Support\Collection;

// ═══════════════════════════════════════════════════════════════
// MAIN EXPORT — Returns multiple sheets
// ═══════════════════════════════════════════════════════════════
class HostingerInvoiceRecordsExport implements WithMultipleSheets
{
    public function __construct(private readonly array $filters = []) {}

    public function sheets(): array
    {
        $inrRecords = $this->getQuery()->where('currency', 'INR')->get();
        $usdRecords = $this->getQuery()->where('currency', '!=', 'INR')->get();
        $allRecords = $this->getQuery()->get();

        return [
            new HostingerSummarySheet($inrRecords, $usdRecords),
            new HostingerCurrencySheet('INR Records', $inrRecords, 'INR'),
            new HostingerCurrencySheet('USD Records', $usdRecords, 'USD'),
            new HostingerAllRecordsSheet($allRecords),
        ];
    }

    private function getQuery()
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
        if (!empty($this->filters['type'])) {
            $query->where('type', $this->filters['type']);
        }
        if (!empty($this->filters['from_date'])) {
            $query->whereDate('invoice_date', '>=', $this->filters['from_date']);
        }
        if (!empty($this->filters['to_date'])) {
            $query->whereDate('invoice_date', '<=', $this->filters['to_date']);
        }

        return $query->orderBy('currency')->orderBy('invoice_date')->orderBy('invoice_number');
    }
}

// ═══════════════════════════════════════════════════════════════
// SHEET 1 — Overall Summary
// ═══════════════════════════════════════════════════════════════
class HostingerSummarySheet implements FromCollection, WithTitle, WithStyles, WithColumnWidths
{
    public function __construct(
        private readonly Collection $inrRecords,
        private readonly Collection $usdRecords,
    ) {}

    public function title(): string
    {
        return 'Summary';
    }

    public function collection(): Collection
    {
        return collect([]); // Data written manually in styles()
    }

    public function columnWidths(): array
    {
        return ['A' => 30, 'B' => 20, 'C' => 20];
    }

    public function styles(Worksheet $sheet): array
    {
        $inr = $this->inrRecords;
        $usd = $this->usdRecords;

        // ── Title ──────────────────────────────────────────────
        $sheet->mergeCells('A1:C1');
        $sheet->setCellValue('A1', 'Hostinger Invoice Export — Summary');
        $sheet->mergeCells('A2:C2');
        $sheet->setCellValue('A2', 'Generated: ' . now()->format('d M Y, h:i A'));

        // ── INR Section ────────────────────────────────────────
        $sheet->mergeCells('A4:C4');
        $sheet->setCellValue('A4', '🇮🇳  INR Summary');

        $sheet->setCellValue('A5', 'Total Records');
        $sheet->setCellValue('B5', $inr->count());
        $sheet->setCellValue('A6', 'Subtotal (Excl. GST)');
        $sheet->setCellValue('B6', '₹ ' . number_format($inr->sum('total_excl_gst'), 2));
        $sheet->setCellValue('A7', 'Total Discount');
        $sheet->setCellValue('B7', '₹ ' . number_format($inr->sum('discount'), 2));
        $sheet->setCellValue('A8', 'GST Amount');
        $sheet->setCellValue('B8', '₹ ' . number_format($inr->sum('gst_amount'), 2));
        $sheet->setCellValue('A9', 'Grand Total (Incl. GST)');
        $sheet->setCellValue('B9', '₹ ' . number_format($inr->sum('line_total'), 2));

        // ── USD Section ────────────────────────────────────────
        $sheet->mergeCells('A11:C11');
        $sheet->setCellValue('A11', '🌐  USD Summary');

        $sheet->setCellValue('A12', 'Total Records');
        $sheet->setCellValue('B12', $usd->count());
        $sheet->setCellValue('A13', 'Subtotal (Excl. GST)');
        $sheet->setCellValue('B13', '$ ' . number_format($usd->sum('total_excl_gst'), 2));
        $sheet->setCellValue('A14', 'Total Discount');
        $sheet->setCellValue('B14', '$ ' . number_format($usd->sum('discount'), 2));
        $sheet->setCellValue('A15', 'GST Amount');
        $sheet->setCellValue('B15', '$ ' . number_format($usd->sum('gst_amount'), 2));
        $sheet->setCellValue('A16', 'Grand Total (Incl. GST)');
        $sheet->setCellValue('B16', '$ ' . number_format($usd->sum('line_total'), 2));

        // ── Combined Section ───────────────────────────────────
        $sheet->mergeCells('A18:C18');
        $sheet->setCellValue('A18', '📊  Overall (Both Currencies)');

        $sheet->setCellValue('A19', 'Total Records');
        $sheet->setCellValue('B19', $inr->count() + $usd->count());
        $sheet->setCellValue('A20', 'INR Grand Total');
        $sheet->setCellValue('B20', '₹ ' . number_format($inr->sum('line_total'), 2));
        $sheet->setCellValue('A21', 'USD Grand Total');
        $sheet->setCellValue('B21', '$ ' . number_format($usd->sum('line_total'), 2));

        // ── Apply Styles ───────────────────────────────────────
        // Title
        $sheet->getStyle('A1')->applyFromArray([
            'font'      => ['bold' => true, 'size' => 16, 'color' => ['rgb' => 'FFFFFF']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4C3D8F']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);
        $sheet->getStyle('A2')->applyFromArray([
            'font'      => ['italic' => true, 'color' => ['rgb' => '888888']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        // INR header
        $sheet->getStyle('A4')->applyFromArray([
            'font' => ['bold' => true, 'size' => 13, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'C2540A']],
        ]);
        $sheet->getStyle('A5:B9')->applyFromArray([
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FFF3E0']],
        ]);
        $sheet->getStyle('A9:B9')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'C2540A']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FFD5A8']],
        ]);

        // USD header
        $sheet->getStyle('A11')->applyFromArray([
            'font' => ['bold' => true, 'size' => 13, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4C3D8F']],
        ]);
        $sheet->getStyle('A12:B16')->applyFromArray([
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'EDE9F8']],
        ]);
        $sheet->getStyle('A16:B16')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => '4C3D8F']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'C5B8F0']],
        ]);

        // Overall header
        $sheet->getStyle('A18')->applyFromArray([
            'font' => ['bold' => true, 'size' => 13, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '2D6A4F']],
        ]);
        $sheet->getStyle('A19:B21')->applyFromArray([
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'D8F3DC']],
        ]);

        // Border all data blocks
        foreach (['A5:B9', 'A12:B16', 'A19:B21'] as $range) {
            $sheet->getStyle($range)->applyFromArray([
                'borders' => [
                    'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'DDDDDD']],
                ],
            ]);
        }

        $sheet->getStyle('A5:A21')->applyFromArray(['font' => ['bold' => true]]);

        $sheet->getRowDimension(1)->setRowHeight(30);

        return [];
    }
}

// ═══════════════════════════════════════════════════════════════
// SHEET 2 & 3 — Currency-specific sheets (INR / USD)
// ═══════════════════════════════════════════════════════════════
class HostingerCurrencySheet implements FromCollection, WithTitle, WithHeadings, WithMapping, WithStyles, WithColumnWidths
{
    public function __construct(
        private readonly string     $sheetTitle,
        private readonly Collection $records,
        private readonly string     $currency,
    ) {}

    public function title(): string
    {
        return $this->sheetTitle;
    }

    public function collection(): Collection
    {
        return $this->records;
    }

    public function headings(): array
    {
        return [
            '#',
            'Invoice #',
            'Invoice Date',
            'Next Billing Date',
            'Billed To (Name)',
            'Billed To (Company)',
            'GSTIN',
            'Description',
            'Client Name',
            'Type',
            'Billing Period',
            'Unit Price',
            'Discount',
            'Excl. GST',
            'GST Amount',
            'Line Total',
            'Currency',
        ];
    }

    public function map($record): array
    {
        static $i = 0;
        $i++;
        return [
            $i,
            $record->invoice_number,
            $record->invoice_date?->format('d M Y'),
            $record->next_billing_date?->format('d M Y'),
            $record->billed_to_name,
            $record->billed_to_company,
            $record->billed_to_gstin,
            $record->description,
            $record->client_name,
            $record->type,
            $record->billing_period,
            $record->unit_price,
            $record->discount,
            $record->total_excl_gst,
            $record->gst_amount,
            $record->line_total,
            $record->currency,
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 5,
            'B' => 18,
            'C' => 14,
            'D' => 16,
            'E' => 22,
            'F' => 22,
            'G' => 18,
            'H' => 35,
            'I' => 18,
            'J' => 10,
            'K' => 22,
            'L' => 12,
            'M' => 12,
            'N' => 14,
            'O' => 12,
            'P' => 13,
            'Q' => 10,
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        $isInr     = $this->currency === 'INR';
        $sym       = $isInr ? '₹' : '$';
        $headerBg  = $isInr ? 'C2540A' : '4C3D8F';
        $totalBg   = $isInr ? 'FFD5A8' : 'C5B8F0';
        $totalText = $isInr ? 'C2540A' : '4C3D8F';
        $altBg     = $isInr ? 'FFF3E0' : 'EDE9F8';

        $lastDataRow = $this->records->count() + 1; // +1 for heading row
        $totalRow    = $lastDataRow + 1;

        // ── Totals row ─────────────────────────────────────────
        $sheet->setCellValue("A{$totalRow}", 'TOTAL');
        $sheet->setCellValue("L{$totalRow}", $sym . ' ' . number_format($this->records->sum('unit_price'), 2));
        $sheet->setCellValue("M{$totalRow}", $sym . ' ' . number_format($this->records->sum('discount'), 2));
        $sheet->setCellValue("N{$totalRow}", $sym . ' ' . number_format($this->records->sum('total_excl_gst'), 2));
        $sheet->setCellValue("O{$totalRow}", $sym . ' ' . number_format($this->records->sum('gst_amount'), 2));
        $sheet->setCellValue("P{$totalRow}", $sym . ' ' . number_format($this->records->sum('line_total'), 2));
        $sheet->setCellValue("Q{$totalRow}", $this->currency);

        // Style totals row
        $sheet->getStyle("A{$totalRow}:Q{$totalRow}")->applyFromArray([
            'font'    => ['bold' => true, 'color' => ['rgb' => $totalText]],
            'fill'    => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $totalBg]],
            'borders' => ['top' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['rgb' => $totalText]]],
        ]);

        // ── Header row ─────────────────────────────────────────
        $sheet->getStyle('A1:Q1')->applyFromArray([
            'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $headerBg]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'wrapText' => true],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(30);

        // ── Alternate row coloring ─────────────────────────────
        if ($lastDataRow > 1) {
            for ($row = 2; $row <= $lastDataRow; $row++) {
                if ($row % 2 === 0) {
                    $sheet->getStyle("A{$row}:Q{$row}")->applyFromArray([
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $altBg]],
                    ]);
                }
            }
        }

        // ── Numeric columns right-align ────────────────────────
        if ($lastDataRow > 1) {
            $sheet->getStyle("L2:P{$lastDataRow}")->applyFromArray([
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
            ]);
        }

        // ── Freeze header pane ─────────────────────────────────
        $sheet->freezePane('A2');

        return [];
    }
}

// ═══════════════════════════════════════════════════════════════
// SHEET 4 — All Records (both currencies together)
// ═══════════════════════════════════════════════════════════════
class HostingerAllRecordsSheet implements FromCollection, WithTitle, WithHeadings, WithMapping, WithStyles, WithColumnWidths
{
    public function __construct(private readonly Collection $records) {}

    public function title(): string
    {
        return 'All Records';
    }

    public function collection(): Collection
    {
        return $this->records;
    }

    public function headings(): array
    {
        return [
            '#',
            'Invoice #',
            'Invoice Date',
            'Next Billing Date',
            'Billed To (Name)',
            'Billed To (Company)',
            'GSTIN',
            'Description',
            'Client Name',
            'Type',
            'Billing Period',
            'Unit Price',
            'Discount',
            'Excl. GST',
            'GST Amount',
            'Line Total',
            'Currency',
        ];
    }

    public function map($record): array
    {
        static $i = 0;
        $i++;
        $sym = $record->currency === 'INR' ? '₹' : '$';
        return [
            $i,
            $record->invoice_number,
            $record->invoice_date?->format('d M Y'),
            $record->next_billing_date?->format('d M Y'),
            $record->billed_to_name,
            $record->billed_to_company,
            $record->billed_to_gstin,
            $record->description,
            $record->client_name,
            $record->type,
            $record->billing_period,
            $sym . ' ' . number_format($record->unit_price, 2),
            $sym . ' ' . number_format($record->discount, 2),
            $sym . ' ' . number_format($record->total_excl_gst, 2),
            $sym . ' ' . number_format($record->gst_amount, 2),
            $sym . ' ' . number_format($record->line_total, 2),
            $record->currency,
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 5,
            'B' => 18,
            'C' => 14,
            'D' => 16,
            'E' => 22,
            'F' => 22,
            'G' => 18,
            'H' => 35,
            'I' => 18,
            'J' => 10,
            'K' => 22,
            'L' => 14,
            'M' => 14,
            'N' => 14,
            'O' => 12,
            'P' => 14,
            'Q' => 10,
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        $inrRecords  = $this->records->where('currency', 'INR');
        $usdRecords  = $this->records->where('currency', '!=', 'INR');
        $lastDataRow = $this->records->count() + 1;

        // ── INR Totals row ─────────────────────────────────────
        $inrRow = $lastDataRow + 1;
        $sheet->setCellValue("A{$inrRow}", 'INR TOTAL');
        $sheet->setCellValue("L{$inrRow}", '₹ ' . number_format($inrRecords->sum('unit_price'), 2));
        $sheet->setCellValue("M{$inrRow}", '₹ ' . number_format($inrRecords->sum('discount'), 2));
        $sheet->setCellValue("N{$inrRow}", '₹ ' . number_format($inrRecords->sum('total_excl_gst'), 2));
        $sheet->setCellValue("O{$inrRow}", '₹ ' . number_format($inrRecords->sum('gst_amount'), 2));
        $sheet->setCellValue("P{$inrRow}", '₹ ' . number_format($inrRecords->sum('line_total'), 2));
        $sheet->setCellValue("Q{$inrRow}", 'INR');

        $sheet->getStyle("A{$inrRow}:Q{$inrRow}")->applyFromArray([
            'font'    => ['bold' => true, 'color' => ['rgb' => 'C2540A']],
            'fill'    => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FFD5A8']],
            'borders' => ['top' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['rgb' => 'C2540A']]],
        ]);

        // ── USD Totals row ─────────────────────────────────────
        $usdRow = $lastDataRow + 2;
        $sheet->setCellValue("A{$usdRow}", 'USD TOTAL');
        $sheet->setCellValue("L{$usdRow}", '$ ' . number_format($usdRecords->sum('unit_price'), 2));
        $sheet->setCellValue("M{$usdRow}", '$ ' . number_format($usdRecords->sum('discount'), 2));
        $sheet->setCellValue("N{$usdRow}", '$ ' . number_format($usdRecords->sum('total_excl_gst'), 2));
        $sheet->setCellValue("O{$usdRow}", '$ ' . number_format($usdRecords->sum('gst_amount'), 2));
        $sheet->setCellValue("P{$usdRow}", '$ ' . number_format($usdRecords->sum('line_total'), 2));
        $sheet->setCellValue("Q{$usdRow}", 'USD');

        $sheet->getStyle("A{$usdRow}:Q{$usdRow}")->applyFromArray([
            'font'    => ['bold' => true, 'color' => ['rgb' => '4C3D8F']],
            'fill'    => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'C5B8F0']],
            'borders' => ['top' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['rgb' => '4C3D8F']]],
        ]);

        // ── Header row ─────────────────────────────────────────
        $sheet->getStyle('A1:Q1')->applyFromArray([
            'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '374151']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'wrapText' => true],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(30);

        // ── Color-code rows by currency ────────────────────────
        if ($lastDataRow > 1) {
            foreach ($this->records as $idx => $record) {
                $row   = $idx + 2;
                $bg    = $record->currency === 'INR' ? 'FFF3E0' : 'EDE9F8';
                $sheet->getStyle("A{$row}:Q{$row}")->applyFromArray([
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $bg]],
                ]);
            }
        }

        $sheet->freezePane('A2');

        return [];
    }
}
