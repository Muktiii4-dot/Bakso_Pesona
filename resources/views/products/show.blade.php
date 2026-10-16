@extends('layouts.app')

@section('title', $product['name'] . ' - Bakso Pesona')

@section('content')

<section class="bg-pesona-cream py-10">

    <div class="mx-auto max-w-7xl px-5 lg:px-8">

        {{-- Breadcrumb --}}
        <nav class="mb-8 text-sm">

            <ol class="flex flex-wrap items-center gap-2 text-gray-500">

                <li>
                    <a
                        href="{{ route('home') }}"
                        class="hover:text-pesona-red"
                    >
                        Home
                    </a>
                </li>

                <li>
                    /
                </li>

                <li>
                    <a
                        href="{{ route('products.index') }}"
                        class="hover:text-pesona-red"
                    >
                        Produk
                    </a>
                </li>

                <li>
                    /
                </li>

                <li class="font-semibold text-gray-900">
                    {{ $product['name'] }}
                </li>

            </ol>

        </nav>


        {{-- Product Detail --}}
        <div class="grid overflow-hidden rounded-3xl bg-white shadow-sm lg:grid-cols-2">


            {{-- Image --}}
            <div class="min-h-[420px] bg-orange-50">

                @if ($product['image'])

                    <img
                        src="{{ filter_var($product['image'], FILTER_VALIDATE_URL) ? $product['image'] : asset($product['image']) }}"
                        alt="{{ $product['name'] }}"
                        class="h-full min-h-[420px] w-full object-cover"
                    >

                @else

                    <div
                        class="flex h-full min-h-[420px] items-center justify-center bg-gradient-to-br from-pesona-red to-pesona-orange"
                    >

                        <span class="text-[150px]">
                            🍲
                        </span>

                    </div>

                @endif

            </div>


            {{-- Information --}}
            <div class="flex flex-col justify-center p-7 md:p-10">

                {{-- Badge --}}
                @if($product['badge'])

                    <div>

                        <span
                            class="inline-flex rounded-full bg-orange-100 px-4 py-2 text-xs font-bold text-pesona-red"
                        >
                            {{ $product['badge'] }}
                        </span>

                    </div>

                @endif


                {{-- Category --}}
                <p class="mt-5 text-sm font-semibold text-pesona-orange">
                    {{ $product['category'] }}
                </p>


                {{-- Name --}}
                <h1 class="mt-2 text-3xl font-black text-gray-900 md:text-4xl">
                    {{ $product['name'] }}
                </h1>


                {{-- Price --}}
                <p class="mt-5 text-3xl font-black text-pesona-red">
                    Rp {{ number_format($product['price'], 0, ',', '.') }}
                </p>


                {{-- Description --}}
                <p class="mt-6 leading-7 text-gray-600">
                    {{ $product['description'] }}
                </p>


                {{-- Availability --}}
                <div class="mt-6 flex items-center gap-2">

                    <span
                        class="h-2.5 w-2.5 rounded-full {{ $product['is_available'] ? 'bg-green-500' : 'bg-red-500' }}"
                    ></span>

                    <span class="text-sm font-medium text-gray-600">

                        @if($product['is_available'])
                            Tersedia
                        @else
                            Tidak tersedia
                        @endif

                    </span>

                </div>


                {{-- Quantity --}}
                <div class="mt-8">

                    <label class="text-sm font-bold text-gray-900">
                        Jumlah
                    </label>

                    <div class="mt-3 flex items-center">

                        <button
                            type="button"
                            class="flex h-11 w-11 items-center justify-center rounded-l-xl border border-gray-200 text-xl font-bold hover:bg-gray-50"
                        >
                            −
                        </button>

                        <input
                            type="number"
                            value="1"
                            min="1"
                            class="h-11 w-16 border-y border-gray-200 text-center text-sm font-bold outline-none"
                        >

                        <button
                            type="button"
                            class="flex h-11 w-11 items-center justify-center rounded-r-xl border border-gray-200 text-xl font-bold hover:bg-gray-50"
                        >
                            +
                        </button>

                    </div>

                </div>


                {{-- Add Cart --}}
                <button
                    type="button"
                    class="mt-7 flex w-full items-center justify-center gap-3 rounded-xl bg-pesona-orange px-6 py-4 font-bold text-white transition hover:bg-pesona-red"
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

                    Tambah ke Keranjang

                </button>


                {{-- Back --}}
                <a
                    href="{{ route('products.index') }}"
                    class="mt-4 text-center text-sm font-semibold text-gray-500 hover:text-pesona-red"
                >
                    ← Kembali ke Produk
                </a>

            </div>

        </div>

    </div>

</section>

@endsection