<?php

namespace App\Services;

use Smalot\PdfParser\Parser;

class MetaInvoicePdfParser
{
    /**
     * Parse a Meta Ads tax invoice PDF.
     *
     * PDF mein teen types ke records hote hain:
     *
     * TYPE A — "Client - Name" format:
     *   "2 Client - Move Maker Rehabilitation (22-12-2025)"
     *   "From 14 Feb 2026, ..."
     *   "Traffic Ads for Clients (22-12-2025) 1 Impression 0.01 INR"
     *
     * TYPE B — Direct campaign name (no "Client -" prefix):
     *   "1 Just smile - leads - 14/2/26 to 28/2/26 – other 4 creative"
     *   "From 14 Feb 2026, ..."
     *   "Just smile - leads - 14/2/26 to 28/2/26 – other 4 creative 1 Impression 0.04 INR"
     *
     * TYPE C — Short brand name:
     *   "3 Cannoli The Cafe - Awareness - 9/2/26 to 15/2/26"
     *   "From 14 Feb 2026, ..."
     *   "Cannoli The Cafe - Awareness - 9/2/26 to 15/2/26 2 Impressions 0.01 INR"
     *
     * FIX: Pehle sirf "Client -" wale records parse hote the.
     *      Ab KISI BHI "N <text>" line ko record maana jata hai.
     */
    public function parse(string $pdfPath, string $originalFilename): array
    {
        $parser = new Parser();
        $pdf    = $parser->parseFile($pdfPath);
        $text   = $pdf->getText();

        // ── Document-level fields ─────────────────────────────────────

        $documentDate = null;
        if (preg_match('/Document date\s+(\d{1,2}\s+\w+\s+\d{4})/i', $text, $m)) {
            $documentDate = $this->parseDate($m[1]);
        }

        $taxInvoiceId = null;
        if (preg_match('/Tax invoice ID\s+([A-Z0-9\-]+)/i', $text, $m)) {
            $taxInvoiceId = trim($m[1]);
        }

        // ── Normalize lines ───────────────────────────────────────────
        $rawLines = array_map('trim', explode("\n", $text));
        $rawLines = array_values(array_filter($rawLines, fn($l) => $l !== ''));

        // ── Pre-process: merge lone line numbers with next line
        // Smalot kabhi kabhi "1\nJust smile..." split kar deta hai
        $lines = [];
        $count = count($rawLines);
        $i     = 0;

        while ($i < $count) {
            $line = $rawLines[$i];

            if (
                preg_match('/^\d{1,2}$/', $line)
                && isset($rawLines[$i + 1])
                && !preg_match('/^\d{1,2}$/', $rawLines[$i + 1])
                && !preg_match('/^From\s+\d/i', $rawLines[$i + 1])
            ) {
                $lines[] = $line . ' ' . $rawLines[$i + 1];
                $i += 2;
                continue;
            }

            $lines[] = $line;
            $i++;
        }

        // ── "Line no." section dhundho ────────────────────────────────
        $lineCount = count($lines);
        $j         = 0;

        while ($j < $lineCount) {
            if (stripos($lines[$j], 'Line no.') !== false) {
                $j++;
                break;
            }
            $j++;
        }

        $records = [];

        // ── Main parse loop ───────────────────────────────────────────
        while ($j < $lineCount) {
            $line = $lines[$j];

            // Naya record: "1 <kuch bhi meaningful>" se shuru ho
            // Condition: line number (1-2 digit) + space + kam se kam 3 chars
            if (preg_match('/^(\d{1,2})\s+(.{3,})$/', $line, $match)) {

                $lineNo  = (int) $match[1];
                $rawHead = trim($match[2]);

                // ── Client name extract karo ──────────────────────────
                $clientName = $this->extractClientName($rawHead);

                // Agar name bahut chota ya sirf numbers/gibberish ho toh skip
                if (mb_strlen($clientName) < 3) {
                    $j++;
                    continue;
                }

                $j++;

                $totalPrice       = 0.0;
                $totalImpressions = 0;
                $campaignTypes    = [];

                // Is record ke saare sub-lines padhna
                while ($j < $lineCount) {
                    $current = $lines[$j];

                    // Agla record shuru ho gaya
                    if (preg_match('/^\d{1,2}\s+.{3,}/', $current)) {
                        break;
                    }

                    // Date range — skip
                    if (preg_match('/^From\s+\d/i', $current)) {
                        $j++;
                        continue;
                    }

                    // Sirf price line jaise "0.04 INR" — skip (header price)
                    if (preg_match('/^[\d,]+\.\d{2}\s+INR\s*$/i', $current)) {
                        $j++;
                        continue;
                    }

                    // Campaign line with Impressions + Price
                    // Pattern: "...text... N Impression(s) X.XX INR"
                    if (preg_match('/([\d,]+)\s+Impressions?\s+([\d,]+\.\d{2})\s+INR\s*$/i', $current, $pm)) {
                        $impressions = (int) str_replace(',', '', $pm[1]);
                        $price       = (float) str_replace(',', '', $pm[2]);

                        $totalPrice       += $price;
                        $totalImpressions += $impressions;

                        // Campaign name: impressions ke pehle wala hissa
                        $campName = preg_replace('/\s*[\d,]+\s+Impressions?.*$/i', '', $current);
                        $campName = $this->cleanCampaignName($campName);

                        if (!empty($campName) && !in_array($campName, $campaignTypes)) {
                            $campaignTypes[] = $campName;
                        }

                        $j++;
                        continue;
                    }

                    $j++;
                }

                // Record save karo agar price mili
                if ($totalPrice > 0) {
                    $combinedCampaign = !empty($campaignTypes)
                        ? implode(' + ', $campaignTypes)
                        : 'Multiple Campaigns';

                    if (mb_strlen($combinedCampaign) > 150) {
                        $combinedCampaign = $campaignTypes[0] . ' + Others';
                    }

                    $records[] = [
                        'client_name'    => $clientName,
                        'price'          => round($totalPrice, 2),
                        'document_date'  => $documentDate,
                        'tax_invoice_id' => $taxInvoiceId,
                        'impressions'    => $totalImpressions,
                        'campaign_type'  => $combinedCampaign,
                        'pdf_filename'   => $originalFilename,
                    ];
                }

                continue;
            }

            $j++;
        }

        // ── Fallback ──────────────────────────────────────────────────
        if (empty($records)) {
            $records = $this->fallbackParse($text, $documentDate, $taxInvoiceId, $originalFilename);
        }

        return $records;
    }

