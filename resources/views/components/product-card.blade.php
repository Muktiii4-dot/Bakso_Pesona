@props([
    'name',
    'description',
    'price',
    'image' => null,
    'badge' => null,
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