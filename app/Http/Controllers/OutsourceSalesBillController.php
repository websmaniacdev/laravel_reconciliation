<?php

namespace App\Http\Controllers;

use App\Models\OutsourceSalesBill;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Smalot\PdfParser\Parser as PdfParser;

class OutsourceSalesBillController extends Controller
{
    // ══════════════════════════════════════════════════════════════════
    // INDEX — returns data for use inside OutsourceReceiptController
    // (bills are shown inside the outsource view, not a separate page)
    // ══════════════════════════════════════════════════════════════════

    public function index(Request $request)
    {
        $query = OutsourceSalesBill::query();

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

        $totalBeforeTax = (clone $query)->sum('amount_before_tax');
        $totalSgst      = (clone $query)->sum('sgst_amount');
        $totalCgst      = (clone $query)->sum('cgst_amount');
        $totalIgst      = (clone $query)->sum('igst_amount');
        $grandTotal     = (clone $query)->sum('total_amount');
        $totalCount     = (clone $query)->count();

        $bills = $query->orderBy('invoice_date', 'desc')->orderBy('id', 'desc')->paginate(100)->withQueryString();

        return view('outsource.sales-bills.index', compact(
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
    // UPLOAD & PARSE PDF
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
                $stored       = $file->store('outsource-sales-bills', 'local');
                $fullPath     = Storage::disk('local')->path($stored);

                $parser = new PdfParser();
                $pdf    = $parser->parseFile($fullPath);
                $text   = $pdf->getText();

                $data = $this->extractFromText($text);
                $data['original_filename'] = $originalName;
                $data['pdf_path']          = $stored;

                OutsourceSalesBill::create($data);
                $success++;
            } catch (\Throwable $e) {
                $errors[] = ($file->getClientOriginalName() ?? 'unknown') . ': ' . $e->getMessage();
            }
        }

        $msg = $success > 0
            ? "{$success} Sales Bill(s) uploaded and parsed successfully!"
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
        $bill      = OutsourceSalesBill::findOrFail($id);
        $validated = $request->validate(['client_name' => 'nullable|string|max:255']);
        $bill->update(['client_name' => $validated['client_name'] ?? null]);
        return response()->json(['success' => true]);
    }

    // ══════════════════════════════════════════════════════════════════
    // DELETE
    // ══════════════════════════════════════════════════════════════════

    public function destroy($id)
    {
        $bill = OutsourceSalesBill::findOrFail($id);
        if ($bill->pdf_path && Storage::disk('local')->exists($bill->pdf_path)) {
            Storage::disk('local')->delete($bill->pdf_path);
        }
        $bill->delete();
        return response()->json(['success' => true]);
    }

    // ══════════════════════════════════════════════════════════════════
    // PDF TEXT PARSER — WBM Invoice Format (WEBSMANIAC INC style)
    //
    // Handles the multi-column PDF layout where smalot/pdfparser
    // reads columns left-to-right, mixing labels & values.
    //
    // Sample invoice fields:
    //   Invoice No : WBM/26-27/24      Date : 30-04-2026
    //   Name : Sanghani Hospital
    //   GSTIN : 24ABHCS2858M1ZU
    //   Place of Supply : Junagadh     State Code : 24
    //   HSN: 9983  Qty: 6  Rate: 3500  GST%: 18%  Amount: 21000
    //   SGST 9%: 1890  CGST 9%: 1890  IGST 0%: 0
    //   Total After Tax: 24780
    // ══════════════════════════════════════════════════════════════════

