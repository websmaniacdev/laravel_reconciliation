<?php

namespace App\Services;

use Smalot\PdfParser\Parser;

/**
 * GoogleWorkspaceReceiptParser
 *
 * MetaInvoicePdfParser ki tarah sirf Smalot use karta hai — pdftotext nahi.
 *
 * PDF text mein 2 types ki lines hoti hain:
 *
 *   DOTTED (garbled) — invoice number / date / billing info:
 *     ".In..v.o..ic..e. .d..a.t.e......31. .M..a.y. .2..0.2..4..."
 *     In lines ka dot_ratio > 20% hota hai
 *
 *   CLEAN — amounts / summary / subscription table:
 *     "Subtotal in INR ₹420.00"
 *     "Integrated GST (18%) ₹75.60"
 *     "Total in INR ₹495.60"
 *     "Summary for 1 May 2024 - 31 May 2024"
 *
 * Strategy:
 *   - Amounts → sirf CLEAN lines se (dotted lines skip karo)
 *   - Invoice date → dotted line se dots remove karke parse karo
 *   - Invoice number → clean "Invoice number: XXXX" line se
 */
class GoogleWorkspaceReceiptParser
{
    public function parse(string $pdfPath, string $originalFilename, string $clientName): array
    {
        // Smalot se text lo — bilkul MetaInvoicePdfParser ki tarah
        $parser = new Parser();
        $pdf    = $parser->parseFile($pdfPath);
        $text   = $pdf->getText();

        if (empty(trim($text))) {
            throw new \RuntimeException("PDF se text extract nahi hua: {$pdfPath}");
        }

        // Lines banao, empty lines hatao
        $lines = array_values(
            array_filter(
                array_map('trim', explode("\n", $text)),
                fn($l) => $l !== ''
            )
        );

        // ── Fields extract karo ───────────────────────────────────────────────

        return [
            'client_name'    => $clientName,
            'invoice_number' => $this->extractInvoiceNumber($lines),
            'invoice_date'   => $this->extractInvoiceDate($lines),
            'subscription'   => 'Google Workspace',
            'description'    => 'Usage',
            'interval'       => $this->extractInterval($lines),
            ...$this->extractAmounts($lines),
            'pdf_filename'   => $originalFilename,
        ];
    }

    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Dotted line hai ya clean?
     * ".In..v.o..ic..e..." → dot ratio > 20% = dotted (garbled)
     */
    private function isDotted(string $line): bool
    {
        $len = mb_strlen($line);
        if ($len === 0) return false;
        return (substr_count($line, '.') / $len) > 0.2;
    }

    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Invoice number — clean line se:
     * "Invoice number: 4983510577"
     */
    private function extractInvoiceNumber(array $lines): ?string
    {
        foreach ($lines as $line) {
            if ($this->isDotted($line)) continue;
            if (preg_match('/Invoice\s+number[:\s]+(\d{8,})/i', $line, $m)) {
                return trim($m[1]);
            }
        }
        // Fallback: dotted line se dots hata ke try karo
        foreach ($lines as $line) {
            if (!$this->isDotted($line)) continue;
            $clean = str_replace('.', '', $line);
            if (preg_match('/Invoice\s*number\s*(\d{8,})/i', $clean, $m)) {
                return trim($m[1]);
            }
        }
        return null;
    }

    /**
     * Invoice date — dotted line se dots hata ke:
     * ".In..v.o..ic..e. .d..a.t.e......31. .M..a.y. .2..0.2..4..."
     * → "Invoice date31 May 2024"
     */
    private function extractInvoiceDate(array $lines): ?string
    {
        // Pehle clean lines mein try karo (naye format ke liye)
        foreach ($lines as $line) {
            if ($this->isDotted($line)) continue;
            if (preg_match('/Invoice\s+date[:\s]+(\d{1,2}\s+\w+\s+\d{4})/i', $line, $m)) {
                return $this->parseDate($m[1]);
            }
        }
        // Dotted lines mein try karo
        foreach ($lines as $line) {
            if (!$this->isDotted($line)) continue;
            $clean = str_replace('.', '', $line);
            if (preg_match('/Invoice\s*date\s*(\d{1,2}\s+\w+\s+\d{4})/i', $clean, $m)) {
                return $this->parseDate($m[1]);
            }
        }
        // Last resort: pehli valid date in document
        foreach ($lines as $line) {
            if ($this->isDotted($line)) continue;
            if (preg_match('/\b(\d{1,2}\s+(?:Jan|Feb|Mar|Apr|May|Jun|Jul|Aug|Sep|Oct|Nov|Dec)\s+\d{4})\b/i', $line, $m)) {
                return $this->parseDate($m[1]);
            }
        }
        return null;
    }

