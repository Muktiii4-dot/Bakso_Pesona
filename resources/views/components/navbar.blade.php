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
                    alt="Logo"
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
                </div>
            </div>
        </div>
    </div>
</header>