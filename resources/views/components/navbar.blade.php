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
        
    </div>
</header>