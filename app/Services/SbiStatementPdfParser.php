<?php

namespace App\Services;

use Smalot\PdfParser\Parser;

/**
 * SBI Card Monthly Statement PDF Parser
 *
 * Sirf ye teen cheez extract karta he page 1 se:
 *   1. Statement Date  (header se — e.g. "23 Jan 2025")
 *   2. Transaction Date (e.g. "30 Dec 24")
 *   3. Transaction Details (e.g. "PAYMENT RECEIVED 000DP014365000905RjZoe6")
 *   4. Amount + Type  (e.g. 4,485.00 C  or  1,200.00 D)
 *
 * PDF locked hone par password pass karo — smalot/pdf-parser automatically
 * decrypt karke text extract karta he.
 */
class SbiStatementPdfParser
{
    // ── Month name → number map (short + full) ────────────────────────
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
        'january' => '01',
        'february' => '02',
        'march' => '03',
        'april' => '04',
        'june' => '06',
        'july' => '07',
        'august' => '08',
        'september' => '09',
        'october' => '10',
        'november' => '11',
        'december' => '12',
    ];

    /**
     * @param  string      $pdfPath         Full path to PDF file on disk
     * @param  string      $originalFilename Original filename (for pdf_filename field)
     * @param  string|null $password         PDF password (null if not locked)
     * @return array<int, array{
     *   transaction_date: string|null,
     *   statement_date: string|null,
     *   transaction_details: string,
     *   amount: float,
     *   type: string,
     *   pdf_filename: string
     * }>
     */
    public function parse(string $pdfPath, string $originalFilename, ?string $password = null): array
    {
        $parser = new Parser();

        // ── Decrypt locked PDF first using qpdf ──────────────────────
        // Smalot PDF parser natively password-protected PDFs read nahi kar
        // sakta, isliye pehle qpdf se decrypt karke temp file banao.
        $tempPath = null;

        try {
            if (!empty($password)) {
                $tempPath = sys_get_temp_dir() . '/sbi_decrypted_' . uniqid() . '.pdf';

                $escapedPwd  = escapeshellarg($password);
                $escapedIn   = escapeshellarg($pdfPath);
                $escapedOut  = escapeshellarg($tempPath);

                $cmd    = "qpdf --password={$escapedPwd} --decrypt {$escapedIn} {$escapedOut} 2>&1";
                $output = shell_exec($cmd);
                $exitCode = 0;

                // Check if decrypted file was created
                if (!file_exists($tempPath) || filesize($tempPath) === 0) {
                    throw new \RuntimeException(
                        "PDF decrypt failed. Password galat ho sakta he. qpdf output: " . trim($output ?? '')
                    );
                }

                $parsePath = $tempPath;
            } else {
                $parsePath = $pdfPath;
            }

            $pdf  = $parser->parseFile($parsePath);
            $text = $pdf->getText();
        } catch (\Exception $e) {
            // Cleanup temp file on error
            if ($tempPath && file_exists($tempPath)) {
                @unlink($tempPath);
            }
            throw new \RuntimeException(
                "PDF parse failed. " .
                    (!empty($password) ? "Password galat ho sakta he. " : "") .
                    "Error: " . $e->getMessage()
            );
        } finally {
            // Always cleanup temp decrypted file
            if ($tempPath && file_exists($tempPath)) {
                @unlink($tempPath);
            }
        }

        // ── Extract Statement Date from header ────────────────────────
        // Pattern: "for Statement dated 23 Jan 2025"
        // Also handles: "Statement Date\n23 Jan 2025"
        $statementDate = null;

        if (preg_match('/for\s+Statement\s+dated\s+(\d{1,2}\s+\w+\s+\d{4})/i', $text, $m)) {
            $statementDate = $this->parseDate($m[1]);
        } elseif (preg_match('/Statement\s+Date\s+(\d{1,2}\s+\w+\s+\d{4})/i', $text, $m)) {
            $statementDate = $this->parseDate($m[1]);
        }

        // ── Normalize lines ───────────────────────────────────────────
        $lines = array_map('trim', explode("\n", $text));
        $lines = array_values(array_filter($lines, fn($l) => $l !== ''));

        // ── Find "Transaction Details" header line ────────────────────
        // Table starts after the "Transaction Details ... Date Amount" header row
        $startIdx = 0;
        $lineCount = count($lines);

        for ($i = 0; $i < $lineCount; $i++) {
            if (
                stripos($lines[$i], 'Transaction Details') !== false
                && stripos($lines[$i], 'Date') !== false
                && stripos($lines[$i], 'Amount') !== false
            ) {
                $startIdx = $i + 1;
                break;
            }
            // Also handle split across two lines
            if (stripos($lines[$i], 'Transaction Details') !== false) {
                $startIdx = $i + 1;
                break;
            }
        }

        $records = [];

        // ── Main parse loop ───────────────────────────────────────────
        // SBI statement transaction line format (from PDF extraction):
        //
        // "30 Dec 24 PAYMENT RECEIVED 000DP014365000905RjZoe6 4,485.00 C"
        //
        // OR split across multiple lines by smalot:
        // "30 Dec 24"
        // "PAYMENT RECEIVED 000DP014365000905RjZoe6"
        // "4,485.00 C"
        //
        // Strategy: scan for lines that start with a date pattern (DD Mon YY)
        // then greedily collect the description + amount line.

        $i = $startIdx;

        while ($i < $lineCount) {
            $line = $lines[$i];

            // ── Check if this line starts with a transaction date ─────
            // Pattern: "30 Dec 24" or "30 Dec 2024" at beginning of line
            if (preg_match('/^(\d{1,2}\s+(?:Jan|Feb|Mar|Apr|May|Jun|Jul|Aug|Sep|Oct|Nov|Dec)\s+\d{2,4})\s*(.*)/i', $line, $m)) {

                $rawDate     = $m[1];
                $restOfLine  = trim($m[2]);
                $txDate      = $this->parseShortDate($rawDate);

                // Collect description + amount
                // restOfLine may already contain everything, or next lines may have it
                $collectedText = $restOfLine;

                // Look ahead to collect more text if amount not yet found
                $j = $i + 1;
                while ($j < $lineCount) {
                    $next = $lines[$j];

                    // Stop if next line looks like another transaction date
                    if (preg_match('/^\d{1,2}\s+(?:Jan|Feb|Mar|Apr|May|Jun|Jul|Aug|Sep|Oct|Nov|Dec)\s+\d{2,4}/i', $next)) {
                        break;
                    }

                    // Stop if we've already found the amount+type marker in collected text
                    if ($this->hasAmountAndType($collectedText)) {
                        break;
                    }

                    $collectedText .= ' ' . $next;
                    $j++;
                }

                $i = $j; // Advance main pointer

                // ── Parse amount + type from collected text ───────────
                $parsed = $this->extractAmountAndType($collectedText);
                if ($parsed === null) {
                    // No valid amount found — skip this line
                    continue;
                }

                [$amount, $type, $description] = $parsed;

                // Skip lines that are clearly not real transactions
                // (e.g. footer notes, balance rows without description)
                if (empty(trim($description)) || $amount <= 0) {
                    continue;
                }

                $records[] = [
                    'transaction_date'    => $txDate,
                    'statement_date'      => $statementDate,
                    'transaction_details' => trim($description),
                    'amount'              => $amount,
                    'type'                => $type,
                    'pdf_filename'        => $originalFilename,
                ];

                continue;
            }

            $i++;
        }

        // ── Fallback: try single-line regex on full text ──────────────
        if (empty($records)) {
            $records = $this->fallbackParse($text, $statementDate, $originalFilename);
        }

        return $records;
    }

    // ─────────────────────────────────────────────────────────────────
    // Private helpers
    // ─────────────────────────────────────────────────────────────────

    /**
     * Check if text already contains amount + C/D marker
     */
    private function hasAmountAndType(string $text): bool
    {
        return (bool) preg_match('/[\d,]+\.\d{2}\s+[CD]\b/i', $text);
    }

    /**
     * Extract amount, type (credit/debit), and description from collected text.
     *
     * Returns [amount, type, description] or null if not found.
     *
     * Patterns:
     *   "PAYMENT RECEIVED 000DP... 4,485.00 C"
     *   "PURCHASE AMAZON 1,200.00 D"
     */
    private function extractAmountAndType(string $text): ?array
    {
        // Pattern: ...description... AMOUNT C|D
        // Amount can have commas: 1,00,000.00
        if (!preg_match('/^(.*?)\s+([\d,]+\.\d{2})\s+([CD])\s*$/i', trim($text), $m)) {
            return null;
        }

        $description = trim($m[1]);
        $amount      = (float) str_replace(',', '', $m[2]);
        $type        = strtolower($m[3]) === 'c' ? 'credit' : 'debit';

        return [$amount, $type, $description];
    }

    /**
     * Parse "30 Dec 24" or "30 Dec 2024" → "2024-12-30"
     */
    private function parseShortDate(string $raw): ?string
    {
        $raw = trim($raw);

        // Try DD Mon YY (2-digit year)
        if (preg_match('/^(\d{1,2})\s+(\w+)\s+(\d{2})$/', $raw, $m)) {
            $day   = str_pad($m[1], 2, '0', STR_PAD_LEFT);
            $month = self::MONTHS[strtolower($m[2])] ?? null;
            $year  = '20' . $m[3];
            if ($month) return "{$year}-{$month}-{$day}";
        }

        // Try DD Mon YYYY (4-digit year)
        if (preg_match('/^(\d{1,2})\s+(\w+)\s+(\d{4})$/', $raw, $m)) {
            $day   = str_pad($m[1], 2, '0', STR_PAD_LEFT);
            $month = self::MONTHS[strtolower($m[2])] ?? null;
            $year  = $m[3];
            if ($month) return "{$year}-{$month}-{$day}";
        }

        return null;
    }

    /**
     * Parse "23 Jan 2025" or "23 January 2025" → "2025-01-23"
     */
    private function parseDate(string $raw): ?string
    {
        return $this->parseShortDate($raw);
    }

    /**
     * Fallback: regex on full raw text for transaction lines
     */
    private function fallbackParse(string $text, ?string $statementDate, string $filename): array
    {
        $records = [];

        // Match: DD Mon YY(YY) <description> AMOUNT C|D
        preg_match_all(
            '/(\d{1,2}\s+(?:Jan|Feb|Mar|Apr|May|Jun|Jul|Aug|Sep|Oct|Nov|Dec)\s+\d{2,4})\s+(.+?)\s+([\d,]+\.\d{2})\s+([CD])\b/i',
            $text,
            $matches,
            PREG_SET_ORDER
        );

        foreach ($matches as $m) {
            $txDate      = $this->parseShortDate($m[1]);
            $description = trim($m[2]);
            $amount      = (float) str_replace(',', '', $m[3]);
            $type        = strtolower($m[4]) === 'c' ? 'credit' : 'debit';

            if (empty($description) || $amount <= 0) continue;

            $records[] = [
                'transaction_date'    => $txDate,
                'statement_date'      => $statementDate,
                'transaction_details' => $description,
                'amount'              => $amount,
                'type'                => $type,
                'pdf_filename'        => $filename,
            ];
        }

        return $records;
    }
}
