<header class="sticky top-0 z-50 norder-b border-white/10 bg-[#74191B]/95 text-white shadow-sm backdrop-blur-md" x-data="{ open: false }">
    <div class="mx-auto max-w-7xl px-4 ms:px-6 lg:px-10">

    {{-- BARIS UTAMA NAVBAR --}}
    <div class="flex h-[84px] items-center justify-between gap-6">

        {{-- LOGO --}}
        <a
            href="{{ route('home') }}"
            class="relative flex h-full w-[180px] shrink-0 items-center"
            aria-label="Bakso Pesona - Beranda"
        >
            <img
                src="{{ asset('images/logo_navbar.svg') }}"
                alt="Bakso Pesona"
                class="absolute right-15 top-1/2 h-[250px] w-auto max-w-none -translate-y-1/2 object-contain"
            >
        </a>

        {{-- MENU NAVBAR --}}
        <nav
            class="relative left-8 hidden shrink-0 items-center gap-7 whitespace-nowrap lg:flex"
            aria-label="Navigasi utama"
        >
            <a href="{{ route('home') }}"
            class="text-xs font-bold text-white/90 transition hover:text-[#FFB45E]">
                Home
            </a>

            <a href="{{ route('home') }}#tentang"
            class="text-xs font-semibold text-white/80 transition hover:text-[#FFB45E]">
                Tentang
            </a>

            <a href="{{ route('home') }}#produk"
            class="text-xs font-semibold text-white/80 transition hover:text-[#FFB45E]">
                Produk
            </a>

            <a href="{{ route('home') }}#kontak"
            class="text-xs font-semibold text-white/80 transition hover:text-[#FFB45E]">
                Kontak
            </a>
        </nav>

        {{-- PENCARIAN --}}
        <form
            action="{{ route('products.index') }}"
            method="GET"
            role="search"
            class="relative left-50 hidden h-10 w-48 shrink-0 items-center gap-2 rounded-full border border-white/30 bg-white px-4 xl:flex"
        >
            <svg class="h-4 w-4 shrink-0 text-[#9A8179]"
                fill="none" stroke="currentColor" viewBox="0 0 24 24"
                aria-hidden="true">
                <circle cx="11" cy="11" r="7" stroke-width="1.8"/>
                <path d="m16 16 4 4" stroke-width="1.8" stroke-linecap="round"/>
            </svg>

            <input
                type="search"
                name="q"
                value="{{ request('q') }}"
                placeholder="Cari produk..."
                class="w-full min-w-0 border-0 bg-transparent text-xs text-[#351B18] outline-none placeholder:text-[#A99993] focus:ring-0"
            >
        </form>

        {{-- IKON AKUN DAN KERANJANG --}}
        <div class="hidden shrink-0 items-center gap-3 lg:flex">
            {{-- Pindahkan kedua elemen <a> akun dan keranjang
                dari kode navbar lamamu ke dalam div ini. --}}
        </div>

        {{-- TOMBOL MOBILE --}}
        <button
            type="button"
            @click="open = !open"
            class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-xl border border-white/15 text-white transition hover:bg-white/10 lg:hidden"
            :aria-expanded="open.toString()"
            aria-label="Buka menu navigasi"
        >
            <svg x-show="!open" class="h-5 w-5"
                fill="none" stroke="currentColor" viewBox="0 0 24 24"
                aria-hidden="true">
                <path d="M4 6h16M4 12h16M4 18h16"
                    stroke-width="1.8" stroke-linecap="round"/>
            </svg>

            <svg x-show="open" x-cloak class="h-5 w-5"
                fill="none" stroke="currentColor" viewBox="0 0 24 24"
                aria-hidden="true">
                <path d="m6 6 12 12M18 6 6 18"
                    stroke-width="1.8" stroke-linecap="round"/>
            </svg>
        </button>
    </div>


        {{-- Menu Mobile --}}
        <div
            x-show="open"
            x-transition
            x-cloak
            class="border-t border-white/10 pc-5 pt-4 lg:hidden"
        >
            {{-- Pencarian Mobile --}}
            <form 
                action="{{ route('products.index') }}"
                method="GET"
                role="search"
                class="flex h-11 items-center gap-2 rounded-xl bg-white px-3"
            >
                <svg
                    class="h-4 w-4 shrink-0 text-[#9A8179]"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                    aria-hidden="true"
                >
                    <circle cx="11" cy="11" r="7" stroke-width="1.8"/>
                    <path
                        d="m16 16 4 4"
                        stroke-width="1.8"
                        stroke-linecap="round"
                    />
                </svg>

                <input 
                    type="search"
                    name="q"
                    value="{{ request('q') }}"
                    placeholder="Cari produk..."
                    class="w-full min-w-0 border-0 bg-transparent text-sm text-[#351B18] outline-none focus:ring-0"
                >

                <button
                    class="shrink-0 text-xs font-bold text-[#B83B25]"
                    type="submit"
                >
                    Cari
                </button>
            </form>

            {{-- Navigasi Mobile --}}
            <nav
                class="mt-3 grid gap-1"
                aria-label="Navigasi Mobile"
            >
                <a 
                    href="{{ route('home') }}"
                    @click="open = flase"
                    class="rounded-xl px-3 py-3 text-sm font-semibold text-white/90 transition hover:bg-white/10"
                >
                    Home
                </a>

                <a 
                    href="{{ route('home') }}#produk"
                    @click="open = flase"
                    class="rounded-xl px-3 py-3 text-sm font-semibold text-white/90 transition hover:bg-white/10"
                >
                    produk
                </a>

                <a 
                    href="{{ route('home') }}#tentang"
                    @click="open = flase"
                    class="rounded-xl px-3 py-3 text-sm font-semibold text-white/90 transition hover:bg-white/10"
                >
                    Tentang
                </a>

                <a 
                    href="{{ route('home') }}#kontak"
                    @click="open = flase"
                    class="rounded-xl px-3 py-3 text-sm font-semibold text-white/90 transition hover:bg-white/10"
                >
                    Kontak
                </a>
            </nav>

            {{-- Tombol Lihat Menu --}}
            <a 
                href="{{ route('products.index') }}"
                class="mt-3 flex w-full items-center justify-center gap-2 rounded-xl bg-[#E87532] px-4 py-3 text-sm font-extrabold text-white transition hover:bg-[#F28A42]"
            >
                Lihat Menu
                <span aria-hidden="true">→</span>
            </a>
        </div>
    </div>
</header>