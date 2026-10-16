@extends('layouts.app')

@section('title', 'Bakso Pesona — Rasa Lezat Bikin Nagih!')

@section('content')

{{-- HERO --}}
<section id="beranda" class="relative isolate overflow-hidden bg-[#74191B] text-white">
    <div class="pointer-events-none absolute -left-20 top-20 h-64 w-64 rounded-full bg-[#E87532]/30 blur-3xl"></div>
    <div class="pointer-events-none absolute -right-24 bottom-0 h-80 w-80 rounded-full bg-[#F8C75E]/20 blur-3xl"></div>
    <div class="pointer-events-none absolute -left-12 bottom-8 hidden h-40 w-40 rotate-12 rounded-[3rem] border-[18px] border-[#E87532]/25 lg:block"></div>

    <div class="relative mx-auto grid min-h-[540px] max-w-7xl items-center gap-10 px-5 py-14 sm:px-8 lg:grid-cols-[0.9fr_1.1fr] lg:gap-8 lg:px-10 lg:py-16">
        <div class="relative z-10 max-w-xl">
            <span class="inline-flex items-center gap-2 rounded-full border border-[#FFB45E]/35 bg-white/5 px-4 py-2 text-xs font-bold text-[#FFE0B2]">
                <span class="h-2 w-2 rounded-full bg-[#FFB45E]"></span>
                Bakso lezat, rasa tak terlupakan
            </span>

            <h1 class="mt-6 text-4xl font-black leading-[1.04] tracking-tight sm:text-5xl lg:text-6xl">
                Rasa Lezat<br>
                <span class="text-[#FFB45E]">Bikin Nagih!</span>
            </h1>

            <p class="mt-5 max-w-md text-sm leading-7 text-white/85 sm:text-base">
                Sajian bakso dengan kuah gurih dan pilihan menu untuk menemani momen makan bersama orang tersayang.
            </p>

            <div class="mt-7 flex flex-wrap items-center gap-3">
                <a href="{{ route('products.index') }}"
                   class="inline-flex items-center gap-3 rounded-full bg-[#E87532] px-6 py-3.5 text-sm font-extrabold text-white shadow-lg shadow-black/20 transition hover:-translate-y-0.5 hover:bg-[#F28A42] focus:outline-none focus:ring-2 focus:ring-white">
                    Pesan Sekarang <span aria-hidden="true">→</span>
                </a>

                <a href="#produk"
                   class="rounded-full border border-white/35 px-5 py-3.5 text-sm font-bold text-white transition hover:bg-white/10">
                    Jelajahi Menu
                </a>
            </div>

            <div class="mt-8 flex flex-wrap gap-x-5 gap-y-2 text-xs font-medium text-white/80">
                <span><span class="mr-2 text-[#FFB45E]">✦</span>Rasa khas pilihan</span>
                <span><span class="mr-2 text-[#FFB45E]">✦</span>Dibuat dengan sepenuh hati</span>
            </div>
        </div>

        <div class="relative mx-auto w-full max-w-[620px] lg:ml-auto">
            <div class="absolute inset-6 rounded-full bg-[#E87532]/30 blur-3xl"></div>

            <div class="absolute -left-2 top-10 z-20 hidden -rotate-12 rounded-2xl bg-[#B83B25] px-4 py-3 shadow-xl sm:block">
                <span class="block text-[10px] font-bold uppercase tracking-[0.18em] text-white/75">Racikan khas</span>
                <span class="mt-1 block text-sm font-black">Bakso Malang Asli!</span>
            </div>

            <div class="relative overflow-hidden rounded-[2rem] border-[6px] border-[#FFE1C6] shadow-2xl shadow-black/30 sm:rounded-[2.5rem]">
                <img
                    src="https://images.unsplash.com/photo-1569718212165-3a8278d5f624?auto=format&fit=crop&w=1200&q=85"
                    alt="Foto sementara hidangan berkuah"
                    class="h-[300px] w-full object-cover sm:h-[390px] lg:h-[430px]"
                    fetchpriority="high"
                >
            </div>

            <div class="absolute -bottom-4 right-2 z-20 rotate-3 rounded-2xl border border-white/20 bg-[#FFF4E8] px-4 py-3 text-[#74191B] shadow-xl sm:right-0">
                <span class="block text-xs font-black sm:text-sm">♡ Favorit pecinta bakso</span>
                <span class="mt-1 block text-[10px] text-[#7B5550]">Temukan menu pilihanmu</span>
            </div>
        </div>
    </div>

    <svg class="absolute bottom-0 left-0 h-9 w-full text-[#FFF8F1] sm:h-14"
         viewBox="0 0 1440 90" preserveAspectRatio="none" aria-hidden="true">
        <path fill="currentColor" d="M0,42 C180,95 330,76 500,48 C690,14 790,85 970,58 C1140,32 1270,25 1440,45 L1440,90 L0,90 Z"></path>
    </svg>
