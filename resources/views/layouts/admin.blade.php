<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Dashboard') - Bakso Pesona</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full bg-pesona-cream text-gray-900 antialiased" x-data="{ sidebarOpen: false }">
    <div class="min-h-full flex">
        {{-- BACKDROP UNTUK MOBILE --}}
        <div
            x-show="sidebarOpen"
            x-cloak
            @click="sidebarOpen = false"
            class="fixed inset-0 z-40 bg-black/50 transition-opacity lg:hidden"
            aria-hidden="true"
        ></div>

        {{-- SIDEBAR ADMIN --}}
        <aside
            :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
            class="fixed inset-y-0 left-0 z-50 flex w-64 flex-col bg-[#74191B] text-white shadow-xl transition-transform duration-300 ease-in-out lg:static lg:shadow-none"
        >
            {{-- HEADER SIDEBAR --}}
            <div class="flex h-20 items-center justify-between border-b border-white/10 px-6">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3">
                    <img
                        src="{{ asset('images/logo_navbar.png') }}"
                        alt="Bakso Pesona"
                        class="h-10 w-auto object-contain"
                    >
                    <div>
                        <span class="block text-base font-bold tracking-tight text-white">Bakso Pesona</span>
                        <span class="block text-xs font-medium text-[#FFB45E]">Admin Panel</span>
                    </div>
                </a>

                <button
                    type="button"
                    @click="sidebarOpen = false"
                    class="rounded-lg p-1 text-white/70 hover:bg-white/10 hover:text-white lg:hidden"
                    aria-label="Tutup menu"
                >
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            {{-- MENU NAVIGASI --}}
            <nav class="flex-1 space-y-1.5 overflow-y-auto px-4 py-6" aria-label="Navigasi admin">
                <a
                    href="{{ route('admin.dashboard') }}"
                    class="group flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-semibold transition {{ request()->routeIs('admin.dashboard') ? 'bg-[#E87532] text-white shadow-sm' : 'text-white/80 hover:bg-white/10 hover:text-white' }}"
                >
                    <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    <span>Dashboard</span>
                </a>

                <a
                    href="{{ route('admin.orders.index') }}"
                    class="group flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-semibold transition {{ request()->routeIs('admin.orders.*') ? 'bg-[#E87532] text-white shadow-sm' : 'text-white/80 hover:bg-white/10 hover:text-white' }}"
                >
                    <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                    </svg>
                    <span>Pesanan</span>
                </a>

                <div class="pt-4">
                    <p class="px-4 text-[11px] font-bold uppercase tracking-wider text-white/50">Produk & Stok</p>
                </div>

                <a
                    href="{{ route('admin.products.index') }}"
                    class="group flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-semibold transition {{ request()->routeIs('admin.products.*') ? 'bg-[#E87532] text-white shadow-sm' : 'text-white/80 hover:bg-white/10 hover:text-white' }}"
                >
                    <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                    </svg>
                    <span>Produk</span>
                </a>

                <a
                    href="{{ route('admin.categories.index') }}"
                    class="group flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-semibold transition {{ request()->routeIs('admin.categories.*') ? 'bg-[#E87532] text-white shadow-sm' : 'text-white/80 hover:bg-white/10 hover:text-white' }}"
                >
                    <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                    </svg>
                    <span>Kategori</span>
                </a>

                <a
                    href="{{ route('admin.ratings.index') }}"
                    class="group flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-semibold transition {{ request()->routeIs('admin.ratings.*') ? 'bg-[#E87532] text-white shadow-sm' : 'text-white/80 hover:bg-white/10 hover:text-white' }}"
                >
                    <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m12 3 2.7 5.47 6.03.88-4.36 4.25 1.03 6.01L12 16.77l-5.4 2.84 1.03-6.01L3.27 9.35l6.03-.88L12 3z"/>
                    </svg>
                    <span>Rating Pelanggan</span>
                </a>

                <div class="flex items-center justify-between rounded-xl px-4 py-2.5 text-sm font-medium text-white/40 cursor-not-allowed select-none">
                    <div class="flex items-center gap-3">
                        <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                        <span>Pelanggan</span>
                    </div>
                    <span class="rounded bg-white/10 px-1.5 py-0.5 text-[10px] font-semibold text-white/50">Segera</span>
                </div>

                <div class="pt-4">
                    <p class="px-4 text-[11px] font-bold uppercase tracking-wider text-white/50">Pengaturan & Audit</p>
                </div>

                <a
                    href="{{ route('admin.audit.index') }}"
                    class="group flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-semibold transition {{ request()->routeIs('admin.audit.*') ? 'bg-[#E87532] text-white shadow-sm' : 'text-white/80 hover:bg-white/10 hover:text-white' }}"
                >
                    <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 12h6m-6 4h6"/>
                    </svg>
                    <span>Riwayat Audit</span>
                </a>

                <div class="pt-4">
                    <p class="px-4 text-[11px] font-bold uppercase tracking-wider text-white/50">Toko</p>
                </div>

                <a
                    href="{{ route('home') }}"
                    target="_blank"
                    class="group flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-medium text-white/80 transition hover:bg-white/10 hover:text-white"
                >
                    <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                    </svg>
                    <span>Lihat Website Toko</span>
                </a>
            </nav>

            {{-- USER PROFILE & LOGOUT FOOTER --}}
            <div class="border-t border-white/10 p-4">
                <div class="flex items-center justify-between gap-3 rounded-xl bg-black/20 p-3">
                    <div class="min-w-0 flex-1">
                        <p class="truncate text-sm font-bold text-white">{{ auth('admin')->user()->name }}</p>
                        <p class="truncate text-xs text-white/60">{{ auth('admin')->user()->email }}</p>
                    </div>

                    <form action="{{ route('admin.logout') }}" method="POST" class="shrink-0">
                        @csrf
                        <button
                            type="submit"
                            title="Keluar"
                            class="rounded-lg p-2 text-white/70 transition hover:bg-white/10 hover:text-white"
                        >
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        {{-- AREA KONTEN UTAMA --}}
        <div class="flex flex-1 flex-col min-w-0 overflow-hidden">
            {{-- TOPBAR ADMIN --}}
            <header class="flex h-16 shrink-0 items-center justify-between border-b border-orange-100 bg-white px-4 sm:px-6 lg:px-8">
                <div class="flex items-center gap-3">
                    <button
                        type="button"
                        @click="sidebarOpen = true"
                        class="rounded-lg p-2 text-gray-600 hover:bg-gray-100 lg:hidden"
                        aria-label="Buka navigasi"
                    >
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>

                    <span class="text-sm font-semibold text-pesona-dark sm:text-base">
                        @yield('header_title', 'Admin Dashboard')
                    </span>
                </div>

                <div class="flex items-center gap-3">
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700">
                        <span class="h-2 w-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        Admin Aktif
                    </span>
                </div>
            </header>

            {{-- MAIN VIEWPORT --}}
            <main class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8">
                {{-- FLASH NOTIFIKASI --}}
                @if (session('success'))
                    <div class="mb-6 flex items-center gap-3 rounded-2xl bg-emerald-50 border border-emerald-200 p-4 text-sm font-medium text-emerald-800">
                        <svg class="h-5 w-5 shrink-0 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        <span>{{ session('success') }}</span>
                    </div>
                @endif

                @if (session('error'))
                    <div class="mb-6 flex items-center gap-3 rounded-2xl bg-red-50 border border-red-200 p-4 text-sm font-medium text-red-800">
                        <svg class="h-5 w-5 shrink-0 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                        <span>{{ session('error') }}</span>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="mb-6 rounded-2xl bg-red-50 border border-red-200 p-4 text-sm text-red-800">
                        <p class="font-semibold">Terjadi kesalahan pada input:</p>
                        <ul class="mt-2 list-inside list-disc space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>
</body>
</html>
