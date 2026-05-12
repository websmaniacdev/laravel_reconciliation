<?php

namespace App\Http\Controllers;

use App\Models\YourGodaddyBill;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Smalot\PdfParser\Parser as PdfParser;

class YourGodaddyBillController extends Controller
{
    // ══════════════════════════════════════════════════════════════════
    // INDEX
    // ══════════════════════════════════════════════════════════════════

    public function index(Request $request)
    {
        $query = YourGodaddyBill::query();

        if ($request->filled('client_name')) {
            $query->where('client_name', 'like', '%' . $request->client_name . '%');
        }
        if ($request->filled('domain_name')) {
            $query->where('domain_name', 'like', '%' . $request->domain_name . '%');
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

        return view('your-godaddy-bill.index', compact(
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
    // UPLOAD
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
                $stored       = $file->store('your-godaddy-bills', 'local');
                $fullPath     = Storage::disk('local')->path($stored);

                $parser = new PdfParser();
                $pdf    = $parser->parseFile($fullPath);
                $text   = $pdf->getText();

                $data = $this->extractFromText($text);
                $data['original_filename'] = $originalName;
                $data['pdf_path']          = $stored;

                YourGodaddyBill::create($data);
                $success++;
            } catch (\Throwable $e) {
                $errors[] = ($file->getClientOriginalName() ?? 'unknown') . ': ' . $e->getMessage();
            }
        }

        return redirect()->back()
            ->with('success', $success > 0 ? "{$success} GoDaddy bill(s) uploaded and parsed!" : 'No bills processed.')
            ->with('upload_errors', $errors);
    }

    // ══════════════════════════════════════════════════════════════════
    // UPDATE CLIENT NAME
    // ══════════════════════════════════════════════════════════════════

    public function updateClientName(Request $request, $id)
    {
        $bill = YourGodaddyBill::findOrFail($id);
        $validated = $request->validate(['client_name' => 'nullable|string|max:255']);
        $bill->update(['client_name' => $validated['client_name'] ?? null]);
        return response()->json(['success' => true]);
    }

    // ══════════════════════════════════════════════════════════════════
    // DELETE
    // ══════════════════════════════════════════════════════════════════

    public function destroy($id)
    {
        $bill = YourGodaddyBill::findOrFail($id);
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
    // Targets Websmaniac INC invoices like:
    //   Invoice No: WBM/25-26/280
    //   Client:     MADHURASH CARDS & GIFTS
    //   Domain:     myshaadicards.com  (from "Domain Renewal (myshaadicards.com)")
    //   Amount:     3398.00 + SGST 305.82 + CGST 305.82 = 4010.00
    //
    private function extractFromText(string $text): array
    {
        $data = [
            'invoice_number'         => null,
            'invoice_date'           => null,
            'invoice_type'           => 'Tax Invoice',
            'client_name'            => null,
            'client_address'         => null,
            'client_gstin'           => null,
            'client_place_of_supply' => null,
            'domain_name'            => null,
            'service_period'         => null,
            'description'            => null,
            'amount_before_tax'      => 0,
            'sgst_amount'            => 0,
            'cgst_amount'            => 0,
            'igst_amount'            => 0,
            'round_off'              => 0,
            'total_amount'           => 0,
        ];

        // ── 1. INVOICE NUMBER ─────────────────────────────────────────
        if (preg_match('/\b([A-Z]{2,8}\/\d{2}-\d{2}\/\d+)\b/', $text, $m)) {
            $data['invoice_number'] = trim($m[1]);
        }

        // ── 2. INVOICE DATE ───────────────────────────────────────────
        if (preg_match('/\b(\d{1,2}[-\/]\d{1,2}[-\/]\d{4})\b/', $text, $m)) {
            $raw = str_replace('/', '-', $m[1]);
            try {
                $data['invoice_date'] = \Carbon\Carbon::createFromFormat('d-m-Y', $raw)->format('Y-m-d');
            } catch (\Throwable) {
                try {
                    $data['invoice_date'] = \Carbon\Carbon::parse($raw)->format('Y-m-d');
                } catch (\Throwable) {
                }
            }
        }

        // ── 3. CLIENT NAME ────────────────────────────────────────────
        if (preg_match('/\bName\s+([A-Za-z][A-Za-z0-9\s\-&\'\.]{1,80}?)(?:\n|Address|GSTIN|Place)/m', $text, $m)) {
            $raw = trim($m[1]);
            if ($raw && strlen($raw) > 1) {
                $data['client_name'] = $raw;
            }
        }

        // ── 4. CLIENT ADDRESS ─────────────────────────────────────────
        if (preg_match('/Address\s*[:\-]?\s*"?([^"]{5,200}?)(?:"|\n\n|GSTIN|Place of Supply)/ms', $text, $m)) {
            $data['client_address'] = trim(preg_replace('/\s+/', ' ', $m[1]));
        }

        // ── 5. CLIENT GSTIN ───────────────────────────────────────────
        $sellerGstin = '24CIBPS7329P1ZM';
        if (preg_match_all('/\b([0-9]{2}[A-Z]{5}[0-9]{4}[A-Z][A-Z0-9]Z[A-Z0-9])\b/', $text, $matches)) {
            $allGstins    = array_values(array_unique($matches[1]));
            $clientGstins = array_filter($allGstins, fn($g) => $g !== $sellerGstin);
            if (!empty($clientGstins)) {
                $data['client_gstin'] = array_values($clientGstins)[0];
            }
        }

        // ── 6. PLACE OF SUPPLY ────────────────────────────────────────
        if (preg_match('/Place\s+of\s+Supply\s*[:\-]?\s*([A-Za-z]+(?:\s+[A-Za-z]+)?)\s/i', $text, $m)) {
            $data['client_place_of_supply'] = trim($m[1]);
        }

        // ── 7. DOMAIN NAME ────────────────────────────────────────────
        // Extract from patterns like:
        //   "Domain Renewal (myshaadicards.com)"
        //   "Domain Registration (example.com)"
        //   "Domain Transfer (test.in)"
        if (preg_match(
            '/Domain\s+(?:Renewal|Registration|Transfer|Hosting)[^\(]*\(\s*([a-z0-9\-\.]+\.[a-z]{2,})\s*\)/i',
            $text,
            $m
        )) {
            $data['domain_name'] = strtolower(trim($m[1]));
        } elseif (preg_match('/\b([a-z0-9\-]+\.[a-z]{2,}(?:\.[a-z]{2})?)\b/i', $text, $m)) {
            // Fallback: any domain-like string (exclude email domains)
            $candidate = strtolower($m[1]);
            if (!in_array($candidate, ['websmaniac.com', 'info@websmaniac.com'])) {
                $data['domain_name'] = $candidate;
            }
        }

        // ── 8. SERVICE PERIOD ─────────────────────────────────────────
        // "( For Two Years)" or "For One Year" etc.
        if (preg_match('/\(\s*For\s+([^)]+)\)/i', $text, $m)) {
            $data['service_period'] = trim($m[1]);
        } elseif (preg_match('/For\s+(One|Two|Three|Four|Five|\d+)\s+Year/i', $text, $m)) {
            $data['service_period'] = 'For ' . trim($m[1]) . ' Year(s)';
        }

        // ── 9. DESCRIPTION ────────────────────────────────────────────
        $desc = [];
        if (preg_match('/Domain\s+(?:Renewal|Registration|Transfer|Hosting)[^\n]{0,100}/i', $text, $m)) {
            $desc[] = trim($m[0]);
        }
        if (!empty($data['service_period'])) {
            $desc[] = $data['service_period'];
        }
        $data['description'] = implode(' — ', $desc) ?: null;

        // ── 10. AMOUNT BEFORE TAX ─────────────────────────────────────
        if (preg_match('/Total\s+Amount\s+Before\s+Tax\s+([\d,]+\.?\d{0,2})/i', $text, $m)) {
            $data['amount_before_tax'] = (float) str_replace(',', '', $m[1]);
        }

        // ── 11. SGST ──────────────────────────────────────────────────
        if (preg_match('/\bSGST\b\s+[\d.]+%\s+([\d,]+\.?\d{2})/i', $text, $m)) {
            $data['sgst_amount'] = (float) str_replace(',', '', $m[1]);
        } elseif (preg_match('/\bSGST\b\s+([\d,]+\.\d{2})/i', $text, $m)) {
            $data['sgst_amount'] = (float) str_replace(',', '', $m[1]);
        }

        // ── 12. CGST ──────────────────────────────────────────────────
        if (preg_match('/\bCGST\b\s+[\d.]+%\s+([\d,]+\.?\d{2})/i', $text, $m)) {
            $data['cgst_amount'] = (float) str_replace(',', '', $m[1]);
        } elseif (preg_match('/\bCGST\b\s+([\d,]+\.\d{2})/i', $text, $m)) {
            $data['cgst_amount'] = (float) str_replace(',', '', $m[1]);
        }

        // ── 13. IGST ──────────────────────────────────────────────────
        if (preg_match('/\bIGST\b\s+[\d.]+%\s+([\d,]+\.?\d{2})/i', $text, $m)) {
            $val = (float) str_replace(',', '', $m[1]);
            if ($val > 0) $data['igst_amount'] = $val;
        }

        // ── 14. ROUND OFF ─────────────────────────────────────────────
        if (preg_match('/Round\s*Off\s+([\d,]+\.?\d{0,2})/i', $text, $m)) {
            $data['round_off'] = (float) str_replace(',', '', $m[1]);
        }

        // ── 15. TOTAL AMOUNT AFTER TAX ────────────────────────────────
        if (preg_match('/Total\s+Amount\s+After\s+Tax\s*[:\-]?\s*([\d,]+\.?\d{0,2})/i', $text, $m)) {
            $data['total_amount'] = (float) str_replace(',', '', $m[1]);
        }

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

        return $data;
    }
}
