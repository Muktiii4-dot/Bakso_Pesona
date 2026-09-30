<footer class="bg-[#7F1D1D] text-white">

    <div class="mx-auto max-w-7xl px-5 py-14 lg:px-8">

        <div class="grid gap-10 md:grid-cols-2 lg:grid-cols-4">

            {{-- Brands --}}
            <div>

                <img 
                    src="{{ asset('images/logo_footer.svg') }}"
                    alt="Logo Footer"
                    class="h-16 w-auto"
                >

                <p class="mt-5 max-w-sm text-sm leading-7 text-white/70">
                    Bakso lezat dengan rasa yang berkesan.
                    Menghadirkan sajian berkulaitas untuk
                    menemani setiap momen anda.
                </p>

                {{-- Social Media --}}
                <div class="mt-6 flex ga-3">

                    <a 
                        href="#"
                        class="flex h-9 w-9 items-center justify-center rounded-full bg-white/10 transition hover:bg-[#E87532]"
                    >
                        Instagram
                    </a>

                    <a 
                        href="#"
                        class="flex h-9 w-9 items-center justify-center rounded-full bg-white/10 transition hover:bg-[#E87532]"
                    >
                        TikTok
                    </a>

                    <a 
                        href="#"
                        class="flex h-9 w-9 items-center justify-center rounded-full bg-white/10 transition hover:bg-[#E87532]"
                    >
                        Facebook
                    </a>

                </div>

            </div>

            {{-- Menu --}}
            <div>

                <h3 class="mb-5 font-bold">Menu</h3>

                <ul class="space-y-3 text-sm text-white/70">

                    <li>
                        <a href="{{ url('/' }}" class="hover:text-white">Home</a>
                    </li>

                    <li>
                        <a href="{{ url('/' }}" class="hover:text-white">Tentang Kami</a>
                    </li>

                    <li>
                        <a href="{{ url('/' }}" class="hover:text-white">Produk</a>
                    </li>

                    <li>
                        <a href="{{ url('/' }}" class="hover:text-white">Kontak</a>
                    </li>
                    
                </ul>

            </div>

            {{-- Layanan --}}
            <div>

                <h3 class="mb-5 font-bold">Layanan</h3>

                <ul class="space-y-3 text-sm text-white/70">

                    <li>Pengiriman</li>
                    <li>Pembayaran</li>
                    <li>FAQ</li>
                    <li>Syarat & Ketentuan</li>

                </ul>

            </div>

            {{-- Kontak --}}
            <div>

                <h3 class="mb-5 font-bold">Kontak</h3>

                <ul class="space-y-4 text-sm text-white/70">

                    <li>WhatsApp: +62 878-2328-4676</li>
                    <li>hello@baksopesona.id</li>
                    <li>Senin - Minggu
                        <br>
                        11:00 - 20:00 WIB
                    </li>

                </ul>

            </div>

        </div>

    </div>

    {{-- Copyright --}}
    <div class="border-t border-white/10">

        <div class="mx-auto max-w-7xl px-5 text-center text-xs text-white/50 lg:px-8">
            © {{ date('Y') }} Bakso Pesona.
            Semua Hak Dilindungi.
        </div>
        
    </div>

</footer>