</section>

{{-- 1. PENGANTAR SINGKAT --}}
<section class="bg-[#FFF8F1] px-5 py-14 sm:px-8 sm:py-20 lg:px-10">
    <div class="mx-auto grid max-w-6xl items-center gap-8 rounded-[2rem] border border-[#F2DED0] bg-white/70 px-6 py-8 sm:px-10 sm:py-10 md:grid-cols-[auto_1fr] md:gap-10">
        <div class="mx-auto flex h-24 w-24 items-center justify-center rounded-[2rem] bg-[#FFE7D5] text-5xl md:h-28 md:w-28" aria-hidden="true">
            🍜
        </div>

        <div>
            <p class="text-xs font-extrabold uppercase tracking-[0.22em] text-[#B83B25]">
                Selamat datang di Bakso Pesona
            </p>

            <h2 class="mt-3 text-2xl font-black leading-tight text-[#351B18] sm:text-3xl">
                Semangkuk kehangatan untuk setiap momen.
            </h2>

            <p class="mt-4 max-w-3xl text-sm leading-7 text-[#715D56] sm:text-base">
                Temukan sajian bakso dan menu pendamping yang cocok untuk dinikmati sendiri,
                bersama keluarga, maupun teman. Jelajahi pilihan menu Bakso Pesona dan temukan favoritmu.
            </p>
        </div>
    </div>
</section>

{{-- 2. TENTANG KAMI --}}
<section id="tentang" class="scroll-mt-24 bg-[#FFF8F1] px-5 pb-16 sm:px-8 sm:pb-20 lg:px-10">
    <div class="mx-auto grid max-w-6xl items-center gap-9 lg:grid-cols-2 lg:gap-14">
        <div class="relative order-2 lg:order-1">
            <div class="absolute -left-3 -top-3 h-24 w-24 rounded-tl-[2rem] border-l-4 border-t-4 border-[#E87532]"></div>

            <img
                src="https://images.unsplash.com/photo-1555396273-367ea4eb4db5?auto=format&fit=crop&w=1000&q=85"
                alt="Foto sementara suasana usaha kuliner"
                class="relative h-[280px] w-full rounded-[2rem] object-cover shadow-lg sm:h-[360px]"
                loading="lazy"
            >

            <div class="absolute -bottom-5 right-4 rounded-2xl bg-[#74191B] px-5 py-4 text-white shadow-xl sm:right-8">
                <span class="block text-sm font-black">Dari kami, untuk kamu</span>
                <span class="mt-1 block text-xs text-white/75">Rasa yang ingin dibagikan</span>
            </div>
        </div>

        <div class="order-1 lg:order-2">
            <p class="text-xs font-extrabold uppercase tracking-[0.22em] text-[#B83B25]">
                Tentang kami
            </p>

            <h2 class="mt-3 text-3xl font-black leading-tight text-[#351B18] sm:text-4xl">
                Setiap sajian punya cerita.
            </h2>

            <p class="mt-5 text-sm leading-7 text-[#715D56] sm:text-base">
                Bakso Pesona ingin menjadi bagian dari momen makan yang hangat dan menyenangkan.
                Melalui pilihan menu bakso dan sajian pendamping, kami ingin membantu pelanggan
                menemukan hidangan yang cocok untuk dinikmati bersama orang tersayang.
            </p>

            <p class="mt-4 text-sm leading-7 text-[#715D56] sm:text-base">
                Bagian ini nantinya dapat dilengkapi dengan cerita asli pemilik usaha,
                perjalanan Bakso Pesona, dan proses di balik produk setelah informasi tersebut tersedia.
            </p>

            <a href="{{ route('home') }}#tentang"
               class="mt-6 inline-flex items-center gap-2 rounded-full bg-[#74191B] px-6 py-3 text-sm font-bold text-white transition hover:bg-[#9A2824]">
                Kenali Cerita Kami <span aria-hidden="true">→</span>
            </a>
        </div>
    </div>
</section>