    // ─────────────────────────────────────────────────────────────────
    // Private helpers
    // ─────────────────────────────────────────────────────────────────

    /**
     * Record heading line se client/campaign name nikalo.
     *
     * Cases:
     *   "Client - Move Maker Rehabilitation (22-12-2025)"  → "Move Maker Rehabilitation"
     *   "Just smile - leads - 14/2/26 to 28/2/26 – ..."   → "Just smile"
     *   "Cannoli The Cafe - Awareness - 9/2/26 to 15/2/26" → "Cannoli The Cafe"
     *   "Phoenix - 11/2/26 to 20/2/26 - Awareness"        → "Phoenix"
     *   "Rio Pipes - Leads - Dealer Distributor - 12-02-26"→ "Rio Pipes"
     *   "Client - Weldor CNC Machine (03-11-2025)"         → "Weldor CNC Machine"
     */
    private function extractClientName(string $raw): string
    {
        // Step 1: "Client - " prefix hata do
        $name = preg_replace('/^Client\s*-\s*/i', '', $raw);

        // Step 2: Trailing parentheses hata do: (22-12-2025) or (B2B Customer)
        $name = preg_replace('/\s*\([^)]*\)\s*$/', '', $name);

        // Step 3: "to" keyword ke baad date range hata do
        // "Just smile - leads - 14/2/26 to 28/2/26 – other 4 creative"
        //                      → "Just smile - leads"
        $name = preg_replace('/\s*[\–\-]?\s*\d{1,2}[\/\-]\d{1,2}[\/\-]\d{2,4}\s+to\s+.*/iu', '', $name);

        // Step 4: Slash-date pattern ke baad sab kuch hata do
        // "Cannoli The Cafe - Awareness - 9/2/26 to..." already handled
        // "Rio Pipes - Leads - Dealer Distributor - 12-02-26"
        //                     → cut at " - 12-02-26"
        $name = preg_replace(
            '/\s*-\s*\d{1,2}[\/\-]\d{1,2}[\/\-]\d{2,4}.*/i',
            '',
            $name
        );

        // Step 5: Month-name date pattern hata do
        $name = preg_replace(
            '/\s*-\s*(?:Jan|Feb|Mar|Apr|May|Jun|Jul|Aug|Sep|Oct|Nov|Dec)\b.*/i',
            '',
            $name
        );

        // Step 6: Common trailing keywords hata do
        // "Just smile - leads" → trailing " - leads" rakhna chahte hain? Nahi,
        // kyuki yeh campaign type hai, client name nahi.
        // Lekin sirf tab hata do jab yeh puri string ke end mein ho.
        $trailingKeywords = 'Leads?|Awareness|Traffic|Engagement|Lifetime|Impressions?'
            . '|Rajkot|Ahmedabad|Mumbai|Delhi|Surat|Vadodara|Dealer|Distributor'
            . '|creative|other\s+\d+\s+creative';

        $name = preg_replace(
            '/(?:\s*[-–]\s*(?:' . $trailingKeywords . '))+\s*$/iu',
            '',
            $name
        );

        return trim($name);
    }

