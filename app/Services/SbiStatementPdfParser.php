<?php

namespace App\Services;

use Smalot\PdfParser\Parser;
use Smalot\PdfParser\Config;
use Illuminate\Support\Facades\Log;

class SbiStatementPdfParser
{
    private const MONTHS = [
        'jan' => '01',
        'feb' => '02',
        'mar' => '03',
        'apr' => '04',
        'may' => '05',
        'jun' => '06',
        'jul' => '07',
        'aug' => '08',
        'sep' => '09',
        'oct' => '10',
        'nov' => '11',
        'dec' => '12',
    ];

    // ================== WINDOWS QPDF PATH ==================
    private const QPDF_PATH = 'C:\\qpdf\\bin\\qpdf.exe';
    // =======================================================

    /**
     * Main Parse Function with Password Support
     */
    public function parse(string $pdfPath, string $originalFilename, ?string $password = null): array
    {
        $workingPath = $pdfPath;

        // Agar password hai to PDF unlock karo
        if (!empty(trim($password))) {
            $workingPath = $this->unlockPdfWithQpdf($pdfPath, $password, $originalFilename);
        }

        $text = $this->extractFirstPageText($workingPath);

        // Temporary file cleanup
        if ($workingPath !== $pdfPath && file_exists($workingPath)) {
            @unlink($workingPath);
        }

        if (empty(trim($text))) {
            throw new \RuntimeException("PDF se text nahi nikla.");
        }

        $statementDate = $this->extractStatementDate($text);
        $records = $this->extractTransactions($text, $statementDate, $originalFilename);

        return $records;
    }

    /**
     * Windows ke liye qpdf.exe use karke PDF Unlock
     */
    private function unlockPdfWithQpdf(string $originalPath, string $password, string $filename): string
    {
        $tempDir = storage_path('app/temp');
        if (!is_dir($tempDir)) {
            mkdir($tempDir, 0777, true);
        }

        $tempUnlockedPath = $tempDir . '/unlocked_' . uniqid() . '_' . basename($filename);

        $qpdfExe = self::QPDF_PATH;   // C:\qpdf\bin\qpdf.exe

        // Simple & clean command (no --force)
        $command = sprintf(
            '"%s" --password=%s --decrypt --no-warn --warning-exit-0 "%s" "%s" 2>&1',
            $qpdfExe,
            escapeshellarg($password),
            $originalPath,
            $tempUnlockedPath
        );

        $output = [];
        $returnCode = 0;
        exec($command, $output, $returnCode);

        $errorOutput = implode("\n", $output);

        // Agar file successfully bani hai to warning ignore karo
        if (file_exists($tempUnlockedPath) && filesize($tempUnlockedPath) > 1000) {
            if ($returnCode !== 0) {
                Log::warning("qpdf ran with warnings for {$filename}", ['output' => $errorOutput]);
            }
            Log::info("PDF unlocked successfully (with possible warnings): " . $filename);
            return $tempUnlockedPath;
        }

        // Real failure case
        Log::error("qpdf unlock failed for {$filename}", [
            'command' => $command,
            'return_code' => $returnCode,
            'output' => $errorOutput
        ]);

        if (stripos($errorOutput, 'incorrect password') !== false || stripos($errorOutput, 'password') !== false) {
            throw new \RuntimeException("Galat PDF Password diya gaya hai!");
        }

        throw new \RuntimeException("PDF unlock failed: " . substr($errorOutput, 0, 350));
    }
    /**
     * Transaction Extractor (Improved for glued text)
     */
    private function extractTransactions(string $text, ?string $statementDate, string $filename): array
    {
        $records = [];

        // Pattern 1: Normal
        $pattern1 = '/(\d{1,2}\s+\w{3}\s+\d{2,4})\s+(.+?)\s+([\d,]+\.\d{2})\s*([CD])/i';
        preg_match_all($pattern1, $text, $matches1, PREG_SET_ORDER);
        foreach ($matches1 as $m) {
            $this->addRecord($records, $m, $statementDate, $filename);
        }

        // Pattern 2: Glued (most common in your SBI PDF)
        $pattern2 = '/(\d{1,2}\s+\w{3}\s+\d{2,4})([A-Z][A-Za-z0-9\s&\/\-.,#]+?)([\d,]+\.\d{2})\s*([CD])/i';
        preg_match_all($pattern2, $text, $matches2, PREG_SET_ORDER);
        foreach ($matches2 as $m) {
            $this->addRecord($records, $m, $statementDate, $filename);
        }

        // Pattern 3: Very tight fallback
        if (empty($records)) {
            $pattern3 = '/(\d{1,2}\s+\w{3}\s+\d{2,4})([A-Z][^\d]{5,70}?)([\d,]+\.\d{2})([CD])/i';
            preg_match_all($pattern3, $text, $matches3, PREG_SET_ORDER);
            foreach ($matches3 as $m) {
                $this->addRecord($records, $m, $statementDate, $filename);
            }
        }

        return $records;
    }

