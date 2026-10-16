@extends('layouts.app')

@section('title', 'Menu Bakso - Bakso Pesona')

@section('content')

{{-- =========================================================
        HEADER
========================================================= --}}

<section class="bg-pesona-dark">

    <div class="mx-auto max-w-7xl px-5 py-16 lg:px-8">

        <div class="max-w-2xl">

            <span class="text-sm font-bold text-pesona-orange">
                Menu Bakso Pesona
            </span>

            <h1 class="mt-3 text-4xl font-black text-white md:text-5xl">
                Pilih Menu Favoritmu
            </h1>

            <p class="mt-5 leading-7 text-white/70">
                Temukan berbagai pilihan bakso, mie,
                cemilan, dan paket favorit dari Bakso Pesona.
            </p>

        </div>

    </div>

</section>


{{-- =========================================================
        CATALOG
========================================================= --}}

<section class="bg-pesona-cream py-12">

    <div class="mx-auto max-w-7xl px-5 lg:px-8">


        {{-- Search & Filter --}}
        <form
            action="{{ route('products.index') }}"
            method="GET"
            class="rounded-2xl border border-orange-100 bg-white p-5 shadow-sm"
        >

            <div class="grid gap-4 md:grid-cols-[1fr_auto_auto]">


                {{-- Search --}}
                <div>

                    <label
                        for="search"
                        class="mb-2 block text-xs font-bold text-gray-600"
                    >
                        Cari Produk
                    </label>

                    <div class="relative">

                        <svg
                            class="absolute left-4 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="m21 21-4.35-4.35m2.1-5.4a7.5 7.5 0 1 1-15 0 7.5 7.5 0 0 1 15 0Z"
                            />
                        </svg>

                        <input
                            id="search"
                            name="q"
                            type="search"
                            value="{{ $search }}"
                            placeholder="Cari bakso, mie, cemilan..."
                            class="h-11 w-full rounded-xl border border-gray-200 pl-11 pr-4 text-sm outline-none transition focus:border-pesona-orange focus:ring-2 focus:ring-pesona-orange/20"
                        >

                    </div>

                </div>


                {{-- Category --}}
                <div>

                    <label
                        for="category"
                        class="mb-2 block text-xs font-bold text-gray-600"
                    >
                        Kategori
                    </label>

                    <select
                        id="category"
                        name="category"
                        class="h-11 min-w-[170px] rounded-xl border border-gray-200 bg-white px-4 text-sm outline-none focus:border-pesona-orange"
                    >

                        <option value="Semua">
                            Semua
                        </option>

                        @foreach($categories as $category)

                            <option
                                value="{{ $category }}"
                                @selected($selectedCategory === $category)
                            >
                                {{ $category }}
                            </option>

                        @endforeach

                    </select>

                </div>


                {{-- Sorting --}}
                <div>

                    <label
                        for="sort"
                        class="mb-2 block text-xs font-bold text-gray-600"
                    >
                        Urutkan
                    </label>

                    <select
                        id="sort"
                        name="sort"
                        class="h-11 min-w-[180px] rounded-xl border border-gray-200 bg-white px-4 text-sm outline-none focus:border-pesona-orange"
                    >

                        <option
                            value="default"
                            @selected($selectedSort === 'default')
                        >
                            Terbaru
                        </option>

                        <option
                            value="price_low"
                            @selected($selectedSort === 'price_low')
                        >
                            Harga Terendah
                        </option>

                        <option
                            value="price_high"
                            @selected($selectedSort === 'price_high')
                        >
                            Harga Tertinggi
                        </option>

                        <option
                            value="name_asc"
                            @selected($selectedSort === 'name_asc')
                        >
                            Nama A-Z
                        </option>

                    </select>

                </div>

            </div>


            {{-- Button --}}
            <div class="mt-4 flex flex-wrap gap-3">

                <button
                    type="submit"
                    class="rounded-xl bg-pesona-orange px-6 py-3 text-sm font-bold text-white transition hover:bg-pesona-red"
                >
                    Terapkan Filter
                </button>

                <a
                    href="{{ route('products.index') }}"
                    class="rounded-xl border border-gray-200 px-6 py-3 text-sm font-bold text-gray-600 transition hover:bg-gray-50"
                >
                    Reset
                </a>

            </div>

        </form>


        {{-- Category Pills --}}
        <div class="mt-8 flex flex-wrap gap-3">

            <a
                href="{{ route('products.index') }}"
                class="rounded-full px-5 py-2.5 text-sm font-semibold transition
                {{ $selectedCategory === '' || $selectedCategory === 'Semua'
                    ? 'bg-pesona-orange text-white'
                    : 'bg-white text-gray-600 hover:bg-pesona-orange hover:text-white' }}"
            >
                Semua
            </a>

            @foreach($categories as $category)

                <a
                    href="{{ route('products.index', ['category' => $category]) }}"
                    class="rounded-full bg-white px-5 py-2.5 text-sm font-semibold text-gray-600 transition hover:bg-pesona-orange hover:text-white
                    {{ $selectedCategory === $category ? 'bg-pesona-orange! text-white!' : '' }}"
                >
                    {{ $category }}
                </a>

            @endforeach

        </div>


        {{-- Result Info --}}
        <div class="mt-10 flex items-center justify-between">

            <div>

                <h2 class="text-xl font-black text-gray-900">
                    Semua Menu
                </h2>

                <p class="mt-1 text-sm text-gray-500">
                    Menampilkan {{ $products->count() }} produk
                </p>

            </div>

        </div>


        {{-- Product Grid --}}
        @if($products->count())

            <div class="mt-6 grid gap-5 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">

                @foreach($products as $product)

                    <x-product-card
                        :name="$product['name']"
                        :description="$product['description']"
                        :price="$product['price']"
                        :image="$product['image']"
                        :badge="$product['badge']"
                        :slug="$product['slug']"
                    />

                @endforeach

            </div>

        @else

            {{-- Empty State --}}
            <div class="mt-8 rounded-2xl bg-white px-6 py-16 text-center">

                <div class="text-6xl">
                    🔍
                </div>

                <h3 class="mt-5 text-xl font-black">
                    Produk Tidak Ditemukan
                </h3>

                <p class="mx-auto mt-2 max-w-md text-sm leading-6 text-gray-500">
                    Tidak ada produk yang sesuai dengan pencarian
                    atau filter yang kamu pilih.
                </p>

                <a
                    href="{{ route('products.index') }}"
                    class="mt-6 inline-flex rounded-xl bg-pesona-orange px-6 py-3 text-sm font-bold text-white"
                >
                    Lihat Semua Produk
                </a>

            </div>

        @endif

    </div>

</section>

@endsection