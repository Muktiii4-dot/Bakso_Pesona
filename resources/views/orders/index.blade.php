
@extends('layouts.app')

@section('title', 'Riwayat Pesanan - Bakso Pesona')

@section('content')
<section class="bg-pesona-cream py-12">
    <div class="mx-auto max-w-7xl px-5 lg:px-8">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <span class="text-sm font-bold text-pesona-orange">
                    Akun Pelanggan
                </span>
                <h1 class="mt-2 text-3xl font-black text-gray-900">
                    Riwayat Pesanan
                </h1>
                <p class="mt-2 text-sm text-gray-500">
                    Pantau pesanan yang pernah kamu buat.
                </p>
            </div>

            <a href="{{ route('products.index') }}"
                class="rounded-xl bg-pesona-orange px-5 py-3 text-sm font-bold text-white transition hover:bg-pesona-red">
                Belanja Lagi
            </a>
        </div>

        @if ($orders->isEmpty())
            <div class="mt-8 rounded-3xl border border-orange-100 bg-white px-6 py-16 text-center">
                <div class="text-5xl" aria-hidden="true">🛍️</div>
                <h2 class="mt-5 text-xl font-black text-gray-900">
                    Belum Ada Pesanan
                </h2>
                <p class="mx-auto mt-2 max-w-md text-sm leading-6 text-gray-500">
                    Pesanan yang berhasil kamu buat akan tampil di halaman ini.
                </p>
                <a href="{{ route('products.index') }}"
                    class="mt-6 inline-flex rounded-xl bg-pesona-orange px-6 py-3 text-sm font-bold text-white">
                    Lihat Menu
                </a>
            </div>
        @else
            <div class="mt-8 space-y-4">
                @foreach ($orders as $order)
                    <article class="rounded-2xl border border-orange-100 bg-white p-5 shadow-sm sm:p-6">
                        <div class="flex flex-wrap items-start justify-between gap-4">
                            <div>
                                <p class="text-xs font-semibold text-gray-500">
                                    Nomor Pesanan
                                </p>
                                <p class="mt-1 font-black text-gray-900">
                                    {{ $order->order_number }}
                                </p>
                                <p class="mt-2 text-sm text-gray-500">
                                    {{ $order->created_at->format('d M Y, H:i') }}
                                </p>
                            </div>

                            <span class="rounded-full bg-orange-50 px-3 py-1.5 text-xs font-bold text-orange-800">
                                {{ str($order->status)->replace('_', ' ')->headline() }}
                            </span>
                        </div>

                        <div class="mt-5 flex flex-wrap items-end justify-between gap-4 border-t border-gray-100 pt-4">
                            <div>
                                <p class="text-xs text-gray-500">Jumlah jenis produk</p>
                                <p class="mt-1 text-sm font-semibold text-gray-800">
                                    {{ $order->items_count }} jenis produk
                                </p>

                                <p class="mt-3 text-xs text-gray-500">Subtotal produk</p>
                                <p class="mt-1 text-sm font-semibold text-gray-800">
                                    Rp{{ number_format(\App\Models\Order::rupiahToInt($order->subtotal), 0, ',', '.') }}
                                </p>

                                <p class="mt-3 text-xs text-gray-500">Total pesanan</p>
                                <p class="mt-1 text-lg font-black text-pesona-red">
                                    @if ($order->total_amount === null)
                                        Menunggu konfirmasi
                                    @else
                                        Rp{{ number_format(\App\Models\Order::rupiahToInt($order->total_amount), 0, ',', '.') }}
                                    @endif
                                </p>
                            </div>

                            <a href="{{ route('orders.show', $order) }}"
                                class="rounded-xl border border-orange-200 px-5 py-3 text-sm font-bold text-pesona-red transition hover:bg-orange-50">
                                Lihat Detail
                            </a>
                        </div>
                    </article>
                @endforeach
            </div>

            <div class="mt-8">
                {{ $orders->links() }}
            </div>
        @endif
    </div>
</section>
@endsection
