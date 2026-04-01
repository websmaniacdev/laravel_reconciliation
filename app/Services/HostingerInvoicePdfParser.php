<?php

namespace App\Services;

use Smalot\PdfParser\Parser;

/**
 * Parses Hostinger tax invoice PDFs.
 *
 * Handles TWO confirmed PDF formats:
 *
 * FORMAT A — USD invoice (WordPress hosting etc.)
 *   Invoice Amount # $269.89 (USD)
 *   BILLED TO: simple block (name, company, GSTIN, country, email, phone)
 *   DESCRIPTION line: "WordPress Pro (billed every year) $299.88 x 1 ($29.99) $269.89 $0.00 $269.89"
 *   Total excl. GST $269.89 / Total $269.89 / Payments ($269.89) / Amount Due (USD) $0.00
 *
 * FORMAT B — INR invoice (domain renewals etc.)
 *   Invoice Amount # ₹749.00 (INR)
 *   BILLED TO: extended block with full postal address (Office, City PIN, State, Country)
 *   DESCRIPTION split across lines:
 *     ".IN Domain (billed every year)"          ← text-only line
 *     "kitchenkingrajkot.in"                    ← domain name continuation
 *     "₹749.00 x 1 - ₹749.00 ₹0.00 ₹749.00"  ← amounts line
 *     "Feb 06, 2025 to Feb 06, 2026"            ← billing period
 *   Total excl. GST ₹749.00 (SGD 11.93)  ← SGD conversion note → strip/ignore
 *   Amount Due (INR) ₹0.00
 */
class HostingerInvoicePdfParser
{
    public function parse(string $pdfPath, string $originalFilename): array
    {
        $parser = new Parser();
        $pdf    = $parser->parseFile($pdfPath);
        $text   = $pdf->getText();

        $lines = array_values(
            array_filter(
                array_map('trim', explode("\n", $text)),
                fn($l) => $l !== ''
            )
        );

        $header   = $this->parseHeader($lines);
        $billedTo = $this->parseBilledTo($lines);
        $items    = $this->parseLineItems($lines);
        $totals   = $this->parseTotals($lines, $header['currency']);

        return [
            'header'     => $header,
            'billed_to'  => $billedTo,
            'line_items' => $items,
            'totals'     => $totals,
            'filename'   => $originalFilename,
        ];
    }

    // ─────────────────────────────────────────────────────────────────
    // HEADER
    // ─────────────────────────────────────────────────────────────────

    private function parseHeader(array $lines): array
    {
        $header = [
            'invoice_number'    => null,
            'invoice_date'      => null,
            'invoice_amount'    => null,
            'currency'          => 'USD',
            'next_billing_date' => null,
            'order_number'      => null,
            'status'            => null,
        ];

        foreach ($lines as $line) {
            // "Invoice # HSG-3557134"  (standalone, not "Invoice Issued #")
            if (preg_match('/^Invoice\s*#\s*([A-Z0-9\-]+)$/i', $line, $m)) {
                $header['invoice_number'] = trim($m[1]);
            }

            // "Invoice Issued # Jan 10, 2025"
            if (preg_match('/Invoice\s+Issued\s*#\s*(.+)/i', $line, $m)) {
                $header['invoice_date'] = $this->parseDate(trim($m[1]));
            }

            // "Invoice Amount # ₹749.00 (INR)"  OR  "Invoice Amount # $269.89 (USD)"
            // Detect currency from symbol first, then explicit code
            if (preg_match('/Invoice\s+Amount\s*#\s*([₹\$])?([\d,]+\.?\d*)\s*\(?([A-Z]{3})?\)?/iu', $line, $m)) {
                $header['invoice_amount'] = $this->parseMoney($m[2]);
                $symbol = $m[1] ?? '';
                $code   = strtoupper($m[3] ?? '');
                if ($symbol === '₹' || $code === 'INR') {
                    $header['currency'] = 'INR';
                } elseif ($symbol === '$' || in_array($code, ['USD', 'EUR', 'GBP', 'SGD'])) {
                    $header['currency'] = $code ?: 'USD';
                }
            }

            // "Next Billing Date # Feb 06, 2026"
            if (preg_match('/Next\s+Billing\s+Date\s*#\s*(.+)/i', $line, $m)) {
                $header['next_billing_date'] = $this->parseDate(trim($m[1]));
            }

            // "Order Nr. # hb_8063975"
            if (preg_match('/Order\s+Nr\.?\s*#\s*([^\s]+)/i', $line, $m)) {
                $header['order_number'] = trim($m[1]);
            }

            // Status line
            if (preg_match('/^(PAID|UNPAID|OVERDUE|CANCELLED)$/i', $line, $m)) {
                $header['status'] = strtoupper($m[1]);
            }
        }

        return $header;
    }