    /**
     * Campaign name se date/suffix clean karo
     */
    private function cleanCampaignName(string $raw): string
    {
        $name = trim($raw);

        // Trailing date in parens: (03-11-2025)
        $name = preg_replace('/\s*\(\d{2}-\d{2}-\d{4}\)\s*$/', '', $name);

        // Trailing slash-date
        $name = preg_replace('/\s*-?\s*\d{1,2}[\/\-]\d{1,2}[\/\-]\d{2,4}\s*$/', '', $name);

        return trim($name);
    }

    private function parseDate(string $dateStr): ?string
    {
        $dt = \DateTime::createFromFormat('d M Y', trim($dateStr));
        if ($dt) return $dt->format('Y-m-d');

        $dt = \DateTime::createFromFormat('d F Y', trim($dateStr));
        if ($dt) return $dt->format('Y-m-d');

        return null;
    }

    private function fallbackParse(
        string $text,
        ?string $documentDate,
        ?string $taxInvoiceId,
        string $filename
    ): array {
        $records = [];

        // Broader fallback pattern
        $pattern = '/^(\d{1,2})\s+(.+)$/m';
        preg_match_all($pattern, $text, $headerMatches, PREG_SET_ORDER | PREG_OFFSET_CAPTURE);

        foreach ($headerMatches as $idx => $hm) {
            $headRaw    = $hm[2][0];
            $clientName = $this->extractClientName($headRaw);

            if (mb_strlen($clientName) < 3) continue;

            // Agla header ki position dhundho
            $startPos = $hm[0][1] + strlen($hm[0][0]);
            $endPos   = isset($headerMatches[$idx + 1])
                ? $headerMatches[$idx + 1][0][1]
                : strlen($text);

            $block = substr($text, $startPos, $endPos - $startPos);

            $totalPrice       = 0.0;
            $totalImpressions = 0;
            $campaignTypes    = [];

            preg_match_all(
                '/([\d,]+)\s+Impressions?\s+([\d,]+\.\d{2})\s+INR/i',
                $block,
                $campMatches,
                PREG_SET_ORDER
            );

            foreach ($campMatches as $cm) {
                $totalImpressions += (int) str_replace(',', '', $cm[1]);
                $totalPrice       += (float) str_replace(',', '', $cm[2]);

                $campLine = trim(preg_replace('/' . preg_quote($cm[0], '/') . '.*$/', '', $cm[0]));
                if (!empty($campLine)) $campaignTypes[] = $campLine;
            }

            if ($totalPrice > 0) {
                $records[] = [
                    'client_name'    => $clientName,
                    'price'          => round($totalPrice, 2),
                    'document_date'  => $documentDate,
                    'tax_invoice_id' => $taxInvoiceId,
                    'impressions'    => $totalImpressions,
                    'campaign_type'  => !empty($campaignTypes) ? $campaignTypes[0] : 'Unknown',
                    'pdf_filename'   => $filename,
                ];
            }
        }

        return $records;
    }
}