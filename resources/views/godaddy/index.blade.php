<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>GoDaddy Receipts</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        [x-cloak] {
            display: none !important;
        }

        [x-collapse] {
            overflow: hidden;
        }

        [x-collapse][style*="height: 0"] {
            display: none;
        }
    </style>
</head>

<body class="bg-gray-100 min-h-screen" x-data="godaddyApp()">

    {{-- ══════════════════ HEADER ══════════════════ --}}
    <header class="bg-white shadow-sm border-b">
        <div class="flex items-center justify-between h-16 max-w-7xl mx-auto px-4">
            @include('layouts.nav')
        </div>
        <div class="max-w-7xl mx-auto px-4 py-4 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 bg-green-600 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064" />
                    </svg>
                </div>
                <div>
                    <h1 class="text-xl font-bold text-gray-800">GoDaddy Receipts</h1>
                    <p class="text-xs text-gray-400">Domain Renewals, Registrations & Transfers</p>
                </div>
            </div>
            <div class="flex gap-2">
                {{-- Upload GoDaddy Export --}}
                <button @click="showUpload = true"
                    class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium flex items-center gap-2 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                    </svg>
                    Upload GoDaddy Export
                </button>
                {{-- Upload Your GoDaddy Bill --}}
                <button @click="showYourBillUpload = true"
                    class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg text-sm font-medium flex items-center gap-2 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414A1 1 0 0119 9.414V19a2 2 0 01-2 2z" />
                    </svg>
                    Upload Your Bill PDF
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
                <p class="font-medium">Some files had errors:</p>
                @foreach (session('upload_errors') as $err)
                    <p class="text-sm mt-1">• {{ $err }}</p>
                @endforeach
            </div>
        </div>
    @endif

    {{-- ══════════════════ MAIN ══════════════════ --}}
    <main class="max-w-7xl mx-auto px-4 py-6">

        {{-- ── FILTER BAR ── --}}
        <div class="bg-white rounded-xl shadow-sm p-5 mb-5">
            <form method="GET" action="{{ route('godaddy.index') }}">
                <div class="grid grid-cols-1 md:grid-cols-6 gap-3 items-end">
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Domain Name</label>
                        <input type="text" name="domain_name" value="{{ request('domain_name') }}"
                            placeholder="e.g. example.com"
                            class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Product</label>
                        <input type="text" name="product_name" value="{{ request('product_name') }}"
                            placeholder="e.g. .COM Renewal"
                            class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">From Date</label>
                        <input type="date" name="from_date" value="{{ request('from_date') }}" x-ref="fromDate"
                            @change="
                                const d = new Date($event.target.value);
                                if (!isNaN(d)) {
                                    const last = new Date(d.getFullYear(), d.getMonth() + 1, 0);
                                    $refs.toDate.value = last.toISOString().split('T')[0];
                                }
                            "
                            class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">To Date</label>
                        <input type="date" name="to_date" value="{{ request('to_date') }}" x-ref="toDate"
                            class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Payment</label>
                        <select name="payment_category"
                            class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-500 outline-none">
                            <option value="">All</option>
                            @foreach ($paymentCategories as $cat)
                                <option value="{{ $cat }}"
                                    {{ request('payment_category') == $cat ? 'selected' : '' }}>
                                    {{ $cat }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex gap-2">
                        <button type="submit"
                            class="flex-1 bg-green-600 hover:bg-green-700 text-white px-3 py-2 rounded-lg text-sm font-medium transition">
                            Filter
                        </button>
                        <a href="{{ route('godaddy.index') }}"
                            class="px-3 py-2 border border-gray-200 rounded-lg text-sm text-gray-600 hover:bg-gray-50 transition">
                            Clear
                        </a>
                        <a href="{{ route('godaddy.export', request()->only(['domain_name', 'product_name', 'from_date', 'to_date', 'payment_category'])) }}"
                            class="px-3 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-sm font-medium transition">
                            Export
                        </a>
                    </div>
                </div>
            </form>
        </div>

        {{-- ── SUMMARY BAR ── --}}
        <div class="bg-white rounded-xl shadow-sm px-5 py-4 mb-5 border border-gray-100">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div class="flex items-center gap-2">
                    <div class="w-2 h-2 rounded-full bg-green-500"></div>
                    <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">
                        @if (request()->hasAny(['domain_name', 'product_name', 'from_date', 'to_date', 'payment_category']))
                            Filtered Summary
                        @else
                            Overall Summary
                        @endif
                    </span>
                    <span class="text-xs bg-green-100 text-green-700 px-2 py-0.5 rounded-full font-medium">
                        {{ number_format($filteredCount) }} records
                    </span>
                </div>
                <div class="flex flex-wrap items-center gap-6">
                    <div class="text-center">
                        <p class="text-xs text-gray-400 mb-0.5">Subtotal</p>
                        <p class="text-base font-bold text-gray-800">₹ {{ number_format($filteredSubtotal, 2) }}</p>
                    </div>
                    <div class="w-px h-8 bg-gray-200 hidden sm:block"></div>
                    <div class="text-center">
                        <p class="text-xs text-gray-400 mb-0.5">ICANN Fees</p>
                        <p class="text-base font-semibold text-purple-600">₹ {{ number_format($filteredIcann, 2) }}
                        </p>
                    </div>
                    <div class="w-px h-8 bg-gray-200 hidden sm:block"></div>
                    <div class="text-center">
                        <p class="text-xs text-gray-400 mb-0.5">Tax</p>
                        <p class="text-base font-semibold text-orange-600">₹ {{ number_format($filteredTax, 2) }}</p>
                    </div>
                    <div class="w-px h-8 bg-gray-200 hidden sm:block"></div>
                    <div class="text-center">
                        <p class="text-xs text-gray-400 mb-0.5">GoDaddy Total Paid</p>
                        <p class="text-lg font-extrabold text-green-700">₹ {{ number_format($filteredOrderTotal, 2) }}
                        </p>
                    </div>
                    @if (isset($yourBillsTotal) && $yourBillsTotal > 0)
                        <div class="w-px h-8 bg-gray-200 hidden sm:block"></div>
                        <div class="text-center">
                            <p class="text-xs text-gray-400 mb-0.5">Billed to Clients</p>
                            <p class="text-lg font-extrabold text-purple-700">₹
                                {{ number_format($yourBillsTotal, 2) }}</p>
                        </div>
                        <div class="w-px h-8 bg-gray-200 hidden sm:block"></div>
                        <div class="text-center">
                            <p class="text-xs text-gray-400 mb-0.5">Net Profit</p>
                            @php $netProfit = ($yourBillsTotal ?? 0) - $filteredOrderTotal; @endphp
                            <p
                                class="text-lg font-extrabold {{ $netProfit >= 0 ? 'text-emerald-600' : 'text-red-600' }}">
                                {{ $netProfit >= 0 ? '+' : '' }}₹ {{ number_format($netProfit, 2) }}
                            </p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Active filters --}}
            @if (request()->hasAny(['domain_name', 'product_name', 'from_date', 'to_date', 'payment_category']))
                <div class="mt-3 flex flex-wrap gap-2 pt-3 border-t border-gray-100">
                    @if (request('domain_name'))
                        <span
                            class="inline-flex items-center gap-1 bg-green-50 border border-green-200 text-green-700 text-xs px-2.5 py-1 rounded-full">
                            Domain: <strong>{{ request('domain_name') }}</strong>
                        </span>
                    @endif
                    @if (request('product_name'))
                        <span
                            class="inline-flex items-center gap-1 bg-blue-50 border border-blue-200 text-blue-700 text-xs px-2.5 py-1 rounded-full">
                            Product: <strong>{{ request('product_name') }}</strong>
                        </span>
                    @endif
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
                    @if (request('payment_category'))
                        <span
                            class="inline-flex items-center gap-1 bg-amber-50 border border-amber-200 text-amber-700 text-xs px-2.5 py-1 rounded-full">
                            Payment: <strong>{{ request('payment_category') }}</strong>
                        </span>
                    @endif
                </div>
            @endif
        </div>

        {{-- ── COMPARISON SUMMARY CARD ── --}}
        @if (isset($matchedCount) && $matchedCount > 0)
            <div
                class="mb-5 bg-gradient-to-r from-purple-50 to-indigo-50 rounded-xl shadow-sm px-5 py-4 border border-purple-200">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-800">GoDaddy Cost vs Your Client Billing — Comparison
                            </h3>
                            <p class="text-xs text-gray-500">{{ $matchedCount }} domain(s) matched in both GoDaddy
                                receipts and Your Bills</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-6 text-right">
                        <div>
                            <p class="text-xs text-gray-500">Total Billed to Clients</p>
                            <p class="text-xl font-bold text-purple-700">₹
                                {{ number_format($yourBillsTotal ?? 0, 2) }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">GoDaddy Cost</p>
                            <p class="text-xl font-bold text-red-600">₹ {{ number_format($filteredOrderTotal, 2) }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- ── PENDING FILES ── --}}
        @if ($pendingFiles->isNotEmpty())
            <div class="bg-white rounded-xl shadow-sm overflow-hidden mb-5">
                <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                    <h2 class="text-sm font-semibold text-gray-700 flex items-center gap-2">
                        <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Pending / Failed Files
                    </h2>
                    <a href="{{ route('godaddy.run') }}"
                        class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition">
                        Run GoDaddy Process
                    </a>
                </div>
                <div class="divide-y divide-gray-50">
                    @foreach ($pendingFiles as $pending)
                        <div class="px-5 py-3 flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                @if ($pending->status === 'pending')
                                    <span
                                        class="inline-flex items-center gap-1 bg-amber-100 text-amber-700 text-xs px-2 py-0.5 rounded-full font-medium">⏳
                                        Pending</span>
                                @elseif ($pending->status === 'processing')
                                    <span
                                        class="inline-flex items-center gap-1 bg-blue-100 text-blue-700 text-xs px-2 py-0.5 rounded-full font-medium">⚙️
                                        Processing</span>
                                @elseif ($pending->status === 'failed')
                                    <span
                                        class="inline-flex items-center gap-1 bg-red-100 text-red-700 text-xs px-2 py-0.5 rounded-full font-medium">❌
                                        Failed</span>
                                @endif
                                <div>
                                    <p class="text-sm text-gray-800 font-medium">
                                        {{ $pending->original_filename }}
                                        <span
                                            class="text-xs text-gray-400 font-normal ml-1 uppercase">{{ $pending->file_type }}</span>
                                    </p>
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

        {{-- ══════════════════ DOMAIN + MONTH WISE COMPARISON ══════════════════ --}}
        @if (isset($groupedData) && count($groupedData) > 0)
            <div class="mb-6">
                <div class="flex items-center justify-between mb-3">
                    <h2 class="text-base font-bold text-gray-800 flex items-center gap-2">
                        <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064" />
                        </svg>
                        Domain & Month Wise Comparison
                        <span class="text-xs font-normal text-gray-400 ml-1">(GoDaddy Cost vs Your Client
                            Billing)</span>
                    </h2>
                    <span class="text-xs text-gray-400">
                        {{ collect($groupedData)->sum(fn($d) => count($d)) }} domains across {{ count($groupedData) }}
                        months
                    </span>
                </div>

                <div class="space-y-4">
                    @foreach ($groupedData as $month => $domains)
                        <div class="bg-white rounded-xl shadow-sm overflow-hidden" x-data="{ monthOpen: true }">

                            {{-- Month Header --}}
                            <div class="px-5 py-3 bg-gradient-to-r from-purple-50 to-indigo-50 border-b border-purple-100 flex items-center justify-between cursor-pointer hover:from-purple-100 hover:to-indigo-100 transition"
                                @click="monthOpen = !monthOpen">
                                <div class="flex items-center gap-3">
                                    <svg :class="{ 'rotate-90': monthOpen }"
                                        class="w-4 h-4 text-purple-500 transition-transform duration-200"
                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5l7 7-7 7" />
                                    </svg>
                                    <h3 class="text-lg font-bold text-gray-800">
                                        @if ($month === '0000-00')
                                            Unknown Date
                                        @else
                                            {{ \Carbon\Carbon::createFromFormat('Y-m', $month)->format('F Y') }}
                                        @endif
                                    </h3>
                                    <span
                                        class="text-xs bg-purple-200 text-purple-700 px-2 py-0.5 rounded-full font-medium">
                                        {{ count($domains) }} domain(s)
                                    </span>
                                </div>

                                @php
                                    $monthCostTotal = collect($domains)->sum(
                                        fn($d) => collect($d['receipts'])->sum('order_total'),
                                    );
                                    $monthBilledTotal = collect($domains)->sum(
                                        fn($d) => collect($d['your_bills'])->sum('total_amount'),
                                    );
                                    $monthProfit = $monthBilledTotal - $monthCostTotal;
                                @endphp
                                <div class="flex items-center gap-4 text-xs flex-wrap justify-end">
                                    <span class="text-red-500 font-medium">GoDaddy: ₹
                                        {{ number_format($monthCostTotal, 2) }}</span>
                                    <span class="text-purple-600 font-medium">Billed: ₹
                                        {{ number_format($monthBilledTotal, 2) }}</span>
                                    @if ($monthBilledTotal > 0)
                                        @if ($monthProfit >= 0)
                                            <span
                                                class="text-green-600 font-bold bg-green-100 px-2 py-0.5 rounded-full">
                                                +₹ {{ number_format($monthProfit, 2) }} profit
                                            </span>
                                        @else
                                            <span class="text-red-600 font-bold bg-red-100 px-2 py-0.5 rounded-full">
                                                ⚠️ -₹ {{ number_format(abs($monthProfit), 2) }} loss
                                            </span>
                                        @endif
                                    @endif
                                </div>
                            </div>

                            {{-- Domains under this month --}}
                            <div x-show="monthOpen" class="divide-y divide-gray-100">
                                @foreach ($domains as $domainKey => $data)
                                    @php
                                        $hasReceipts = !empty($data['receipts']);
                                        $hasYourBills = !empty($data['your_bills']);
                                        $isMatched = $hasReceipts && $hasYourBills;

                                        $costTotal = collect($data['receipts'])->sum('order_total');
                                        $billedTotal = collect($data['your_bills'])->sum('total_amount');
                                        $profit = $billedTotal - $costTotal;
                                        $isProfit = $profit >= 0;

                                        $profitPct = $costTotal > 0 ? round(($profit / $costTotal) * 100, 1) : 0;
                                    @endphp

                                    <div class="px-5 py-3 hover:bg-gray-50 transition" x-data="{ domainOpen: false }">

                                        {{-- Domain row header --}}
                                        <div class="flex items-center justify-between cursor-pointer"
                                            @click="domainOpen = !domainOpen">
                                            <div class="flex items-center gap-3">
                                                <svg :class="{ 'rotate-90': domainOpen }"
                                                    class="w-3 h-3 text-gray-400 transition-transform duration-200"
                                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M9 5l7 7-7 7" />
                                                </svg>
                                                <div>
                                                    <h4 class="font-semibold text-gray-800 font-mono text-sm">
                                                        {{ $domainKey }}</h4>
                                                    <div class="flex flex-wrap items-center gap-2 mt-0.5">
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
                                                            @if ($isProfit)
                                                                <span
                                                                    class="text-xs bg-emerald-100 text-emerald-700 px-2 py-0.5 rounded-full font-medium">
                                                                    📈 +{{ $profitPct }}% margin
                                                                </span>
                                                            @else
                                                                <span
                                                                    class="text-xs bg-red-100 text-red-700 px-2 py-0.5 rounded-full font-medium">
                                                                    ⚠️ {{ $profitPct }}% loss
                                                                </span>
                                                            @endif
                                                        @elseif ($hasReceipts && !$hasYourBills)
                                                            <span
                                                                class="text-xs bg-yellow-100 text-yellow-700 px-2 py-0.5 rounded-full">
                                                                ⚠️ Not billed to client yet
                                                            </span>
                                                        @elseif (!$hasReceipts && $hasYourBills)
                                                            <span
                                                                class="text-xs bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full">
                                                                📋 Only in Your Bills
                                                            </span>
                                                        @endif

                                                        {{-- Client name from your bills --}}
                                                        @if ($hasYourBills && !empty($data['your_bills'][0]->client_name))
                                                            <span class="text-xs text-gray-400">
                                                                Client: <span
                                                                    class="font-medium text-gray-600">{{ $data['your_bills'][0]->client_name }}</span>
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="flex items-center gap-6 text-sm flex-shrink-0">
                                                @if ($hasReceipts)
                                                    <div class="text-right">
                                                        <p class="text-xs text-gray-400">GoDaddy Cost</p>
                                                        <p class="font-semibold text-red-600">₹
                                                            {{ number_format($costTotal, 2) }}</p>
                                                    </div>
                                                @endif
                                                @if ($hasYourBills)
                                                    <div class="text-right">
                                                        <p class="text-xs text-gray-400">Billed to Client</p>
                                                        <p class="font-semibold text-purple-600">₹
                                                            {{ number_format($billedTotal, 2) }}</p>
                                                    </div>
                                                @endif
                                                @if ($isMatched)
                                                    <div class="text-right">
                                                        <p class="text-xs text-gray-400">Profit / Loss</p>
                                                        <p
                                                            class="font-bold {{ $isProfit ? 'text-emerald-600' : 'text-red-600' }}">
                                                            {{ $isProfit ? '+' : '' }}₹
                                                            {{ number_format($profit, 2) }}
                                                        </p>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>

                                        {{-- Domain detail (collapsible) --}}
                                        <div x-show="domainOpen" class="mt-3 pl-6">
                                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">

                                                {{-- GoDaddy Receipts (What you PAID) --}}
                                                <div
                                                    class="bg-red-50/40 rounded-lg overflow-hidden border border-red-100">
                                                    <div
                                                        class="px-3 py-2 bg-red-100 border-b border-red-200 flex items-center gap-2">
                                                        <svg class="w-4 h-4 text-red-600" fill="none"
                                                            stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064" />
                                                        </svg>
                                                        <h5 class="text-sm font-semibold text-red-800">
                                                            GoDaddy Receipts — What You Paid
                                                            ({{ count($data['receipts']) }})
                                                        </h5>
                                                    </div>
                                                    @if (empty($data['receipts']))
                                                        <p class="px-3 py-4 text-xs text-gray-400 text-center italic">
                                                            No GoDaddy receipt found for this domain/month</p>
                                                    @else
                                                        <div class="overflow-x-auto">
                                                            <table class="w-full text-xs">
                                                                <thead class="bg-red-50">
                                                                    <tr>
                                                                        <th
                                                                            class="px-3 py-2 text-left font-semibold text-gray-500">
                                                                            Domain</th>
                                                                        <th
                                                                            class="px-3 py-2 text-left font-semibold text-gray-500">
                                                                            Product</th>
                                                                        <th
                                                                            class="px-3 py-2 text-left font-semibold text-gray-500">
                                                                            Length</th>
                                                                        <th
                                                                            class="px-3 py-2 text-left font-semibold text-gray-500">
                                                                            Date</th>
                                                                        <th
                                                                            class="px-3 py-2 text-right font-semibold text-gray-500">
                                                                            Subtotal</th>
                                                                        <th
                                                                            class="px-3 py-2 text-right font-semibold text-gray-500">
                                                                            Tax</th>
                                                                        <th
                                                                            class="px-3 py-2 text-right font-semibold text-gray-500">
                                                                            Total Paid</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody class="divide-y divide-red-100">
                                                                    @foreach ($data['receipts'] as $receipt)
                                                                        <tr class="hover:bg-red-50">
                                                                            <td
                                                                                class="px-3 py-2 font-mono text-gray-700">
                                                                                {{ $receipt->domain_name }}</td>
                                                                            <td class="px-3 py-2 text-gray-500 max-w-xs truncate"
                                                                                title="{{ $receipt->product_name }}">
                                                                                {{ $receipt->product_name ?? '—' }}
                                                                            </td>
                                                                            <td
                                                                                class="px-3 py-2 text-gray-500 whitespace-nowrap">
                                                                                {{ $receipt->length ?? '—' }}</td>
                                                                            <td
                                                                                class="px-3 py-2 text-gray-500 whitespace-nowrap">
                                                                                {{ $receipt->order_date?->format('d M Y') ?? '—' }}
                                                                            </td>
                                                                            <td
                                                                                class="px-3 py-2 text-right text-gray-600">
                                                                                ₹
                                                                                {{ number_format($receipt->subtotal, 2) }}
                                                                            </td>
                                                                            <td
                                                                                class="px-3 py-2 text-right text-orange-600">
                                                                                {{ $receipt->tax_amount > 0 ? '₹ ' . number_format($receipt->tax_amount, 2) : '—' }}
                                                                            </td>
                                                                            <td
                                                                                class="px-3 py-2 text-right font-semibold text-red-700">
                                                                                ₹
                                                                                {{ number_format($receipt->order_total, 2) }}
                                                                            </td>
                                                                        </tr>
                                                                    @endforeach
                                                                </tbody>
                                                                @if (count($data['receipts']) >= 1)
                                                                    <tfoot>
                                                                        <tr class="bg-red-100 font-bold">
                                                                            <td colspan="4"
                                                                                class="px-3 py-2 text-right">Total
                                                                                GoDaddy Cost:</td>
                                                                            <td
                                                                                class="px-3 py-2 text-right text-gray-700">
                                                                                ₹
                                                                                {{ number_format(collect($data['receipts'])->sum('subtotal'), 2) }}
                                                                            </td>
                                                                            <td
                                                                                class="px-3 py-2 text-right text-orange-600">
                                                                                ₹
                                                                                {{ number_format(collect($data['receipts'])->sum('tax_amount'), 2) }}
                                                                            </td>
                                                                            <td
                                                                                class="px-3 py-2 text-right text-red-700 text-sm">
                                                                                ₹ {{ number_format($costTotal, 2) }}
                                                                            </td>
                                                                        </tr>
                                                                    </tfoot>
                                                                @endif
                                                            </table>
                                                        </div>
                                                    @endif
                                                </div>

                                                {{-- Your GoDaddy Bills (What you CHARGED clients) --}}
                                                <div
                                                    class="bg-purple-50/40 rounded-lg overflow-hidden border border-purple-100">
                                                    <div
                                                        class="px-3 py-2 bg-purple-100 border-b border-purple-200 flex items-center gap-2">
                                                        <svg class="w-4 h-4 text-purple-600" fill="none"
                                                            stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414A1 1 0 0119 9.414V19a2 2 0 01-2 2z" />
                                                        </svg>
                                                        <h5 class="text-sm font-semibold text-purple-800">
                                                            Your Bills to Client — What You Charged
                                                            ({{ count($data['your_bills']) }})
                                                        </h5>
                                                    </div>
                                                    @if (empty($data['your_bills']))
                                                        <p class="px-3 py-4 text-xs text-gray-400 text-center italic">
                                                            No bill raised to client for this domain/month</p>
                                                    @else
                                                        <div class="overflow-x-auto">
                                                            <table class="w-full text-xs">
                                                                <thead class="bg-purple-50">
                                                                    <tr>
                                                                        <th
                                                                            class="px-3 py-2 text-left font-semibold text-gray-500">
                                                                            Invoice #</th>
                                                                        <th
                                                                            class="px-3 py-2 text-left font-semibold text-gray-500">
                                                                            Client</th>
                                                                        <th
                                                                            class="px-3 py-2 text-left font-semibold text-gray-500">
                                                                            Domain</th>
                                                                        <th
                                                                            class="px-3 py-2 text-left font-semibold text-gray-500">
                                                                            Date</th>
                                                                        <th
                                                                            class="px-3 py-2 text-right font-semibold text-gray-500">
                                                                            Before Tax</th>
                                                                        <th
                                                                            class="px-3 py-2 text-right font-semibold text-gray-500">
                                                                            SGST</th>
                                                                        <th
                                                                            class="px-3 py-2 text-right font-semibold text-gray-500">
                                                                            CGST</th>
                                                                        <th
                                                                            class="px-3 py-2 text-right font-semibold text-gray-500">
                                                                            Total</th>
                                                                        <th
                                                                            class="px-3 py-2 text-center font-semibold text-gray-500">
                                                                            Del</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody class="divide-y divide-purple-100">
                                                                    @foreach ($data['your_bills'] as $bill)
                                                                        <tr class="hover:bg-purple-50">
                                                                            <td
                                                                                class="px-3 py-2 font-mono text-gray-700">
                                                                                {{ $bill->invoice_number ?? '—' }}</td>
                                                                            <td class="px-3 py-2 text-gray-700 font-medium max-w-xs truncate"
                                                                                title="{{ $bill->client_name }}">
                                                                                {{ $bill->client_name ?? '—' }}
                                                                            </td>
                                                                            <td
                                                                                class="px-3 py-2 font-mono text-gray-500">
                                                                                {{ $bill->domain_name ?? '—' }}</td>
                                                                            <td
                                                                                class="px-3 py-2 text-gray-500 whitespace-nowrap">
                                                                                {{ $bill->invoice_date?->format('d M Y') ?? '—' }}
                                                                            </td>
                                                                            <td
                                                                                class="px-3 py-2 text-right text-gray-700">
                                                                                ₹
                                                                                {{ number_format($bill->amount_before_tax, 2) }}
                                                                            </td>
                                                                            <td
                                                                                class="px-3 py-2 text-right text-orange-600">
                                                                                {{ $bill->sgst_amount > 0 ? '₹ ' . number_format($bill->sgst_amount, 2) : '—' }}
                                                                            </td>
                                                                            <td
                                                                                class="px-3 py-2 text-right text-orange-600">
                                                                                {{ $bill->cgst_amount > 0 ? '₹ ' . number_format($bill->cgst_amount, 2) : '—' }}
                                                                            </td>
                                                                            <td
                                                                                class="px-3 py-2 text-right font-semibold text-purple-700">
                                                                                ₹
                                                                                {{ number_format($bill->total_amount, 2) }}
                                                                            </td>
                                                                            <td class="px-3 py-2 text-center">
                                                                                <button
                                                                                    onclick="deleteYourGodaddyBill({{ $bill->id }})"
                                                                                    class="text-red-400 hover:text-red-600 p-1 rounded hover:bg-red-50 transition"
                                                                                    title="Delete">
                                                                                    <svg class="w-3.5 h-3.5"
                                                                                        fill="none"
                                                                                        stroke="currentColor"
                                                                                        viewBox="0 0 24 24">
                                                                                        <path stroke-linecap="round"
                                                                                            stroke-linejoin="round"
                                                                                            stroke-width="2"
                                                                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                                                    </svg>
                                                                                </button>
                                                                            </td>
                                                                        </tr>
                                                                    @endforeach
                                                                </tbody>
                                                                @if (count($data['your_bills']) >= 1)
                                                                    <tfoot>
                                                                        <tr class="bg-purple-100 font-bold">
                                                                            <td colspan="4"
                                                                                class="px-3 py-2 text-right">Total
                                                                                Billed:</td>
                                                                            <td
                                                                                class="px-3 py-2 text-right text-gray-700">
                                                                                ₹
                                                                                {{ number_format(collect($data['your_bills'])->sum('amount_before_tax'), 2) }}
                                                                            </td>
                                                                            <td
                                                                                class="px-3 py-2 text-right text-orange-600">
                                                                                ₹
                                                                                {{ number_format(collect($data['your_bills'])->sum('sgst_amount'), 2) }}
                                                                            </td>
                                                                            <td
                                                                                class="px-3 py-2 text-right text-orange-600">
                                                                                ₹
                                                                                {{ number_format(collect($data['your_bills'])->sum('cgst_amount'), 2) }}
                                                                            </td>
                                                                            <td
                                                                                class="px-3 py-2 text-right text-purple-700 text-sm">
                                                                                ₹ {{ number_format($billedTotal, 2) }}
                                                                            </td>
                                                                            <td></td>
                                                                        </tr>
                                                                    </tfoot>
                                                                @endif
                                                            </table>
                                                        </div>
                                                    @endif
                                                </div>

                                            </div>

                                            {{-- Profit / Loss result bar --}}
                                            @if ($isMatched)
                                                <div
                                                    class="mt-3 p-3 rounded-lg border {{ $isProfit ? 'bg-emerald-50 border-emerald-200' : 'bg-red-50 border-red-200' }}">
                                                    <div
                                                        class="flex flex-wrap items-center justify-between gap-3 text-sm">
                                                        <div class="flex items-center gap-2">
                                                            <span class="text-lg">{{ $isProfit ? '✅' : '⚠️' }}</span>
                                                            <span
                                                                class="font-semibold {{ $isProfit ? 'text-emerald-800' : 'text-red-800' }}">
                                                                {{ $isProfit ? 'Profitable' : 'Billed less than GoDaddy cost' }}
                                                            </span>
                                                        </div>
                                                        <div class="flex flex-wrap gap-4 text-xs">
                                                            <span>
                                                                GoDaddy Paid:
                                                                <strong class="text-red-600">₹
                                                                    {{ number_format($costTotal, 2) }}</strong>
                                                            </span>
                                                            <span>
                                                                Billed to Client:
                                                                <strong class="text-purple-600">₹
                                                                    {{ number_format($billedTotal, 2) }}</strong>
                                                            </span>
                                                            <span
                                                                class="font-bold {{ $isProfit ? 'text-emerald-700' : 'text-red-700' }}">
                                                                {{ $isProfit ? 'Profit' : 'Loss' }}:
                                                                {{ $isProfit ? '+' : '-' }}₹
                                                                {{ number_format(abs($profit), 2) }}
                                                                ({{ $profitPct }}%)
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif

                                        </div>{{-- end domain detail --}}
                                    </div>{{-- end domain row --}}
                                @endforeach
                            </div>{{-- end domains --}}
                        </div>{{-- end month card --}}
                    @endforeach
                </div>
            </div>
        @endif

        {{-- ══════════════════ GODADDY RECEIPTS TABLE ══════════════════ --}}
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                <h2 class="text-sm font-semibold text-gray-700 flex items-center gap-2">
                    <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414A1 1 0 0119 9.414V19a2 2 0 01-2 2z" />
                    </svg>
                    GoDaddy Domain Receipts
                </h2>
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
                                Order Date</th>
                            <th
                                class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Domain Name</th>
                            <th
                                class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Product</th>
                            <th
                                class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Length</th>
                            <th
                                class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                ICANN</th>
                            <th
                                class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Subtotal</th>
                            <th
                                class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Tax</th>
                            <th
                                class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Order Total</th>
                            <th
                                class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Payment</th>
                            <th
                                class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse ($records as $index => $record)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-4 py-3 text-gray-400 text-xs">{{ $records->firstItem() + $index }}</td>
                                <td class="px-4 py-3 text-gray-600 text-xs whitespace-nowrap">
                                    {{ $record->order_date ? $record->order_date->format('d M Y') : '—' }}
                                </td>
                                <td class="px-4 py-3">
                                    <span
                                        class="font-medium text-gray-800 font-mono text-xs">{{ $record->domain_name ?? '—' }}</span>
                                    @if ($record->receipt_number)
                                        <div class="text-xs text-gray-400 font-mono mt-0.5">
                                            #{{ $record->receipt_number }}</div>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    @php
                                        $productLower = strtolower($record->product_name ?? '');
                                        $badgeClass = 'bg-gray-100 text-gray-600';
                                        if (str_contains($productLower, 'renewal')) {
                                            $badgeClass = 'bg-blue-100 text-blue-700';
                                        } elseif (str_contains($productLower, 'registration')) {
                                            $badgeClass = 'bg-green-100 text-green-700';
                                        } elseif (str_contains($productLower, 'transfer')) {
                                            $badgeClass = 'bg-purple-100 text-purple-700';
                                        }
                                    @endphp
                                    <span
                                        class="inline-flex items-center text-xs px-2 py-0.5 rounded-full font-medium {{ $badgeClass }}">
                                        {{ $record->product_name ?? '—' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-gray-600 text-xs whitespace-nowrap">
                                    {{ $record->length ?? '—' }}</td>
                                <td class="px-4 py-3 text-right text-purple-600 text-xs">
                                    {{ $record->icann_fee > 0 ? number_format($record->icann_fee, 2) : '—' }}
                                </td>
                                <td class="px-4 py-3 text-right text-gray-700">
                                    {{ number_format($record->subtotal, 2) }}</td>
                                <td class="px-4 py-3 text-right text-orange-600 text-xs">
                                    {{ $record->tax_amount > 0 ? number_format($record->tax_amount, 2) : '—' }}
                                </td>
                                <td class="px-4 py-3 text-right font-bold text-green-700">
                                    ₹ {{ number_format($record->order_total, 2) }}
                                </td>
                                <td class="px-4 py-3">
                                    @if ($record->payment_category)
                                        <span
                                            class="inline-flex items-center text-xs px-2 py-0.5 rounded-full font-medium bg-amber-100 text-amber-700">
                                            {{ $record->payment_category }}
                                        </span>
                                        @if ($record->payment_sub_category && $record->payment_sub_category !== $record->payment_category)
                                            <div class="text-xs text-gray-400 mt-0.5">
                                                {{ $record->payment_sub_category }}</div>
                                        @endif
                                    @else
                                        <span class="text-gray-400">—</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <button @click="deleteRecord({{ $record->id }})"
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
                                <td colspan="11" class="px-4 py-12 text-center text-gray-400">
                                    <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064" />
                                    </svg>
                                    <p class="font-medium">No receipts found</p>
                                    <p class="text-sm mt-1">Upload a GoDaddy Excel or CSV export to get started.</p>
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
                                    class="px-3 py-1.5 text-sm bg-green-600 text-white border border-green-600 rounded-lg">{{ $page }}</span>
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

        {{-- ══════════════════ YOUR GODADDY BILLS TABLE ══════════════════ --}}
        @if (isset($yourBills) && $yourBills->count() > 0)
            <div class="bg-white rounded-xl shadow-sm overflow-hidden mt-6">
                <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                    <h2 class="text-sm font-semibold text-gray-700 flex items-center gap-2">
                        <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414A1 1 0 0119 9.414V19a2 2 0 01-2 2z" />
                        </svg>
                        Your GoDaddy Bills (Billed to Clients)
                    </h2>
                    <span class="text-xs text-gray-400">{{ $yourBills->count() }} bill(s)</span>
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
                                    Client</th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                    Domain</th>
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
                                    Total</th>
                                <th
                                    class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                    Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @foreach ($yourBills as $i => $bill)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-4 py-3 text-gray-400 text-xs">{{ $i + 1 }}</td>
                                    <td class="px-4 py-3 font-mono text-gray-700 text-xs">
                                        {{ $bill->invoice_number ?? '—' }}</td>
                                    <td class="px-4 py-3 text-gray-600 text-xs whitespace-nowrap">
                                        {{ $bill->invoice_date?->format('d M Y') ?? '—' }}
                                    </td>
                                    <td class="px-4 py-3 font-medium text-gray-800 text-xs">
                                        {{ $bill->client_name ?? '—' }}</td>
                                    <td class="px-4 py-3 font-mono text-purple-700 text-xs">
                                        {{ $bill->domain_name ?? '—' }}</td>
                                    <td class="px-4 py-3 text-gray-500 text-xs max-w-xs truncate"
                                        title="{{ $bill->description }}">
                                        {{ $bill->description ?? '—' }}
                                    </td>
                                    <td class="px-4 py-3 text-right text-gray-700 text-xs">₹
                                        {{ number_format($bill->amount_before_tax, 2) }}</td>
                                    <td class="px-4 py-3 text-right text-orange-600 text-xs">
                                        {{ $bill->sgst_amount > 0 ? '₹ ' . number_format($bill->sgst_amount, 2) : '—' }}
                                    </td>
                                    <td class="px-4 py-3 text-right text-orange-600 text-xs">
                                        {{ $bill->cgst_amount > 0 ? '₹ ' . number_format($bill->cgst_amount, 2) : '—' }}
                                    </td>
                                    <td class="px-4 py-3 text-right font-bold text-purple-700">₹
                                        {{ number_format($bill->total_amount, 2) }}</td>
                                    <td class="px-4 py-3 text-center">
                                        <button onclick="deleteYourGodaddyBill({{ $bill->id }})"
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
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="bg-purple-50 font-bold border-t-2 border-purple-200">
                                <td colspan="6"
                                    class="px-4 py-3 text-right text-gray-700 text-xs uppercase tracking-wider">
                                    Total ({{ $yourBills->count() }} bill(s))
                                </td>
                                <td class="px-4 py-3 text-right text-gray-800">₹
                                    {{ number_format($yourBills->sum('amount_before_tax'), 2) }}</td>
                                <td class="px-4 py-3 text-right text-orange-600">₹
                                    {{ number_format($yourBills->sum('sgst_amount'), 2) }}</td>
                                <td class="px-4 py-3 text-right text-orange-600">₹
                                    {{ number_format($yourBills->sum('cgst_amount'), 2) }}</td>
                                <td class="px-4 py-3 text-right text-purple-700 text-base">₹
                                    {{ number_format($yourBills->sum('total_amount'), 2) }}</td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        @endif

    </main>

    {{-- ══════════════════ UPLOAD MODAL: GoDaddy Export ══════════════════ --}}
    <div x-show="showUpload" x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm"
        @click.self="showUpload = false">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg mx-4 p-6" @click.stop>
            <div class="flex items-center justify-between mb-5">
                <h2 class="text-lg font-semibold text-gray-800">Upload GoDaddy Export File</h2>
                <button @click="showUpload = false" class="text-gray-400 hover:text-gray-600 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <form method="POST" action="{{ route('godaddy.upload') }}" enctype="multipart/form-data">
                @csrf
                <div class="border-2 border-dashed border-gray-200 rounded-xl p-8 text-center hover:border-green-400 transition cursor-pointer"
                    @click="$refs.fileInput.click()" @dragover.prevent @drop.prevent="handleDrop($event)">
                    <svg class="w-10 h-10 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                    </svg>
                    <p class="text-sm text-gray-500">Click to select or drag & drop</p>
                    <p class="text-xs text-gray-400 mt-1">Supports <strong>.xlsx</strong> and <strong>.csv</strong> •
                        Multiple files ok</p>
                    <input type="file" name="files[]" multiple accept=".xlsx,.xls,.csv" x-ref="fileInput"
                        class="hidden" @change="handleFiles($event)">
                </div>
                <div x-show="uploadFiles.length > 0" class="mt-3 space-y-1 max-h-40 overflow-y-auto">
                    <template x-for="(f, i) in uploadFiles" :key="i">
                        <div class="flex items-center gap-2 text-sm text-gray-600 bg-gray-50 rounded-lg px-3 py-2">
                            <svg class="w-4 h-4 text-green-500 flex-shrink-0" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414A1 1 0 0119 9.414V19a2 2 0 01-2 2z" />
                            </svg>
                            <span x-text="f.name" class="truncate flex-1"></span>
                            <span class="text-gray-400 text-xs" x-text="(f.size/1024).toFixed(0) + ' KB'"></span>
                        </div>
                    </template>
                </div>
                <div class="mt-3 bg-green-50 border border-green-100 rounded-lg px-4 py-3">
                    <p class="text-xs text-green-700">
                        📋 Upload GoDaddy billing history export (Excel/CSV).
                        Records are queued and processed automatically.
                    </p>
                </div>
                <div class="flex gap-3 mt-5">
                    <button type="button" @click="showUpload = false"
                        class="flex-1 px-4 py-2.5 border border-gray-200 rounded-xl text-sm text-gray-600 hover:bg-gray-50 transition">Cancel</button>
                    <button type="submit" :disabled="uploadFiles.length === 0"
                        class="flex-1 px-4 py-2.5 bg-green-600 hover:bg-green-700 disabled:bg-green-300 text-white rounded-xl text-sm font-medium transition">
                        Upload & Queue
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ══════════════════ UPLOAD MODAL: Your GoDaddy Bill PDF ══════════════════ --}}
    <div x-show="showYourBillUpload" x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm"
        @click.self="showYourBillUpload = false">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg mx-4 p-6" @click.stop>
            <div class="flex items-center justify-between mb-5">
                <div>
                    <h2 class="text-lg font-semibold text-gray-800">Upload Your GoDaddy Bill PDF(s)</h2>
                    <p class="text-xs text-gray-400 mt-0.5">Websmaniac invoices you raised to clients for domain
                        services</p>
                </div>
                <button @click="showYourBillUpload = false" class="text-gray-400 hover:text-gray-600 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <form method="POST" action="{{ route('your-godaddy-bill.upload') }}" enctype="multipart/form-data">
                @csrf
                <div class="border-2 border-dashed border-gray-200 rounded-xl p-8 text-center hover:border-purple-400 transition cursor-pointer"
                    @click="$refs.yourBillFileInput.click()" @dragover.prevent
                    @drop.prevent="handleYourBillDrop($event)">
                    <svg class="w-10 h-10 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                    </svg>
                    <p class="text-sm text-gray-500">Click to select PDF(s) or drag & drop</p>
                    <p class="text-xs text-gray-400 mt-1">Domain name auto-extracted • Client name, SGST, CGST parsed •
                        Max 10MB</p>
                    <input type="file" name="pdfs[]" multiple accept=".pdf" x-ref="yourBillFileInput"
                        class="hidden" @change="yourBillFiles = Array.from($event.target.files)">
                </div>
                <div x-show="yourBillFiles.length > 0" class="mt-3 space-y-1 max-h-40 overflow-y-auto">
                    <template x-for="(f, i) in yourBillFiles" :key="i">
                        <div class="flex items-center gap-2 text-sm text-gray-600 bg-gray-50 rounded-lg px-3 py-2">
                            <svg class="w-4 h-4 text-purple-400 flex-shrink-0" fill="currentColor"
                                viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" />
                            </svg>
                            <span x-text="f.name" class="truncate flex-1"></span>
                            <span class="text-gray-400 text-xs" x-text="(f.size/1024).toFixed(0) + ' KB'"></span>
                        </div>
                    </template>
                </div>
                <div class="mt-3 bg-purple-50 border border-purple-100 rounded-lg px-4 py-3">
                    <p class="text-xs text-purple-700">
                        🌐 Each PDF = <strong>1 bill record</strong>. Domain name is auto-extracted from service
                        description
                        (e.g. "Domain Renewal (myshaadicards.com)"). SGST + CGST parsed automatically.
                    </p>
                </div>
                <div class="flex gap-3 mt-5">
                    <button type="button" @click="showYourBillUpload = false"
                        class="flex-1 px-4 py-2.5 border border-gray-200 rounded-xl text-sm text-gray-600 hover:bg-gray-50 transition">Cancel</button>
                    <button type="submit" :disabled="yourBillFiles.length === 0"
                        class="flex-1 px-4 py-2.5 bg-purple-600 hover:bg-purple-700 disabled:bg-purple-300 text-white rounded-xl text-sm font-medium transition">
                        Upload & Parse PDF(s)
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ══════════════════ ALPINE.JS ══════════════════ --}}
    <script>
        function godaddyApp() {
            return {
                showUpload: false,
                showYourBillUpload: false,
                uploadFiles: [],
                yourBillFiles: [],

                handleFiles(e) {
                    const all = Array.from(e.target.files);
                    const valid = all.filter(f => f.name.match(/\.(xlsx|xls|csv)$/i));
                    const invalid = all.filter(f => !f.name.match(/\.(xlsx|xls|csv)$/i));
                    if (invalid.length) {
                        alert('Only .xlsx, .xls, .csv allowed.\nSkipped: ' + invalid.map(f => f.name).join(', '));
                    }
                    this.uploadFiles = valid;
                },

                handleDrop(e) {
                    const files = Array.from(e.dataTransfer.files).filter(f => f.name.match(/\.(xlsx|xls|csv)$/i));
                    if (files.length) {
                        this.uploadFiles = files;
                        const dt = new DataTransfer();
                        files.forEach(f => dt.items.add(f));
                        this.$refs.fileInput.files = dt.files;
                    } else {
                        alert('Only .xlsx, .xls, .csv files allowed.');
                    }
                },

                handleYourBillDrop(e) {
                    const files = Array.from(e.dataTransfer.files).filter(f => f.type === 'application/pdf');
                    if (files.length) {
                        this.yourBillFiles = files;
                        const dt = new DataTransfer();
                        files.forEach(f => dt.items.add(f));
                        this.$refs.yourBillFileInput.files = dt.files;
                    } else {
                        alert('Only PDF files allowed.');
                    }
                },

                deleteRecord(id) {
                    if (!confirm('Delete this GoDaddy receipt?')) return;
                    fetch(`/godaddy/${id}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json',
                        }
                    }).then(r => r.json()).then(d => {
                        if (d.success) window.location.reload();
                        else alert('Delete failed.');
                    });
                },
            };
        }

        function retryPending(id) {
            fetch(`/godaddy/pending/${id}/retry`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                }
            }).then(r => r.json()).then(d => {
                if (d.success) window.location.reload();
                else alert('Retry failed: ' + (d.message || 'Unknown error'));
            });
        }

        function deletePending(id) {
            if (!confirm('Remove this pending file entry?')) return;
            fetch(`/godaddy/pending/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                }
            }).then(r => r.json()).then(d => {
                if (d.success) window.location.reload();
                else alert('Remove failed.');
            });
        }

        function deleteYourGodaddyBill(id) {
            if (!confirm('Delete this bill? This cannot be undone.')) return;
            fetch(`/your-godaddy-bill/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                }
            }).then(r => r.json()).then(d => {
                if (d.success) window.location.reload();
                else alert('Delete failed.');
            });
        }
    </script>

</body>

</html>