    // ─────────────────────────────────────────────────────────────────
    // BILLED TO
    // Handles extended address blocks: street, city+PIN, state, country
    // ─────────────────────────────────────────────────────────────────

    private function parseBilledTo(array $lines): array
    {
        $bt = [
            'name'    => null,
            'company' => null,
            'gstin'   => null,
            'country' => null,
            'email'   => null,
            'phone'   => null,
        ];

        $start = null;
        foreach ($lines as $i => $line) {
            if (stripos($line, 'BILLED TO') !== false) {
                $start = $i + 1;
                break;
            }
        }

        if ($start === null) return $bt;

        $block = [];
        for ($i = $start; $i < count($lines); $i++) {
            if (stripos($lines[$i], 'DESCRIPTION') !== false) break;
            $block[] = $lines[$i];
        }

        $knownCountries = [
            'India',
            'Singapore',
            'United States',
            'USA',
            'UK',
            'Germany',
            'Australia',
            'Canada',
            'France',
            'Netherlands',
        ];

        $indianStates = [
            'Gujarat',
            'Maharashtra',
            'Rajasthan',
            'Karnataka',
            'Tamil Nadu',
            'Telangana',
            'Andhra Pradesh',
            'Uttar Pradesh',
            'Delhi',
            'Punjab',
            'West Bengal',
            'Kerala',
            'Madhya Pradesh',
            'Haryana',
            'Bihar',
            'Odisha',
            'Assam',
            'Chhattisgarh',
            'Jharkhand',
            'Goa',
        ];

        foreach ($block as $bLine) {
            // Email
            if (filter_var($bLine, FILTER_VALIDATE_EMAIL)) {
                $bt['email'] = $bLine;
                continue;
            }

            // Phone (digits, +, spaces, dashes, 8+ chars)
            if (preg_match('/^[\+\d][\d\s\-]{7,}$/', $bLine)) {
                $bt['phone'] = $bLine;
                continue;
            }

            // GSTIN: uppercase alphanumeric, 10+ chars, contains digit
            if (preg_match('/^[A-Z0-9]{10,}$/i', $bLine) && preg_match('/\d/', $bLine)) {
                $bt['gstin'] = $bLine;
                continue;
            }

            // Country
            if (in_array($bLine, $knownCountries)) {
                $bt['country'] = $bLine;
                continue;
            }

            // Indian state → skip
            if (in_array($bLine, $indianStates)) {
                continue;
            }

            // Address line: has comma + digit (street address), OR city+PIN pattern
            if ($this->looksLikeAddress($bLine)) {
                continue;
            }

            // Name (first clean line)
            if ($bt['name'] === null) {
                $bt['name'] = $bLine;
                continue;
            }

            // Company (second clean line)
            if ($bt['company'] === null) {
                $bt['company'] = $bLine;
                continue;
            }

            // Everything else = skip (extra address lines)
        }

        return $bt;
    }

    /**
     * Detect postal address lines to skip them in Billed To parsing.
     * "Office-535, The City Centre, Raiya Road" → comma + digit
     * "Rajkot 360007" → word + 5-6 digit PIN
     */
    private function looksLikeAddress(string $line): bool
    {
        // Has comma AND at least one digit → likely a street address
        if (str_contains($line, ',') && preg_match('/\d/', $line)) {
            return true;
        }
        // Word(s) followed by 5 or 6 digit postal code
        if (preg_match('/\b\d{5,6}\b/', $line)) {
            return true;
        }
        return false;
    }

    // ─────────────────────────────────────────────────────────────────
    // LINE ITEMS
    // ─────────────────────────────────────────────────────────────────

