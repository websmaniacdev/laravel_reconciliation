<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Godaddy Receipts</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>
</head>

<body class="bg-gray-100 min-h-screen" x-data="godaddyApp()">

    {{-- ══════════════════ HEADER ══════════════════ --}}
    <header class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-4 py-4 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 bg-green-600 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064" />
                    </svg>
                </div>
                <div>
                    <h1 class="text-xl font-bold text-gray-800">Godaddy Receipts</h1>
                    <p class="text-xs text-gray-400">Domain Renewals, Registrations & Transfers</p>
                </div>
            </div>
            <button @click="showUpload = true"
                class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium flex items-center gap-2 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                </svg>
                Upload Excel / CSV
            </button>
        </div>
    </header>

    {{-- ══════════════════ FLASH MESSAGES ══════════════════ --}}
    @if (session('success'))
        <div class="max-w-7xl mx-auto px-4 mt-4">
            <div
                class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg flex items-center gap-2">
                <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
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
                        <p class="text-base font-semibold text-purple-600">₹ {{ number_format($filteredIcann, 2) }}</p>
                    </div>
                    <div class="w-px h-8 bg-gray-200 hidden sm:block"></div>
                    <div class="text-center">
                        <p class="text-xs text-gray-400 mb-0.5">Tax</p>
                        <p class="text-base font-semibold text-orange-600">₹ {{ number_format($filteredTax, 2) }}</p>
                    </div>
                    <div class="w-px h-8 bg-gray-200 hidden sm:block"></div>
                    <div class="text-center">
                        <p class="text-xs text-gray-400 mb-0.5">Order Total</p>
                        <p class="text-lg font-extrabold text-green-700">₹ {{ number_format($filteredOrderTotal, 2) }}
                        </p>
                    </div>
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
                    {{-- <span class="text-xs text-gray-400">Run: <code
                            class="bg-gray-100 px-2 py-0.5 rounded font-mono">php artisan godaddy:process-pending
                            --sync</code></span> --}}
                    <button
                        class="bg-green-600 hover:bg-green-700 text-white px-3 py-2 rounded-lg text-sm font-medium">
                        <a href="{{ route('godaddy.run') }}" class="btn btn-primary">
                            Run GoDaddy Process
                        </a>
                    </button>
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
                                    <p class="text-sm text-gray-800 font-medium">{{ $pending->original_filename }}
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

        {{-- ── RECEIPTS TABLE ── --}}
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                <h2 class="text-sm font-semibold text-gray-700">Godaddy Domain Receipts</h2>
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
                                    <span class="font-medium text-gray-800">{{ $record->domain_name ?? '—' }}</span>
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
                                    <p class="text-sm mt-1">Upload a Godaddy Excel or CSV export to get started.</p>
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

    </main>

    {{-- ══════════════════ UPLOAD MODAL ══════════════════ --}}
    <div x-show="showUpload" x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm"
        @click.self="showUpload = false">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg mx-4 p-6" @click.stop>
            <div class="flex items-center justify-between mb-5">
                <h2 class="text-lg font-semibold text-gray-800">Upload Godaddy Export File</h2>
                <button @click="showUpload = false" class="text-gray-400 hover:text-gray-600 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form method="POST" action="{{ route('godaddy.upload') }}" enctype="multipart/form-data">
                @csrf

                {{-- File Drop Zone --}}
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
                            <span class="text-gray-400 text-xs flex-shrink-0"
                                x-text="(f.size/1024).toFixed(0) + ' KB'"></span>
                        </div>
                    </template>
                </div>

                <div class="mt-3 bg-green-50 border border-green-100 rounded-lg px-4 py-3">
                    <p class="text-xs text-green-700">
                        📋 Godaddy billing history <br>
                        <code class="font-mono bg-green-100 px-1 py-0.5 rounded mt-1 inline-block">php artisan
                            godaddy:process-pending --sync</code>
                    </p>
                </div>

                <div class="flex gap-3 mt-5">
                    <button type="button" @click="showUpload = false"
                        class="flex-1 px-4 py-2.5 border border-gray-200 rounded-xl text-sm text-gray-600 hover:bg-gray-50 transition">
                        Cancel
                    </button>
                    <button type="submit" :disabled="uploadFiles.length === 0"
                        class="flex-1 px-4 py-2.5 bg-green-600 hover:bg-green-700 disabled:bg-green-300 text-white rounded-xl text-sm font-medium transition">
                        Upload & Queue
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
                uploadFiles: [],

                handleFiles(e) {
                    // Extension-based filter — CSV ka MIME type browser-to-browser alag hota hai
                    const all = Array.from(e.target.files);
                    const valid = all.filter(f => f.name.match(/\.(xlsx|xls|csv)$/i));
                    const invalid = all.filter(f => !f.name.match(/\.(xlsx|xls|csv)$/i));
                    if (invalid.length) {
                        alert('Only .xlsx, .xls, .csv files allowed.\nSkipped: ' + invalid.map(f => f.name).join(', '));
                    }
                    this.uploadFiles = valid;
                },

                handleDrop(e) {
                    // Extension-based only — MIME type unreliable for CSV
                    const files = Array.from(e.dataTransfer.files).filter(f =>
                        f.name.match(/\.(xlsx|xls|csv)$/i)
                    );
                    if (files.length) {
                        this.uploadFiles = files;
                        const dt = new DataTransfer();
                        files.forEach(f => dt.items.add(f));
                        this.$refs.fileInput.files = dt.files;
                    } else {
                        alert('Only .xlsx, .xls, .csv files allowed.');
                    }
                },

                deleteRecord(id) {
                    if (!confirm('Delete this receipt?')) return;
                    fetch(`/godaddy/${id}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json',
                        }
                    }).then(r => r.json()).then(data => {
                        if (data.success) window.location.reload();
                        else alert('Delete failed.');
                    });
                },
            }
        }

        function retryPending(id) {
            fetch(`/godaddy/pending/${id}/retry`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                }
            }).then(r => r.json()).then(data => {
                if (data.success) window.location.reload();
                else alert('Retry failed: ' + (data.message || 'Unknown error'));
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
            }).then(r => r.json()).then(data => {
                if (data.success) window.location.reload();
                else alert('Remove failed.');
            });
        }
    </script>

</body>

</html>
