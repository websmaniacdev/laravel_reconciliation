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

        .collapsible-content {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease-out;
        }

        .collapsible-content.open {
            max-height: 2000px;
            transition: max-height 0.5s ease-in;
        }
    </style>
</head>

<body class="bg-gray-100 min-h-screen" x-data="hostingerApp()">

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

        {{-- ── FILTER BAR (OLD) ── --}}
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
                        <label class="block text-xs font-medium text-gray-500 mb-1">Billed To / Client</label>
                        <input type="text" name="billed_to" value="{{ request('billed_to') }}"
                            placeholder="Name or company..."
                            class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-violet-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Description/Client</label>
                        <input type="text" name="description" value="{{ request('description') }}"
                            placeholder="WordPress, .IN Domain..."
                            class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-violet-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Type</label>
                        <select name="type"
                            class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-violet-500 outline-none">
                            <option value="">All Types</option>
                            <option value="Domain" {{ request('type') === 'Domain' ? 'selected' : '' }}>Domain</option>
                            <option value="Hosting" {{ request('type') === 'Hosting' ? 'selected' : '' }}>Hosting
                            </option>
                        </select>
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

        {{-- ══════════════════ OVERALL SUMMARY — INR + USD SPLIT (OLD) ══════════════════ --}}
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

            @if ($inrCount === 0 && $usdCount === 0)
                <div class="lg:col-span-2 bg-white rounded-xl shadow-sm px-5 py-4 border border-gray-100">
                    <p class="text-sm text-gray-400 text-center">No records yet. Upload Hostinger PDF invoices to see
                        totals here.</p>
                </div>
            @endif
        </div>

        {{-- NEW: Comparison Summary Card --}}
        @if ($matchedCount > 0)
            <div
                class="mb-5 bg-gradient-to-r from-green-50 to-emerald-50 rounded-xl shadow-sm px-5 py-4 border border-green-200">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-800">Hostinger vs Your Bills Comparison</h3>
                            <p class="text-xs text-gray-500">{{ $matchedCount }} client(s) have both Hostinger records
                                and Your Bills</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-xs text-gray-500">Total Your Bills Amount</p>
                        <p class="text-xl font-bold text-green-700">₹ {{ number_format($grandTotal, 2) }}</p>
                    </div>
                </div>
            </div>
        @endif

        {{-- ── PENDING PDFs (OLD) ── --}}
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
                    <a href="{{ route('hostinger.invoices.run') }}"
                        class="bg-violet-600 hover:bg-violet-700 text-white px-3 py-2 rounded-lg text-sm font-medium">
                        Run Hostinger Process
                    </a>
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

        {{-- ========== NEW: MONTH + CLIENT WISE COMPARISON SECTION ========== --}}
        @if (count($groupedData) > 0)
            <div class="mb-6">
                <div class="flex items-center justify-between mb-3">
                    <h2 class="text-base font-bold text-gray-800 flex items-center gap-2">
                        <svg class="w-5 h-5 text-violet-600" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                        </svg>
                        Month & Client Wise Comparison (Hostinger vs Your Bills)
                    </h2>
                    <span
                        class="text-xs text-gray-400">{{ collect($groupedData)->sum(function ($c) {return count($c);}) }}
                        clients across {{ count($groupedData) }} months</span>
                </div>
                <div class="space-y-4">
                    @foreach ($groupedData as $month => $clients)
                        <div class="bg-white rounded-xl shadow-sm overflow-hidden" x-data="{ monthOpen: true }">
                            {{-- Month Header --}}
                            <div class="px-5 py-3 bg-gradient-to-r from-violet-50 to-purple-50 border-b border-violet-100 flex items-center justify-between cursor-pointer hover:bg-violet-100 transition"
                                @click="monthOpen = !monthOpen">
                                <div class="flex items-center gap-3">
                                    <svg :class="{ 'rotate-90': monthOpen }"
                                        class="w-4 h-4 text-violet-500 transition-transform" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5l7 7-7 7" />
                                    </svg>
                                    <h3 class="text-lg font-bold text-gray-800">
                                        {{ \Carbon\Carbon::createFromFormat('Y-m', $month)->format('F Y') }}
                                    </h3>
                                    <span class="text-xs bg-violet-200 text-violet-700 px-2 py-0.5 rounded-full">
                                        {{ count($clients) }} client(s)
                                    </span>
                                </div>
                                <div class="flex items-center gap-4 text-xs">
                                    @php
                                        $monthHostingerTotal = collect($clients)->sum(function ($c) {
                                            return collect($c['hostinger'])->sum('line_total');
                                        });
                                        $monthYourBillTotal = collect($clients)->sum(function ($c) {
                                            return collect($c['your_bills'])->sum('total_amount');
                                        });
                                    @endphp
                                    <span class="text-violet-600">Hostinger: ₹
                                        {{ number_format($monthHostingerTotal, 2) }}</span>
                                    <span class="text-orange-600">Your Bills: ₹
                                        {{ number_format($monthYourBillTotal, 2) }}</span>
                                    @if (abs($monthHostingerTotal - $monthYourBillTotal) > 1)
                                        <span class="text-red-500 font-medium">⚠️ Diff: ₹
                                            {{ number_format(abs($monthHostingerTotal - $monthYourBillTotal), 2) }}</span>
                                    @endif
                                </div>
                            </div>

                            {{-- Clients List under Month --}}
                            <div x-show="monthOpen" x-collapse class="divide-y divide-gray-100">
                                @foreach ($clients as $clientName => $data)
                                    @php
                                        $hasHostinger = !empty($data['hostinger']);
                                        $hasYourBills = !empty($data['your_bills']);
                                        $isMatched = $hasHostinger && $hasYourBills;

                                        $hostingerTotal = collect($data['hostinger'])->sum('line_total');
                                        $yourBillTotal = collect($data['your_bills'])->sum('total_amount');
                                        $amountDiff = abs($hostingerTotal - $yourBillTotal);
                                        $isAmountMatch = $amountDiff <= 1;
                                    @endphp

                                    <div class="px-5 py-3 hover:bg-gray-50 transition" x-data="{ clientOpen: false }">
                                        {{-- Client Header --}}
                                        <div class="flex items-center justify-between cursor-pointer"
                                            @click="clientOpen = !clientOpen">
                                            <div class="flex items-center gap-3">
                                                <svg :class="{ 'rotate-90': clientOpen }"
                                                    class="w-3 h-3 text-gray-400 transition-transform" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M9 5l7 7-7 7" />
                                                </svg>
                                                <div>
                                                    <h4 class="font-semibold text-gray-800">{{ $clientName }}</h4>
                                                    <div class="flex items-center gap-2 mt-0.5">
                                                        @if ($isMatched)
                                                            <span
                                                                class="inline-flex items-center gap-1 text-xs bg-green-100 text-green-700 px-2 py-0.5 rounded-full">
                                                                <svg class="w-3 h-3" fill="currentColor"
                                                                    viewBox="0 0 20 20">
                                                                    <path fill-rule="evenodd"
                                                                        d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" />
                                                                </svg>
                                                                Matched
                                                            </span>
                                                            @if ($isAmountMatch)
                                                                <span
                                                                    class="text-xs bg-green-100 text-green-700 px-2 py-0.5 rounded-full">✅
                                                                    Amount matches</span>
                                                            @else
                                                                <span
                                                                    class="text-xs bg-red-100 text-red-700 px-2 py-0.5 rounded-full">⚠️
                                                                    Amount mismatch: ₹
                                                                    {{ number_format($amountDiff, 2) }}</span>
                                                            @endif
                                                        @elseif($hasHostinger && !$hasYourBills)
                                                            <span
                                                                class="text-xs bg-yellow-100 text-yellow-700 px-2 py-0.5 rounded-full">⚠️
                                                                Missing in Your Bills</span>
                                                        @elseif(!$hasHostinger && $hasYourBills)
                                                            <span
                                                                class="text-xs bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full">📋
                                                                Only in Your Bills</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="flex items-center gap-4 text-sm">
                                                <span class="text-violet-600">Hostinger: ₹
                                                    {{ number_format($hostingerTotal, 2) }}</span>
                                                <span class="text-orange-600">Your Bills: ₹
                                                    {{ number_format($yourBillTotal, 2) }}</span>
                                            </div>
                                        </div>

                                        {{-- Client Details (Collapsible) --}}
                                        <div x-show="clientOpen" x-collapse class="mt-3 pl-6">
                                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                                                {{-- Hostinger Records Table --}}
                                                <div class="bg-violet-50/30 rounded-lg overflow-hidden">
                                                    <div class="px-3 py-2 bg-violet-100 border-b border-violet-200">
                                                        <h5
                                                            class="text-sm font-semibold text-violet-800 flex items-center gap-2">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                                viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414A1 1 0 0119 9.414V19a2 2 0 01-2 2z" />
                                                            </svg>
                                                            Hostinger Records ({{ count($data['hostinger']) }})
                                                        </h5>
                                                    </div>
                                                    <div class="overflow-x-auto">
                                                        <table class="w-full text-xs">
                                                            <thead class="bg-violet-100/50">
                                                                <tr>
                                                                    <th class="px-3 py-2 text-left">Invoice #</th>
                                                                    <th class="px-3 py-2 text-left">Date</th>
                                                                    <th class="px-3 py-2 text-right">Amount</th>
                                                                    <th class="px-3 py-2 text-center">Curr</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody class="divide-y divide-violet-100">
                                                                @foreach ($data['hostinger'] as $record)
                                                                    <tr class="hover:bg-violet-50">
                                                                        <td class="px-3 py-2 font-mono">
                                                                            {{ $record->invoice_number ?? '—' }}</td>
                                                                        <td class="px-3 py-2 whitespace-nowrap">
                                                                            {{ $record->invoice_date?->format('d M Y') ?? '—' }}
                                                                        </td>
                                                                        <td class="px-3 py-2 text-right font-medium">
                                                                            {{ $record->currency === 'INR' ? '₹' : '$' }}
                                                                            {{ number_format($record->line_total, 2) }}
                                                                        </td>
                                                                        <td class="px-3 py-2 text-center">
                                                                            <span
                                                                                class="px-1.5 py-0.5 rounded text-xs {{ $record->currency === 'INR' ? 'bg-orange-100 text-orange-700' : 'bg-violet-100 text-violet-700' }}">
                                                                                {{ $record->currency }}
                                                                            </span>
                                                                        </td>
                                                                    </tr>
                                                                @endforeach
                                                            </tbody>
                                                            @if (count($data['hostinger']) > 1)
                                                                <tfoot>
                                                                    <tr class="bg-violet-100 font-bold">
                                                                        <td colspan="3"
                                                                            class="px-3 py-2 text-right">Total:</td>
                                                                        <td
                                                                            class="px-3 py-2 text-right text-violet-700">
                                                                            ₹ {{ number_format($hostingerTotal, 2) }}
                                                                        </td>
                                                                        <td></td>
                                                                    </tr>
                                                                </tfoot>
                                                            @endif
                                                        </table>
                                                    </div>
                                                </div>

                                                {{-- Your Bills Table --}}
                                                <div class="bg-orange-50/30 rounded-lg overflow-hidden">
                                                    <div class="px-3 py-2 bg-orange-100 border-b border-orange-200">
                                                        <h5
                                                            class="text-sm font-semibold text-orange-800 flex items-center gap-2">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                                viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                            </svg>
                                                            Your Hostinger Bills ({{ count($data['your_bills']) }})
                                                        </h5>
                                                    </div>
                                                    <div class="overflow-x-auto">
                                                        <table class="w-full text-xs">
                                                            <thead class="bg-orange-100/50">
                                                                <tr>
                                                                    <th class="px-3 py-2 text-left">Invoice #</th>
                                                                    <th class="px-3 py-2 text-left">Date</th>
                                                                    <th class="px-3 py-2 text-right">Before Tax</th>
                                                                    <th class="px-3 py-2 text-right">GST</th>
                                                                    <th class="px-3 py-2 text-right">Total</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody class="divide-y divide-orange-100">
                                                                @foreach ($data['your_bills'] as $bill)
                                                                    <tr class="hover:bg-orange-50">
                                                                        <td class="px-3 py-2 font-mono">
                                                                            {{ $bill->invoice_number ?? '—' }}</td>
                                                                        <td class="px-3 py-2 whitespace-nowrap">
                                                                            {{ $bill->invoice_date?->format('d M Y') ?? '—' }}
                                                                        </td>
                                                                        <td class="px-3 py-2 text-right">₹
                                                                            {{ number_format($bill->amount_before_tax, 2) }}
                                                                        </td>
                                                                        <td
                                                                            class="px-3 py-2 text-right text-orange-600">
                                                                            ₹
                                                                            {{ number_format($bill->sgst_amount + $bill->cgst_amount + $bill->igst_amount, 2) }}
                                                                        </td>
                                                                        <td
                                                                            class="px-3 py-2 text-right font-medium text-orange-700">
                                                                            ₹
                                                                            {{ number_format($bill->total_amount, 2) }}
                                                                        </td>
                                                                    </tr>
                                                                @endforeach
                                                            </tbody>
                                                            @if (count($data['your_bills']) > 1)
                                                                <tfoot>
                                                                    <tr class="bg-orange-100 font-bold">
                                                                        <td colspan="3"
                                                                            class="px-3 py-2 text-right">Total:</td>
                                                                        <td class="px-3 py-2 text-right">₹
                                                                            {{ number_format(collect($data['your_bills'])->sum('amount_before_tax'), 2) }}
                                                                        </td>
                                                                        <td
                                                                            class="px-3 py-2 text-right text-orange-600">
                                                                            ₹
                                                                            {{ number_format(collect($data['your_bills'])->sum('sgst_amount') + collect($data['your_bills'])->sum('cgst_amount') + collect($data['your_bills'])->sum('igst_amount'), 2) }}
                                                                        </td>
                                                                        <td
                                                                            class="px-3 py-2 text-right font-bold text-orange-700">
                                                                            ₹ {{ number_format($yourBillTotal, 2) }}
                                                                        </td>
                                                                    </tr>
                                                                </tfoot>
                                                            @endif
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- Comparison Summary for this client --}}
                                            @if ($isMatched)
                                                <div
                                                    class="mt-3 p-3 rounded-lg {{ $isAmountMatch ? 'bg-green-50 border border-green-200' : 'bg-red-50 border border-red-200' }}">
                                                    <div class="flex items-center justify-between text-sm">
                                                        <div class="flex items-center gap-2">
                                                            <span>{{ $isAmountMatch ? '✅' : '⚠️' }}</span>
                                                            <span
                                                                class="font-medium">{{ $isAmountMatch ? 'Amounts Match Perfectly' : 'Amount Mismatch Detected' }}</span>
                                                        </div>
                                                        <div class="flex gap-4">
                                                            <span>Hostinger Total: <strong class="text-violet-600">₹
                                                                    {{ number_format($hostingerTotal, 2) }}</strong></span>
                                                            <span>Your Bills Total: <strong class="text-orange-600">₹
                                                                    {{ number_format($yourBillTotal, 2) }}</strong></span>
                                                            @if (!$isAmountMatch)
                                                                <span class="text-red-600">Difference: ₹
                                                                    {{ number_format($amountDiff, 2) }}</span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- ══════════════════ OLD: LINE ITEMS TABLE ══════════════════ --}}
        <div class="bg-white rounded-xl shadow-sm overflow-hidden mt-6">
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
                                Client Name</th>
                            <th
                                class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Description</th>
                            <th
                                class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Type</th>
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
                                <td class="px-4 py-3 text-gray-600 text-xs whitespace-nowrap">
                                    @if ($record->client_name)
                                        <span
                                            class="inline-block px-2 py-1 bg-gray-100 rounded cursor-pointer hover:bg-amber-100 transition"
                                            onclick="editClientName({{ $record->id }}, '{{ $record->client_name }}', this)">
                                            {{ $record->client_name }}
                                        </span>
                                    @else
                                        <span class="text-gray-400 italic text-xs cursor-pointer hover:text-gray-600"
                                            onclick="editClientName({{ $record->id }}, '', this)">
                                            — (click to add)
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 max-w-xs">
                                    <p class="text-xs text-gray-700 truncate" title="{{ $record->description }}">
                                        {{ $record->description }}</p>
                                </td>
                                <td class="px-4 py-3 text-gray-600 text-xs whitespace-nowrap">
                                    {{ $record->type ?? '—' }}</td>
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
                                <td colspan="14" class="px-4 py-12 text-center text-gray-400">
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
                        {{ $records->links() }}
                    </div>
                </div>
            @endif
        </div>
    </main>

    {{-- UPLOAD MODAL --}}
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
                    <p class="text-xs text-gray-400 mt-1">Multiple PDFs supported (INR & USD) • Max 10MB each</p>
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

        function editClientName(recordId, currentName, element) {
            const newName = prompt("Enter Client Name:", currentName || "");
            if (newName === null) return;
            const trimmedName = newName.trim();
            if (trimmedName === currentName) return;

            fetch(`/hostinger-invoices/${recordId}/client-name`, {
                method: 'PATCH',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    client_name: trimmedName
                })
            }).then(r => r.json()).then(data => {
                if (data.success) {
                    if (trimmedName === '') {
                        element.outerHTML = `<span class="text-gray-400 italic text-xs cursor-pointer hover:text-gray-600"
                            onclick="editClientName(${recordId}, '', this)">— (click to add)</span>`;
                    } else {
                        element.textContent = trimmedName;
                        element.classList.remove('bg-amber-100');
                        element.classList.add('bg-gray-100');
                    }
                    alert('Client name updated successfully!');
                } else {
                    alert('Failed to update: ' + (data.message || ''));
                }
            }).catch(err => {
                alert('Error updating client name.');
            });
        }
    </script>
</body>

</html>
