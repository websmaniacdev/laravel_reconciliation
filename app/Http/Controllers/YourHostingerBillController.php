<?php

namespace App\Http\Controllers;

use App\Models\YourHostingerBill;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Smalot\PdfParser\Parser as PdfParser;

class YourHostingerBillController extends Controller
{
    // ══════════════════════════════════════════════════════════════════
    // INDEX
    // ══════════════════════════════════════════════════════════════════

    public function index(Request $request)
    {
        $query = YourHostingerBill::query();

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

        $bills = $query->orderBy('invoice_date', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(100)
            ->withQueryString();

        return view('your-hostinger-bill.index', compact(
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
                $stored       = $file->store('your-hostinger-bills', 'local');
                $fullPath     = Storage::disk('local')->path($stored);

                $parser = new PdfParser();
                $pdf    = $parser->parseFile($fullPath);
                $text   = $pdf->getText();

                $data = $this->extractFromText($text);
                $data['original_filename'] = $originalName;
                $data['pdf_path']          = $stored;

                YourHostingerBill::create($data);
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
        $bill = YourHostingerBill::findOrFail($id);
        $validated = $request->validate([
            'client_name' => 'nullable|string|max:255',
        ]);
        $bill->update(['client_name' => $validated['client_name'] ?? null]);
        return response()->json(['success' => true]);
    }

    // ══════════════════════════════════════════════════════════════════
    // DELETE
    // ══════════════════════════════════════════════════════════════════

    public function destroy($id)
    {
        $bill = YourHostingerBill::findOrFail($id);
        if ($bill->pdf_path && Storage::disk('local')->exists($bill->pdf_path)) {
            Storage::disk('local')->delete($bill->pdf_path);
        }
        $bill->delete();
        return response()->json(['success' => true]);
    }

    // ══════════════════════════════════════════════════════════════════
    // PDF TEXT PARSER  —  FIXED & ROBUST
    // ══════════════════════════════════════════════════════════════════
    //
    // NOTE: smalot/pdfparser reads multi-column PDFs left-to-right per
    // page column, so "Total Amount After Tax" label and its value
    // (6844.00) may land far apart in the extracted string.
    // Strategy:
    //   • Use tight, specific patterns for each field
    //   • For total_amount: try direct match, fallback = compute from parts
    //
    private function extractFromText(string $text): array
    {
        $data = [
            'invoice_number'    => null,
            'invoice_date'      => null,
            'invoice_type'      => 'Tax Invoice',
            'client_name'       => null,
            'client_domain'     => null,
            'client_address'    => null,
            'client_gstin'      => null,
            'client_state'      => null,
            'particulars'       => null,
            'description'       => null,
            'hsn'               => null,
            'qty'               => 1,
            'rate'              => 0,
            'amount_before_tax' => 0,
            'sgst_amount'       => 0,
            'cgst_amount'       => 0,
            'igst_amount'       => 0,
            'round_off'         => 0,
            'total_amount'      => 0,
        ];

        // ── 1. INVOICE NUMBER  ────────────────────────────────────────
        // Direct token match: WBM/26-27/14
        // Pattern: LETTERS / YY-YY / NUMBER
        if (preg_match('/\b([A-Z]{2,8}\/\d{2}-\d{2}\/\d+)\b/', $text, $m)) {
            $data['invoice_number'] = trim($m[1]);
        }

        // ── 2. INVOICE DATE  ──────────────────────────────────────────
        // Direct token match: 16-04-2026  (DD-MM-YYYY)
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

        // ── 3. CLIENT NAME  ───────────────────────────────────────────
        // "Name POWER SALES CORPORATION" — all-caps, 4-60 chars
        // if (preg_match('/Name\s*[:\-]?\s*([A-Z][A-Z\s]{3,60}?)(?:\n|Address|GSTIN|Place)/m', $text, $m)) {
        //     $raw = trim($m[1]);
        //     if ($raw) {
        //         $data['client_name'] = ucwords(strtolower($raw));
        //     }
        // }

        // ── 4. CLIENT GSTIN  ──────────────────────────────────────────
        // Standard GSTIN: 15 chars — grab all, last one = client
        if (preg_match_all('/\b([0-9]{2}[A-Z]{5}[0-9]{4}[A-Z][A-Z0-9]Z[A-Z0-9])\b/', $text, $matches)) {
            $data['client_gstin'] = end($matches[1]);
        }

        // ── 5. PARTICULARS & DESCRIPTION  ────────────────────────────
        if (preg_match('/INFORMATION TECHNOLOGY SERVICE/i', $text)) {
            $data['particulars'] = 'INFORMATION TECHNOLOGY SERVICE';
        }
        if (preg_match('/Domain and Hosting Renewal\s*\(([^)]+)\)/i', $text, $m)) {
            $domain = strtolower(trim($m[1]));
            $data['description']   = 'Domain and Hosting Renewal (' . $domain . ')';
            $data['client_domain'] = $domain;
            if (empty($data['client_name'])) {
                $data['client_name'] = YourHostingerBill::cleanClientName($domain);
            }
        }

        // ── 6. HSN CODE  ──────────────────────────────────────────────
        if (preg_match('/\b(9983|9984|9985|9988|9989)\b/', $text, $m)) {
            $data['hsn'] = $m[1];
        }

        // ── 7. AMOUNT BEFORE TAX  ─────────────────────────────────────
        // "Total Amount Before Tax 5800.00"
        if (preg_match('/Total\s+Amount\s+Before\s+Tax\s+([\d,]+\.?\d{0,2})/i', $text, $m)) {
            $data['amount_before_tax'] = (float) str_replace(',', '', $m[1]);
            $data['rate']              = $data['amount_before_tax'];
        }

        // ── 8. SGST AMOUNT (direct ₹ amount)  ────────────────────────
        // PDF may show:  "SGST 9.00% 522.00"  OR  "SGST 522.00"
        if (preg_match('/\bSGST\b\s+[\d.]+%\s+([\d,]+\.?\d{2})/i', $text, $m)) {
            $data['sgst_amount'] = (float) str_replace(',', '', $m[1]);
        } elseif (preg_match('/\bSGST\b\s+([\d,]+\.\d{2})/i', $text, $m)) {
            $data['sgst_amount'] = (float) str_replace(',', '', $m[1]);
        }

        // ── 9. CGST AMOUNT (direct ₹ amount)  ────────────────────────
        if (preg_match('/\bCGST\b\s+[\d.]+%\s+([\d,]+\.?\d{2})/i', $text, $m)) {
            $data['cgst_amount'] = (float) str_replace(',', '', $m[1]);
        } elseif (preg_match('/\bCGST\b\s+([\d,]+\.\d{2})/i', $text, $m)) {
            $data['cgst_amount'] = (float) str_replace(',', '', $m[1]);
        }

        // ── 10. IGST AMOUNT (direct ₹ amount)  ───────────────────────
        // For intra-state invoices IGST = 0; text may read "IGST\n• bullet..."
        // We only capture if a real number follows directly
        if (preg_match('/\bIGST\b\s+[\d.]+%\s+([\d,]+\.?\d{2})/i', $text, $m)) {
            $data['igst_amount'] = (float) str_replace(',', '', $m[1]);
        } elseif (preg_match('/\bIGST\b\s+([\d,]+\.\d{2})/i', $text, $m)) {
            $val = (float) str_replace(',', '', $m[1]);
            // Guard: ignore stray 0.00 that belongs to Round Off line
            if ($val > 0) {
                $data['igst_amount'] = $val;
            }
        }

        // ── 11. ROUND OFF  ────────────────────────────────────────────
        if (preg_match('/Round\s*Off\s+([\d,]+\.?\d{0,2})/i', $text, $m)) {
            $data['round_off'] = (float) str_replace(',', '', $m[1]);
        }

        // ── 12. TOTAL AMOUNT AFTER TAX  ───────────────────────────────
        // Try A: direct — "Total Amount After Tax 6844.00"
        if (preg_match('/Total\s+Amount\s+After\s+Tax\s+([\d,]+\.?\d{0,2})/i', $text, $m)) {
            $data['total_amount'] = (float) str_replace(',', '', $m[1]);
        }

        // Try B: compute from parts (most reliable when PDF is scrambled)
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

        // Try C: last resort — scan for a large number >= before_tax in the text
        // (handles "Bank Name 6844.00" scrambling)
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
