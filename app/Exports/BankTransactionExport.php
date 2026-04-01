<?php

namespace App\Exports;

use App\Models\BankTransaction;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class BankTransactionExport implements FromQuery, WithHeadings, WithMapping, WithStyles, WithEvents
{
    protected array $filters;
    protected float $totalCredit = 0;
    protected float $totalDebit  = 0;
    protected int   $rowIndex    = 0;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function query()
    {
        $query = BankTransaction::query();

        if (!empty($this->filters['search'])) {
            $query->where('transaction_details', 'like', '%' . $this->filters['search'] . '%');
        }

        if (!empty($this->filters['from_date'])) {
            $query->whereDate('transaction_date', '>=', $this->filters['from_date']);
        }

        if (!empty($this->filters['to_date'])) {
            $query->whereDate('transaction_date', '<=', $this->filters['to_date']);
        }

        if (!empty($this->filters['type'])) {
            $query->where('type', $this->filters['type']);
        }

        return $query->orderBy('transaction_date', 'desc');
    }

    public function headings(): array
    {
        return ['#', 'Date', 'Transaction Details', 'Type', 'Amount (₹)', 'PDF File'];
    }

    public function map($tx): array
    {
        $this->rowIndex++;

        if ($tx->type === 'credit') {
            $this->totalCredit += (float) $tx->amount;
        } else {
            $this->totalDebit += (float) $tx->amount;
        }

        return [
            $this->rowIndex,
            $tx->transaction_date ? $tx->transaction_date->format('d M Y') : '',
            $tx->transaction_details,
            strtoupper($tx->type),
            number_format((float) $tx->amount, 2),
            $tx->pdf_filename,
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font'      => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF1D4ED8']],
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

                foreach (range('A', 'F') as $col) {
                    $sheet->getColumnDimension($col)->setAutoSize(true);
                }

                $creditRow = $lastRow + 2;
                $debitRow  = $creditRow + 1;
                $netRow    = $creditRow + 2;

                $sheet->setCellValue("D{$creditRow}", 'Total Credit');
                $sheet->setCellValue("E{$creditRow}", number_format($this->totalCredit, 2));

                $sheet->setCellValue("D{$debitRow}", 'Total Debit');
                $sheet->setCellValue("E{$debitRow}", number_format($this->totalDebit, 2));

                $net = $this->totalCredit - $this->totalDebit;
                $sheet->setCellValue("D{$netRow}", 'Net (Credit - Debit)');
                $sheet->setCellValue("E{$netRow}", number_format($net, 2));

                foreach ([$creditRow, $debitRow, $netRow] as $row) {
                    $sheet->getStyle("D{$row}:E{$row}")->getFont()->setBold(true);
                    $sheet->getStyle("D{$row}:E{$row}")->getBorders()->getAllBorders()
                        ->setBorderStyle(Border::BORDER_THIN);
                }

                $sheet->getStyle("D{$netRow}:E{$netRow}")
                    ->getFill()->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('FFDBEAFE');
            },
        ];
    }
}
