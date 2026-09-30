@extends('layouts.app')
@section('title', 'Bakso Pesona -Rasa Lezat Bikin Nagih')
@section('content')

{{-- Hero --}}
<section class="relative overflow-hidden bg-[#7F1D1D]">

    <div class="mx-auto grid min-h-[620px] max-w-7xl items-center gap-10 px-5 py-16 lg:grid-cols-2 lg:px-8">

        {{-- Text --}}
        <div class="relative z-10">
            
            <span class="text-sm font-bold text-[#E87532]">Bakso Pesona</span>

            <h1 class="mt-4 max-w-xl text-5xl font-black leading-tight text-white md:text-6xl"
            >
                Rasa Lezat

                <br>

                <span class="text-[#F59E0B]">
                    Bikin Nagih!
                </span>
            </h1>

            <p class="mt-6 max-w-lg text-base leading-7 text-white/80"
            >
                bakso berkualitas dengan cita rasa autentik
                yang selalu bikin kamu ketagihan.
            </p>

            <div class="mt-8">

            <a 
                href="produk"
                class="inline-flex items-center gap-3 rounded-full bg-[#E87532] px-7 py-3.5 text-sm font-bold text-white shadow-lg transition hover:-TRANSLATE-Y-1 HOVER:BG-[#F59E0B]"
                >
                Pesan Sekarang
                <span>→</span>
            </a>

            </div>

        </div>

        {{-- Hero Image --}}
        <div class="relative">

            <div class="absolute inset-0 rounded-full bg-[E87532]/20 blur-3xl"></div>

            <div class="relative flex min-h-[400px] items-center justify-center">

                {{-- Temporary Visual --}}
                <div class="flex h-[360px] w-[360px] items-center justify-center rounded-full bg-[#E87532] shadow-2xl md:h-[440px] md:w-[440px]"
                >

                    <span class="text-[150px] md:text-[190px]">🍲</span>

                </div>

            </div>

        </div>

    </div>

</section>

{{-- Category --}}
<section class="bg-[#FFF9F5] py-14">

    <div class="mx-auto max-w-7xl px-5 lg:px-8">

        <div class="flex items-center gap-3">

            <span class="text-2xl">
                🍲
            </span>

            <h2 class="text-2xl font-black text-gray-900">
                Kategori Produk
            </h2>

        </div>


        <div class="mt-6 flex flex-wrap gap-3">

            @foreach([
                'Semua',
                'Bakso',
                'Mie',
                'Cemilan',
                'Paket'
            ] as $category)

                <button
                    type="button"
                    class="rounded-full px-6 py-2.5 text-sm font-semibold transition
                    {{ $category === 'Semua'
                        ? 'bg-[#E87532] text-white'
                        : 'bg-white text-gray-600 hover:bg-[#E87532] hover:text-white' }}"
                >
                    {{ $category }}
                </button>

            @endforeach

        </div>

    </div>

</section>


{{-- PRODUCTS --}}
<section
    id="produk"
    class="bg-[#FFF9F5] pb-16"
>

    <div class="mx-auto max-w-7xl px-5 lg:px-8">

        <div class="flex items-center justify-between">

            <div class="flex items-center gap-3">

                <span class="text-2xl">
                    🔥
                </span>

                <h2 class="text-2xl font-black text-gray-900">
                    Produk Pilihan
                </h2>

            </div>

            <a
                href="#"
                class="text-sm font-bold text-[#B83B25] hover:text-[#E87532]"
            >
                Lihat Semua →
            </a>

        </div>


        <div class="mt-8 grid gap-5 sm:grid-cols-2 lg:grid-cols-4">

            <x-product-card
                name="Bakso Malang Original"
                description="Bakso, tahu, siomay, mie, sayur dan kuah gurih."
                :price="25000"
                badge="Best Seller"
            />

            <x-product-card
                name="Bakso Pedas"
                description="Bakso dengan kuah pedas dan cita rasa khas."
                :price="25000"
            />

            <x-product-card
                name="Bakso Keju"
                description="Bakso isi keju dengan kuah gurih."
                :price="30000"
            />

            <x-product-card
                name="Mie Bakso Jumbo"
                description="Mie dengan bakso ukuran jumbo yang mengenyangkan."
                :price="35000"
            />

        </div>

    </div>

