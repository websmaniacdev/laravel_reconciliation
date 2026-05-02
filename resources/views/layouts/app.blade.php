<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Bank Statement Manager</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        [x-cloak] {
            display: none !important;
        }

        .merged-row {
            background: #eff6ff;
        }
    </style>
</head>

<body class="bg-gray-100 min-h-screen" x-data="stmtApp()">

    {{-- ══════════════════ HEADER ══════════════════ --}}
    <header class="bg-white shadow-md border-b">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex items-center justify-between h-16">

                <!-- Navigation -->
                <nav class="hidden md:flex items-center space-x-6 text-sm font-medium">

                    <a href="{{ route('invoices.index') }}" class="text-gray-700 hover:text-blue-600 transition">
                        Invoices
                    </a>

                    <a href="{{ route('outsource.index') }}" class="text-gray-700 hover:text-blue-600 transition">
                        Gsuite
                    </a>

                    <a href="{{ route('hostinger.invoices.index') }}"
                        class="text-gray-700 hover:text-blue-600 transition">
                        Hostinger
                    </a>

                    <a href="{{ route('bankstatements.index') }}" class="text-gray-700 hover:text-blue-600 transition">
                        Bank Statements
                    </a>

                    <a href="{{ route('godaddy.index') }}" class="text-gray-700 hover:text-blue-600 transition">
                        GoDaddy
                    </a>

                </nav>

            </div>
        </div>