{{-- 3. BANNER INOVASI PRODUK --}}
<section id="bakso-instan" class="bg-[#FFF8F1] px-5 pb-16 sm:px-8 sm:pb-20 lg:px-10">
    <div class="relative mx-auto grid max-w-7xl items-center gap-6 overflow-hidden rounded-[2rem] bg-gradient-to-r from-[#B52B20] via-[#E44C2B] to-[#F07835] px-6 py-8 text-white shadow-lg sm:px-10 sm:py-10 lg:grid-cols-[1fr_0.9fr] lg:px-12 lg:py-12">
        <div class="pointer-events-none absolute -right-10 -top-16 h-52 w-52 rounded-full border-[28px] border-white/10"></div>

        <div class="relative z-10">
            <span class="inline-flex rounded-full border border-white/35 bg-white/10 px-3 py-1.5 text-[10px] font-extrabold uppercase tracking-[0.18em]">
                Inovasi Bakso Pesona
            </span>

            <h2 class="mt-4 max-w-xl text-3xl font-black leading-tight sm:text-4xl lg:text-5xl">
                Bakso Malang Instan
            </h2>

            <p class="mt-4 max-w-lg text-sm leading-7 text-white/90 sm:text-base">
                Kenali ide sajian Bakso Malang dalam bentuk paket instan yang direncanakan
                untuk dinikmati di rumah. Informasi isi paket, cara penyajian, dan ketersediaan
                akan ditampilkan setelah detail produk dikonfirmasi.
            </p>

            <a href="{{ route('products.index') }}"
               class="mt-6 inline-flex items-center gap-3 rounded-full bg-[#FFF4E8] px-6 py-3 text-sm font-extrabold text-[#8F241F] transition hover:-translate-y-0.5 hover:bg-white">
                Jelajahi Produk <span aria-hidden="true">→</span>
            </a>
        </div>

        <div class="relative mx-auto w-full max-w-md">
            <div class="absolute inset-5 rounded-full bg-[#74191B]/25 blur-2xl"></div>

            <img
                src="https://images.unsplash.com/photo-1569718212165-3a8278d5f624?auto=format&fit=crop&w=1000&q=85"
                alt="Foto sementara inspirasi sajian berkuah"
                class="relative h-56 w-full rounded-[1.5rem] border-4 border-white/30 object-cover shadow-2xl sm:h-72 lg:h-80"
                loading="lazy"
            >

            <span class="absolute -right-1 -top-3 rotate-6 rounded-full bg-[#F8C75E] px-4 py-3 text-center text-xs font-black leading-tight text-[#5A211C] shadow-lg sm:-right-3">
                Praktis<br>dinikmati*
            </span>
        </div>

        <p class="relative z-10 text-[10px] text-white/75 lg:col-span-2">
            *Konsep visual sementara; detail produk dan petunjuk penyajian perlu dikonfirmasi oleh pemilik usaha.
        </p>
    </div>
</section>