    private function extractFromText(string $text): array
    {
        $data = [
            'invoice_number'    => null,
            'invoice_date'      => null,
            'invoice_type'      => 'Tax Invoice',
            'client_name'       => null,
            'client_address'    => null,
            'client_gstin'      => null,
            'client_state'      => null,
            'client_state_code' => null,
            'place_of_supply'   => null,
            'particulars'       => null,
            'description'       => null,
            'hsn'               => null,
            'qty'               => 1,
            'rate'              => 0,
            'gst_percent'       => 18,
            'amount_before_tax' => 0,
            'sgst_percent'      => 0,
            'sgst_amount'       => 0,
            'cgst_percent'      => 0,
            'cgst_amount'       => 0,
            'igst_percent'      => 0,
            'igst_amount'       => 0,
            'round_off'         => 0,
            'total_amount'      => 0,
        ];

        // ── 1. INVOICE NUMBER  ─────────────────────────────────────
        // WBM/26-27/24  or  WBM/25-26/14  etc.
        if (preg_match('/\b([A-Z]{2,8}\/\d{2}-\d{2}\/\d+)\b/', $text, $m)) {
            $data['invoice_number'] = trim($m[1]);
        }

        // ── 2. INVOICE DATE (DD-MM-YYYY)  ──────────────────────────
        if (preg_match('/\b(\d{2}-\d{2}-\d{4})\b/', $text, $m)) {
            try {
                $data['invoice_date'] = \Carbon\Carbon::createFromFormat('d-m-Y', $m[1])->format('Y-m-d');
            } catch (\Throwable $e) {
                try {
                    $data['invoice_date'] = \Carbon\Carbon::parse($m[1])->format('Y-m-d');
                } catch (\Throwable $e2) {
                }
            }
        }

        // ── 3. CLIENT NAME  ────────────────────────────────────────
        // "Name Sanghani Hospital" or "Name : Sanghani Hospital"
        if (preg_match('/Name\s*[:\-]?\s*([A-Z][A-Za-z\s&.\-]{2,60}?)(?:\n|Address|GSTIN|Place|$)/m', $text, $m)) {
            $raw = trim($m[1]);
            if ($raw && strlen($raw) > 2) {
                $data['client_name'] = $raw;
            }
        }

        // ── 4. CLIENT ADDRESS  ─────────────────────────────────────
        if (preg_match('/Address\s*[:\-]?\s*(.+?)(?:GSTIN|Place of Supply|State\s*:|$)/si', $text, $m)) {
            $addr = trim(preg_replace('/\s+/', ' ', $m[1]));
            if ($addr) {
                $data['client_address'] = $addr;
            }
        }

        // ── 5. CLIENT GSTIN  ───────────────────────────────────────
        // All GSTINs in text; last one = client (first = supplier)
        if (preg_match_all('/\b([0-9]{2}[A-Z]{5}[0-9]{4}[A-Z][A-Z0-9]Z[A-Z0-9])\b/', $text, $matches)) {
            $data['client_gstin'] = end($matches[1]);
        }

        // ── 6. PLACE OF SUPPLY & STATE CODE  ──────────────────────
        if (preg_match('/Place\s+of\s+Supply\s*[:\-]?\s*([A-Za-z\s]+?)(?:State|$|\n)/im', $text, $m)) {
            $data['place_of_supply'] = trim($m[1]);
        }
        if (preg_match('/State\s*(?:Code)?\s*[:\-]?\s*([A-Za-z]+).*?(\d{2})/is', $text, $m)) {
            $data['client_state']      = trim($m[1]);
            $data['client_state_code'] = trim($m[2]);
        }

        // ── 7. PARTICULARS  ────────────────────────────────────────
        if (preg_match('/INFORMATION TECHNOLOGY SERVICE/i', $text)) {
            $data['particulars'] = 'INFORMATION TECHNOLOGY SERVICE';
        }

        // ── 8. DESCRIPTION (service description line)  ─────────────
        // e.g. "Gsuite Renewal (April 2026 to September 2026)"
        if (preg_match('/((?:Gsuite|G-?Suite|Domain|Hosting|Cloud)\s+Renewal[^\\n]{0,80})/i', $text, $m)) {
            $data['description'] = trim($m[1]);
        }

        // ── 9. HSN CODE  ───────────────────────────────────────────
        if (preg_match('/\b(9983|9984|9985|9988|9989)\b/', $text, $m)) {
            $data['hsn'] = $m[1];
        }

        // ── 10. QTY & RATE  ────────────────────────────────────────
        // "6.000 21000.00" after HSN or "Qty ... Rate ... Amount"
        // Look for pattern: HSN  qty  rate  amount in sequence
        if (preg_match('/\b9983\b\s+([\d.]+)\s+([\d,]+\.?\d{0,2})/i', $text, $m)) {
            $data['qty']  = (float) $m[1];
            $data['rate'] = (float) str_replace(',', '', $m[2]);
        } elseif (preg_match('/(\d+\.\d{3})\s+([\d,]+\.\d{3})\s+([\d,]+\.\d{2})/i', $text, $m)) {
            // qty(3dp)  rate(3dp)  amount(2dp)
            $data['qty']  = (float) $m[1];
            $data['rate'] = (float) str_replace(',', '', $m[2]);
        }

        // ── 11. GST %  ─────────────────────────────────────────────
        if (preg_match('/(\d{1,2})%\s*(?:GST|gst)/i', $text, $m)) {
            $data['gst_percent'] = (float) $m[1];
        } elseif (preg_match('/GST%?\s*[:\-]?\s*(\d{1,2})%?/i', $text, $m)) {
            $data['gst_percent'] = (float) $m[1];
        }

        // ── 12. AMOUNT BEFORE TAX  ─────────────────────────────────
        if (preg_match('/Total\s+Amount\s+Before\s+Tax\s+([\d,]+\.?\d{0,2})/i', $text, $m)) {
            $data['amount_before_tax'] = (float) str_replace(',', '', $m[1]);
            if ($data['rate'] == 0 && $data['qty'] > 0) {
                $data['rate'] = $data['amount_before_tax'] / $data['qty'];
            }
        }

        // ── 13. SGST  ──────────────────────────────────────────────
        if (preg_match('/\bSGST\b\s+([\d.]+)%\s+([\d,]+\.?\d{2})/i', $text, $m)) {
            $data['sgst_percent'] = (float) $m[1];
            $data['sgst_amount']  = (float) str_replace(',', '', $m[2]);
        } elseif (preg_match('/\bSGST\b\s+([\d,]+\.\d{2})/i', $text, $m)) {
            $data['sgst_amount'] = (float) str_replace(',', '', $m[1]);
        }

        // ── 14. CGST  ──────────────────────────────────────────────
        if (preg_match('/\bCGST\b\s+([\d.]+)%\s+([\d,]+\.?\d{2})/i', $text, $m)) {
            $data['cgst_percent'] = (float) $m[1];
            $data['cgst_amount']  = (float) str_replace(',', '', $m[2]);
        } elseif (preg_match('/\bCGST\b\s+([\d,]+\.\d{2})/i', $text, $m)) {
            $data['cgst_amount'] = (float) str_replace(',', '', $m[1]);
        }

        // ── 15. IGST  ──────────────────────────────────────────────
        if (preg_match('/\bIGST\b\s+([\d.]+)%\s+([\d,]+\.?\d{2})/i', $text, $m)) {
            $data['igst_percent'] = (float) $m[1];
            $val = (float) str_replace(',', '', $m[2]);
            if ($val > 0) $data['igst_amount'] = $val;
        } elseif (preg_match('/\bIGST\b\s+([\d,]+\.\d{2})/i', $text, $m)) {
            $val = (float) str_replace(',', '', $m[1]);
            if ($val > 0) $data['igst_amount'] = $val;
        }

        // ── 16. ROUND OFF  ─────────────────────────────────────────
        if (preg_match('/Round\s*Off\s+([\d,]+\.?\d{0,2})/i', $text, $m)) {
            $data['round_off'] = (float) str_replace(',', '', $m[1]);
        }

        // ── 17. TOTAL AMOUNT AFTER TAX  ────────────────────────────
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
