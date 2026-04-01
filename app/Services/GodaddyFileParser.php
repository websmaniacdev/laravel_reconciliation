<?php

namespace App\Services;

use Illuminate\Support\Carbon;

/**
 * GodaddyFileParser
 *
 * Godaddy exported Excel / CSV file se yeh columns extract karta hai:
 *   - Order date
 *   - Product name
 *   - Name  (domain name)
 *   - ICANN fee
 *   - Length
 *   - Subtotal amount
 *   - Tax amount
 *   - Order total
 *   - Payment Category
 *   - Payment Sub-Category
 *
 * Supported formats: .xlsx, .csv
 * Required package: maatwebsite/excel OR just PhpSpreadsheet
 * Install: composer require phpoffice/phpspreadsheet
 */
class GodaddyFileParser
{
    // ── Column name map (Godaddy column → our field) ─────────────────
    private const COLUMN_MAP = [
        'receipt_number'       => ['receipt number'],
        'order_date'           => ['order date'],
        'product_name'         => ['product name'],
        'domain_name'          => ['name'],
        'icann_fee'            => ['icann fee'],
        'length'               => ['length'],
        'subtotal'             => ['subtotal amount', 'subtotal'],
        'tax_amount'           => ['tax amount'],
        'order_total'          => ['order total'],
        'currency'             => ['currency'],
        'payment_category'     => ['payment category'],
        'payment_sub_category' => ['payment sub-category', 'payment subcategory'],
    ];

    /**
     * Parse file and return array of records.
     *
     * @param  string $filePath   Full path to .xlsx or .csv file
     * @param  string $filename   Original filename (for source_filename column)
     * @return array              Array of associative arrays (one per row)
     * @throws \RuntimeException  If file cannot be parsed or has no valid rows
     */
    public function parse(string $filePath, string $filename): array
    {
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        $rows = match ($ext) {
            'csv'  => $this->readCsv($filePath),
            'xlsx', 'xls' => $this->readExcel($filePath),
            default => throw new \RuntimeException("Unsupported file type: .{$ext}. Only .xlsx, .xls, .csv supported."),
        };

        if (empty($rows)) {
            throw new \RuntimeException("File empty hai ya header row nahi mili.");
        }

        return $this->mapRows($rows, $filename);
    }

    // ─────────────────────────────────────────────────────────────────
    // File readers
    // ─────────────────────────────────────────────────────────────────

    private function readExcel(string $filePath): array
    {
        if (!class_exists(\PhpOffice\PhpSpreadsheet\IOFactory::class)) {
            throw new \RuntimeException(
                "PhpSpreadsheet not installed. Run: composer require phpoffice/phpspreadsheet"
            );
        }

        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($filePath);
        $sheet       = $spreadsheet->getActiveSheet();
        $rows        = [];

        foreach ($sheet->toArray(null, true, true, false) as $row) {
            // Skip completely empty rows
            if (empty(array_filter($row, fn($v) => $v !== null && $v !== ''))) {
                continue;
            }
            $rows[] = $row;
        }

        return $rows;
    }

    private function readCsv(string $filePath): array
    {
        $rows   = [];
        $handle = fopen($filePath, 'r');

        if (!$handle) {
            throw new \RuntimeException("CSV file open nahi hua: {$filePath}");
        }

        // Detect delimiter (comma or semicolon)
        $firstLine = fgets($handle);
        rewind($handle);
        $delimiter = substr_count($firstLine, ';') > substr_count($firstLine, ',') ? ';' : ',';

        while (($row = fgetcsv($handle, 0, $delimiter)) !== false) {
            if (!empty(array_filter($row, fn($v) => $v !== null && $v !== ''))) {
                $rows[] = $row;
            }
        }

        fclose($handle);
        return $rows;
    }

    // ─────────────────────────────────────────────────────────────────
    // Row mapping
    // ─────────────────────────────────────────────────────────────────

    private function mapRows(array $rows, string $filename): array
    {
        if (empty($rows)) {
            throw new \RuntimeException("File mein koi rows nahi hain.");
        }

        // First row = headers
        $headerRow = array_map(
            fn($h) => strtolower(trim((string) $h)),
            $rows[0]
        );

        // Build column index map: our_field → column_index
        $colIndex = [];
        foreach (self::COLUMN_MAP as $field => $aliases) {
            foreach ($aliases as $alias) {
                $idx = array_search($alias, $headerRow, true);
                if ($idx !== false) {
                    $colIndex[$field] = $idx;
                    break;
                }
            }
        }

        // Validate required columns
        $required = ['order_date', 'product_name', 'domain_name', 'order_total'];
        foreach ($required as $req) {
            if (!isset($colIndex[$req])) {
                throw new \RuntimeException(
                    "Required column '{$req}' file mein nahi mili. " .
                        "Available columns: " . implode(', ', $headerRow)
                );
            }
        }

        $records = [];

        // Process data rows (skip header)
        for ($i = 1; $i < count($rows); $i++) {
            $row = $rows[$i];

            // Get value safely
            $get = function (string $field) use ($row, $colIndex): mixed {
                if (!isset($colIndex[$field])) return null;
                $idx = $colIndex[$field];
                return isset($row[$idx]) ? trim((string) $row[$idx]) : null;
            };

            $orderTotal = $this->toFloat($get('order_total'));

            // Skip rows with zero or null order total
            if ($orderTotal <= 0) {
                continue;
            }

            $records[] = [
                'receipt_number'       => $get('receipt_number'),
                'order_date'           => $this->parseDate($get('order_date')),
                'product_name'         => $get('product_name'),
                'domain_name'          => $get('domain_name'),
                'icann_fee'            => $this->toFloat($get('icann_fee')),
                'length'               => $get('length'),
                'subtotal'             => $this->toFloat($get('subtotal')),
                'tax_amount'           => $this->toFloat($get('tax_amount')),
                'order_total'          => $orderTotal,
                'currency'             => $get('currency') ?? 'INR',
                'payment_category'     => $get('payment_category'),
                'payment_sub_category' => $get('payment_sub_category'),
                'source_filename'      => $filename,
            ];
        }

        if (empty($records)) {
            throw new \RuntimeException(
                "File mein koi valid rows nahi mili (order_total > 0 wali rows chahiye)."
            );
        }

        return $records;
    }

    // ─────────────────────────────────────────────────────────────────
    // Helpers
    // ─────────────────────────────────────────────────────────────────

    private function toFloat(?string $value): float
    {
        if ($value === null || $value === '') return 0.0;
        // Remove commas, currency symbols
        $clean = preg_replace('/[^\d.\-]/', '', str_replace(',', '', $value));
        return (float) ($clean ?: 0);
    }

    private function parseDate(?string $value): ?string
    {
        if (empty($value)) return null;

        try {
            // ISO format: "2026-03-25T04:47:13.000Z"
            if (preg_match('/^\d{4}-\d{2}-\d{2}/', $value)) {
                return Carbon::parse($value)->format('Y-m-d');
            }

            // Other formats: "25 Mar 2026", "03/25/2026", etc.
            return Carbon::parse($value)->format('Y-m-d');
        } catch (\Throwable $e) {
            return null;
        }
    }
}