    private function parseLineItems(array $lines): array
    {
        $items = [];

        $start = null;
        foreach ($lines as $i => $line) {
            if (stripos($line, 'DESCRIPTION') !== false) {
                $start = $i + 1;
                break;
            }
        }

        if ($start === null) return $items;

        $block = [];
        for ($i = $start; $i < count($lines); $i++) {
            if (preg_match('/^Total\s+excl\./i', $lines[$i])) break;
            $block[] = $lines[$i];
        }

        $count = count($block);
        $j     = 0;

        while ($j < $count) {
            $line = $block[$j];

            // Skip column header repeats
            if (preg_match('/^(PRICE|DISCOUNT|TOTAL|GST\s+AMOUNT)/i', $line)) {
                $j++;
                continue;
            }

            if ($this->lineHasAmounts($line)) {
                // ── FORMAT A: amounts on same line as (or after) description ──
                $item = $this->parseAmountLine($line, null);
                if ($item !== null) {
                    if (isset($block[$j + 1]) && $this->isBillingPeriod($block[$j + 1])) {
                        $item['billing_period'] = trim($block[$j + 1]);
                        $j++;
                    }
                    $items[] = $item;
                }
                $j++;
                continue;
            }

            // ── FORMAT B: description-only line(s), amounts come after ──
            $descParts = [trim($line)];
            $j++;

            while ($j < $count) {
                $next = $block[$j];

                if ($this->isBillingPeriod($next)) break;
                if (preg_match('/^(PRICE|DISCOUNT|TOTAL|GST\s+AMOUNT)/i', $next)) break;

                if ($this->lineHasAmounts($next)) {
                    $fullDesc = implode(' ', array_filter($descParts));
                    $item     = $this->parseAmountLine($next, $fullDesc);
                    if ($item !== null) {
                        if (isset($block[$j + 1]) && $this->isBillingPeriod($block[$j + 1])) {
                            $item['billing_period'] = trim($block[$j + 1]);
                            $j++;
                        }
                        $items[] = $item;
                    }
                    $j++;
                    break;
                }

                // Another description continuation line (e.g. domain name like kitchenkingrajkot.in)
                $descParts[] = trim($next);
                $j++;
            }
        }

        return $items;
    }

    /**
     * Does this line contain at least one monetary amount?
     * Matches: ₹749.00  $299.88  749.00
     */
    private function lineHasAmounts(string $line): bool
    {
        // Must contain ₹ or $ symbol with digits
        if (preg_match('/[₹\$][\d,]+/u', $line)) {
            return true;
        }
        // OR a plain decimal number that looks like a price (not just a domain like ".in")
        // Must be preceded by a space or be at the start, and followed by space or end
        if (preg_match('/(?:^|\s)([\d,]+\.\d{2})(?:\s|$)/u', $line)) {
            return true;
        }
        return false;
    }

    /**
     * Is this line a billing period? e.g. "Feb 06, 2025 to Feb 06, 2026"
     */
    private function isBillingPeriod(string $line): bool
    {
        return (bool) preg_match('/\w+\s+\d{1,2},\s+\d{4}\s+to\s+/i', $line);
    }

    /**
     * Parse a line that contains amounts, optionally with an override description.
     *
     * Column order: Description | Unit Price | Discount | Total Excl GST | GST Amount | Line Total
     *
     * INR line:  "₹749.00 x 1 - ₹749.00 ₹0.00 ₹749.00"
     * USD line:  "WordPress Pro (billed every year) $299.88 x 1 ($29.99) $269.89 $0.00 $269.89"
     * Zero line: "Daily Backup $0.00 x 1 - $0.00 $0.00 $0.00"
     */
    private function parseAmountLine(string $line, ?string $overrideDesc): ?array
    {
        // Normalize: remove currency symbols, strip secondary currency notes "(SGD 11.93)"
        $norm = $this->stripCurrencySymbols($line);
        $norm = preg_replace('/\([A-Z]{3}\s+[\d,]+\.?\d*\)/i', '', $norm);

        // Extract all decimal numbers
        preg_match_all('/\(?([\d,]+\.\d{2})\)?/', $norm, $numMatches);
        $amounts = array_map(
            fn($s) => (float) str_replace(',', '', $s),
            $numMatches[1]
        );

        if (empty($amounts)) return null;

        // Detect parenthesised discount
        $discountRaw = null;
        if (preg_match('/\(([\d,]+\.\d{2})\)/', $norm, $dm)) {
            $discountRaw = (float) str_replace(',', '', $dm[1]);
        }

        // Build description
        $description = $overrideDesc;
        if ($description === null) {
            // Strip everything from the first money symbol onward
            $description = preg_replace('/\s*[₹\$][\d,]+.*/u', '', $line);
            // Fallback: strip from first bare decimal
            $description = preg_replace('/\s+[\d,]+\.\d{2}.*/u', '', $description);
            $description = trim($description);
        }

        // Remove "x N" quantity notation
        $description = preg_replace('/\s*x\s*\d+/i', '', $description);
        $description = trim(preg_replace('/[\s\-–]+$/', '', $description));

        if (mb_strlen($description) < 1) {
            $description = 'Item';
        }

        // Map amounts to columns
        $n = count($amounts);

        if ($n >= 5) {
            $unitPrice    = $amounts[0];
            $discount     = $discountRaw ?? $amounts[1];
            $totalExclGst = $amounts[2];
            $gstAmount    = $amounts[3];
            $lineTotal    = $amounts[4];
        } elseif ($n === 4) {
            $unitPrice    = $amounts[0];
            $discount     = $discountRaw ?? 0.0;
            $totalExclGst = $amounts[1];
            $gstAmount    = $amounts[2];
            $lineTotal    = $amounts[3];
        } elseif ($n === 3) {
            $unitPrice    = $amounts[0];
            $discount     = $discountRaw ?? 0.0;
            $totalExclGst = $amounts[1];
            $gstAmount    = 0.0;
            $lineTotal    = $amounts[2];
        } elseif ($n === 2) {
            $unitPrice    = $amounts[0];
            $discount     = 0.0;
            $totalExclGst = $amounts[1];
            $gstAmount    = 0.0;
            $lineTotal    = $amounts[1];
        } else {
            $unitPrice    = $amounts[0];
            $discount     = 0.0;
            $totalExclGst = $amounts[0];
            $gstAmount    = 0.0;
            $lineTotal    = $amounts[0];
        }

        return [
            'description'    => $description,
            'billing_period' => null,
            'unit_price'     => round($unitPrice, 2),
            'discount'       => round($discount, 2),
            'total_excl_gst' => round($totalExclGst, 2),
            'gst_amount'     => round($gstAmount, 2),
            'line_total'     => round($lineTotal, 2),
        ];
    }

