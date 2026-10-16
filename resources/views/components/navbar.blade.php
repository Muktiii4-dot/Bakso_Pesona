<<<<<<< HEAD
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
=======
<header class="sticky top-0 z-50 bg-[#7F1D1D] shadow-lg">
    <div class="mx-auto max-w-7xl px-5 lg:px-8">

        <div class="flex h-[72px] items-center justify-between">

            {{-- Logo --}}
            <a
                href="{{ url('/') }}"
                class="flex shrink-0 items-center"
            >
                <img 
                    src="{{ asset('images/logo_navbar.svg') }}"
                    alt="Logo Navbar"
                    class="h-12 w-auto"
                >
            </a>

            {{-- Navigation --}}
            <nav class="hidden items-center gap-8 lg:flex">

                <a 
                    href="{{ url('/') }}"
                    class="text-sm font-medium text-white transition hover:text-[#E87532]"    
                >Home</a>

                <a 
                    href="#tentang"
                    class="text-sm font-medium text-white transition hover:text-[#E87532]"    
                >Tentang</a>

                <a 
                    href="#produk"
                    class="text-sm font-medium text-white transition hover:text-[#E87532]"    
                >Produk</a>

                <a 
                    href="#kontak"
                    class="text-sm font-medium text-white transition hover:text-[#E87532]"    
                >Kontak</a>

            </nav>

            {{-- Search --}}
            <div class="hidden lg:flex">

                <div class="flex h-10 w-40 items-center rounded-full bg-white px-4">

                    <svg
                        class="mr-2 h-4 w- text-gray-400"
                        fill="none"
                        stroke="currentColor"
                        viewbox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M15.75 6.75a3"/>
                    </svg>

                    <input
                        type="text"
                        placeholder="Cari produk..."
                        class="w-full border-0 bg-transparent text-xs outline-none"
                    >
                </div>

            </div>

            {{-- Icons --}}
            <div class="hidden items-center gap-4 lg:flex">

                {{-- User Akun --}}
                <button
                    type="button"
                    class="text-white transition hover:text-[#E87532]"
                >
                    <svg
                        class="h-5 w-5"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M15.75 6.75a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.5 20.25a7.5 7.5 0 0 1 15 0"
                        />
                    </svg>
                </button>

                {{-- Keranjang --}}
                <button
                    type="button"
                    class="relative text-white transition hover:text-[#E87532]"
                >
                    <svg
                        class="h-5 w-5"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13 5.4 5M7 13l-2 2h12m-9 4a1 1 0 1 1-2 0m10 0a1 1 0 1 1-2 0"
                        />
                    </svg>

                    <span class="absolute -right-2 -top-2 flex h-4 w-4 items-center justify-center rounded-full bg-[#E87532] text-[9px] font-bold text-white"> 0 </span>
                </button>

            </div>

            {{-- Mobile --}}
            <button
                type="button"
                class="text-white lg:hidden"
            >
                <svg
                    class="h-6 w-6"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M4 6h16M4 18h16"
                    />
                </svg>
            </button>

        </div>
        
>>>>>>> b6e12ae2d658d726b7aeb6d4674d9599852860b2
    </div>
</header>