</section>


{{-- PROMO BANNER --}}

<section class="pb-16">

    <div class="mx-auto max-w-7xl px-5 lg:px-8">

        <div
            class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-[#B83B25] to-[#E87532] px-8 py-12 md:px-12"
        >

            <div class="relative z-10 max-w-lg">

                <h2
                    class="text-3xl font-black leading-tight text-white md:text-4xl"
                >
                    Cita Rasa Terbaik
                    <br>
                    untuk Setiap Momen
                </h2>

                <p class="mt-4 leading-7 text-white/80">
                    Nikmati kelezatan bakso Pesona kapan saja,
                    di mana saja.
                </p>

                <a
                    href="#produk"
                    class="mt-7 inline-flex rounded-full bg-white px-6 py-3 text-sm font-bold text-[#B83B25] transition hover:bg-gray-100"
                >
                    Lihat Produk →
                </a>

            </div>

        </div>

    </div>

</section>


{{-- =========================================================
     FEATURES
========================================================= --}}

<section class="pb-16">

    <div class="mx-auto grid max-w-7xl gap-4 px-5 sm:grid-cols-2 lg:grid-cols-4 lg:px-8">

        @foreach([
            [
                'icon' => '🛡️',
                'title' => 'Halal MUI',
                'desc' => '100% Halal dan Aman'
            ],
            [
                'icon' => '⭐',
                'title' => 'Produk Berkualitas',
                'desc' => 'Bahan pilihan terbaik'
            ],
            [
                'icon' => '🚚',
                'title' => 'Pengiriman Cepat',
                'desc' => 'Pesanan sampai dengan aman'
            ],
            [
                'icon' => '💳',
                'title' => 'Pembayaran Aman',
                'desc' => 'Banyak metode pembayaran'
            ]
        ] as $feature)

            <div
                class="rounded-2xl border border-orange-100 bg-white p-5"
            >

                <div class="flex items-center gap-4">

                    <div
                        class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-orange-50 text-xl"
                    >
                        {{ $feature['icon'] }}
                    </div>

                    <div>

                        <h3 class="text-sm font-bold">
                            {{ $feature['title'] }}
                        </h3>

                        <p class="mt-1 text-xs text-gray-500">
                            {{ $feature['desc'] }}
                        </p>

                    </div>

                </div>

            </div>

        @endforeach

    </div>

</section>


{{-- =========================================================
     TESTIMONIAL
========================================================= --}}

<section class="bg-[#FFF3EB] py-16">

    <div class="mx-auto max-w-7xl px-5 lg:px-8">

        <div class="flex items-center gap-3">

            <span class="text-2xl">
                💬
            </span>

            <h2 class="text-2xl font-black">
                Apa Kata Mereka?
            </h2>

        </div>


        <div class="mt-8 grid gap-5 md:grid-cols-3">

            @foreach([
                [
                    'name' => 'Rina Sari',
                    'text' => 'Baksonya enak banget, kuahnya gurih dan dagingnya terasa banget.',
                ],
                [
                    'name' => 'Andi Pratama',
                    'text' => 'Paketnya rapi, pengiriman cepat. Rasanya juga sesuai ekspektasi.',
                ],
                [
                    'name' => 'Siti Rahma',
                    'text' => 'Harga terjangkau, kualitas rasa luar biasa.',
                ]
            ] as $testimonial)

                <article
                    class="rounded-2xl bg-white p-6 shadow-sm"
                >

                    <div class="flex items-center gap-4">

                        <div
                            class="flex h-12 w-12 items-center justify-center rounded-full bg-[#E87532] font-bold text-white"
                        >
                            {{ substr($testimonial['name'], 0, 1) }}
                        </div>

                        <div>

                            <h3 class="font-bold">
                                {{ $testimonial['name'] }}
                            </h3>

                            <div class="text-sm text-[#E87532]">
                                ★★★★★
                            </div>

                        </div>

                    </div>

                    <p class="mt-5 text-sm leading-6 text-gray-600">
                        "{{ $testimonial['text'] }}"
                    </p>

                </article>

            @endforeach

        </div>

    </div>

</section>

@endsection