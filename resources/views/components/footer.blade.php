<footer class="bg-[#7F1D1D] text-white">
    <div class="mx-auto max-w-7xl px-6 py-14 lg:px-8">
        <div class="grid gap-10 md:grid-cols-2 lg:grid-cols-4">

            // Brand //
            <div>

                <img 
                    src="{{ asset('images/logo_footer.svg') }}"
                    alt="Logo Footer"
                    class="h-16 w-auto"
                >

                <p class="mt-5 max-w-md leading-7 text-white/70">
                    Menghadirkan sajian bakso yang lezat,
                    hangat, dan dibuat untuk memberikan
                    pengalaman makan yang berkesan.
                </p>

            </div>

            // Navigation //
            <div>
                <h3 class="font-bold">Navigasi</h3>

                <ul class="mt-4 space-y-3 text-sm text-white/70">
                    <li>
                        <a href="{{ url('/') }}" class="transition hover:text-[#e87532]">Home</a>
                    </li>

                    <li>
                        <a href="#menu" class="transition hover:text-[#e87532]">Menu</a>
                    </li>

                    <li>
                        <a href="#about" class="transition hover:text-[#e87532]">Tentang Kami</a>
                    </li>

                    <li>
                        <a href="#contact" class="transition hover:text-[#e87532]">Kontak</a>
                    </li>
                </ul>

            </div>

            // Contact //
            <div id="contact">
                <h3 class="font-bold">Kontak</h3>

                <ul class="mt-4 space-y3 text-sm text-white/70">
                    <li>Senin -Minggu</li>
                    <li>11:00 - 20:00</li>
                    <li>WhatsApp: +62 878-2328-4676</li>
                </ul>

            </div>

        </div>

        <div class="mt-12 border-t border-white/10 pt-6 text-center text-sm text-white/50">
            &copy; {{ date('Y') }} Bakso Pesona. All rights reserved.
        </div>
    </div>
</footer>