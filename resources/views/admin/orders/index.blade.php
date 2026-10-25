@extends('layouts.admin')

@section('title', 'Daftar Pesanan')
@section('header_title', 'Manajemen Pesanan')

@section('content')
<div class="space-y-6">
    {{-- HEADER HALAMAN --}}
    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-black text-pesona-dark sm:text-3xl">
                Pesanan
            </h1>
            <p class="mt-1 text-sm text-gray-600">
                Pantau dan kelola pesanan pelanggan Bakso Pesona.
            </p>
        </div>

        <div class="text-xs text-gray-500 font-medium">
            Total {{ $orders->total() }} pesanan ditemukan
        </div>
    </div>

    {{-- FORM FILTER, PENCARIAN & SORT --}}
    <form
        method="GET"
        action="{{ route('admin.orders.index') }}"
        class="rounded-2xl border border-orange-100 bg-white p-5 shadow-sm"
    >
        @if (request('selected'))
            <input type="hidden" name="selected" value="{{ request('selected') }}">
        @endif

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-6">
            {{-- PENCARIAN --}}
            <div class="lg:col-span-1">
                <label for="search" class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-gray-500">
                    Cari Pesanan
                </label>
                <input
                    type="text"
                    id="search"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="No. pesanan, nama, HP..."
                    class="w-full rounded-xl border border-gray-200 px-3 py-2 text-xs text-gray-900 placeholder:text-gray-400 focus:border-pesona-orange focus:outline-none focus:ring-1 focus:ring-pesona-orange"
                >
            </div>

            {{-- FILTER STATUS PESANAN --}}
            <div>
                <label for="status" class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-gray-500">
                    Status Pesanan
                </label>
                <select
                    id="status"
                    name="status"
                    class="w-full rounded-xl border border-gray-200 px-3 py-2 text-xs text-gray-900 focus:border-pesona-orange focus:outline-none focus:ring-1 focus:ring-pesona-orange"
                >
                    <option value="">Semua Status</option>
                    @foreach ($statusLabels as $value => $label)
                        <option value="{{ $value }}" @selected(request('status') === $value)>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- FILTER STATUS PEMBAYARAN --}}
            <div>
                <label for="payment_status" class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-gray-500">
                    Status Bayar
                </label>
                <select
                    id="payment_status"
                    name="payment_status"
                    class="w-full rounded-xl border border-gray-200 px-3 py-2 text-xs text-gray-900 focus:border-pesona-orange focus:outline-none focus:ring-1 focus:ring-pesona-orange"
                >
                    <option value="">Semua Pembayaran</option>
                    @foreach ($paymentStatusLabels as $pValue => $pLabel)
                        <option value="{{ $pValue }}" @selected(request('payment_status') === $pValue)>
                            {{ $pLabel }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="attention" class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-gray-500">
                    Tindakan Berikutnya
                </label>
                <select
                    id="attention"
                    name="attention"
                    class="w-full rounded-xl border border-gray-200 px-3 py-2 text-xs text-gray-900 focus:border-pesona-orange focus:outline-none focus:ring-1 focus:ring-pesona-orange"
                >
                    <option value="">Semua Tindakan</option>
                    <option value="shipping" @selected($attention === 'shipping')>Isi ongkir</option>
                    <option value="instructions" @selected($attention === 'instructions')>Kirim instruksi</option>
                    <option value="verification" @selected($attention === 'verification')>Verifikasi transaksi</option>
                    <option value="payment_review" @selected($attention === 'payment_review')>Review pembayaran</option>
                    <option value="ready_to_pack" @selected($attention === 'ready_to_pack')>Siap dikemas</option>
                </select>
            </div>

            {{-- PENGURUTAN --}}
            <div>
                <label for="sort" class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-gray-500">
                    Urutan
                </label>
                <select
                    id="sort"
                    name="sort"
                    class="w-full rounded-xl border border-gray-200 px-3 py-2 text-xs text-gray-900 focus:border-pesona-orange focus:outline-none focus:ring-1 focus:ring-pesona-orange"
                >
                    <option value="latest" @selected(($sort ?? request('sort')) === 'latest')>Terbaru (Default)</option>
                    <option value="oldest" @selected(($sort ?? request('sort')) === 'oldest')>Terlama</option>
                    <option value="total_high" @selected(($sort ?? request('sort')) === 'total_high')>Total Tertinggi</option>
                    <option value="total_low" @selected(($sort ?? request('sort')) === 'total_low')>Total Terendah</option>
                </select>
            </div>

            {{-- TOMBOL AKSI FILTER --}}
            <div class="flex items-end gap-2">
                <button
                    type="submit"
                    class="flex-1 rounded-xl bg-pesona-dark px-3 py-2 text-xs font-bold text-white transition hover:bg-black"
                >
                    Terapkan
                </button>

                @if (request()->hasAny(['search', 'status', 'payment_status', 'sort']))
                    <a
                        href="{{ route('admin.orders.index') }}"
                        class="rounded-xl border border-gray-200 bg-gray-50 px-3 py-2 text-center text-xs font-semibold text-gray-700 transition hover:bg-gray-100"
                    >
                        Reset
                    </a>
                @endif
            </div>
        </div>
    </form>

    {{-- AREA KERJA UTAMA: SPLIT LAYOUT (TABEL DI KIRI/TENGAH + PANEL DETAIL DI KANAN) --}}
    <div class="flex flex-col lg:flex-row items-start gap-6">

        {{-- AREA TABEL PESANAN --}}
        <div class="flex-1 min-w-0 w-full space-y-4">
            <div class="overflow-hidden rounded-2xl border border-orange-100 bg-white shadow-sm">
                @if ($orders->isEmpty())
                    <div class="px-6 py-16 text-center">
                        <div class="text-4xl" aria-hidden="true">📦</div>
                        <h2 class="mt-4 text-base font-bold text-pesona-dark">
                            Tidak ada pesanan yang sesuai filter.
                        </h2>
                        <p class="mt-1 text-xs text-gray-500">
                            Coba sesuaikan kata kunci pencarian atau reset filter.
                        </p>
                        @if (request()->hasAny(['search', 'status', 'payment_status', 'sort']))
                            <a
                                href="{{ route('admin.orders.index') }}"
                                class="mt-4 inline-flex rounded-xl bg-orange-100 px-4 py-2 text-xs font-bold text-pesona-dark hover:bg-orange-200"
                            >
                                Hapus Filter
                            </a>
                        @endif
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs">
                            <thead class="bg-gray-50/80 uppercase tracking-wider text-gray-500 text-[11px]">
                                <tr>
                                    <th class="px-4 py-3.5 font-semibold">Nomor Pesanan</th>
                                    <th class="px-4 py-3.5 font-semibold">Pelanggan</th>
                                    <th class="px-4 py-3.5 font-semibold">Total</th>
                                    <th class="px-4 py-3.5 font-semibold">Status Pesanan</th>
                                    <th class="px-4 py-3.5 font-semibold">Pembayaran</th>
                                    <th class="px-4 py-3.5 font-semibold">Tenggat</th>
                                    <th class="px-4 py-3.5 font-semibold">Tanggal</th>
                                    <th class="px-4 py-3.5 font-semibold">Tindakan Berikutnya</th>
                                    <th class="px-4 py-3.5 text-right font-semibold">Aksi</th>
                                </tr>
                            </thead>

                            <tbody class="divide-y divide-gray-100">
                                @foreach ($orders as $order)
                                    @php
                                        $isSelected = $selectedOrder && $selectedOrder->id === $order->id;
                                        $statusClasses = [
                                            'pending_confirmation' => 'bg-amber-100 text-amber-800',
                                            'awaiting_payment' => 'bg-blue-100 text-blue-800',
                                            'processing' => 'bg-purple-100 text-purple-800',
                                            'shipped' => 'bg-indigo-100 text-indigo-800',
                                            'completed' => 'bg-emerald-100 text-emerald-800',
                                            'cancelled' => 'bg-red-100 text-red-800',
                                        ];
                                        $badgeClass = $statusClasses[$order->status] ?? 'bg-gray-100 text-gray-800';

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
                                        $nextAction = match (true) {
                                            $order->status === \App\Models\Order::STATUS_CANCELLED => 'Tidak ada · pesanan dibatalkan',
                                            $order->payment_review_required || ($order->payment_due_at && $order->payment_due_at->isPast()) => 'Tinjau mutasi dan status stok',
                                            $order->shipping_cost === null => 'Tetapkan ongkir',
                                            $order->payment_status === \App\Models\Order::PAYMENT_AWAITING_CONFIRMATION => 'Kirim instruksi QRIS',
                                            $order->payment_status === \App\Models\Order::PAYMENT_AWAITING_PAYMENT => 'Tunggu / cocokkan transaksi',
                                            $order->payment_status === \App\Models\Order::PAYMENT_UNDER_VERIFICATION => 'Verifikasi transaksi aktual',
                                            $order->payment_status === \App\Models\Order::PAYMENT_PAID && $order->status === \App\Models\Order::STATUS_PENDING_CONFIRMATION => 'Siapkan pesanan',
                                            $order->status === \App\Models\Order::STATUS_PROCESSING => 'Perbarui pengiriman',
                                            default => 'Lihat detail',
                                        };
                                    @endphp

                                    <tr class="transition {{ $isSelected ? 'bg-orange-50/70 border-l-4 border-pesona-orange' : 'hover:bg-orange-50/20' }}">
                                        {{-- NOMOR PESANAN --}}
                                        <td class="whitespace-nowrap px-4 py-3.5 font-bold text-pesona-dark">
                                            <a
                                                href="{{ route('admin.orders.index', array_merge(request()->query(), ['selected' => $order->id])) }}"
                                                class="hover:text-pesona-orange hover:underline focus:outline-none"
                                            >
                                                {{ $order->order_number }}
                                            </a>
                                        </td>

                                        {{-- PELANGGAN --}}
                                        <td class="px-4 py-3.5">
                                            <p class="font-bold text-gray-900">
                                                {{ $order->user?->name ?? $order->recipient_name }}
                                            </p>
                                            <p class="text-[11px] text-gray-500">
                                                {{ $order->phone }}
                                            </p>
                                        </td>

                                        {{-- TOTAL --}}
                                        <td class="whitespace-nowrap px-4 py-3.5 font-black text-pesona-dark">
                                            @if ($order->total_amount !== null)
                                                Rp{{ number_format(\App\Models\Order::rupiahToInt($order->total_amount), 0, ',', '.') }}
                                            @else
                                                <span class="rounded bg-amber-50 px-2 py-0.5 text-[10px] font-bold text-amber-700 border border-amber-200">
                                                    Menunggu ongkir
                                                </span>
                                            @endif
                                        </td>

                                        {{-- STATUS PESANAN --}}
                                        <td class="whitespace-nowrap px-4 py-3.5">
                                            <span class="inline-flex rounded-full px-2.5 py-0.5 text-[11px] font-bold {{ $badgeClass }}">
                                                {{ $order->status_label }}
                                            </span>
                                        </td>

                                        {{-- PEMBAYARAN --}}
                                        <td class="whitespace-nowrap px-4 py-3.5">
                                            <span class="inline-flex rounded-full px-2.5 py-0.5 text-[11px] font-semibold {{ $pBadgeClass }}">
                                                {{ $order->payment_status_label }}
                                            </span>
                                        </td>

                                        {{-- TENGGAT --}}
                                        <td class="whitespace-nowrap px-4 py-3.5 text-[11px]">
                                            @if ($order->payment_instructions_issued_at && $order->payment_due_at)
                                                <span class="{{ $order->payment_due_at->isPast() || $order->payment_review_required ? 'font-bold text-red-700' : 'text-gray-600' }}">
                                                    {{ $order->payment_due_at->timezone('Asia/Jakarta')->format('d/m H:i') }} WIB
                                                </span>
                                            @else
                                                <span class="text-gray-400">Belum aktif</span>
                                            @endif
                                        </td>

                                        {{-- TANGGAL --}}
                                        <td class="whitespace-nowrap px-4 py-3.5 text-gray-500">
                                            {{ $order->created_at->format('d/m/Y H:i') }}
                                        </td>

                                        <td class="min-w-40 px-4 py-3.5 text-[11px] font-semibold text-gray-700">
                                            {{ $nextAction }}
                                        </td>

                                        {{-- AKSI --}}
                                        <td class="whitespace-nowrap px-4 py-3.5 text-right">
                                            {{-- DESKTOP LINK: PILIH PANEL KANAN --}}
                                            <a
                                                href="{{ route('admin.orders.index', array_merge(request()->query(), ['selected' => $order->id])) }}"
                                                class="hidden lg:inline-flex items-center gap-1 rounded-lg px-2.5 py-1 text-xs font-bold transition {{ $isSelected ? 'bg-pesona-orange text-white shadow-sm' : 'bg-orange-50 text-pesona-orange hover:bg-pesona-orange hover:text-white' }}"
                                            >
                                                {{ $isSelected ? 'Terpilih' : 'Pilih' }}
                                            </a>

                                            {{-- MOBILE LINK: BUKA HALAMAN DETAIL PENUH --}}
                                            <a
                                                href="{{ route('admin.orders.show', $order) }}"
                                                class="inline-flex lg:hidden items-center gap-1 rounded-lg bg-orange-50 px-2.5 py-1 text-xs font-bold text-pesona-orange hover:bg-pesona-orange hover:text-white"
                                            >
                                                Detail &rarr;
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="border-t border-gray-100 px-4 py-3">
                        {{ $orders->links() }}
                    </div>
                @endif
            </div>
        </div>

        {{-- PANEL DETAIL DI KANAN (DESKTOP) --}}
        <div class="hidden lg:block w-96 xl:w-[420px] 2xl:w-[460px] shrink-0 sticky top-6 self-start max-h-[calc(100vh-5rem)] overflow-y-auto">
            @if ($selectedOrder)
                @include('admin.orders.partials.detail-panel', ['order' => $selectedOrder])
            @else
                <div class="rounded-2xl border border-dashed border-gray-200 bg-white p-8 text-center text-gray-400">
                    <p class="text-sm font-semibold">Pilih salah satu pesanan untuk melihat detail dan tindakan.</p>
                </div>
            @endif
        </div>

    </div>
</div>
@endsection