    // ─────────────────────────────────────────────────────────────────
    // FOOTER TOTALS
    // ─────────────────────────────────────────────────────────────────

    private function parseTotals(array $lines, string $currency): array
    {
        $totals = [
            'subtotal'       => 0.0,
            'total_discount' => 0.0,
            'gst_amount'     => 0.0,
            'grand_total'    => 0.0,
            'amount_paid'    => 0.0,
            'amount_due'     => 0.0,
            'currency'       => $currency,
        ];

        foreach ($lines as $line) {
            // Normalize: strip ₹/$, remove "(SGD ...)" style notes
            $norm = $this->stripCurrencySymbols($line);
            $norm = preg_replace('/\([A-Z]{3}\s+[\d,]+\.?\d*\)/i', '', $norm);

            // "Total excl. GST 749.00"
            if (preg_match('/Total\s+excl\.?\s+GST\s+([\d,]+\.?\d*)/i', $norm, $m)) {
                $totals['subtotal'] = $this->parseMoney($m[1]);
            }

            // "Total 749.00" (must not match "Total excl.")
            if (preg_match('/^Total\s+([\d,]+\.?\d*)/i', $norm, $m)) {
                $totals['grand_total'] = $this->parseMoney($m[1]);
            }

            // "Payments (749.00)" or "Payments 749.00"
            if (preg_match('/Payments?\s+\(?([\d,]+\.?\d*)\)?/i', $norm, $m)) {
                $totals['amount_paid'] = $this->parseMoney($m[1]);
            }

            // "Amount Due (INR) 0.00"
            if (preg_match('/Amount\s+Due\s*(?:\(([A-Z]{3})\))?\s*([\d,]+\.?\d*)/i', $norm, $m)) {
                $totals['amount_due'] = $this->parseMoney($m[2]);
                if (!empty($m[1])) {
                    $totals['currency'] = strtoupper($m[1]);
                }
            }

            // Currency from column header "GST AMOUNT (INR)"
            if (preg_match('/GST\s+AMOUNT\s*\(([A-Z]{3})\)/i', $line, $m)) {
                $totals['currency'] = strtoupper($m[1]);
            }
        }

        return $totals;
    }

    // ─────────────────────────────────────────────────────────────────
    // HELPERS
    // ─────────────────────────────────────────────────────────────────

    /**
     * Remove ₹ and $ currency symbols from a string.
     */
    private function stripCurrencySymbols(string $line): string
    {
        return preg_replace('/[₹\$]/u', '', $line);
    }

    private function parseMoney(string $raw): float
    {
        $clean = preg_replace('/[₹\$,\(\)\s]/u', '', $raw);
        return (float) $clean;
    }

    private function parseDate(string $dateStr): ?string
    {
        $dateStr = trim($dateStr);

        $formats = ['M d, Y', 'F d, Y', 'd M Y', 'Y-m-d', 'M j, Y', 'F j, Y'];
        foreach ($formats as $fmt) {
            $dt = \DateTime::createFromFormat($fmt, $dateStr);
            if ($dt !== false) {
                return $dt->format('Y-m-d');
            }
        }

        return null;
    }
}