{{-- 4. KATEGORI PRODUK --}}
<section id="kategori" class="scroll-mt-24 bg-white px-5 py-14 sm:px-8 sm:py-16 lg:px-10">
    <div class="mx-auto max-w-7xl">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-xs font-extrabold uppercase tracking-[0.22em] text-[#B83B25]">
                    Pilih kategori favoritmu
                </p>

                <h2 class="mt-2 text-2xl font-black text-[#351B18] sm:text-3xl">
                    Kategori Produk
                </h2>

                <p class="mt-3 max-w-2xl text-sm leading-6 text-[#806A62]">
                    Dari bakso klasik hingga camilan, pilih kategori untuk menemukan menu yang sesuai dengan seleramu.
                </p>
            </div>

            <a href="{{ route('products.index') }}"
               class="text-sm font-extrabold text-[#B83B25] hover:text-[#E87532]">
                Lihat semua produk →
            </a>
        </div>

        <div class="mt-8 grid grid-cols-2 gap-3 sm:grid-cols-3 sm:gap-4 lg:grid-cols-5">
            @foreach([
                ['label' => 'Semua', 'value' => 'Semua', 'icon' => '✦'],
                ['label' => 'Bakso', 'value' => 'Bakso', 'icon' => '🍲'],
                ['label' => 'Mie', 'value' => 'Mie', 'icon' => '🍜'],
                ['label' => 'Cemilan', 'value' => 'Cemilan', 'icon' => '🥟'],
                ['label' => 'Paket', 'value' => 'Paket', 'icon' => '🎁'],
            ] as $category)
                <a
                    href="{{ route('products.index', $category['value'] === 'Semua' ? [] : ['category' => $category['value']]) }}"
                    class="group flex min-h-28 flex-col items-center justify-center gap-3 rounded-2xl border border-[#F1DED2] bg-[#FFFAF6] px-4 py-5 text-center transition duration-200 hover:-translate-y-1 hover:border-[#E87532] hover:bg-[#FFF0E5] hover:shadow-md"
                >
                    <span class="flex h-11 w-11 items-center justify-center rounded-2xl bg-[#FFE6D5] text-xl text-[#B83B25] transition group-hover:bg-[#E87532] group-hover:text-white" aria-hidden="true">
                        {{ $category['icon'] }}
                    </span>

                    <span class="text-sm font-extrabold text-[#49251F]">
                        {{ $category['label'] }}
                    </span>
                </a>
            @endforeach
        </div>
    </div>
</section>

{{-- 5. PRODUK PILIHAN --}}
<section id="produk" class="scroll-mt-24 bg-[#FFF8F1] px-5 py-14 sm:px-8 sm:py-16 lg:px-10">
    <div class="mx-auto max-w-7xl">
        <div class="flex items-end justify-between gap-4">
            <div>
                <p class="text-xs font-extrabold uppercase tracking-[0.22em] text-[#B83B25]">
                    Pilihan pelanggan
                </p>

                <h2 class="mt-2 text-2xl font-black text-[#351B18] sm:text-3xl">
                    Produk Pilihan Terbaik
                </h2>

                <p class="mt-3 max-w-2xl text-sm leading-6 text-[#806A62]">
                    Lihat beberapa pilihan menu Bakso Pesona dan temukan sajian yang ingin kamu nikmati berikutnya.
                </p>
            </div>

            <a href="{{ route('products.index') }}"
               class="shrink-0 pb-1 text-xs font-extrabold text-[#B83B25] transition hover:text-[#E87532] sm:text-sm">
                Lihat Semua →
            </a>
        </div>

        <div class="mt-8 grid grid-cols-2 gap-3 sm:grid-cols-2 sm:gap-5 lg:grid-cols-4">
            <x-product-card
                name="Bakso Malang Original"
                slug="bakso-malang-original"
                description="Bakso, tahu, siomay, mie, dan kuah gurih."
                :price="25000"
                image="https://images.unsplash.com/photo-1569718212165-3a8278d5f624?auto=format&fit=crop&w=700&q=80"
                badge="Favorit"
            />

            <x-product-card
                name="Bakso Pedas"
                slug="bakso-pedas"
                description="Kuah pedas dengan racikan bumbu khas."
                :price="25000"
                image="https://images.unsplash.com/photo-1563245372-f21724e3856d?auto=format&fit=crop&w=700&q=80"
            />

            <x-product-card
                name="Bakso Keju"
                slug="bakso-keju"
                description="Perpaduan rasa gurih dan isian keju."
                :price="30000"
                image="https://images.unsplash.com/photo-1547592180-85f173990554?auto=format&fit=crop&w=700&q=80"
            />

            <x-product-card
                name="Mie Bakso Jumbo"
                slug="mie-bakso-jumbo"
                description="Porsi mantap untuk menemani waktu makan."
                :price="35000"
                image="https://images.unsplash.com/photo-1565123409695-7b5ef63a7a6d?auto=format&fit=crop&w=700&q=80"
            />
        </div>

        <p class="mt-4 text-xs leading-5 text-[#8B7770]">
            Foto produk masih berupa gambar sementara dan dapat diganti dengan foto resmi Bakso Pesona.
        </p>
    </div>
</section>

{{-- 6. KEUNGGULAN KAMI --}}
<section class="bg-white px-5 py-14 sm:px-8 sm:py-16 lg:px-10">
    <div class="mx-auto max-w-7xl">
        <div class="mx-auto max-w-2xl text-center">
            <p class="text-xs font-extrabold uppercase tracking-[0.22em] text-[#B83B25]">
                Kenapa memilih Bakso Pesona?
            </p>

            <h2 class="mt-2 text-2xl font-black text-[#351B18] sm:text-3xl">
                Hal kecil yang membuat pengalaman lebih nyaman.
            </h2>

            <p class="mt-3 text-sm leading-6 text-[#806A62]">
                Kami ingin pengalaman memilih menu dan melakukan pemesanan terasa jelas, mudah, dan menyenangkan.
            </p>
        </div>

        <div class="mt-9 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            @foreach([
                ['icon' => '✦', 'title' => 'Pilihan Menu', 'desc' => 'Beragam pilihan menu untuk berbagai selera.'],
                ['icon' => '♡', 'title' => 'Dibuat dengan Perhatian', 'desc' => 'Setiap sajian diupayakan memberi pengalaman makan yang menyenangkan.'],
                ['icon' => '⌕', 'title' => 'Pemesanan Mudah', 'desc' => 'Jelajahi katalog dan temukan detail produk yang kamu inginkan.'],
                ['icon' => '✓', 'title' => 'Informasi Jelas', 'desc' => 'Detail produk dan harga ditampilkan agar mudah dipahami.'],
            ] as $feature)
                <article class="rounded-[1.5rem] border border-[#F1E2D8] bg-[#FFFAF6] p-6 transition hover:-translate-y-1 hover:shadow-lg">
                    <span class="flex h-14 w-14 items-center justify-center rounded-2xl bg-[#FFE6D5] text-2xl font-black text-[#B83B25]">
                        {{ $feature['icon'] }}
                    </span>

                    <h3 class="mt-5 text-base font-black text-[#49251F]">
                        {{ $feature['title'] }}
                    </h3>

                    <p class="mt-2 text-sm leading-6 text-[#806A62]">
                        {{ $feature['desc'] }}
                    </p>
                </article>
            @endforeach
        </div>
    </div>
