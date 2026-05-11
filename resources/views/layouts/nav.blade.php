<nav class="hidden md:flex items-center gap-2 bg-white border border-gray-200 rounded-2xl px-3 py-2 shadow-sm">

    {{-- Normal Nav Item --}}
    @php
        $navClass = 'px-4 py-2 rounded-xl text-sm font-medium transition-all duration-200';
        $activeClass = 'bg-gradient-to-r from-blue-600 to-indigo-600 text-white shadow';
        $inactiveClass = 'text-gray-600 hover:bg-gray-100 hover:text-gray-900';
    @endphp

    {{-- Invoices --}}
    <a href="{{ route('invoices.index') }}"
        class="{{ $navClass }} {{ request()->routeIs('invoices.*') ? $activeClass : $inactiveClass }}">
        Invoices
    </a>

    {{-- Gsuite --}}
    <a href="{{ route('outsource.index') }}"
        class="{{ $navClass }} {{ request()->routeIs('outsource.*') ? $activeClass : $inactiveClass }}">
        Gsuite
    </a>

    {{-- Hostinger Dropdown --}}
    <div class="relative group">

        <button
            class="{{ $navClass }}
            {{ request()->routeIs('hostinger.invoices.*') || request()->routeIs('your-hostinger-bill.*')
                ? 'bg-gradient-to-r from-violet-600 to-purple-600 text-white shadow'
                : $inactiveClass }}">

            <div class="flex items-center gap-2">
                <span>Hostinger</span>

                <svg xmlns="http://www.w3.org/2000/svg"
                    class="w-4 h-4 transition-transform duration-200 group-hover:rotate-180" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </div>
        </button>

        {{-- Dropdown Menu --}}
        <div
            class="absolute left-0 mt-3 w-60 bg-white border border-gray-200 rounded-2xl shadow-xl p-2 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50">

            {{-- Hostinger Bill --}}
            <a href="{{ route('hostinger.invoices.index') }}"
                class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-all
                {{ request()->routeIs('hostinger.invoices.*')
                    ? 'bg-violet-100 text-violet-700'
                    : 'text-gray-700 hover:bg-gray-100' }}">

                <div class="w-8 h-8 rounded-lg bg-violet-100 flex items-center justify-center">
                    🧾
                </div>

                <div>
                    <div>Hostinger Bill</div>
                    <div class="text-xs text-gray-500 font-normal">
                        Manage Hostinger invoices
                    </div>
                </div>
            </a>

            {{-- Your Hostinger Bill --}}
            <a href="{{ route('your-hostinger-bill.index') }}"
                class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-all
                {{ request()->routeIs('your-hostinger-bill.*')
                    ? 'bg-purple-100 text-purple-700'
                    : 'text-gray-700 hover:bg-gray-100' }}">

                <div class="w-8 h-8 rounded-lg bg-purple-100 flex items-center justify-center">
                    📄
                </div>

                <div>
                    <div>Your Hostinger Bill</div>
                    <div class="text-xs text-gray-500 font-normal">
                        Personal Hostinger billing
                    </div>
                </div>
            </a>

        </div>
    </div>

    {{-- Bank Statements --}}
    <a href="{{ route('bankstatements.index') }}"
        class="{{ $navClass }} {{ request()->routeIs('bankstatements.*') ? $activeClass : $inactiveClass }}">
        Bank Statements
    </a>

    {{-- GoDaddy --}}
    <a href="{{ route('godaddy.index') }}"
        class="{{ $navClass }} {{ request()->routeIs('godaddy.*') ? $activeClass : $inactiveClass }}">
        GoDaddy
    </a>

</nav>
