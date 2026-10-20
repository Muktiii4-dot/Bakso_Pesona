@if ($paginator->hasPages())
    <nav 
        role="navigation" aria-label="navigasi halaman produk"
        class="flex flex-wrap items-center justify-center gap-2"
    >
        {{-- Sebelumnya --}}
        @if ($paginator->onFirstPage())
            <span class="rounded-xl border border-gray-200 px-4 py-2.5 text-sm text-gray-400">
                Sebelumnya
            </span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}"
                class="rounded-xl border border-orange-100 bg-white px-4 py-2.5 text-sm font-semibold text-[#74191B] transition hover:bg-orange-50">
                Sebelumnya
            </a>
        @endif

        {{-- Nomor Halaman --}}
        @foreach ($elements as $element)
            @if (is_string($element))
                <span class="px-2 py-2 text-sm text-gray-400">
                    {{ $element }}
                </span>
            @endif

            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span 
                            aria-current="page"
                            class="min-w-10 rounded-xl bg-[#E87532] px-3 py-2.5 text-center text-sm font-bold text-white"
                        >
                            {{ $page }}
                        </span>
                    @else
                        <a href="{{ $url }}"
                            class="min-w-10 rounded-xl border border-orange-100 bg-white px-3 py-2.5 text-center text-sm font-semibold text-[#74191B] transition hover:bg-orange-50"
                            aria-label="Halaman {{ $page }}">
                            {{ $page }}
                        </a>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- Berikutnya --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}"
                class="rounded-xl border border-orange-100 bg-white px-4 py-2.5 text-sm font-semibold text-[#74191B] transition hover:bg-orange-50">
                Berikutnya
            </a>
        @else
            <span class="rounded-xl border border-gray-200 px-4 py-2.5 text-sm text-gray-400">
                Berikutnya
            </span>
        @endif
    </nav>
@endif