</section>

{{-- 7. APA KATA MEREKA --}}
<section class="bg-[#FFF1E7] px-5 py-14 sm:px-8 sm:py-16 lg:px-10">
    <div class="mx-auto max-w-7xl">
        <div>
            <p class="text-xs font-extrabold uppercase tracking-[0.22em] text-[#B83B25]">
                Cerita pelanggan
            </p>

            <h2 class="mt-2 text-2xl font-black text-[#351B18] sm:text-3xl">
                Apa Kata Mereka?
            </h2>

            <p class="mt-3 max-w-2xl text-sm leading-6 text-[#806A62]">
                Pengalaman pelanggan dapat membantu orang lain mengenal Bakso Pesona.
            </p>
        </div>

        <div class="mt-8 grid gap-4 md:grid-cols-3">
            @foreach([
                ['name' => 'Rina Sari', 'text' => 'Baksonya enak banget, kuahnya gurih dan dagingnya terasa.'],
                ['name' => 'Andi Pratama', 'text' => 'Paketnya rapi dan rasanya sesuai ekspektasi.'],
                ['name' => 'Siti Rahma', 'text' => 'Harga dan pilihan menunya menarik untuk dicoba lagi.'],
            ] as $testimonial)
                <article class="rounded-[1.5rem] border border-[#F0DED2] bg-white p-6 shadow-sm">
                    <div class="flex items-center gap-3">
                        <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-[#74191B] text-lg font-black text-white">
                            {{ mb_substr($testimonial['name'], 0, 1) }}
                        </span>

                        <div>
                            <h3 class="text-sm font-black text-[#351B18]">
                                {{ $testimonial['name'] }}
                            </h3>

                            <p class="mt-1 text-xs text-[#8B7770]">
                                Contoh tampilan testimoni
                            </p>
                        </div>

                        <span class="ml-auto text-sm tracking-widest text-[#E87532]" aria-label="Rating contoh">
                            ★★★★★
                        </span>
                    </div>

                    <p class="mt-5 text-sm leading-7 text-[#715D56]">
                        “{{ $testimonial['text'] }}”
                    </p>
                </article>
            @endforeach
        </div>

        <p class="mt-4 text-xs leading-5 text-[#8B7770]">
            Catatan: nama dan kutipan di atas merupakan konten demo. Ganti dengan testimoni nyata dan izin pelanggan sebelum website dipublikasikan.
        </p>
    </div>
</section>

{{-- CTA PENUTUP --}}
<section class="bg-[#FFF8F1] px-5 py-10 sm:px-8 sm:py-14 lg:px-10">
    <div class="mx-auto flex max-w-7xl flex-col items-start justify-between gap-6 rounded-[2rem] bg-[#74191B] px-7 py-8 text-white sm:px-10 sm:py-10 md:flex-row md:items-center">
        <div>
            <p class="text-xs font-extrabold uppercase tracking-[0.2em] text-[#FFB45E]">
                Saatnya memilih favoritmu
            </p>

            <h2 class="mt-2 text-2xl font-black sm:text-3xl">
                Sudah menemukan menu pilihan?
            </h2>

            <p class="mt-3 text-sm leading-6 text-white/75">
                Jelajahi katalog Bakso Pesona dan lihat pilihan menu yang tersedia.
            </p>
        </div>

        <a href="{{ route('products.index') }}"
            class="inline-flex shrink-0 items-center gap-3 rounded-full bg-[#E87532] px-6 py-3.5 text-sm font-extrabold text-white transition hover:bg-[#F28A42]">
            Lihat Menu <span aria-hidden="true">→</span>
        </a>
    </div>
</section>

@endsection