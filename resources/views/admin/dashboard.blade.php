@extends('layouts.admin')

@section('title', 'Dashboard')
@section('header_title', 'Dashboard Operasional')

@section('content')
<div class="space-y-8">
    {{-- WELCOME BANNER --}}
    <div class="rounded-3xl bg-gradient-to-r from-[#74191B] to-[#B83B25] p-6 text-white shadow-sm sm:p-8">
        <div class="max-w-2xl">
            <span class="inline-block rounded-full bg-white/15 px-3 py-1 text-xs font-semibold text-[#FFB45E]">
                Selamat Datang, Admin Bakso Pesona
            </span>
            <h1 class="mt-3 text-2xl font-black tracking-tight sm:text-3xl">
                Ringkasan Operasional Toko
            </h1>
            <p class="mt-2 text-sm text-white/80">
                Pantau pesanan, pengiriman instruksi QRIS, verifikasi transaksi, dan tindakan operasional yang masih tertunda.
            </p>
        </div>
    </div>

    {{-- KARTU STATISTIK PESANAN --}}
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
        {{-- TOTAL PESANAN --}}
        <div class="rounded-2xl border border-orange-100 bg-white p-6 shadow-sm">
            <div class="flex items-center justify-between">
                <p class="text-xs font-bold uppercase tracking-wider text-gray-500">Total Pesanan</p>
                <span class="rounded-xl bg-orange-50 p-2 text-pesona-dark">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                    </svg>
                </span>
            </div>
            <p class="mt-4 text-3xl font-black text-pesona-dark">{{ $totalOrders }}</p>
            <p class="mt-1 text-xs text-gray-500">Semua pesanan yang pernah dibuat</p>
        </div>

        {{-- MENUNGGU KONFIRMASI ONGKIR --}}
        <div class="rounded-2xl border border-amber-200 bg-amber-50/50 p-6 shadow-sm">
            <div class="flex items-center justify-between">
                <p class="text-xs font-bold uppercase tracking-wider text-amber-800">Menunggu Ongkir</p>
                <span class="rounded-xl bg-amber-100 p-2 text-amber-800">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </span>
            </div>
            <p class="mt-4 text-3xl font-black text-amber-900">{{ $ordersAwaitingShipping }}</p>
            <a href="{{ route('admin.orders.index', ['attention' => 'shipping']) }}" class="mt-1 inline-flex items-center gap-1 text-xs font-semibold text-amber-700 hover:underline">
                Perlu ongkir &rarr;
            </a>
        </div>

        {{-- MENUNGGU PEMBAYARAN --}}
        <div class="rounded-2xl border border-blue-100 bg-white p-6 shadow-sm">
            <div class="flex items-center justify-between">
                <p class="text-xs font-bold uppercase tracking-wider text-gray-500">Menunggu Pembayaran</p>
                <span class="rounded-xl bg-blue-50 p-2 text-blue-600">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </span>
            </div>
            <p class="mt-4 text-3xl font-black text-blue-600">{{ $awaitingPaymentOrders }}</p>
            <a href="{{ route('admin.orders.index', ['payment_status' => \App\Models\Order::PAYMENT_AWAITING_PAYMENT]) }}" class="mt-1 inline-flex items-center gap-1 text-xs font-semibold text-blue-600 hover:underline">
                Lihat pembayaran aktif &rarr;
            </a>
        </div>

        {{-- SEDANG DIPROSES --}}
        <div class="rounded-2xl border border-emerald-100 bg-white p-6 shadow-sm">
            <div class="flex items-center justify-between">
                <p class="text-xs font-bold uppercase tracking-wider text-gray-500">Sedang Diproses</p>
                <span class="rounded-xl bg-emerald-50 p-2 text-emerald-600">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </span>
            </div>
            <p class="mt-4 text-3xl font-black text-emerald-600">{{ $processingOrders ?? 0 }}</p>
            <a href="{{ route('admin.orders.index', ['status' => 'processing']) }}" class="mt-1 inline-flex items-center gap-1 text-xs font-semibold text-emerald-600 hover:underline">
                Siap disiapkan & dikirim &rarr;
            </a>
        </div>
    </div>

    <section class="rounded-2xl border border-orange-100 bg-white p-5 shadow-sm sm:p-6">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h2 class="text-base font-black text-pesona-dark">Tindakan yang Memerlukan Perhatian</h2>
                <p class="mt-1 text-xs text-gray-500">Angka dihitung langsung dari status pesanan, pembayaran, stok, dan moderasi saat ini.</p>
            </div>
            <a href="{{ route('admin.audit.index') }}" class="text-xs font-bold text-pesona-orange hover:underline">Lihat riwayat audit &rarr;</a>
        </div>
        <div class="mt-4 grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
            @foreach ([
                ['label' => 'Ongkir belum diisi', 'count' => $ordersAwaitingShipping, 'attention' => 'shipping', 'class' => 'border-amber-200 bg-amber-50 text-amber-900'],
                ['label' => 'Instruksi belum dikirim', 'count' => $ordersAwaitingInstructions, 'attention' => 'instructions', 'class' => 'border-blue-200 bg-blue-50 text-blue-900'],
                ['label' => 'Transaksi diverifikasi', 'count' => $paymentsUnderVerification, 'attention' => 'verification', 'class' => 'border-orange-200 bg-orange-50 text-orange-900'],
                ['label' => 'Pembayaran terlambat / review', 'count' => $paymentsNeedingReview, 'attention' => 'payment_review', 'class' => 'border-red-200 bg-red-50 text-red-900'],
                ['label' => 'Siap dikemas', 'count' => $ordersReadyToPack, 'attention' => 'ready_to_pack', 'class' => 'border-emerald-200 bg-emerald-50 text-emerald-900'],
            ] as $action)
                <a href="{{ route('admin.orders.index', ['attention' => $action['attention']]) }}" class="rounded-xl border p-4 transition hover:shadow-sm {{ $action['class'] }}">
                    <span class="block text-xs font-bold">{{ $action['label'] }}</span>
                    <span class="mt-2 block text-2xl font-black">{{ $action['count'] }}</span>
                    <span class="mt-1 block text-[11px] font-semibold">Buka daftar tindakan &rarr;</span>
                </a>
            @endforeach
            <a href="{{ route('admin.products.index', ['low_stock' => 1]) }}" class="rounded-xl border border-rose-200 bg-rose-50 p-4 text-rose-900 transition hover:shadow-sm">
                <span class="block text-xs font-bold">Produk stok rendah (≤ 5)</span>
                <span class="mt-2 block text-2xl font-black">{{ $lowStockProducts }}</span>
                <span class="mt-1 block text-[11px] font-semibold">Periksa produk &rarr;</span>
            </a>
            <a href="{{ route('admin.ratings.index', ['status' => 'pending']) }}" class="rounded-xl border border-violet-200 bg-violet-50 p-4 text-violet-900 transition hover:shadow-sm">
                <span class="block text-xs font-bold">Ulasan menunggu moderasi</span>
                <span class="mt-2 block text-2xl font-black">{{ $pendingRatings }}</span>
                <span class="mt-1 block text-[11px] font-semibold">Buka ulasan &rarr;</span>
            </a>
        </div>
    </section>

    {{-- KARTU INFO TOKO & PRODUK --}}
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
        <div class="flex items-center gap-5 rounded-2xl border border-orange-100 bg-white p-6 shadow-sm">
            <div class="rounded-2xl bg-orange-100 p-4 text-pesona-orange">
                <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                </svg>
            </div>
            <div>
                <p class="text-xs font-bold uppercase tracking-wider text-gray-500">Total Produk Aktif</p>
                <p class="mt-1 text-2xl font-black text-pesona-dark">{{ $totalProducts }} Produk</p>
                <p class="mt-1 text-xs text-gray-500">Tersedia di katalog Bakso Pesona</p>
            </div>
        </div>

        <div class="flex items-center gap-5 rounded-2xl border border-orange-100 bg-white p-6 shadow-sm">
            <div class="rounded-2xl bg-rose-100 p-4 text-pesona-dark">
                <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
            </div>
            <div>
                <p class="text-xs font-bold uppercase tracking-wider text-gray-500">Pelanggan Terdaftar</p>
                <p class="mt-1 text-2xl font-black text-pesona-dark">{{ $totalCustomers }} Pengguna</p>
                <p class="mt-1 text-xs text-gray-500">Akun pelanggan terdaftar di sistem</p>
            </div>
        </div>
    </div>

    {{-- TABEL PESANAN TERBARU --}}
    <div class="overflow-hidden rounded-2xl border border-orange-100 bg-white shadow-sm">
        <div class="flex flex-col gap-2 border-b border-gray-100 px-6 py-5 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-lg font-bold text-pesona-dark">
                    Pesanan Terbaru
                </h2>
                <p class="text-xs text-gray-500">5 pesanan terakhir yang masuk ke toko</p>
            </div>

            <a
                href="{{ route('admin.orders.index') }}"
                class="inline-flex items-center gap-1 text-sm font-bold text-pesona-orange hover:underline"
            >
                Lihat Semua Pesanan &rarr;
            </a>
        </div>

        @if ($latestOrders->isEmpty())
            <div class="px-6 py-12 text-center text-gray-500">
                Belum ada pesanan yang masuk.
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-gray-50/80 text-xs uppercase tracking-wider text-gray-500">
                        <tr>
                            <th class="px-6 py-4 font-semibold">Nomor Pesanan</th>
                            <th class="px-6 py-4 font-semibold">Pelanggan</th>
                            <th class="px-6 py-4 font-semibold">Status Pesanan</th>
                            <th class="px-6 py-4 font-semibold">Status Bayar</th>
                            <th class="px-6 py-4 font-semibold">Total Biaya</th>
                            <th class="px-6 py-4 font-semibold">Tanggal</th>
                            <th class="px-6 py-4 text-right font-semibold">Aksi</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-100">
                        @foreach ($latestOrders as $order)
                            <tr class="hover:bg-orange-50/30 transition">
                                <td class="whitespace-nowrap px-6 py-4 font-bold text-pesona-dark">
                                    {{ $order->order_number }}
                                </td>

                                <td class="px-6 py-4">
                                    <p class="font-medium text-gray-900">
                                        {{ $order->user?->name ?? $order->recipient_name }}
                                    </p>
                                    <p class="text-xs text-gray-500">{{ $order->phone }}</p>
                                </td>

                                <td class="whitespace-nowrap px-6 py-4">
                                    @php
                                        $statusClasses = [
                                            'pending_confirmation' => 'bg-amber-100 text-amber-800',
                                            'awaiting_payment' => 'bg-blue-100 text-blue-800',
                                            'processing' => 'bg-purple-100 text-purple-800',
                                            'shipped' => 'bg-indigo-100 text-indigo-800',
                                            'completed' => 'bg-emerald-100 text-emerald-800',
                                            'cancelled' => 'bg-red-100 text-red-800',
                                        ];
                                        $badgeClass = $statusClasses[$order->status] ?? 'bg-gray-100 text-gray-800';
                                    @endphp
                                    <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-bold {{ $badgeClass }}">
                                        {{ $order->status_label }}
                                    </span>
                                </td>

                                <td class="whitespace-nowrap px-6 py-4">
                                    @php
                                        $paymentClasses = [
                                            'awaiting_confirmation' => 'bg-gray-100 text-gray-700',
                                            'awaiting_payment' => 'bg-blue-50 text-blue-700',
                                            'under_verification' => 'bg-amber-50 text-amber-700',
                                            'paid' => 'bg-emerald-50 text-emerald-700',
                                            'rejected' => 'bg-red-50 text-red-700',
                                            'unpaid' => 'bg-gray-100 text-gray-600',
                                            'refunded' => 'bg-pink-50 text-pink-700',
                                        ];
                                        $pBadgeClass = $paymentClasses[$order->payment_status] ?? 'bg-gray-100 text-gray-700';
                                    @endphp
                                    <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $pBadgeClass }}">
                                        {{ $order->payment_status_label }}
                                    </span>
                                </td>

                                <td class="whitespace-nowrap px-6 py-4 font-semibold text-gray-900">
                                    @if ($order->total_amount !== null)
                                        Rp{{ number_format(\App\Models\Order::rupiahToInt($order->total_amount), 0, ',', '.') }}
                                    @else
                                        <span class="text-xs font-medium text-amber-700">Menunggu ongkir</span>
                                    @endif
                                </td>

                                <td class="whitespace-nowrap px-6 py-4 text-xs text-gray-500">
                                    {{ $order->created_at->format('d/m/Y H:i') }}
                                </td>

                                <td class="whitespace-nowrap px-6 py-4 text-right">
                                    <a
                                        href="{{ route('admin.orders.show', $order) }}"
                                        class="rounded-lg bg-orange-50 px-3 py-1.5 text-xs font-bold text-pesona-orange transition hover:bg-pesona-orange hover:text-white"
                                    >
                                        Kelola
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
@endsection
