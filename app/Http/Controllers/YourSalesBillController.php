<?php

namespace App\Http\Controllers;

use App\Models\YourSalesBill;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Smalot\PdfParser\Parser as PdfParser;

class YourSalesBillController extends Controller
{
    // ══════════════════════════════════════════════════════════════════
    // INDEX  (list + filter)
    // ══════════════════════════════════════════════════════════════════

    public function index(Request $request)
    {
        $query = YourSalesBill::query();

        if ($request->filled('client_name')) {
            $query->where('client_name', 'like', '%' . $request->client_name . '%');
        }
        if ($request->filled('invoice_number')) {
            $query->where('invoice_number', 'like', '%' . $request->invoice_number . '%');
        }
        if ($request->filled('from_date')) {
            $query->whereDate('invoice_date', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('invoice_date', '<=', $request->to_date);
        }

        // ── Totals ────────────────────────────────────────────────────
        $totalBeforeTax = (clone $query)->sum('amount_before_tax');
        $totalSgst      = (clone $query)->sum('sgst_amount');
        $totalCgst      = (clone $query)->sum('cgst_amount');
        $totalIgst      = (clone $query)->sum('igst_amount');
        $grandTotal     = (clone $query)->sum('total_amount');
        $totalCount     = (clone $query)->count();

        $bills = $query->orderBy('invoice_date', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(100)
            ->withQueryString();

        return view('your-sales-bill.index', compact(
            'bills',
            'totalBeforeTax',
            'totalSgst',
            'totalCgst',
            'totalIgst',
            'grandTotal',
            'totalCount'
        ));
    }

    // ══════════════════════════════════════════════════════════════════
    // UPLOAD  —  1 record per PDF (multiple line items summed)
    // ══════════════════════════════════════════════════════════════════

    public function upload(Request $request)
    {
        $request->validate([
            'pdfs'   => 'required|array|min:1|max:50',
            'pdfs.*' => 'required|file|mimes:pdf|max:10240',
        ]);

        $success = 0;
        $errors  = [];

        foreach ($request->file('pdfs') as $file) {
            try {
                $originalName = $file->getClientOriginalName();
                $stored       = $file->store('your-sales-bills', 'local');
                $fullPath     = Storage::disk('local')->path($stored);

                $parser = new PdfParser();
                $pdf    = $parser->parseFile($fullPath);
                $text   = $pdf->getText();

                $data = $this->extractFromText($text);
                $data['original_filename'] = $originalName;
                $data['pdf_path']          = $stored;

                YourSalesBill::create($data);
                $success++;
            } catch (\Throwable $e) {
                $errors[] = ($file->getClientOriginalName() ?? 'unknown') . ': ' . $e->getMessage();
            }
        }

        $msg = $success > 0
            ? "{$success} bill(s) uploaded and parsed successfully!"
            : 'No bills were processed.';

        return redirect()->back()
            ->with('success', $msg)
            ->with('upload_errors', $errors);
    }

    // ══════════════════════════════════════════════════════════════════
    // UPDATE CLIENT NAME
    // ══════════════════════════════════════════════════════════════════

    public function updateClientName(Request $request, $id)
    {
        $bill      = YourSalesBill::findOrFail($id);
        $validated = $request->validate(['client_name' => 'nullable|string|max:255']);
        $bill->update(['client_name' => $validated['client_name'] ?? null]);
        return response()->json(['success' => true]);
    }

    // ══════════════════════════════════════════════════════════════════
    // DELETE
    // ══════════════════════════════════════════════════════════════════

    public function destroy($id)
    {
        $bill = YourSalesBill::findOrFail($id);
        if ($bill->pdf_path && Storage::disk('local')->exists($bill->pdf_path)) {
            Storage::disk('local')->delete($bill->pdf_path);
        }
        $bill->delete();
        return response()->json(['success' => true]);
    }

    // ══════════════════════════════════════════════════════════════════
    // PDF TEXT PARSER
    // ══════════════════════════════════════════════════════════════════
    //
    // Strategy:
    //   • 1 record per PDF — all line-item amounts are SUMMED into
    //     amount_before_tax (we use the "Total Amount Before Tax" field
    //     printed at the bottom of every invoice, not individual rows).
    //   • Individual line items are stored in JSON (line_items) for
    //     reference and combined into a single description string.
    //   • Tax amounts (SGST/CGST/IGST) are taken from the invoice totals
    //     section, NOT computed from individual rows.
    //
    private function extractFromText(string $text): array
    {
        $data = [
            'invoice_number'        => null,
            'invoice_date'          => null,
            'invoice_type'          => 'Tax Invoice',
            'client_name'           => null,
            'client_address'        => null,
            'client_gstin'          => null,
            'client_place_of_supply' => null,
            'client_state'          => null,
            'client_state_code'     => null,
            'line_items'            => [],
            'description'           => null,
            'amount_before_tax'     => 0,
            'sgst_amount'           => 0,
            'cgst_amount'           => 0,
            'igst_amount'           => 0,
            'round_off'             => 0,
            'total_amount'          => 0,
        ];

        // ── 1. INVOICE NUMBER  ────────────────────────────────────────
        // Pattern: WBM/26-27/24  or  WBM/26-27/08
        if (preg_match('/\b([A-Z]{2,8}\/\d{2}-\d{2}\/\d+)\b/', $text, $m)) {
            $data['invoice_number'] = trim($m[1]);
        }

        // ── 2. INVOICE DATE  ──────────────────────────────────────────
        // DD-MM-YYYY  or  D/M/YYYY  or  DD/MM/YYYY
        $datePattern = '/\b(\d{1,2}[-\/]\d{1,2}[-\/]\d{4})\b/';
        if (preg_match($datePattern, $text, $m)) {
            $raw = str_replace('/', '-', $m[1]);
            try {
                // Try DD-MM-YYYY first (Indian format)
                $data['invoice_date'] = \Carbon\Carbon::createFromFormat('d-m-Y', $raw)->format('Y-m-d');
            } catch (\Throwable $e) {
                try {
                    $data['invoice_date'] = \Carbon\Carbon::parse($raw)->format('Y-m-d');
                } catch (\Throwable $e2) {
                }
            }
        }

        // ── 3. CLIENT NAME  ───────────────────────────────────────────
        // "Name Sanghani Hospital" or "Name Just Smile"
        // smalot/pdfparser reads: "Name Sanghani Hospital\n" or "Name Just Smile\n"
        if (preg_match('/\bName\s+([A-Za-z][A-Za-z0-9\s\-&\'\.]{1,80}?)(?:\n|Address|GSTIN|Place)/m', $text, $m)) {
            $raw = trim($m[1]);
            if ($raw && strlen($raw) > 1) {
                $data['client_name'] = $raw;
            }
        }

        // ── 4. CLIENT ADDRESS  ────────────────────────────────────────
        if (preg_match('/Address\s*[:\-]?\s*"?([^"]{5,200}?)(?:"|\n\n|GSTIN|Place of Supply)/ms', $text, $m)) {
            $data['client_address'] = trim(preg_replace('/\s+/', ' ', $m[1]));
        }

        // ── 5. CLIENT GSTIN  ──────────────────────────────────────────
        // Standard GSTIN: 15-char alphanum. Invoices have TWO GSTINs:
        //   [0] = seller (24CIBPS7329P1ZM)
        //   [1] = client (e.g. 24ABHCS2858M1ZU)
        // We want the CLIENT's, which appears AFTER "GSTIN :" label near the billing-to section.
        if (preg_match_all('/\b([0-9]{2}[A-Z]{5}[0-9]{4}[A-Z][A-Z0-9]Z[A-Z0-9])\b/', $text, $matches)) {
            // Remove seller's GSTIN (24CIBPS7329P1ZM)
            $allGstins = array_values(array_unique($matches[1]));
            // Seller GSTIN is in the header; client's comes after "GSTIN :" in billing section
            // Grab the last one that is NOT the seller's
            $sellerGstin = '24CIBPS7329P1ZM'; // known seller GSTIN
            $clientGstins = array_filter($allGstins, fn($g) => $g !== $sellerGstin);
            if (!empty($clientGstins)) {
                $data['client_gstin'] = array_values($clientGstins)[0];
            }
        }

        // ── 6. PLACE OF SUPPLY & STATE  ───────────────────────────────
        if (preg_match('/Place\s+of\s+Supply\s*[:\-]?\s*([A-Za-z]+(?:\s+[A-Za-z]+)?)\s/i', $text, $m)) {
            $data['client_place_of_supply'] = trim($m[1]);
        }
        if (preg_match('/State\s*[:\-]?\s*(Gujarat|[A-Za-z]+(?:\s+[A-Za-z]+)?)\s/i', $text, $m)) {
            $data['client_state'] = trim($m[1]);
        }
        if (preg_match('/State\s+Code\s*[:\-]?\s*(\d{2})/i', $text, $m)) {
            $data['client_state_code'] = $m[1];
        }

        // ── 7. LINE ITEMS  ────────────────────────────────────────────
        // smalot reads multi-column PDFs left→right per column.
        // The table rows look like:
        //   "1 INFORMATION TECHNOLOGY SERVICE 9983 6.000 3500.000 18% 21000.00"
        //   "Gsuite Renewal (April 2026 to September 2026)"
        //   "(6 Month Renewal)"
        // We extract: particulars + sub-description + qty + rate + amount per row.
        $lineItems = [];

        // Match rows: Sr.No  INFORMATION TECHNOLOGY SERVICE  HSN  Qty  Rate  GST%  Amount
        // The text may split across lines so we use a flexible approach.
        // Pattern captures: Sr#, Particulars, HSN, Qty, Rate, GST%, Amount
        $rowPattern = '/(\d+)\s+INFORMATION TECHNOLOGY SERVICE\s+(\d{4})\s+([\d.]+)\s+([\d.]+)\s+\d+%?\s+([\d,]+\.?\d{0,2})/i';
        if (preg_match_all($rowPattern, $text, $rows, PREG_SET_ORDER)) {
            foreach ($rows as $row) {
                $lineItems[] = [
                    'sr'        => (int) $row[1],
                    'hsn'       => $row[2],
                    'qty'       => (float) $row[3],
                    'rate'      => (float) $row[4],
                    'amount'    => (float) str_replace(',', '', $row[5]),
                    'particulars' => 'INFORMATION TECHNOLOGY SERVICE',
                ];
            }
        }

        // Fallback: grab any sub-descriptions (italic lines like "Promotional cost", "SMM (Hiring post campaign)")
        // These follow INFORMATION TECHNOLOGY SERVICE lines in the PDF text
        $subDescs = [];
        if (preg_match_all('/(Promotional cost|SMM[^\\n]{0,80}|Gsuite Renewal[^\\n]{0,80}|Domain[^\\n]{0,80}|Hosting[^\\n]{0,80}|Website[^\\n]{0,80})/i', $text, $sdMatches)) {
            $subDescs = array_map('trim', $sdMatches[1]);
        }

        // Also try a broader pattern if the above didn't catch line items
        if (empty($lineItems)) {
            // Simpler: just find rows with "9983" HSN and amount
            if (preg_match_all('/9983\s+([\d.]+)\s+([\d.]+)\s+\d+\s+([\d,]+\.?\d{2})/i', $text, $rows2, PREG_SET_ORDER)) {
                foreach ($rows2 as $i => $row) {
                    $lineItems[] = [
                        'sr'          => $i + 1,
                        'hsn'         => '9983',
                        'qty'         => (float) $row[1],
                        'rate'        => (float) $row[2],
                        'amount'      => (float) str_replace(',', '', $row[3]),
                        'particulars' => 'INFORMATION TECHNOLOGY SERVICE',
                    ];
                }
            }
        }

        // Attach sub-descriptions to line items if counts match
        if (!empty($subDescs) && !empty($lineItems)) {
            foreach ($lineItems as $i => &$item) {
                if (isset($subDescs[$i])) {
                    $item['sub_description'] = $subDescs[$i];
                }
            }
            unset($item);
        }

        $data['line_items'] = $lineItems;

        // Build combined description string
        $descParts = [];
        foreach ($lineItems as $item) {
            $part = $item['particulars'];
            if (!empty($item['sub_description'])) {
                $part .= ' - ' . $item['sub_description'];
            }
            $descParts[] = $part;
        }
        if (!empty($subDescs) && empty($descParts)) {
            $descParts = $subDescs;
        }
        $data['description'] = implode(' | ', $descParts) ?: null;

        // ── 8. TOTAL AMOUNT BEFORE TAX  ───────────────────────────────
        // We use the PRINTED total, NOT sum of line items, to be PDF-accurate.
        if (preg_match('/Total\s+Amount\s+Before\s+Tax\s+([\d,]+\.?\d{0,2})/i', $text, $m)) {
            $data['amount_before_tax'] = (float) str_replace(',', '', $m[1]);
        } elseif (!empty($lineItems)) {
            // Fallback: sum line item amounts
            $data['amount_before_tax'] = round(array_sum(array_column($lineItems, 'amount')), 2);
        }

        // ── 9. SGST  ──────────────────────────────────────────────────
        if (preg_match('/\bSGST\b\s+[\d.]+%\s+([\d,]+\.?\d{2})/i', $text, $m)) {
            $data['sgst_amount'] = (float) str_replace(',', '', $m[1]);
        } elseif (preg_match('/\bSGST\b\s+([\d,]+\.\d{2})/i', $text, $m)) {
            $data['sgst_amount'] = (float) str_replace(',', '', $m[1]);
        }

        // ── 10. CGST  ─────────────────────────────────────────────────
        if (preg_match('/\bCGST\b\s+[\d.]+%\s+([\d,]+\.?\d{2})/i', $text, $m)) {
            $data['cgst_amount'] = (float) str_replace(',', '', $m[1]);
        } elseif (preg_match('/\bCGST\b\s+([\d,]+\.\d{2})/i', $text, $m)) {
            $data['cgst_amount'] = (float) str_replace(',', '', $m[1]);
        }

        // ── 11. IGST  ─────────────────────────────────────────────────
        if (preg_match('/\bIGST\b\s+[\d.]+%\s+([\d,]+\.?\d{2})/i', $text, $m)) {
            $val = (float) str_replace(',', '', $m[1]);
            if ($val > 0) $data['igst_amount'] = $val;
        } elseif (preg_match('/\bIGST\b\s+([\d,]+\.\d{2})/i', $text, $m)) {
            $val = (float) str_replace(',', '', $m[1]);
            if ($val > 0) $data['igst_amount'] = $val;
        }

        // ── 12. ROUND OFF  ────────────────────────────────────────────
        if (preg_match('/Round\s*Off\s+([\d,]+\.?\d{0,2})/i', $text, $m)) {
            $data['round_off'] = (float) str_replace(',', '', $m[1]);
        }

        // ── 13. TOTAL AMOUNT AFTER TAX  ───────────────────────────────
        // Try A: direct label
        if (preg_match('/Total\s+Amount\s+After\s+Tax\s*[:\-]?\s*([\d,]+\.?\d{0,2})/i', $text, $m)) {
            $data['total_amount'] = (float) str_replace(',', '', $m[1]);
        }

        // Try B: compute from parts
        if ($data['total_amount'] == 0 && $data['amount_before_tax'] > 0) {
            $data['total_amount'] = round(
                $data['amount_before_tax']
                    + $data['sgst_amount']
                    + $data['cgst_amount']
                    + $data['igst_amount']
                    + $data['round_off'],
                2
            );
        }

        // Try C: largest plausible number in text
        if ($data['total_amount'] == 0 && $data['amount_before_tax'] > 0) {
            preg_match_all('/\b(\d{3,7}\.\d{2})\b/', $text, $all);
            if (!empty($all[1])) {
                $nums = array_unique(array_map('floatval', $all[1]));
                rsort($nums);
                foreach ($nums as $num) {
                    if ($num > $data['amount_before_tax'] && $num < $data['amount_before_tax'] * 2.5) {
                        $data['total_amount'] = $num;
                        break;
                    }
                }
            }
        }

        return $data;
    }
}
