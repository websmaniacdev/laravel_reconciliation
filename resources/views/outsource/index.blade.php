<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Gsuite Receipts</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        [x-cloak] {
            display: none !important;
        }

        .merged-row {
            background: #f0f9ff;
        }
    </style>
</head>

<body class="bg-gray-100 min-h-screen" x-data="outsourceApp()">

    {{-- ══════════════════ HEADER ══════════════════ --}}
    <header class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-4 py-4 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 bg-indigo-600 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414A1 1 0 0119 9.414V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <div>
                    <h1 class="text-xl font-bold text-gray-800">Gsuite Receipts</h1>
                    <p class="text-xs text-gray-400">Google Workspace & Other Subscriptions</p>
                </div>
            </div>
            <button @click="showUpload = true"
                class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-medium flex items-center gap-2 transition">
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
                <p class="font-medium">Some PDFs had errors:</p>
                @foreach (session('upload_errors') as $err)
                    <p class="text-sm mt-1">• {{ $err }}</p>
                @endforeach
            </div>
        </div>
    @endif

    {{-- ══════════════════ MAIN CONTENT ══════════════════ --}}
    <main class="max-w-7xl mx-auto px-4 py-6">

        {{-- ── FILTER BAR ── --}}
        <div class="bg-white rounded-xl shadow-sm p-5 mb-5">
            <form method="GET" action="{{ route('outsource.index') }}">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Client Name</label>
                        <input type="text" name="client_name" value="{{ request('client_name') }}"
                            placeholder="Search client..."
                            class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
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
                            class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">To Date</label>
                        <input type="date" name="to_date" value="{{ request('to_date') }}" x-ref="toDate"
                            class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
                    </div>
                    <div class="flex gap-2">
                        <button type="submit"
                            class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition">
                            Filter
                        </button>
                        <a href="{{ route('outsource.index') }}"
                            class="px-4 py-2 border border-gray-200 rounded-lg text-sm text-gray-600 hover:bg-gray-50 transition">
                            Clear
                        </a>
                        <a href="{{ route('outsource.export', [
                            'client_name' => request('client_name'),
                            'from_date' => request('from_date'),
                            'to_date' => request('to_date'),
                        ]) }}"
                            class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg text-sm font-medium transition">
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
                    <div class="w-2 h-2 rounded-full bg-indigo-500"></div>
                    <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">
                        @if (request('client_name') || request('from_date') || request('to_date'))
                            Filtered Results Summary
                        @else
                            Overall Summary (All Records)
                        @endif
                    </span>
                    @if (request('client_name') || request('from_date') || request('to_date'))
                        <span class="text-xs bg-indigo-100 text-indigo-700 px-2 py-0.5 rounded-full font-medium">
                            {{ number_format($filteredCount) }} records
                        </span>
                    @endif
                </div>

                <div class="flex flex-wrap items-center gap-6">
                    <div class="text-center">
                        <p class="text-xs text-gray-400 mb-0.5">Subtotal (excl. GST)</p>
                        <p class="text-base font-bold text-gray-800">₹ {{ number_format($filteredSubtotal, 2) }}</p>
                    </div>
                    <div class="w-px h-8 bg-gray-200 hidden sm:block"></div>
                    <div class="text-center">
                        <p class="text-xs text-gray-400 mb-0.5">IGST 18%</p>
                        <p class="text-base font-semibold text-orange-600">₹ {{ number_format($filteredGst, 2) }}</p>
                    </div>
                    <div class="w-px h-8 bg-gray-200 hidden sm:block"></div>
                    <div class="text-center">
                        <p class="text-xs text-gray-400 mb-0.5">Grand Total</p>
                        <p class="text-lg font-extrabold text-indigo-700">₹ {{ number_format($filteredGrandTotal, 2) }}
                        </p>
                    </div>
                    <div class="w-px h-8 bg-gray-200 hidden sm:block"></div>
                    <div class="text-center">
                        <p class="text-xs text-gray-400 mb-0.5">Total Receipts</p>
                        <p class="text-base font-semibold text-gray-700">{{ number_format($filteredCount) }}</p>
                    </div>
                </div>
            </div>

            {{-- Active filters --}}
            @if (request('client_name') || request('from_date') || request('to_date'))
                <div class="mt-3 flex flex-wrap gap-2 pt-3 border-t border-gray-100">
                    @if (request('client_name'))
                        <span
                            class="inline-flex items-center gap-1 bg-indigo-50 border border-indigo-200 text-indigo-700 text-xs px-2.5 py-1 rounded-full">
                            Client: <strong>{{ request('client_name') }}</strong>
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
                </div>
            @endif
        </div>

        {{-- ── MERGE TOOLBAR ── --}}
        <div x-show="selectedIds.length >= 2" x-cloak
            class="bg-amber-50 border border-amber-200 rounded-xl px-5 py-4 mb-5 flex items-center justify-between">
            <p class="text-sm text-amber-800 font-medium">
                <span x-text="selectedIds.length"></span> records selected
            </p>
            <div class="flex items-center gap-2">
                <button @click="showMergeModal = true"
                    class="bg-amber-500 hover:bg-amber-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                    </svg>
                    Merge Selected
                </button>
                <button @click="showMergeByMonthModal = true"
                    class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    Merge by Month
                </button>
            </div>
        </div>

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
                    {{-- <span class="text-xs text-gray-400">Run: <code
                            class="bg-gray-100 px-2 py-0.5 rounded font-mono">php artisan outsource:process-pending
                            --sync</code></span> --}}
                    <button
                        class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-mediumm">
                        <a href="{{ route('outsource.run') }}" class="btn btn-primary">
                            Run Gsuite Process
                        </a>
                    </button>
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
                                    <p class="text-sm text-gray-800 font-medium">
                                        {{ $pending->original_filename }}
                                        <span class="text-gray-400 font-normal ml-1">—
                                            {{ $pending->client_name }}</span>
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
                <h2 class="text-sm font-semibold text-gray-700">Gsuite Receipts</h2>
                <span class="text-xs text-gray-400">{{ $records->total() }} total records</span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-100">
                            <th class="px-4 py-3 text-left">
                                <input type="checkbox" @change="toggleAll($event)"
                                    class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                            </th>
                            <th
                                class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                #</th>
                            <th
                                class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Client Name</th>
                            <th
                                class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Invoice No.</th>
                            <th
                                class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Invoice Date</th>
                            <th
                                class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Subscription</th>
                            <th
                                class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Interval</th>
                            <th
                                class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Subtotal (INR)</th>
                            <th
                                class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                GST 18%</th>
                            <th
                                class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Grand Total</th>
                            <th
                                class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($records as $index => $record)
                            <tr class="{{ $record->is_merged ? 'merged-row' : 'hover:bg-gray-50' }} transition"
                                :class="selectedIds.includes({{ $record->id }}) ? 'bg-amber-50' : ''">
                                <td class="px-4 py-3">
                                    <input type="checkbox" :value="{{ $record->id }}" x-model="selectedIds"
                                        class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                </td>
                                <td class="px-4 py-3 text-gray-400 text-xs">{{ $records->firstItem() + $index }}</td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-2">
                                        <span class="font-medium text-gray-800">{{ $record->client_name }}</span>
                                        @if ($record->is_merged)
                                            <span
                                                class="bg-indigo-100 text-indigo-700 text-xs px-2 py-0.5 rounded-full font-medium">Merged</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-gray-500 text-xs font-mono">
                                    {{ $record->invoice_number ?? '—' }}</td>
                                <td class="px-4 py-3 text-gray-600">
                                    {{ $record->invoice_date ? $record->invoice_date->format('d M Y') : '—' }}
                                </td>
                                <td class="px-4 py-3 text-gray-700 max-w-xs">
                                    <div class="font-medium text-xs">{{ $record->subscription ?? '—' }}</div>
                                    @if ($record->description)
                                        <div class="text-gray-400 text-xs mt-0.5">{{ $record->description }}</div>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-gray-500 text-xs">{{ $record->interval ?? '—' }}</td>
                                <td class="px-4 py-3 text-right text-gray-700">
                                    {{ number_format($record->subtotal, 2) }}</td>
                                <td class="px-4 py-3 text-right text-orange-600 text-xs">
                                    {{ number_format($record->gst_amount, 2) }}</td>
                                <td class="px-4 py-3 text-right font-bold text-indigo-700">₹
                                    {{ number_format($record->grand_total, 2) }}</td>
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
                                            d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414A1 1 0 0119 9.414V19a2 2 0 01-2 2z" />
                                    </svg>
                                    <p class="font-medium">No receipts found</p>
                                    <p class="text-sm mt-1">Upload some PDF receipts to get started.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>

                    {{-- Selected Total Footer --}}
                    <tfoot x-show="selectedIds.length > 0" x-cloak>
                        <tr class="bg-amber-50 border-t-2 border-amber-300">
                            <td colspan="9"
                                class="px-4 py-3 text-right text-xs font-semibold text-amber-800 uppercase tracking-wider">
                                Selected (<span x-text="selectedIds.length"></span> records) Total
                            </td>
                            <td class="px-4 py-3 text-right font-bold text-amber-900" id="selectedGrandTotal">—</td>
                            <td></td>
                        </tr>
                    </tfoot>
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
                                    class="px-3 py-1.5 text-sm bg-indigo-600 text-white border border-indigo-600 rounded-lg">{{ $page }}</span>
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
                <h2 class="text-lg font-semibold text-gray-800">Upload Gsuite Receipt PDF(s)</h2>
                <button @click="showUpload = false" class="text-gray-400 hover:text-gray-600 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form method="POST" action="{{ route('outsource.upload') }}" enctype="multipart/form-data">
                @csrf

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">
                        Client Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="client_name" x-model="uploadClientName"
                        placeholder="e.g. Sangani Hospitals"
                        class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 outline-none"
                        required>
                </div>

                <div class="border-2 border-dashed border-gray-200 rounded-xl p-8 text-center hover:border-indigo-400 transition cursor-pointer"
                    @click="$refs.fileInput.click()" @dragover.prevent @drop.prevent="handleDrop($event)">
                    <svg class="w-10 h-10 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                    </svg>
                    <p class="text-sm text-gray-500">Click to select PDF(s) or drag & drop</p>
                    <p class="text-xs text-gray-400 mt-1">Multiple PDFs supported • Max 10MB each</p>
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

                <div class="mt-3 bg-indigo-50 border border-indigo-100 rounded-lg px-4 py-3">
                    <p class="text-xs text-indigo-700">
                        PDFs will be queued for processing.<br>
                        If not processed automatically, run: <code
                            class="font-mono bg-indigo-100 px-1 py-0.5 rounded">php artisan outsource:process-pending
                            --sync</code>
                    </p>
                </div>

                <div class="flex gap-3 mt-5">
                    <button type="button" @click="showUpload = false"
                        class="flex-1 px-4 py-2.5 border border-gray-200 rounded-xl text-sm text-gray-600 hover:bg-gray-50 transition">Cancel</button>
                    <button type="submit" :disabled="uploadFiles.length === 0 || !uploadClientName.trim()"
                        class="flex-1 px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 disabled:bg-indigo-300 text-white rounded-xl text-sm font-medium transition">
                        Queue PDF(s) for Import
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ══════════════════ MERGE MODAL ── --}}
    <div x-show="showMergeModal" x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm"
        @click.self="showMergeModal = false">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4 p-6" @click.stop>
            <div class="flex items-center justify-between mb-5">
                <h2 class="text-lg font-semibold text-gray-800">Merge Records</h2>
                <button @click="showMergeModal = false" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="bg-amber-50 border border-amber-200 rounded-lg p-3 mb-4">
                <p class="text-sm text-amber-800">
                    <strong x-text="selectedIds.length"></strong> records will be merged into one.
                </p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Merged Record Name *</label>
                <input type="text" x-model="mergedName" placeholder="e.g. Sangani Hospitals - All 2026"
                    class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-amber-500 outline-none">
            </div>

            <div class="flex gap-3 mt-5">
                <button @click="showMergeModal = false"
                    class="flex-1 px-4 py-2.5 border border-gray-200 rounded-xl text-sm text-gray-600 hover:bg-gray-50 transition">Cancel</button>
                <button @click="submitMerge()" :disabled="!mergedName.trim()"
                    class="flex-1 px-4 py-2.5 bg-amber-500 hover:bg-amber-600 disabled:bg-amber-200 text-white rounded-xl text-sm font-medium transition">
                    Confirm Merge
                </button>
            </div>
        </div>
    </div>

    {{-- ══════════════════ MERGE BY MONTH MODAL ── --}}
    <div x-show="showMergeByMonthModal" x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm"
        @click.self="showMergeByMonthModal = false">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4 p-6" @click.stop>
            <div class="flex items-center justify-between mb-5">
                <h2 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    Merge by Month
                </h2>
                <button @click="showMergeByMonthModal = false" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="bg-purple-50 border border-purple-200 rounded-lg p-3 mb-4">
                <p class="text-sm text-purple-800">
                    <strong x-text="selectedIds.length"></strong> records selected.<br>
                    They will be grouped month-wise.
                </p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Base Name *</label>
                <input type="text" x-model="mergeByMonthName" placeholder="e.g. Sangani Hospitals"
                    class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-purple-500 outline-none">
                <p class="text-xs text-gray-400 mt-1">Month name will be added automatically (e.g. "February 2026")</p>
            </div>

            <div class="flex gap-3 mt-5">
                <button @click="showMergeByMonthModal = false"
                    class="flex-1 px-4 py-2.5 border border-gray-200 rounded-xl text-sm text-gray-600 hover:bg-gray-50 transition">Cancel</button>
                <button @click="submitMergeByMonth()" :disabled="!mergeByMonthName.trim()"
                    class="flex-1 px-4 py-2.5 bg-purple-600 hover:bg-purple-700 disabled:bg-purple-200 text-white rounded-xl text-sm font-medium transition">
                    Merge by Month
                </button>
            </div>
        </div>
    </div>

    {{-- ══════════════════ ALPINE.JS ── --}}
    <script>
        // For selected grand total calculation
        const recordGrandTotals = {
            @foreach ($records as $record)
                {{ $record->id }}: {{ $record->grand_total ?? 0 }},
            @endforeach
        };

        function outsourceApp() {
            return {
                showUpload: false,
                showMergeModal: false,
                showMergeByMonthModal: false,
                uploadFiles: [],
                uploadClientName: '',
                selectedIds: [],
                mergedName: '',
                mergeByMonthName: '',

                init() {
                    this.$watch('selectedIds', () => {
                        const total = this.selectedIds.reduce((sum, id) => {
                            return sum + (recordGrandTotals[id] || 0);
                        }, 0);
                        const el = document.getElementById('selectedGrandTotal');
                        if (el) {
                            el.textContent = '₹ ' + total.toLocaleString('en-IN', {
                                minimumFractionDigits: 2,
                                maximumFractionDigits: 2
                            });
                        }
                    });
                },

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

                toggleAll(e) {
                    if (e.target.checked) {
                        this.selectedIds = Array.from(
                            document.querySelectorAll('input[type=checkbox][x-model]')
                        ).map(el => parseInt(el.value)).filter(v => !isNaN(v));
                    } else {
                        this.selectedIds = [];
                    }
                },

                deleteRecord(id) {
                    if (!confirm('Delete this receipt?')) return;
                    fetch(`/outsource/${id}`, {
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

                submitMerge() {
                    if (!this.mergedName.trim() || this.selectedIds.length < 2) return;
                    fetch('{{ route('outsource.merge') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({
                            record_ids: this.selectedIds,
                            merged_name: this.mergedName
                        })
                    }).then(r => r.json()).then(data => {
                        if (data.success) {
                            this.showMergeModal = false;
                            this.selectedIds = [];
                            this.mergedName = '';
                            window.location.reload();
                        } else {
                            alert('Merge failed');
                        }
                    });
                },

                submitMergeByMonth() {
                    if (!this.mergeByMonthName.trim() || this.selectedIds.length < 2) return;
                    fetch('{{ route('outsource.mergeByMonth') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({
                            record_ids: this.selectedIds,
                            merged_name: this.mergeByMonthName
                        })
                    }).then(r => r.json()).then(data => {
                        if (data.success) {
                            this.showMergeByMonthModal = false;
                            this.selectedIds = [];
                            this.mergeByMonthName = '';
                            window.location.reload();
                        } else {
                            alert('Merge by Month failed');
                        }
                    });
                },
            }
        }

        function retryPending(id) {
            fetch(`/outsource/pending/${id}/retry`, {
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
            if (!confirm('Remove this pending PDF entry?')) return;
            fetch(`/outsource/pending/${id}`, {
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
