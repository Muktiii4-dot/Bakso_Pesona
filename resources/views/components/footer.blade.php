
<footer id="kontak" class="relative isolate overflow-hidden bg-[#74191B] text-white">

    {{-- Dekorasi latar belakang --}}
    <div
        aria-hidden="true"
        class="pointer-events-none absolute -right-20 -top-24 -z-10 h-64 w-64 rounded-full bg-[#B83B25]/25 blur-3xl sm:h-80 sm:w-80"
    ></div>

    <div
        aria-hidden="true"
        class="pointer-events-none absolute -bottom-20 -left-20 -z-10 h-56 w-56 rounded-full bg-[#E87532]/10 blur-3xl"
    ></div>

    {{-- Dekorasi lengkungan di sudut kanan bawah --}}
    <div
        aria-hidden="true"
        class="pointer-events-none absolute -bottom-20 -right-12 -z-10 h-36 w-72 rotate-[-12deg] rounded-[50%] bg-gradient-to-r from-[#B83B25] to-[#E87532] opacity-90 sm:-bottom-24 sm:-right-8 sm:h-44 sm:w-96"
    ></div>

    {{-- Konten utama footer --}}
    <div class="mx-auto max-w-7xl px-5 pb-10 pt-12 sm:px-8 sm:pt-16 lg:px-10 lg:pb-12">

        <div class="grid grid-cols-1 gap-10 sm:grid-cols-2 sm:gap-x-8 lg:grid-cols-12 lg:gap-x-10 lg:gap-y-12">

            {{-- Kolom 1: Identitas merek --}}
            <div class="lg:col-span-4">

                <a
                    href="{{ route('home') }}"
                    aria-label="Bakso Pesona - Beranda"
                    class="relative flex h-24 w-48 shrink-0 items-center"
                >
                    <img
                        src="{{ asset('images/logo_footer.svg') }}"
                        alt="Bakso Pesona"
                        class="absolute left-0 top-1/2 h-[250px] w-auto max-w-none -translate-y-1/2 object-contain"
                        loading="lazy"
                    >
                </a>

                <p class="mt-5 max-w-sm text-sm leading-7 text-white/75">
                    Bakso lezat, rasa tak terlupakan. Sajian hangat untuk
                    menemani momen bersama orang tersayang.
                </p>

                {{-- Media sosial --}}
                <div class="mt-6 flex flex-wrap items-center gap-3">

                    {{-- Ganti tanda # dengan URL akun resmi jika sudah tersedia --}}
                    <a
                        href="#"
                        aria-label="Instagram Bakso Pesona"
                        class="flex h-10 w-10 items-center justify-center rounded-full border border-white/15 bg-white/5 text-white transition hover:border-[#E87532] hover:bg-[#E87532]"
                    >
                        <svg class="h-4 w-4" fill="none" stroke="currentColor"
                             viewBox="0 0 24 24" aria-hidden="true">
                            <rect x="3" y="3" width="18" height="18" rx="5"
                                  stroke-width="1.7"/>
                            <circle cx="12" cy="12" r="4"
                                    stroke-width="1.7"/>
                            <circle cx="17.5" cy="6.5" r="1"
                                    fill="currentColor" stroke="none"/>
                        </svg>
                    </a>

                    <a
                        href="#"
                        aria-label="TikTok Bakso Pesona"
                        class="flex h-10 w-10 items-center justify-center rounded-full border border-white/15 bg-white/5 text-xs font-black text-white transition hover:border-[#E87532] hover:bg-[#E87532]"
                    >
                        TK
                    </a>

                    <a
                        href="#"
                        aria-label="YouTube Bakso Pesona"
                        class="flex h-10 w-10 items-center justify-center rounded-full border border-white/15 bg-white/5 text-white transition hover:border-[#E87532] hover:bg-[#E87532]"
                    >
                        <svg class="h-4 w-4" fill="currentColor"
                             viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M23 7.2a3 3 0 0 0-2.1-2.1C19 4.6 12 4.6 12 4.6s-7 0-8.9.5A3 3 0 0 0 1 7.2 31 31 0 0 0 .5 12 31 31 0 0 0 1 16.8a3 3 0 0 0 2.1 2.1c1.9.5 8.9.5 8.9.5s7 0 8.9-.5a3 3 0 0 0 2.1-2.1 31 31 0 0 0 .5-4.8 31 31 0 0 0-.5-4.8ZM9.5 15.4V8.6l6 3.4-6 3.4Z"/>
                        </svg>
                    </a>

                </div>
            </div>

            {{-- Kolom 2: Menu navigasi --}}
            <div class="lg:col-span-2">

                <h2 class="text-sm font-extrabold uppercase tracking-[0.16em] text-white">
                    Menu
                </h2>

                <ul class="mt-5 space-y-3 text-sm text-white/75">

                    <li>
                        <a href="{{ route('home') }}"
                           class="transition hover:text-[#FFB45E]">
                            Home
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('home') }}#tentang"
                           class="transition hover:text-[#FFB45E]">
                            Tentang Kami
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('home') }}#produk"
                           class="transition hover:text-[#FFB45E]">
                            Produk
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('home') }}#kontak"
                           class="transition hover:text-[#FFB45E]">
                            Kontak
                        </a>
                    </li>

                </ul>
            </div>

            {{-- Kolom 3: Layanan --}}
            <div class="lg:col-span-3">

                <h2 class="text-sm font-extrabold uppercase tracking-[0.16em] text-white">
                    Layanan
                </h2>

                <ul class="mt-5 space-y-3 text-sm text-white/75">

                    <li>
                        <a href="{{ route('products.index') }}"
                           class="transition hover:text-[#FFB45E]">
                            Pilih Produk
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('products.index') }}"
                           class="transition hover:text-[#FFB45E]">
                            Informasi Pemesanan
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('home') }}#kontak"
                           class="transition hover:text-[#FFB45E]">
                            Bantuan
                        </a>
                    </li>

                </ul>
            </div>

            {{-- Kolom 4: Kontak --}}
            <div class="lg:col-span-3">

                <h2 class="text-sm font-extrabold uppercase tracking-[0.16em] text-white">
                    Hubungi Kami
                </h2>

                <div class="mt-5 space-y-5 text-sm text-white/75">

                    <a
                        href="https://wa.me/6287823284676"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="group flex items-start gap-3 transition hover:text-[#FFB45E]"
                    >
                        <svg class="mt-0.5 h-5 w-5 shrink-0 text-[#FFB45E]"
                             fill="none" stroke="currentColor"
                             viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M21 11.5a8.4 8.4 0 0 1-12.4 7.4L3 20l1.2-5.3A8.5 8.5 0 1 1 21 11.5Z"
                                  stroke-width="1.6"
                                  stroke-linecap="round"
                                  stroke-linejoin="round"/>
                            <path d="M9 9a5 5 0 0 0 6 6"
                                  stroke-width="1.6"
                                  stroke-linecap="round"/>
                        </svg>

                        <span class="break-words">
                            WhatsApp<br>
                            <span class="mt-1 inline-block text-white">
                                +62 878-2328-4676
                            </span>
                        </span>
                    </a>

                    <a
                        href="mailto:hello@baksopesona.id"
                        class="group flex items-start gap-3 transition hover:text-[#FFB45E]"
                    >
                        <svg class="mt-0.5 h-5 w-5 shrink-0 text-[#FFB45E]"
                             fill="none" stroke="currentColor"
                             viewBox="0 0 24 24" aria-hidden="true">
                            <rect x="3" y="5" width="18" height="14" rx="2"
                                  stroke-width="1.6"/>
                            <path d="m4 7 8 6 8-6"
                                  stroke-width="1.6"
                                  stroke-linecap="round"
                                  stroke-linejoin="round"/>
                        </svg>

                        <span class="break-all">
                            hello@baksopesona.id
                        </span>
                    </a>

                    <div class="flex items-start gap-3">
                        <svg class="mt-0.5 h-5 w-5 shrink-0 text-[#FFB45E]"
                             fill="none" stroke="currentColor"
                             viewBox="0 0 24 24" aria-hidden="true">
                            <circle cx="12" cy="12" r="9"
                                    stroke-width="1.6"/>
                            <path d="M12 7v5l3 2"
                                  stroke-width="1.6"
                                  stroke-linecap="round"
                                  stroke-linejoin="round"/>
                        </svg>

                        <p class="leading-6">
                            Senin – Sabtu<br>
                            <span class="text-white">
                                11.00 – 20.00 WIB
                            </span>
                        </p>
                    </div>

                </div>
            </div>

        </div>
    </div>

    {{-- Copyright --}}
    <div class="relative border-t border-white/10">

        <div class="mx-auto flex max-w-7xl flex-col gap-3 px-5 py-5 text-xs text-white/60 sm:px-8 md:flex-row md:items-center md:justify-between lg:px-10">

            <p>
                &copy; {{ date('Y') }} Bakso Pesona. Hak cipta dilindungi.
            </p>

            <p class="max-w-md leading-5 md:text-right">
                Dibuat dengan sepenuh hati untuk para pencinta bakso.
            </p>

        </div>
    </div>

</footer>
