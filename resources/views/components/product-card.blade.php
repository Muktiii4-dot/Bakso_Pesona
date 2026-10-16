@props([
    'name',
    'description',
    'price',
    'image' => null,
    'badge' => null,
<<<<<<< HEAD
    'slug' => null,
])

<article class="group flex h-full min-w-0 flex-col overflow-hidden rounded-2xl border border-[#F0DED4] bg-white shadow-[0_4px_16px_rgba(83,37,24,0.045)] transition duration-300 hover:-translate-y-1 hover:border-[#E9B89E] hover:shadow-[0_12px_28px_rgba(83,37,24,0.11)]">

    {{-- GAMBAR PRODUK --}}
    <a
        href="{{ $slug ? route('products.show', $slug) : route('products.index') }}"
        class="relative block aspect-[1.18/1] overflow-hidden bg-[#F6E6DC]"
        aria-label="Lihat {{ $name }}"
    >
        @if ($image)
            <img
                src="{{ filter_var($image, FILTER_VALIDATE_URL) ? $image : asset($image) }}"
                alt="{{ $name }}"
                class="h-full w-full object-cover transition duration-500 group-hover:scale-105"
                loading="lazy"
                onerror="this.style.display='none'; this.nextElementSibling?.classList.remove('hidden'); this.nextElementSibling?.classList.add('flex')"
            >

            <div class="hidden absolute inset-0 items-center justify-center bg-gradient-to-br from-[#B83B25] to-[#E87532] text-5xl" aria-hidden="true">
                🍜
            </div>
        @else
            <div class="flex h-full w-full items-center justify-center bg-gradient-to-br from-[#B83B25] to-[#E87532] text-5xl" aria-hidden="true">
                🍜
            </div>
        @endif

        @if ($badge)
            <span class="absolute left-2.5 top-2.5 rounded-full bg-[#E87532] px-2.5 py-1 text-[9px] font-extrabold tracking-wide text-white shadow-sm sm:left-3 sm:top-3 sm:text-[10px]">
                {{ $badge }}
            </span>
        @endif
    </a>

    {{-- INFORMASI PRODUK --}}
    <div class="flex flex-1 flex-col p-3 sm:p-4">
        <h3 class="text-xs font-extrabold leading-5 text-[#351B18] transition group-hover:text-[#B83B25] sm:text-sm">
            <a href="{{ $slug ? route('products.show', $slug) : route('products.index') }}">
                {{ $name }}
            </a>
        </h3>

        <p class="mt-1.5 line-clamp-2 text-[10px] leading-4 text-[#8B7770] sm:text-xs sm:leading-5">
            {{ $description }}
        </p>

        {{-- HARGA DAN TOMBOL --}}
        <div class="mt-auto flex items-end justify-between gap-2 pt-4">
            <div class="min-w-0">
                <span class="block text-[9px] font-medium text-[#A18E86] sm:text-[10px]">
                    Harga
                </span>

                <p class="mt-0.5 text-xs font-black text-[#B83B25] sm:text-sm">
                    Rp {{ number_format((float) $price, 0, ',', '.') }}
                </p>
            </div>

            <a
                href="{{ $slug ? route('products.show', $slug) : route('products.index') }}"
                class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-[#E87532] text-lg font-medium leading-none text-white transition hover:bg-[#7F1D1D] focus:outline-none focus:ring-2 focus:ring-[#E87532] focus:ring-offset-2 sm:h-8 sm:w-8"
                aria-label="Lihat {{ $name }}"
            >
                +
            </a>
        </div>
    </div>
</article>
=======
])

<div class="group overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm transition duration-300 hover:-translate-y-1 hover:shadow-xl">

    {{-- Image --}}
    <div class="relative aspect-[4/3] overflow-hidden bg-gray-100">

        @if ($image)

            <img
                src="{{ asset($image) }}"
                alt="{{ $name }}"
                class="h-full w-full object-cover transition duration-500 group-hover:scale-105"
            >

        @else

            <div class="flex h-full items-center justify-center bg-gradient-to-br from-[#B83B25] to-[#E87532]">
                <span class="text-6xl">🍲</span>
            </div>

        @endif

    </div>

    {{-- Content --}}
    <div class="p-4">

        <h3 class="font-bold text-gray-900">{{ $name }}</h3>
        <p class="mt-1 line-clamp-3 text-xs leading-5 text-gray-500">{{ $description }}</p>

        <div class="mt-4 flex items-center justify-between">
            <span class="font-bold text-[#B83B25]">
                Rp {{ number_format($price, 0, ',', '.') }}
            </span>

            <button type="button" class="flex h-9 w-9 items-center justify-center rounded-full bg-[#E87532] text-xl font-bold text-white transition hover:bg-[#B83B25]"
            >
                +
            </button>

        </div>

    </div>
    
</div>
>>>>>>> b6e12ae2d658d726b7aeb6d4674d9599852860b2
