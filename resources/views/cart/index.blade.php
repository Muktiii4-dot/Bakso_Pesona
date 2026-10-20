@extends('layouts.app')

@section('title', 'Keranjang Belanja — Bakso Pesona')

@section('content')
<section class="min-h-[70vh] bg-[#FFF9F3] px-4 py-10 sm:px-6 sm:py-14 lg:px-8">
    <div class="mx-auto max-w-7xl">

        {{-- Header --}}
        <div class="mb-8">
            <p class="text-sm font-bold uppercase tracking-[0.2em] text-[#B83B25]">
                Bakso Pesona
            </p>

            <h1 class="mt-2 text-3xl font-black tracking-tight text-[#74191B] sm:text-4xl">
                Keranjang Belanja
            </h1>

            <p class="mt-3 text-sm leading-6 text-gray-600 sm:text-base">
                Periksa pilihan produkmu sebelum melanjutkan ke tahap pemesanan.
            </p>
        </div>

        {{-- Notifikasi --}}
        @if (session('success'))
            <div role="status" class="mb-5 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm font-medium text-green-800">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div role="alert" class="mb-5 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-800">
                {{ session('error') }}
            </div>
        @endif
    </div>
</section>