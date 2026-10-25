@extends('layouts.admin')

@section('title', 'Manajemen Pesanan')
@section('header_title', 'Manajemen Pesanan')

@section('content')
<div class="space-y-6">

    {{-- HEADER --}}
    <section class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="text-sm font-bold uppercase tracking-[0.18em] text-pesona-orange">
                Operasional toko
            </p>
            <h1 class="mt-1 text-2xl font-black tracking-tight text-pesona-dark sm:text-3xl">
                Manajemen Pesanan
            </h1>
            <p class="mt-2 max-w-2xl text-sm leading-6 text-gray-500">
                Pantau pesanan pelanggan, periksa pembayaran, tetapkan ongkir,
                dan kelola proses pemenuhan pesanan.
            </p>
        </div>

        <div class="inline-flex w-fit items-center gap-2 rounded-full border border-orange-100 bg-white px-4 py-2 text-sm font-semibold text-gray-600 shadow-sm">
            <span class="h-2 w-2 rounded-full bg-pesona-orange"></span>
            {{ number_format($totalOrders) }} pesanan tersimpan
        </div>
    </section>

    {{-- RINGKASAN --}}
    <section class="grid grid-cols-1 gap-4 sm:grid-cols-2 2xl:grid-cols-4">

        <div class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <p class="text-sm font-medium text-gray-500">Semua Pesanan</p>
                    <p class="mt-3 text-3xl font-black tracking-tight text-pesona-dark">
                        {{ number_format($totalOrders) }}
                    </p>
                    <p class="mt-1 text-xs text-gray-400">Total pesanan tersimpan</p>
                </div>
                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-orange-50 text-pesona-orange">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 7h18M5 7l1.5 13h11L19 7M9 7V5a3 3 0 0 1 6 0v2"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="rounded-2xl border border-amber-100 bg-amber-50/60 p-5">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <p class="text-sm font-medium text-amber-800">Perlu Tindakan Admin</p>
                    <p class="mt-3 text-3xl font-black tracking-tight text-amber-950">
                        {{ number_format($ordersRequiringAction) }}
                    </p>
                    <p class="mt-1 text-xs text-amber-700/80">Pesanan yang perlu ditindaklanjuti</p>
                </div>
                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-white/80 text-amber-700">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                        <circle cx="12" cy="12" r="9"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="rounded-2xl border border-blue-100 bg-blue-50/60 p-5">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <p class="text-sm font-medium text-blue-800">Menunggu Pembayaran</p>
                    <p class="mt-3 text-3xl font-black tracking-tight text-blue-950">
                        {{ number_format($awaitingPaymentOrders) }}
                    </p>
                    <p class="mt-1 text-xs text-blue-700/80">Pantau status pembayaran</p>
                </div>
                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-white/80 text-blue-700">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                        <rect x="3" y="5" width="18" height="14" rx="2"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18m-14 5h4"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="rounded-2xl border border-orange-100 bg-orange-50/60 p-5">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <p class="text-sm font-medium text-orange-900">Perlu Verifikasi</p>
                    <p class="mt-3 text-3xl font-black tracking-tight text-orange-950">
                        {{ number_format($paymentsUnderVerification) }}
                    </p>
                    <p class="mt-1 text-xs text-orange-800/80">Periksa transaksi aktual</p>
                </div>
                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-white/80 text-orange-800">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 3 4 6v5c0 5 3.5 8 8 10 4.5-2 8-5 8-10V6l-8-3Z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="m9 12 2 2 4-4"/>
                    </svg>
                </div>
            </div>
        </div>

    </section>

    {{-- PENCARIAN DAN FILTER --}}
    <section class="rounded-2xl border border-gray-100 bg-white p-4 shadow-sm sm:p-5">
        <div class="mb-5 flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-base font-bold text-gray-900">Cari dan filter pesanan</h2>
                <p class="mt-1 text-xs text-gray-500">
                    Gunakan filter untuk menemukan pesanan yang perlu ditangani.
                </p>
            </div>

            @if (request()->hasAny(['search', 'status', 'payment_status', 'attention', 'date_from', 'date_to', 'sort']))
                <a
                    href="{{ route('admin.orders.index') }}"
                    class="mt-2 inline-flex w-fit items-center gap-2 text-sm font-semibold text-pesona-orange hover:text-pesona-dark sm:mt-0"
                >
                    <span aria-hidden="true">&times;</span>
                    Reset semua filter
                </a>
            @endif
        </div>

        <form method="GET" action="{{ route('admin.orders.index') }}" class="space-y-4">
            @if (request('selected'))
                <input type="hidden" name="selected" value="{{ request('selected') }}">
            @endif

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">

                {{-- PENCARIAN --}}
                <div class="xl:col-span-2">
                    <label for="search" class="mb-2 block text-sm font-semibold text-gray-700">
                        Kata kunci
                    </label>
                    <div class="relative">
                        <svg class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                            <circle cx="11" cy="11" r="7"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="m16 16 4 4"/>
                        </svg>
                        <input
                            type="text"
                            id="search"
                            name="search"
                            value="{{ request('search') }}"
                            placeholder="Nomor pesanan, nama, atau nomor HP..."
                            class="w-full rounded-xl border border-gray-200 bg-gray-50 py-2.5 pl-10 pr-3 text-sm text-gray-900 placeholder:text-gray-400 transition focus:border-pesona-orange focus:bg-white focus:outline-none focus:ring-2 focus:ring-orange-100"
                        >
                    </div>
                </div>

                {{-- STATUS PESANAN --}}
                <div>
                    <label for="status" class="mb-2 block text-sm font-semibold text-gray-700">
                        Status pesanan
                    </label>
                    <select
                        id="status"
                        name="status"
                        class="w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm text-gray-800 focus:border-pesona-orange focus:outline-none focus:ring-2 focus:ring-orange-100"
                    >
                        <option value="">Semua Status</option>
                        @foreach ($statusLabels as $value => $label)
                            <option value="{{ $value }}" @selected(request('status') === $value)>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- STATUS PEMBAYARAN --}}
                <div>
                    <label for="payment_status" class="mb-2 block text-sm font-semibold text-gray-700">
                        Status pembayaran
                    </label>
                    <select
                        id="payment_status"
                        name="payment_status"
                        class="w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm text-gray-800 focus:border-pesona-orange focus:outline-none focus:ring-2 focus:ring-orange-100"
                    >
                        <option value="">Semua Pembayaran</option>
                        @foreach ($paymentStatusLabels as $pValue => $pLabel)
                            <option value="{{ $pValue }}" @selected(request('payment_status') === $pValue)>
                                {{ $pLabel }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- TINDAKAN --}}
                <div>
                    <label for="attention" class="mb-2 block text-sm font-semibold text-gray-700">
                        Tindakan berikutnya
                    </label>
                    <select
                        id="attention"
                        name="attention"
                        class="w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm text-gray-800 focus:border-pesona-orange focus:outline-none focus:ring-2 focus:ring-orange-100"
                    >
                        <option value="">Semua Tindakan</option>
                        <option value="shipping" @selected(($attention ?? request('attention')) === 'shipping')>Tetapkan ongkir</option>
                        <option value="instructions" @selected(($attention ?? request('attention')) === 'instructions')>Kirim instruksi</option>
                        <option value="verification" @selected(($attention ?? request('attention')) === 'verification')>Verifikasi transaksi</option>
                        <option value="payment_review" @selected(($attention ?? request('attention')) === 'payment_review')>Review pembayaran</option>
                        <option value="ready_to_pack" @selected(($attention ?? request('attention')) === 'ready_to_pack')>Siap dikemas</option>
                    </select>
                </div>

                {{-- PENGURUTAN --}}
                <div>
                    <label for="sort" class="mb-2 block text-sm font-semibold text-gray-700">
                        Urutkan berdasarkan
                    </label>
                    <select
                        id="sort"
                        name="sort"
                        class="w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm text-gray-800 focus:border-pesona-orange focus:outline-none focus:ring-2 focus:ring-orange-100"
                    >
                        <option value="latest" @selected(($sort ?? request('sort', 'latest')) === 'latest')>Terbaru</option>
                        <option value="oldest" @selected(($sort ?? request('sort')) === 'oldest')>Terlama</option>
                        <option value="total_high" @selected(($sort ?? request('sort')) === 'total_high')>Total tertinggi</option>
                        <option value="total_low" @selected(($sort ?? request('sort')) === 'total_low')>Total terendah</option>
                    </select>
                </div>

                {{-- RENTANG TANGGAL --}}
                <div>
                    <label for="date_from" class="mb-2 block text-sm font-semibold text-gray-700">
                        Dari tanggal
                    </label>
                    <input
                        type="date"
                        id="date_from"
                        name="date_from"
                        value="{{ request('date_from') }}"
                        class="w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm text-gray-800 focus:border-pesona-orange focus:outline-none focus:ring-2 focus:ring-orange-100"
                    >
                    @error('date_from')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="date_to" class="mb-2 block text-sm font-semibold text-gray-700">
                        Sampai tanggal
                    </label>
                    <input
                        type="date"
                        id="date_to"
                        name="date_to"
                        value="{{ request('date_to') }}"
                        class="w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm text-gray-800 focus:border-pesona-orange focus:outline-none focus:ring-2 focus:ring-orange-100"
                    >
                    @error('date_to')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

            </div>

            <div class="flex flex-col gap-2 border-t border-gray-100 pt-4 sm:flex-row sm:justify-end">
                @if (request()->hasAny(['search', 'status', 'payment_status', 'attention', 'date_from', 'date_to', 'sort']))
                    <a
                        href="{{ route('admin.orders.index') }}"
                        class="inline-flex items-center justify-center rounded-xl border border-gray-200 px-5 py-2.5 text-sm font-semibold text-gray-600 transition hover:bg-gray-50"
                    >
                        Reset
                    </a>
                @endif

                <button
                    type="submit"
                    class="inline-flex items-center justify-center gap-2 rounded-xl bg-pesona-dark px-5 py-2.5 text-sm font-bold text-white shadow-sm transition hover:bg-black focus:outline-none focus:ring-2 focus:ring-pesona-orange focus:ring-offset-2"
                >
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 5h16l-6.5 7.5V19l-3-1.5v-5L4 5Z"/>
                    </svg>
                    Terapkan Filter
                </button>
            </div>
        </form>
    </section>

    {{-- AREA KERJA: TABEL DAN PANEL DETAIL --}}
    <section class="space-y-4">

        <div class="flex flex-col gap-1 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h2 class="text-lg font-black text-pesona-dark">Daftar Pesanan</h2>
                <p class="mt-1 text-sm text-gray-500">
                    Pilih pesanan untuk melihat ringkasannya sebelum membuka halaman detail.
                </p>
            </div>
        </div>

        <div class="flex min-w-0 flex-col items-start gap-5 2xl:flex-row">

            {{-- TABEL --}}
            <div class="w-full min-w-0 flex-1">
                <div class="overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-sm">

                    @if ($orders->isEmpty())
                        <div class="px-6 py-16 text-center">
                            <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-orange-50 text-2xl" aria-hidden="true">
                                📦
                            </div>
                            <h3 class="mt-4 text-base font-bold text-gray-900">
                                Tidak ada pesanan ditemukan
                            </h3>
                            <p class="mx-auto mt-2 max-w-sm text-sm leading-6 text-gray-500">
                                Belum ada pesanan yang cocok dengan pencarian atau filter saat ini.
                                Coba ubah kriteria pencarian.
                            </p>

                            @if (request()->hasAny(['search', 'status', 'payment_status', 'attention', 'date_from', 'date_to', 'sort']))
                                <a
                                    href="{{ route('admin.orders.index') }}"
                                    class="mt-5 inline-flex items-center justify-center rounded-xl bg-pesona-dark px-4 py-2.5 text-sm font-bold text-white transition hover:bg-black"
                                >
                                    Hapus Semua Filter
                                </a>
                            @endif
                        </div>
                    @else

                        <div class="border-b border-gray-100 px-4 py-3 sm:px-5">
                            <p class="text-sm text-gray-500">
                                Menampilkan
                                <span class="font-bold text-gray-900">{{ $orders->firstItem() ?? 0 }}–{{ $orders->lastItem() ?? 0 }}</span>
                                dari
                                <span class="font-bold text-gray-900">{{ number_format($orders->total()) }}</span>
                                pesanan
                            </p>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="w-full min-w-[1050px] text-left text-sm">
                                <thead class="bg-gray-50 text-xs uppercase tracking-wide text-gray-500">
                                    <tr>
                                        <th scope="col" class="px-4 py-4 font-bold">Nomor Pesanan</th>
                                        <th scope="col" class="px-4 py-4 font-bold">Pelanggan</th>
                                        <th scope="col" class="px-4 py-4 font-bold">Total</th>
                                        <th scope="col" class="px-4 py-4 font-bold">Status Pesanan</th>
                                        <th scope="col" class="px-4 py-4 font-bold">Pembayaran</th>
                                        <th scope="col" class="px-4 py-4 font-bold">Tanggal</th>
                                        <th scope="col" class="px-4 py-4 font-bold">Tindakan Berikutnya</th>
                                        <th scope="col" class="px-4 py-4 text-right font-bold">Aksi</th>
                                    </tr>
                                </thead>

                                <tbody class="divide-y divide-gray-100">
                                    @foreach ($orders as $order)
                                        @php
                                            $isSelected = $selectedOrder && $selectedOrder->id === $order->id;

                                            $statusClasses = [
                                                'pending_confirmation' => 'border-amber-200 bg-amber-50 text-amber-800',
                                                'awaiting_payment' => 'border-blue-200 bg-blue-50 text-blue-800',
                                                'processing' => 'border-purple-200 bg-purple-50 text-purple-800',
                                                'shipped' => 'border-indigo-200 bg-indigo-50 text-indigo-800',
                                                'completed' => 'border-emerald-200 bg-emerald-50 text-emerald-800',
                                                'cancelled' => 'border-red-200 bg-red-50 text-red-800',
                                            ];

                                            $paymentClasses = [
                                                'awaiting_confirmation' => 'border-gray-200 bg-gray-50 text-gray-700',
                                                'awaiting_payment' => 'border-blue-200 bg-blue-50 text-blue-700',
                                                'under_verification' => 'border-amber-200 bg-amber-50 text-amber-800',
                                                'paid' => 'border-emerald-200 bg-emerald-50 text-emerald-800',
                                                'rejected' => 'border-red-200 bg-red-50 text-red-800',
                                                'unpaid' => 'border-gray-200 bg-gray-50 text-gray-600',
                                                'refunded' => 'border-pink-200 bg-pink-50 text-pink-800',
                                            ];

                                            $badgeClass = $statusClasses[$order->status] ?? 'border-gray-200 bg-gray-50 text-gray-700';
                                            $pBadgeClass = $paymentClasses[$order->payment_status] ?? 'border-gray-200 bg-gray-50 text-gray-700';

                                            $nextAction = match (true) {
                                                $order->status === \App\Models\Order::STATUS_CANCELLED
                                                    => 'Pesanan dibatalkan',

                                                $order->payment_review_required
                                                    => 'Tinjau pembayaran dan stok',

                                                $order->payment_due_at && $order->payment_due_at->isPast()
                                                    => 'Periksa tenggat pembayaran',

                                                $order->shipping_cost === null
                                                    => 'Tetapkan ongkir',

                                                $order->payment_status === \App\Models\Order::PAYMENT_AWAITING_CONFIRMATION
                                                    => 'Kirim instruksi QRIS',

                                                $order->payment_status === \App\Models\Order::PAYMENT_AWAITING_PAYMENT
                                                    => 'Tunggu / cocokkan transaksi',

                                                $order->payment_status === \App\Models\Order::PAYMENT_UNDER_VERIFICATION
                                                    => 'Verifikasi transaksi aktual',

                                                $order->payment_status === \App\Models\Order::PAYMENT_PAID
                                                    && $order->status === \App\Models\Order::STATUS_PENDING_CONFIRMATION
                                                    => 'Siapkan pesanan',

                                                $order->status === \App\Models\Order::STATUS_PROCESSING
                                                    => 'Perbarui pengiriman',

                                                default => 'Lihat detail',
                                            };
                                        @endphp

                                        <tr class="transition-colors {{ $isSelected ? 'bg-orange-50/70' : 'hover:bg-gray-50/80' }}">
                                            {{-- NOMOR PESANAN --}}
                                            <td class="whitespace-nowrap border-l-4 px-4 py-4 {{ $isSelected ? 'border-pesona-orange' : 'border-transparent' }}">
                                                <a
                                                    href="{{ route('admin.orders.index', array_merge(request()->query(), ['selected' => $order->id])) }}"
                                                    aria-current="{{ $isSelected ? 'true' : 'false' }}"
                                                    class="font-bold text-pesona-dark transition hover:text-pesona-orange hover:underline focus:outline-none focus:ring-2 focus:ring-orange-200"
                                                >
                                                    {{ $order->order_number }}
                                                </a>
                                            </td>

                                            {{-- PELANGGAN --}}
                                            <td class="px-4 py-4">
                                                <p class="font-semibold text-gray-900">
                                                    {{ $order->user?->name ?? $order->recipient_name }}
                                                </p>
                                                <p class="mt-1 text-xs text-gray-500">
                                                    {{ $order->phone ?: 'Nomor HP belum tersedia' }}
                                                </p>
                                            </td>

                                            {{-- TOTAL --}}
                                            <td class="whitespace-nowrap px-4 py-4">
                                                @if ($order->total_amount !== null)
                                                    <span class="font-bold text-gray-900">
                                                        Rp{{ number_format(\App\Models\Order::rupiahToInt($order->total_amount), 0, ',', '.') }}
                                                    </span>
                                                @else
                                                    <span class="inline-flex rounded-lg border border-amber-200 bg-amber-50 px-2.5 py-1 text-xs font-semibold text-amber-800">
                                                        Menunggu ongkir
                                                    </span>
                                                @endif
                                            </td>

                                            {{-- STATUS PESANAN --}}
                                            <td class="whitespace-nowrap px-4 py-4">
                                                <span class="inline-flex rounded-full border px-2.5 py-1 text-xs font-semibold {{ $badgeClass }}">
                                                    {{ $order->status_label }}
                                                </span>
                                            </td>

                                            {{-- STATUS PEMBAYARAN --}}
                                            <td class="whitespace-nowrap px-4 py-4">
                                                <span class="inline-flex rounded-full border px-2.5 py-1 text-xs font-semibold {{ $pBadgeClass }}">
                                                    {{ $order->payment_status_label }}
                                                </span>

                                                @if ($order->payment_instructions_issued_at && $order->payment_due_at)
                                                    <p class="mt-1.5 text-[11px] {{ $order->payment_due_at->isPast() || $order->payment_review_required ? 'font-semibold text-red-700' : 'text-gray-500' }}">
                                                        Tenggat:
                                                        {{ $order->payment_due_at->timezone('Asia/Jakarta')->format('d/m/Y H:i') }} WIB
                                                    </p>
                                                @endif
                                            </td>

                                            {{-- TANGGAL --}}
                                            <td class="whitespace-nowrap px-4 py-4 text-gray-600">
                                                <p>{{ $order->created_at->timezone('Asia/Jakarta')->format('d M Y') }}</p>
                                                <p class="mt-1 text-xs text-gray-400">
                                                    {{ $order->created_at->timezone('Asia/Jakarta')->format('H:i') }} WIB
                                                </p>
                                            </td>

                                            {{-- TINDAKAN BERIKUTNYA --}}
                                            <td class="min-w-44 px-4 py-4">
                                                <div class="flex items-start gap-2">
                                                    <span class="mt-1.5 h-2 w-2 shrink-0 rounded-full {{ $order->payment_review_required || ($order->payment_due_at && $order->payment_due_at->isPast()) ? 'bg-red-500' : ($order->shipping_cost === null ? 'bg-amber-500' : 'bg-pesona-orange') }}"></span>
                                                    <span class="text-xs leading-5 text-gray-600">
                                                        {{ $nextAction }}
                                                    </span>
                                                </div>
                                            </td>

                                            {{-- AKSI --}}
                                            <td class="whitespace-nowrap px-4 py-4 text-right">
                                                <div class="flex items-center justify-end gap-2">
                                                    <a
                                                        href="{{ route('admin.orders.index', array_merge(request()->query(), ['selected' => $order->id])) }}"
                                                        aria-current="{{ $isSelected ? 'true' : 'false' }}"
                                                        class="hidden rounded-lg px-3 py-2 text-xs font-bold transition 2xl:inline-flex {{ $isSelected ? 'bg-orange-100 text-pesona-dark ring-1 ring-orange-200' : 'border border-gray-200 bg-white text-gray-600 hover:border-orange-200 hover:text-pesona-orange' }}"
                                                    >
                                                        {{ $isSelected ? 'Terpilih' : 'Pilih' }}
                                                    </a>

                                                    <a
                                                        href="{{ route('admin.orders.show', ['order' => $order->id] + request()->query()) }}"
                                                        class="inline-flex items-center justify-center gap-1.5 rounded-lg bg-pesona-dark px-3 py-2 text-xs font-bold text-white transition hover:bg-pesona-orange focus:outline-none focus:ring-2 focus:ring-orange-200"
                                                    >
                                                        Detail
                                                        <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14m-6-6 6 6-6 6"/>
                                                        </svg>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        {{-- PAGINATION --}}
                        <div class="border-t border-gray-100 bg-white px-4 py-4 sm:px-5">
                            {{ $orders->links() }}
                        </div>
                    @endif
                </div>
            </div>

            {{-- PANEL RINGKASAN PESANAN TERPILIH --}}
            <aside class="w-full shrink-0 space-y-3 2xl:sticky 2xl:top-6 2xl:w-[360px] 2xl:max-h-[calc(100vh-3rem)] 2xl:overflow-y-auto">

                <div class="flex items-center justify-between gap-3">
                    <div>
                        <h3 class="text-base font-black text-pesona-dark">Ringkasan Pesanan</h3>
                        <p class="mt-1 text-xs text-gray-500">Informasi pesanan yang dipilih</p>
                    </div>
                    @if ($selectedOrder)
                        <span class="rounded-full bg-orange-50 px-3 py-1 text-xs font-bold text-pesona-orange">
                            Terpilih
                        </span>
                    @endif
                </div>

                @if ($selectedOrder)
                    @include('admin.orders.partials.detail-panel', ['order' => $selectedOrder])
                @else
                    <div class="rounded-2xl border border-dashed border-gray-200 bg-white px-6 py-10 text-center">
                        <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-xl bg-gray-50 text-gray-400">
                            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true">
                                <rect x="4" y="3" width="16" height="18" rx="2"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 8h8M8 12h8M8 16h5"/>
                            </svg>
                        </div>
                        <h4 class="mt-4 text-sm font-bold text-gray-800">
                            Belum ada pesanan dipilih
                        </h4>
                        <p class="mx-auto mt-2 max-w-xs text-xs leading-5 text-gray-500">
                            Klik nomor pesanan atau tombol pilih pada tabel untuk melihat ringkasan di sini.
                        </p>
                    </div>
                @endif

            </aside>

        </div>
    </section>

</div>
@endsection