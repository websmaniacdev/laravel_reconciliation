<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Your Hostinger Bills</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>
</head>

<body class="bg-gray-100 min-h-screen" x-data="yourBillApp()">

    {{-- ══════════════════ HEADER ══════════════════ --}}
    <header class="bg-white shadow-sm border-b">
        <div class="flex items-center justify-between h-16 max-w-7xl mx-auto px-4">
            @include('layouts.nav')
        </div>
        <div class="max-w-7xl mx-auto px-4 py-4 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 bg-violet-600 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414A1 1 0 0119 9.414V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <div>
                    <h1 class="text-xl font-bold text-gray-800">Your Hostinger Bills</h1>
                    <p class="text-xs text-gray-400">Upload your WBM bills — SGST, CGST, GST & totals auto-extracted</p>
                </div>
            </div>
            <div class="flex gap-2">
                <button @click="showUpload = true"
                    class="bg-violet-600 hover:bg-violet-700 text-white px-4 py-2 rounded-lg text-sm font-medium flex items-center gap-2 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                    </svg>
                    Upload PDF Bill(s)
                </button>
            </div>
        </div>
    </header>

    {{-- ══════════════════ FLASH MESSAGES ══════════════════ --}}
    @if (session('success'))
        <div class="max-w-7xl mx-auto px-4 mt-4">
            <div
                class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg flex items-center gap-2">
                <svg class="w-5 h-5 text-green-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" />
                </svg>
                {{ session('success') }}
            </div>
        </div>
    @endif

    @if (session('upload_errors') && count(session('upload_errors')) > 0)
        <div class="max-w-7xl mx-auto px-4 mt-2">
            <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">
                <p class="font-medium">Some PDFs had errors:</p>
                @foreach (session('upload_errors') as $err)
                    <p class="text-sm mt-1">• {{ $err }}</p>
                @endforeach
            </div>
        </div>
    @endif

    <main class="max-w-7xl mx-auto px-4 py-6">

        {{-- ══════════════════ SUMMARY CARDS ══════════════════ --}}
        <div class="mb-5 grid grid-cols-2 md:grid-cols-5 gap-4">
            <div class="bg-white rounded-xl shadow-sm px-4 py-4 border border-gray-100">
                <p class="text-xs text-gray-400 mb-1">Total Bills</p>
                <p class="text-xl font-bold text-gray-800">{{ number_format($totalCount) }}</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm px-4 py-4 border border-gray-100">
                <p class="text-xs text-gray-400 mb-1">Before Tax</p>
                <p class="text-xl font-bold text-gray-700">₹ {{ number_format($totalBeforeTax, 2) }}</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm px-4 py-4 border border-orange-100">
                <p class="text-xs text-gray-400 mb-1">SGST</p>
                <p class="text-xl font-bold text-orange-600">₹ {{ number_format($totalSgst, 2) }}</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm px-4 py-4 border border-orange-100">
                <p class="text-xs text-gray-400 mb-1">CGST</p>
                <p class="text-xl font-bold text-orange-600">₹ {{ number_format($totalCgst, 2) }}</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm px-4 py-4 border border-violet-100">
                <p class="text-xs text-gray-400 mb-1">Grand Total</p>
                <p class="text-xl font-extrabold text-violet-700">₹ {{ number_format($grandTotal, 2) }}</p>
            </div>
        </div>

        {{-- ── FILTER BAR ── --}}
        <div class="bg-white rounded-xl shadow-sm p-5 mb-5">
            <form method="GET" action="{{ route('your-hostinger-bill.index') }}">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-3 items-end">
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Client Name</label>
                        <input type="text" name="client_name" value="{{ request('client_name') }}"
                            placeholder="Power Sales..."
                            class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-violet-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Invoice #</label>
                        <input type="text" name="invoice_number" value="{{ request('invoice_number') }}"
                            placeholder="WBM/26-27/..."
                            class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-violet-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">From Date</label>
                        <input type="date" name="from_date" value="{{ request('from_date') }}"
                            class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-violet-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">To Date</label>
                        <input type="date" name="to_date" value="{{ request('to_date') }}"
                            class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-violet-500 outline-none">
                    </div>
                    <div class="flex gap-2">
                        <button type="submit"
                            class="flex-1 bg-violet-600 hover:bg-violet-700 text-white px-3 py-2 rounded-lg text-sm font-medium transition">Filter</button>
                        <a href="{{ route('your-hostinger-bill.index') }}"
                            class="px-3 py-2 border border-gray-200 rounded-lg text-sm text-gray-600 hover:bg-gray-50 transition">Clear</a>
                    </div>
                </div>
            </form>
        </div>

        {{-- ══════════════════ BILLS TABLE ══════════════════ --}}
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                <h2 class="text-sm font-semibold text-gray-700">Your Hostinger Bills</h2>
                <span class="text-xs text-gray-400">{{ $bills->total() }} total records</span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-100">
                            <th
                                class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                #</th>
                            <th
                                class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Client Name</th>
                            <th
                                class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Invoice No</th>
                            <th
                                class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Date</th>
                            <th
                                class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Description</th>
                            <th
                                class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Before Tax</th>
                            <th
                                class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                SGST</th>
                            <th
                                class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                CGST</th>
                            <th
                                class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                IGST</th>
                            <th
                                class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Total Amount</th>
                            <th
                                class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($bills as $index => $bill)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-4 py-3 text-gray-400 text-xs">{{ $bills->firstItem() + $index }}</td>

                                {{-- Client Name (editable, no .com) --}}
                                <td class="px-4 py-3">
                                    @if ($bill->client_name)
                                        <span
                                            class="inline-block px-2 py-1 bg-gray-100 rounded text-xs font-medium text-gray-800 cursor-pointer hover:bg-violet-100 transition"
                                            onclick="editClientName({{ $bill->id }}, '{{ $bill->client_name }}', this)">
                                            {{ $bill->client_name }}
                                        </span>
                                    @else
                                        <span class="text-gray-400 italic text-xs cursor-pointer hover:text-gray-600"
                                            onclick="editClientName({{ $bill->id }}, '', this)">
                                            — (click to add)
                                        </span>
                                    @endif
                                    @if ($bill->client_domain)
                                        <p class="text-xs text-gray-400 mt-0.5">{{ $bill->client_domain }}</p>
                                    @endif
                                </td>

                                {{-- Invoice Number --}}
                                <td class="px-4 py-3">
                                    <span class="font-mono text-xs bg-violet-50 text-violet-700 px-2 py-0.5 rounded">
                                        {{ $bill->invoice_number ?? '—' }}
                                    </span>
                                </td>

                                {{-- Date --}}
                                <td class="px-4 py-3 text-gray-600 text-xs whitespace-nowrap">
                                    {{ $bill->invoice_date?->format('d M Y') ?? '—' }}
                                </td>

                                {{-- Description --}}
                                <td class="px-4 py-3 max-w-xs">
                                    <p class="text-xs text-gray-700 font-medium">{{ $bill->particulars ?? '—' }}</p>
                                    @if ($bill->description)
                                        <p class="text-xs text-gray-400 truncate" title="{{ $bill->description }}">
                                            {{ $bill->description }}
                                        </p>
                                    @endif
                                </td>

                                {{-- Amount Before Tax --}}
                                <td class="px-4 py-3 text-right text-gray-700 text-xs whitespace-nowrap">
                                    ₹ {{ number_format($bill->amount_before_tax, 2) }}
                                </td>

                                {{-- SGST --}}
                                <td class="px-4 py-3 text-right text-xs whitespace-nowrap">
                                    @if ($bill->sgst_amount > 0)
                                        <span class="text-orange-600 font-medium">₹
                                            {{ number_format($bill->sgst_amount, 2) }}</span>
                                        <span class="text-gray-400 block">{{ $bill->sgst_percent }}%</span>
                                    @else
                                        <span class="text-gray-300">—</span>
                                    @endif
                                </td>

                                {{-- CGST --}}
                                <td class="px-4 py-3 text-right text-xs whitespace-nowrap">
                                    @if ($bill->cgst_amount > 0)
                                        <span class="text-orange-600 font-medium">₹
                                            {{ number_format($bill->cgst_amount, 2) }}</span>
                                        <span class="text-gray-400 block">{{ $bill->cgst_percent }}%</span>
                                    @else
                                        <span class="text-gray-300">—</span>
                                    @endif
                                </td>

                                {{-- IGST --}}
                                <td class="px-4 py-3 text-right text-xs whitespace-nowrap">
                                    @if ($bill->igst_amount > 0)
                                        <span class="text-blue-600 font-medium">₹
                                            {{ number_format($bill->igst_amount, 2) }}</span>
                                        <span class="text-gray-400 block">{{ $bill->igst_percent }}%</span>
                                    @else
                                        <span class="text-gray-300">—</span>
                                    @endif
                                </td>

                                {{-- Total Amount --}}
                                <td class="px-4 py-3 text-right font-bold text-violet-700 whitespace-nowrap">
                                    ₹ {{ number_format($bill->total_amount, 2) }}
                                </td>

                                {{-- Actions --}}
                                <td class="px-4 py-3 text-center">
                                    <button onclick="deleteBill({{ $bill->id }})"
                                        class="text-red-400 hover:text-red-600 transition p-1 rounded hover:bg-red-50"
                                        title="Delete">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="12" class="px-4 py-12 text-center text-gray-400">
                                    <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414A1 1 0 0119 9.414V19a2 2 0 01-2 2z" />
                                    </svg>
                                    <p class="font-medium">No bills found</p>
                                    <p class="text-sm mt-1">Upload a WBM Hostinger PDF bill to get started.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>

                    {{-- Footer Totals --}}
                    @if ($bills->count() > 0)
                        <tfoot>
                            <tr class="bg-violet-50 font-bold border-t-2 border-violet-200">
                                <td colspan="5"
                                    class="px-4 py-3 text-right text-violet-800 text-xs uppercase tracking-wider">
                                    Page Total ({{ $bills->count() }} bills)
                                </td>
                                <td class="px-4 py-3 text-right text-gray-800 text-xs whitespace-nowrap">
                                    ₹ {{ number_format($bills->sum('amount_before_tax'), 2) }}
                                </td>
                                <td class="px-4 py-3 text-right text-orange-700 text-xs whitespace-nowrap">
                                    ₹ {{ number_format($bills->sum('sgst_amount'), 2) }}
                                </td>
                                <td class="px-4 py-3 text-right text-orange-700 text-xs whitespace-nowrap">
                                    ₹ {{ number_format($bills->sum('cgst_amount'), 2) }}
                                </td>
                                <td class="px-4 py-3 text-right text-blue-700 text-xs whitespace-nowrap">
                                    ₹ {{ number_format($bills->sum('igst_amount'), 2) }}
                                </td>
                                <td></td>
                                <td class="px-4 py-3 text-right text-violet-700 text-base whitespace-nowrap">
                                    ₹ {{ number_format($bills->sum('total_amount'), 2) }}
                                </td>
                                <td></td>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>

            {{-- PAGINATION --}}
            @if ($bills->hasPages())
                <div class="border-t border-gray-100 px-4 py-3 flex items-center justify-between">
                    <p class="text-sm text-gray-500">
                        Showing {{ $bills->firstItem() }}–{{ $bills->lastItem() }} of {{ $bills->total() }} records
                    </p>
                    <div class="flex items-center gap-1">
                        @if ($bills->onFirstPage())
                            <span class="px-3 py-1.5 text-sm text-gray-300 border border-gray-100 rounded-lg">←
                                Prev</span>
                        @else
                            <a href="{{ $bills->previousPageUrl() }}"
                                class="px-3 py-1.5 text-sm text-gray-600 border border-gray-200 rounded-lg hover:bg-gray-50 transition">←
                                Prev</a>
                        @endif
                        @foreach ($bills->getUrlRange(max(1, $bills->currentPage() - 2), min($bills->lastPage(), $bills->currentPage() + 2)) as $page => $url)
                            @if ($page == $bills->currentPage())
                                <span
                                    class="px-3 py-1.5 text-sm bg-violet-600 text-white border border-violet-600 rounded-lg">{{ $page }}</span>
                            @else
                                <a href="{{ $url }}"
                                    class="px-3 py-1.5 text-sm text-gray-600 border border-gray-200 rounded-lg hover:bg-gray-50 transition">{{ $page }}</a>
                            @endif
                        @endforeach
                        @if ($bills->hasMorePages())
                            <a href="{{ $bills->nextPageUrl() }}"
                                class="px-3 py-1.5 text-sm text-gray-600 border border-gray-200 rounded-lg hover:bg-gray-50 transition">Next
                                →</a>
                        @else
                            <span class="px-3 py-1.5 text-sm text-gray-300 border border-gray-100 rounded-lg">Next
                                →</span>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </main>

    {{-- ══════════════════ UPLOAD MODAL ══════════════════ --}}
    <div x-show="showUpload" x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm"
        @click.self="showUpload = false">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg mx-4 p-6" @click.stop>
            <div class="flex items-center justify-between mb-5">
                <h2 class="text-lg font-semibold text-gray-800">Upload Your Hostinger Bill PDF(s)</h2>
                <button @click="showUpload = false" class="text-gray-400 hover:text-gray-600 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <form method="POST" action="{{ route('your-hostinger-bill.upload') }}" enctype="multipart/form-data">
                @csrf
                <div class="border-2 border-dashed border-gray-200 rounded-xl p-8 text-center hover:border-violet-400 transition cursor-pointer"
                    @click="$refs.fileInput.click()" @dragover.prevent @drop.prevent="handleDrop($event)">
                    <svg class="w-10 h-10 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                    </svg>
                    <p class="text-sm text-gray-500">Click to select WBM bill PDF(s) or drag & drop</p>
                    <p class="text-xs text-gray-400 mt-1">SGST, CGST, GST & amounts auto-extracted • Max 10MB each</p>
                    <input type="file" name="pdfs[]" multiple accept=".pdf" x-ref="fileInput" class="hidden"
                        @change="handleFiles($event)">
                </div>
                <div x-show="uploadFiles.length > 0" class="mt-3 space-y-1 max-h-40 overflow-y-auto">
                    <template x-for="(f, i) in uploadFiles" :key="i">
                        <div class="flex items-center gap-2 text-sm text-gray-600 bg-gray-50 rounded-lg px-3 py-2">
                            <svg class="w-4 h-4 text-red-400 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" />
                            </svg>
                            <span x-text="f.name" class="truncate flex-1"></span>
                            <span class="text-gray-400 text-xs flex-shrink-0"
                                x-text="(f.size/1024).toFixed(0) + ' KB'"></span>
                        </div>
                    </template>
                </div>
                <div class="mt-3 bg-violet-50 border border-violet-100 rounded-lg px-4 py-3">
                    <p class="text-xs text-violet-700">
                        📋 PDF will be parsed immediately — client name (without .com), invoice no, date,
                        SGST, CGST, amount before tax & total amount all extracted automatically.
                    </p>
                </div>
                <div class="flex gap-3 mt-5">
                    <button type="button" @click="showUpload = false"
                        class="flex-1 px-4 py-2.5 border border-gray-200 rounded-xl text-sm text-gray-600 hover:bg-gray-50 transition">Cancel</button>
                    <button type="submit" :disabled="uploadFiles.length === 0"
                        class="flex-1 px-4 py-2.5 bg-violet-600 hover:bg-violet-700 disabled:bg-violet-300 text-white rounded-xl text-sm font-medium transition">
                        Upload & Parse PDF(s)
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function yourBillApp() {
            return {
                showUpload: false,
                showManual: false,
                uploadFiles: [],
                handleFiles(e) {
                    this.uploadFiles = Array.from(e.target.files);
                },
                handleDrop(e) {
                    const files = Array.from(e.dataTransfer.files).filter(f => f.type === 'application/pdf');
                    if (files.length) {
                        this.uploadFiles = files;
                        const dt = new DataTransfer();
                        files.forEach(f => dt.items.add(f));
                        this.$refs.fileInput.files = dt.files;
                    }
                },
            };
        }

        function deleteBill(id) {
            if (!confirm('Delete this bill record?')) return;
            fetch(`/your-hostinger-bill/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                }
            }).then(r => r.json()).then(d => {
                if (d.success) location.reload();
                else alert('Delete failed.');
            });
        }

        function editClientName(id, currentName, element) {
            const newName = prompt("Enter Client Name (without .com):", currentName || "");
            if (newName === null) return;
            const trimmed = newName.trim();
            if (trimmed === currentName) return;

            fetch(`/your-hostinger-bill/${id}/client-name`, {
                method: 'PATCH',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    client_name: trimmed
                })
            }).then(r => r.json()).then(data => {
                if (data.success) {
                    if (trimmed === '') {
                        element.outerHTML = `<span class="text-gray-400 italic text-xs cursor-pointer hover:text-gray-600"
                            onclick="editClientName(${id}, '', this)">— (click to add)</span>`;
                    } else {
                        element.textContent = trimmed;
                    }
                } else {
                    alert('Failed to update: ' + (data.message || ''));
                }
            });
        }
    </script>
</body>

</html>
