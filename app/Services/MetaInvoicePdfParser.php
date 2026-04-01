<?php

namespace App\Services;

use Smalot\PdfParser\Parser;

class MetaInvoicePdfParser
{
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
        // Smalot kabhi kabhi "1\nJust smile..." split
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

        // ── "Line no." section  ────────────────────────────────
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

            // Naya record: "1 <kuch bhi meaningful>"
            // Condition: line number (1-2 digit) + space +
            if (preg_match('/^(\d{1,2})\s+(.{3,})$/', $line, $match)) {

                $lineNo  = (int) $match[1];
                $rawHead = trim($match[2]);

                // ── Client name extract karo ──────────────────────────
                $clientName = $this->extractClientName($rawHead);

                if (mb_strlen($clientName) < 3) {
                    $j++;
                    continue;
                }

                $j++;

                $totalPrice       = 0.0;
                $totalImpressions = 0;
                $campaignTypes    = [];

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

                    //  price line jaise "0.04 INR" — skip (header price)
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

                        // Campaign name: impressions
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

                // Record  price avalable
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

    private function extractClientName(string $raw): string
    {
        $name = preg_replace('/^Client\s*-\s*/i', '', $raw);
        $name = preg_replace('/\s*\([^)]*\)\s*$/', '', $name);
        $name = preg_replace('/\s*[\–\-]?\s*\d{1,2}[\/\-]\d{1,2}[\/\-]\d{2,4}\s+to\s+.*/iu', '', $name);


        $name = preg_replace(
            '/\s*-\s*\d{1,2}[\/\-]\d{1,2}[\/\-]\d{2,4}.*/i',
            '',
            $name
        );

        $name = preg_replace(
            '/\s*-\s*(?:Jan|Feb|Mar|Apr|May|Jun|Jul|Aug|Sep|Oct|Nov|Dec)\b.*/i',
            '',
            $name
        );

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

            // header position
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
