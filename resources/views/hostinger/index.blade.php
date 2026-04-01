<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Hostinger Invoice Manager</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>
</head>

<body class="bg-gray-100 min-h-screen" x-data="hostingerApp()">

    {{-- ══════════════════ HEADER ══════════════════ --}}
    <header class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-4 py-4 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 bg-violet-600 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414A1 1 0 0119 9.414V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <div>
                    <h1 class="text-xl font-bold text-gray-800">Hostinger Invoice Manager</h1>
                    <p class="text-xs text-gray-400">Upload Hostinger PDF invoices to extract and track billing</p>
                </div>
            </div>
            <button @click="showUpload = true"
                class="bg-violet-600 hover:bg-violet-700 text-white px-4 py-2 rounded-lg text-sm font-medium flex items-center gap-2 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                </svg>
                Upload PDF(s)
            </button>
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

        {{-- ── FILTER BAR ── --}}
        <div class="bg-white rounded-xl shadow-sm p-5 mb-5">
            <form method="GET" action="{{ route('hostinger.invoices.index') }}">
                <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-6 gap-3 items-end">
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Invoice #</label>
                        <input type="text" name="invoice_number" value="{{ request('invoice_number') }}"
                            placeholder="HSG-..."
                            class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-violet-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Billed To</label>
                        <input type="text" name="billed_to" value="{{ request('billed_to') }}"
                            placeholder="Name or company..."
                            class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-violet-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Description</label>
                        <input type="text" name="description" value="{{ request('description') }}"
                            placeholder="WordPress, .IN Domain..."
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
                            class="flex-1 bg-violet-600 hover:bg-violet-700 text-white px-3 py-2 rounded-lg text-sm font-medium transition">
                            Filter
                        </button>
                        <a href="{{ route('hostinger.invoices.index') }}"
                            class="px-3 py-2 border border-gray-200 rounded-lg text-sm text-gray-600 hover:bg-gray-50 transition">
                            Clear
                        </a>
                        <a href="{{ route('hostinger.invoices.export', request()->only(['invoice_number', 'billed_to', 'description', 'from_date', 'to_date'])) }}"
                            class="px-3 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg text-sm font-medium transition">
                            Export
                        </a>
                    </div>
                </div>
            </form>
        </div>

        {{-- ══════════════════ OVERALL SUMMARY — INR + USD SPLIT ══════════════════ --}}
        <div class="mb-5 grid grid-cols-1 lg:grid-cols-2 gap-4">

            {{-- INR Summary Card --}}
            @if ($inrCount > 0)
                <div class="bg-white rounded-xl shadow-sm px-5 py-4 border border-orange-100">
                    <div class="flex items-center gap-2 mb-3">
                        <div class="w-2 h-2 rounded-full bg-orange-400"></div>
                        <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">INR Summary</span>
                        <span class="text-xs bg-orange-100 text-orange-700 px-2 py-0.5 rounded-full font-medium">
                            {{ number_format($inrCount) }} items
                        </span>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-xs text-gray-400 mb-0.5">Subtotal (excl. GST)</p>
                            <p class="text-base font-bold text-gray-800">₹ {{ number_format($inrSubtotal, 2) }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 mb-0.5">Discount</p>
                            <p class="text-base font-semibold text-green-600">
                                {{ $inrDiscount > 0 ? '- ₹ ' . number_format($inrDiscount, 2) : '—' }}
                            </p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 mb-0.5">GST Amount</p>
                            <p class="text-base font-semibold text-orange-600">₹ {{ number_format($inrGst, 2) }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 mb-0.5">Grand Total</p>
                            <p class="text-lg font-extrabold text-orange-700">₹ {{ number_format($inrGrandTotal, 2) }}
                            </p>
                        </div>
                    </div>
                </div>
            @endif

            {{-- USD Summary Card --}}
            @if ($usdCount > 0)
                <div class="bg-white rounded-xl shadow-sm px-5 py-4 border border-violet-100">
                    <div class="flex items-center gap-2 mb-3">
                        <div class="w-2 h-2 rounded-full bg-violet-500"></div>
                        <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">USD Summary</span>
                        <span class="text-xs bg-violet-100 text-violet-700 px-2 py-0.5 rounded-full font-medium">
                            {{ number_format($usdCount) }} items
                        </span>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-xs text-gray-400 mb-0.5">Subtotal (excl. GST)</p>
                            <p class="text-base font-bold text-gray-800">$ {{ number_format($usdSubtotal, 2) }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 mb-0.5">Discount</p>
                            <p class="text-base font-semibold text-green-600">
                                {{ $usdDiscount > 0 ? '- $ ' . number_format($usdDiscount, 2) : '—' }}
                            </p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 mb-0.5">GST Amount</p>
                            <p class="text-base font-semibold text-orange-600">$ {{ number_format($usdGst, 2) }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 mb-0.5">Grand Total</p>
                            <p class="text-lg font-extrabold text-violet-700">$ {{ number_format($usdGrandTotal, 2) }}
                            </p>
                        </div>
                    </div>
                </div>
            @endif

            {{-- If both are zero (no records yet) --}}
            @if ($inrCount === 0 && $usdCount === 0)
                <div class="lg:col-span-2 bg-white rounded-xl shadow-sm px-5 py-4 border border-gray-100">
                    <p class="text-sm text-gray-400 text-center">No records yet. Upload Hostinger PDF invoices to see
                        totals here.</p>
                </div>
            @endif
        </div>

        {{-- Active filters display --}}
        @if (request()->hasAny(['invoice_number', 'billed_to', 'description', 'from_date', 'to_date']))
            <div class="flex flex-wrap gap-2 mb-4">
                @foreach (['invoice_number' => 'Invoice #', 'billed_to' => 'Billed To', 'description' => 'Description'] as $key => $label)
                    @if (request($key))
                        <span
                            class="inline-flex items-center gap-1 bg-violet-50 border border-violet-200 text-violet-700 text-xs px-2.5 py-1 rounded-full">
                            {{ $label }}: <strong>{{ request($key) }}</strong>
                        </span>
                    @endif
                @endforeach
                @if (request('from_date'))
                    <span
                        class="inline-flex items-center gap-1 bg-purple-50 border border-purple-200 text-purple-700 text-xs px-2.5 py-1 rounded-full">
                        From: <strong>{{ \Carbon\Carbon::parse(request('from_date'))->format('d M Y') }}</strong>
                    </span>
                @endif
                @if (request('to_date'))
                    <span
                        class="inline-flex items-center gap-1 bg-purple-50 border border-purple-200 text-purple-700 text-xs px-2.5 py-1 rounded-full">
                        To: <strong>{{ \Carbon\Carbon::parse(request('to_date'))->format('d M Y') }}</strong>
                    </span>
                @endif
                <span
                    class="inline-flex items-center gap-1 bg-gray-100 text-gray-600 text-xs px-2.5 py-1 rounded-full">
                    {{ number_format($filteredCount) }} records
                </span>
            </div>
        @endif

        {{-- ── PENDING PDFs ── --}}
        @if ($pendingPdfs->isNotEmpty())
            <div class="bg-white rounded-xl shadow-sm overflow-hidden mb-5">
                <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                    <h2 class="text-sm font-semibold text-gray-700 flex items-center gap-2">
                        <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Pending / Failed PDFs
                    </h2>
                    <span class="text-xs text-gray-400">
                        Run: <code class="bg-gray-100 px-2 py-0.5 rounded font-mono">php artisan
                            hostinger:process-pending --sync</code>
                    </span>
                </div>
                <div class="divide-y divide-gray-50">
                    @foreach ($pendingPdfs as $pending)
                        <div class="px-5 py-3 flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                @if ($pending->status === 'pending')
                                    <span
                                        class="inline-flex items-center gap-1 bg-amber-100 text-amber-700 text-xs px-2 py-0.5 rounded-full font-medium">⏳
                                        Pending</span>
                                @elseif($pending->status === 'processing')
                                    <span
                                        class="inline-flex items-center gap-1 bg-blue-100 text-blue-700 text-xs px-2 py-0.5 rounded-full font-medium">⚙️
                                        Processing</span>
                                @elseif($pending->status === 'failed')
                                    <span
                                        class="inline-flex items-center gap-1 bg-red-100 text-red-700 text-xs px-2 py-0.5 rounded-full font-medium">❌
                                        Failed</span>
                                @endif
                                <div>
                                    <p class="text-sm text-gray-800 font-medium">{{ $pending->original_filename }}</p>
                                    @if ($pending->error_message)
                                        <p class="text-xs text-red-500 mt-0.5">{{ $pending->error_message }}</p>
                                    @endif
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                @if ($pending->status === 'failed')
                                    <button onclick="retryPending({{ $pending->id }})"
                                        class="text-xs bg-blue-100 text-blue-700 hover:bg-blue-200 px-3 py-1.5 rounded-lg font-medium transition">Retry</button>
                                @endif
                                <button onclick="deletePending({{ $pending->id }})"
                                    class="text-xs bg-red-50 text-red-500 hover:bg-red-100 px-3 py-1.5 rounded-lg font-medium transition">Remove</button>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- ── LINE ITEMS TABLE ── --}}
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                <h2 class="text-sm font-semibold text-gray-700">Invoice Line Items</h2>
                <span class="text-xs text-gray-400">{{ $records->total() }} total records</span>
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
                                Invoice #</th>
                            <th
                                class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Date</th>
                            <th
                                class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Billed To</th>
                            <th
                                class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Description</th>
                            <th
                                class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Billing Period</th>
                            <th
                                class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Unit Price</th>
                            <th
                                class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Discount</th>
                            <th
                                class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Excl. GST</th>
                            <th
                                class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                GST</th>
                            <th
                                class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Total</th>
                            <th
                                class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Curr.</th>
                            <th
                                class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($records as $index => $record)
                            @php
                                $sym = $record->currency === 'INR' ? '₹' : '$';
                                $currClass =
                                    $record->currency === 'INR'
                                        ? 'bg-orange-50 text-orange-700'
                                        : 'bg-violet-50 text-violet-700';
                            @endphp
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-4 py-3 text-gray-400 text-xs">{{ $records->firstItem() + $index }}</td>
                                <td class="px-4 py-3">
                                    <span class="font-mono text-xs {{ $currClass }} px-2 py-0.5 rounded">
                                        {{ $record->invoice_number ?? '—' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-gray-600 text-xs whitespace-nowrap">
                                    {{ $record->invoice_date?->format('d M Y') ?? '—' }}
                                </td>
                                <td class="px-4 py-3">
                                    <p class="font-medium text-gray-800 text-xs">{{ $record->billed_to_name ?? '—' }}
                                    </p>
                                    @if ($record->billed_to_company)
                                        <p class="text-gray-400 text-xs">{{ $record->billed_to_company }}</p>
                                    @endif
                                </td>
                                <td class="px-4 py-3 max-w-xs">
                                    <p class="text-xs text-gray-700 truncate" title="{{ $record->description }}">
                                        {{ $record->description }}</p>
                                </td>
                                <td class="px-4 py-3 text-gray-400 text-xs whitespace-nowrap">
                                    {{ $record->billing_period ?? '—' }}
                                </td>
                                <td class="px-4 py-3 text-right text-gray-700 text-xs whitespace-nowrap">
                                    {{ $sym }} {{ number_format($record->unit_price, 2) }}
                                </td>
                                <td class="px-4 py-3 text-right text-green-600 text-xs whitespace-nowrap">
                                    @if ($record->discount > 0)
                                        - {{ $sym }} {{ number_format($record->discount, 2) }}
                                    @else
                                        —
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-right text-gray-700 text-xs whitespace-nowrap">
                                    {{ $sym }} {{ number_format($record->total_excl_gst, 2) }}
                                </td>
                                <td class="px-4 py-3 text-right text-orange-600 text-xs whitespace-nowrap">
                                    {{ $record->gst_amount > 0 ? $sym . ' ' . number_format($record->gst_amount, 2) : '—' }}
                                </td>
                                <td class="px-4 py-3 text-right font-semibold text-gray-800 whitespace-nowrap">
                                    {{ $sym }} {{ number_format($record->line_total, 2) }}
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span
                                        class="text-xs font-medium {{ $currClass }} px-1.5 py-0.5 rounded">{{ $record->currency }}</span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <button onclick="deleteRecord({{ $record->id }})"
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
                                <td colspan="13" class="px-4 py-12 text-center text-gray-400">
                                    <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414A1 1 0 0119 9.414V19a2 2 0 01-2 2z" />
                                    </svg>
                                    <p class="font-medium">No records found</p>
                                    <p class="text-sm mt-1">Upload some Hostinger PDF invoices to get started.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- PAGINATION --}}
            @if ($records->hasPages())
                <div class="border-t border-gray-100 px-4 py-3 flex items-center justify-between">
                    <p class="text-sm text-gray-500">
                        Showing {{ $records->firstItem() }}–{{ $records->lastItem() }} of {{ $records->total() }}
                        records
                    </p>
                    <div class="flex items-center gap-1">
                        @if ($records->onFirstPage())
                            <span class="px-3 py-1.5 text-sm text-gray-300 border border-gray-100 rounded-lg">←
                                Prev</span>
                        @else
                            <a href="{{ $records->previousPageUrl() }}"
                                class="px-3 py-1.5 text-sm text-gray-600 border border-gray-200 rounded-lg hover:bg-gray-50 transition">←
                                Prev</a>
                        @endif
                        @foreach ($records->getUrlRange(max(1, $records->currentPage() - 2), min($records->lastPage(), $records->currentPage() + 2)) as $page => $url)
                            @if ($page == $records->currentPage())
                                <span
                                    class="px-3 py-1.5 text-sm bg-violet-600 text-white border border-violet-600 rounded-lg">{{ $page }}</span>
                            @else
                                <a href="{{ $url }}"
                                    class="px-3 py-1.5 text-sm text-gray-600 border border-gray-200 rounded-lg hover:bg-gray-50 transition">{{ $page }}</a>
                            @endif
                        @endforeach
                        @if ($records->hasMorePages())
                            <a href="{{ $records->nextPageUrl() }}"
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

        {{-- ══════════════════ PDF-WISE SUMMARY TABLE ══════════════════ --}}
        {{-- @if ($summaries->isNotEmpty())
            <div class="bg-white rounded-xl shadow-sm overflow-hidden mt-6">
                <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                    <h2 class="text-sm font-semibold text-gray-700 flex items-center gap-2">
                        <svg class="w-4 h-4 text-violet-500" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V7a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414A1 1 0 0119 9.414V19a2 2 0 01-2 2z" />
                        </svg>
                        PDF-wise Invoice Summary
                    </h2>
                    <span class="text-xs text-gray-400">{{ $summaries->count() }} PDF(s)</span>
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
                                    PDF File</th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                    Invoice #</th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                    Invoice Date</th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                    Next Billing</th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                    Billed To</th>
                                <th
                                    class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                    Subtotal</th>
                                <th
                                    class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                    Discount</th>
                                <th
                                    class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                    GST</th>
                                <th
                                    class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                    Grand Total</th>
                                <th
                                    class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                    Amount Due</th>
                                <th
                                    class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                    Items</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @foreach ($summaries as $i => $summary)
                                @php
                                    $sym = $summary->currency === 'INR' ? '₹' : '$';
                                    $currBg = $summary->currency === 'INR' ? 'bg-orange-50' : '';
                                @endphp
                                <tr class="hover:bg-gray-50 transition {{ $currBg }}">
                                    <td class="px-4 py-3 text-gray-400 text-xs">{{ $i + 1 }}</td>
                                    <td class="px-4 py-3 text-xs font-mono text-gray-600 max-w-xs truncate"
                                        title="{{ $summary->pdf_filename }}">
                                        {{ $summary->pdf_filename }}
                                    </td>
                                    <td class="px-4 py-3">
                                        @php $cClass = $summary->currency === 'INR' ? 'bg-orange-100 text-orange-700' : 'bg-violet-50 text-violet-700'; @endphp
                                        <span class="font-mono text-xs {{ $cClass }} px-2 py-0.5 rounded">
                                            {{ $summary->invoice_number ?? '—' }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-gray-600 text-xs whitespace-nowrap">
                                        {{ $summary->invoice_date?->format('d M Y') ?? '—' }}
                                    </td>
                                    <td class="px-4 py-3 text-gray-600 text-xs whitespace-nowrap">
                                        {{ $summary->next_billing_date?->format('d M Y') ?? '—' }}
                                    </td>
                                    <td class="px-4 py-3">
                                        <p class="text-xs font-medium text-gray-800">
                                            {{ $summary->billed_to_name ?? '—' }}</p>
                                        @if ($summary->billed_to_company)
                                            <p class="text-xs text-gray-400">{{ $summary->billed_to_company }}</p>
                                        @endif
                                        @if ($summary->billed_to_gstin)
                                            <p class="text-xs text-gray-400 font-mono">{{ $summary->billed_to_gstin }}
                                            </p>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-right text-gray-700 text-xs whitespace-nowrap">
                                        {{ $sym }} {{ number_format($summary->subtotal, 2) }}
                                    </td>
                                    <td class="px-4 py-3 text-right text-green-600 text-xs whitespace-nowrap">
                                        {{ $summary->total_discount > 0 ? '- ' . $sym . ' ' . number_format($summary->total_discount, 2) : '—' }}
                                    </td>
                                    <td class="px-4 py-3 text-right text-orange-600 text-xs whitespace-nowrap">
                                        {{ $summary->gst_amount > 0 ? $sym . ' ' . number_format($summary->gst_amount, 2) : '—' }}
                                    </td>
                                    <td
                                        class="px-4 py-3 text-right font-bold whitespace-nowrap
                                        {{ $summary->currency === 'INR' ? 'text-orange-700' : 'text-violet-700' }}">
                                        {{ $sym }} {{ number_format($summary->grand_total, 2) }}
                                    </td>
                                    <td class="px-4 py-3 text-right text-xs whitespace-nowrap">
                                        @if ($summary->amount_due > 0)
                                            <span class="text-red-600 font-semibold">{{ $sym }}
                                                {{ number_format($summary->amount_due, 2) }}</span>
                                        @else
                                            <span class="text-green-600 font-medium">PAID</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-center text-gray-500 text-xs">
                                        {{ $summary->total_records }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        @php
                            $inrSummaries = $summaries->where('currency', 'INR');
                            $usdSummaries = $summaries->where('currency', '!=', 'INR');
                        @endphp
                        @if ($inrSummaries->isNotEmpty())
                            <tfoot>
                                <tr class="bg-orange-50 font-bold border-t-2 border-orange-200">
                                    <td colspan="6"
                                        class="px-4 py-3 text-right text-orange-800 text-xs uppercase tracking-wider">
                                        INR Total ({{ $inrSummaries->count() }} PDFs)
                                    </td>
                                    <td class="px-4 py-3 text-right text-orange-800 text-xs whitespace-nowrap">₹
                                        {{ number_format($inrSummaries->sum('subtotal'), 2) }}</td>
                                    <td class="px-4 py-3 text-right text-green-700 text-xs whitespace-nowrap">
                                        {{ $inrSummaries->sum('total_discount') > 0 ? '- ₹ ' . number_format($inrSummaries->sum('total_discount'), 2) : '—' }}
                                    </td>
                                    <td class="px-4 py-3 text-right text-orange-600 text-xs whitespace-nowrap">₹
                                        {{ number_format($inrSummaries->sum('gst_amount'), 2) }}</td>
                                    <td class="px-4 py-3 text-right text-orange-700 text-base whitespace-nowrap">₹
                                        {{ number_format($inrSummaries->sum('grand_total'), 2) }}</td>
                                    <td class="px-4 py-3 text-right text-xs whitespace-nowrap text-gray-600">₹
                                        {{ number_format($inrSummaries->sum('amount_due'), 2) }}</td>
                                    <td class="px-4 py-3 text-center text-gray-600 text-xs">
                                        {{ $inrSummaries->sum('total_records') }}</td>
                                </tr>
                            </tfoot>
                        @endif
                        @if ($usdSummaries->isNotEmpty())
                            <tfoot>
                                <tr class="bg-violet-50 font-bold border-t-2 border-violet-200">
                                    <td colspan="6"
                                        class="px-4 py-3 text-right text-violet-800 text-xs uppercase tracking-wider">
                                        USD Total ({{ $usdSummaries->count() }} PDFs)
                                    </td>
                                    <td class="px-4 py-3 text-right text-violet-800 text-xs whitespace-nowrap">$
                                        {{ number_format($usdSummaries->sum('subtotal'), 2) }}</td>
                                    <td class="px-4 py-3 text-right text-green-700 text-xs whitespace-nowrap">
                                        {{ $usdSummaries->sum('total_discount') > 0 ? '- $ ' . number_format($usdSummaries->sum('total_discount'), 2) : '—' }}
                                    </td>
                                    <td class="px-4 py-3 text-right text-orange-600 text-xs whitespace-nowrap">$
                                        {{ number_format($usdSummaries->sum('gst_amount'), 2) }}</td>
                                    <td class="px-4 py-3 text-right text-violet-700 text-base whitespace-nowrap">$
                                        {{ number_format($usdSummaries->sum('grand_total'), 2) }}</td>
                                    <td class="px-4 py-3 text-right text-xs whitespace-nowrap text-gray-600">$
                                        {{ number_format($usdSummaries->sum('amount_due'), 2) }}</td>
                                    <td class="px-4 py-3 text-center text-gray-600 text-xs">
                                        {{ $usdSummaries->sum('total_records') }}</td>
                                </tr>
                            </tfoot>
                        @endif
                    </table>
                </div>
            </div>
        @endif --}}

    </main>

    {{-- ══════════════════ UPLOAD MODAL ══════════════════ --}}
    <div x-show="showUpload" x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm"
        @click.self="showUpload = false">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg mx-4 p-6" @click.stop>
            <div class="flex items-center justify-between mb-5">
                <h2 class="text-lg font-semibold text-gray-800">Upload Hostinger Invoice PDF(s)</h2>
                <button @click="showUpload = false" class="text-gray-400 hover:text-gray-600 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <form method="POST" action="{{ route('hostinger.invoices.upload') }}" enctype="multipart/form-data">
                @csrf
                <div class="border-2 border-dashed border-gray-200 rounded-xl p-8 text-center hover:border-violet-400 transition cursor-pointer"
                    @click="$refs.fileInput.click()" @dragover.prevent @drop.prevent="handleDrop($event)">
                    <svg class="w-10 h-10 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                    </svg>
                    <p class="text-sm text-gray-500">Click to select Hostinger PDF(s) or drag & drop</p>
                    <p class="text-xs text-gray-400 mt-1">Multiple PDFs supported (INR &amp; USD) • Max 10MB each</p>
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
                        📋 PDFs will be queued for processing. After upload, run:<br>
                        <code class="font-mono bg-violet-100 px-1 py-0.5 rounded mt-1 inline-block">php artisan
                            hostinger:process-pending --sync</code>
                    </p>
                </div>
                <div class="flex gap-3 mt-5">
                    <button type="button" @click="showUpload = false"
                        class="flex-1 px-4 py-2.5 border border-gray-200 rounded-xl text-sm text-gray-600 hover:bg-gray-50 transition">Cancel</button>
                    <button type="submit" :disabled="uploadFiles.length === 0"
                        class="flex-1 px-4 py-2.5 bg-violet-600 hover:bg-violet-700 disabled:bg-violet-300 text-white rounded-xl text-sm font-medium transition">
                        Queue PDF(s) for Import
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function hostingerApp() {
            return {
                showUpload: false,
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

        function deleteRecord(id) {
            if (!confirm('Delete this record?')) return;
            fetch(`/hostinger-invoices/${id}`, {
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

        function retryPending(id) {
            fetch(`/hostinger-invoices/pending/${id}/retry`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                }
            }).then(r => r.json()).then(d => {
                if (d.success) location.reload();
                else alert('Retry failed: ' + (d.message || ''));
            });
        }

        function deletePending(id) {
            if (!confirm('Remove this pending PDF entry?')) return;
            fetch(`/hostinger-invoices/pending/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                }
            }).then(r => r.json()).then(d => {
                if (d.success) location.reload();
                else alert('Remove failed.');
            });
        }
    </script>
</body>

</html>