    private function addRecord(array &$records, array $match, ?string $statementDate, string $filename): void
    {
        $rawDate   = trim($match[1]);
        $desc      = trim(preg_replace('/\s+/', ' ', $match[2]));
        $amountStr = str_replace(',', '', $match[3]);
        $typeChar  = strtoupper(trim($match[4]));

        $txDate = $this->parseShortDate($rawDate);
        $amount = (float) $amountStr;

        if ($txDate && $amount > 0 && strlen($desc) >= 4) {
            $type = ($typeChar === 'C') ? 'credit' : 'debit';

            $records[] = [
                'transaction_date'    => $txDate,
                'statement_date'      => $statementDate,
                'transaction_details' => $desc,
                'amount'              => $amount,
                'type'                => $type,
                'pdf_filename'        => $filename,
            ];
        }
    }

    private function extractFirstPageText(string $pdfPath): string
    {
        $parser = new Parser();
        $config = new Config();
        $config->setRetainImageContent(false);

        try {
            $pdf = $parser->parseFile($pdfPath, $config);
            $text = $pdf->getPages()[0]->getText() ?? '';
        } catch (\Throwable $e) {
            try {
                $pdf = $parser->parseFile($pdfPath);
                $text = $pdf->getPages()[0]->getText() ?? '';
            } catch (\Throwable $e2) {
                throw new \RuntimeException("PDF text extraction failed.");
            }
        }

        $text = str_replace(["\r", "\t", "\f"], "\n", $text);
        $text = preg_replace('/\s+/', ' ', $text);
        $text = preg_replace('/(\d{1,2})\s*(\w{3})\s*(\d{2,4})/i', '$1 $2 $3', $text);

        return trim($text);
    }

    private function extractStatementDate(string $text): ?string
    {
        if (
            preg_match('/Statement dated (\d{1,2} \w+ \d{4})/i', $text, $m) ||
            preg_match('/for Statement dated (\d{1,2} \w+ \d{4})/i', $text, $m)
        ) {
            return $this->parseShortDate($m[1]);
        }
        return null;
    }

    private function parseShortDate(string $raw): ?string
    {
        $raw = trim($raw);
        if (preg_match('/^(\d{1,2})\s+(\w{3})\s+(\d{2,4})$/i', $raw, $m)) {
            $day   = str_pad($m[1], 2, '0', STR_PAD_LEFT);
            $mon   = strtolower($m[2]);
            $month = self::MONTHS[$mon] ?? null;
            $year  = (strlen($m[3]) === 2 ? '20' . $m[3] : $m[3]);

            if ($month) {
                return "{$year}-{$month}-{$day}";
            }
        }
        return null;
    }

    // Debugging ke liye
    public function getRawText(string $pdfPath, ?string $password = null): string
    {
        $workingPath = $pdfPath;

        if (!empty($password)) {
            try {
                $workingPath = $this->unlockPdfWithQpdf($pdfPath, $password, 'debug.pdf');
            } catch (\Throwable $e) {
                return "Unlock Error: " . $e->getMessage();
            }
        }

        $text = $this->extractFirstPageText($workingPath);

        if ($workingPath !== $pdfPath && file_exists($workingPath)) {
            @unlink($workingPath);
        }

        return "=== RAW TEXT START ===\n" . $text . "\n=== RAW TEXT END ===\n";
    }
}