    /**
     * Interval — "Summary for 1 May 2024 - 31 May 2024" clean line se
     */
    private function extractInterval(array $lines): ?string
    {
        foreach ($lines as $line) {
            if ($this->isDotted($line)) continue;
            if (preg_match('/Summary\s+for\s+(.+)/i', $line, $m)) {
                return trim($m[1]);
            }
        }
        // Fallback: koi bhi date range
        foreach ($lines as $line) {
            if ($this->isDotted($line)) continue;
            if (preg_match(
                '/(\d{1,2}\s+(?:Jan|Feb|Mar|Apr|May|Jun|Jul|Aug|Sep|Oct|Nov|Dec)(?:\s+\d{4})?\s*[-–]\s*\d{1,2}\s+(?:Jan|Feb|Mar|Apr|May|Jun|Jul|Aug|Sep|Oct|Nov|Dec)(?:\s+\d{4})?)/i',
                $line,
                $m
            )) {
                return trim($m[1]);
            }
        }
        return null;
    }

    /**
     * Amounts — SIRF clean lines se (dotted lines skip)
     *
     * Clean lines mein amounts reliable hote hain:
     *   "Subtotal in INR ₹420.00"
     *   "Integrated GST (18%) ₹75.60"
     *   "Total in INR ₹495.60"
     */
    private function extractAmounts(array $lines): array
    {
        $rowAmounts = [];     // Saare subscription amounts collect karenge
        $subtotal   = 0.0;
        $gst        = 0.0;
        $grandTotal = 0.0;

        foreach ($lines as $line) {
            if ($this->isDotted($line)) continue;

            // 1. Direct Subtotal line (agar PDF mein diya ho)
            if ($subtotal == 0.0 && preg_match('/Subtotal\s+in\s+INR\s*₹?\s*([\d,]+\.\d{2})/i', $line, $m)) {
                $subtotal = (float) str_replace(',', '', $m[1]);
            }

            // 2. Integrated GST
            if ($gst == 0.0 && preg_match('/Integrated\s+GST\s*\([^)]+\)\s*₹?\s*([\d,]+\.\d{2})/i', $line, $m)) {
                $gst = (float) str_replace(',', '', $m[1]);
            }

            // 3. Grand Total
            if (preg_match('/(?:^|\s)Total\s+in\s+INR\s*₹?\s*([\d,]+\.\d{2})/i', $line, $m)) {
                $grandTotal = (float) str_replace(',', '', $m[1]);
            }

            // 4. NEW: Subscription table rows se amount nikaalo (multiple records ke liye)
            // Example: "Google Workspace Business Starter  ...  72.56"  ya "298.06"
            if (preg_match('/Business\s+Starter.*?(\d{1,3}(?:,\d{3})*\.\d{2})\s*$/i', $line, $m)) {
                $amount = (float) str_replace(',', '', $m[1]);
                if ($amount > 0) {
                    $rowAmounts[] = $amount;
                }
            }
        }

        // Agar table rows se amounts mile → unka sum lo (yeh most reliable hai multiple records ke liye)
        if (!empty($rowAmounts)) {
            $calculatedSubtotal = array_sum($rowAmounts);

            // Agar PDF mein Subtotal bhi diya hai to cross-check (optional)
            if ($subtotal > 0 && abs($subtotal - $calculatedSubtotal) > 1) {
                // mismatch → table sum ko priority do
                $subtotal = $calculatedSubtotal;
            } else if ($subtotal == 0) {
                $subtotal = $calculatedSubtotal;
            }
        }

        // Fallback calculation
        if ($subtotal > 0) {
            if ($gst == 0.0) {
                $gst = round($subtotal * 0.18, 2);
            }
            if ($grandTotal == 0.0 || abs($grandTotal - ($subtotal + $gst)) > 1) {
                $grandTotal = round($subtotal + $gst, 2);
            }
        }

        // Last resort: agar kuch bhi nahi mila
        if ($subtotal == 0.0) {
            throw new \RuntimeException(
                "Subtotal extract nahi hua. Clean lines:\n"
                    . implode("\n", array_filter($lines, fn($l) => !$this->isDotted($l)))
            );
        }

        return [
            'subtotal'    => round($subtotal, 2),
            'gst_amount'  => round($gst, 2),
            'grand_total' => round($grandTotal, 2),
        ];
    }

    // ─────────────────────────────────────────────────────────────────────────

    private function parseDate(string $str): ?string
    {
        $str = trim(preg_replace('/\s+/', ' ', $str));
        foreach (['d M Y', 'd F Y'] as $fmt) {
            $dt = \DateTime::createFromFormat($fmt, $str);
            if ($dt) return $dt->format('Y-m-d');
        }
        return null;
    }
}