@extends('layouts.app')

@section('title', 'Pesanan Berhasil — Bakso Pesona')

@section('content')
<section class="min-h-[70vh] bg-[#FFF9F3] px-4 py-12 sm:px-6 sm:py-16 lg:px-8">
    <div class="mx-auto max-w-3xl rounded-3xl border border-orange-100 bg-white p-6 shadow-sm sm:p-10">
        <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-green-50 text-green-700">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="m5 12 4 4L19 6" /></svg>
        </div>
        <p class="mt-5 text-center text-sm font-bold uppercase tracking-[0.2em] text-[#B83B25]">Bakso Pesona</p>
        <h1 class="mt-2 text-center text-3xl font-black text-[#74191B]">Pesanan berhasil dibuat!</h1>
        <p class="mt-3 text-center text-sm leading-6 text-gray-600">Simpan nomor pesanan berikut. Admin akan menetapkan ongkir, menghitung total akhir, lalu mengirim instruksi pembayaran QRIS melalui WhatsApp.</p>

        <div class="mt-8 rounded-2xl bg-orange-50 p-5 text-center">
            <p class="text-sm text-gray-600">Nomor pesanan</p>
            <p class="mt-1 break-all text-xl font-black text-[#74191B]">{{ $order->order_number }}</p>
            <p class="mt-3 text-sm font-bold text-[#B83B25]">Menunggu konfirmasi admin</p>
        </div>

        <div class="mt-6 space-y-3">
            @foreach ($order->items as $item)
                <div class="flex justify-between gap-4 text-sm">
                    <span class="text-gray-600">{{ $item->product_name }} × {{ $item->quantity }}</span>
                    <span class="shrink-0 font-bold text-gray-800">Rp{{ number_format(\App\Models\Order::rupiahToInt($item->subtotal), 0, ',', '.') }}</span>
                </div>
            @endforeach
        </div>
        <div class="my-5 border-t border-dashed border-orange-200"></div>
        <div class="flex justify-between gap-4">
            <span class="font-bold text-gray-700">Subtotal produk</span>
            <span class="text-lg font-black text-[#B83B25]">Rp{{ number_format(\App\Models\Order::rupiahToInt($order->subtotal), 0, ',', '.') }}</span>
        </div>
        <p class="mt-3 text-xs leading-5 text-gray-500">Ongkos kirim dan total akhir belum ditetapkan. Jangan melakukan pembayaran sebelum instruksi QRIS resmi dari admin diterima. Tenggat belum dimulai.</p>

        <div class="mt-8 flex flex-col gap-3 sm:flex-row">
            <a href="{{ route('products.index') }}" class="inline-flex flex-1 justify-center rounded-full bg-[#B83B25] px-5 py-3 text-sm font-bold text-white transition hover:bg-[#74191B]">Lanjut belanja</a>
            <a href="{{ route('home') }}" class="inline-flex flex-1 justify-center rounded-full border border-orange-200 px-5 py-3 text-sm font-bold text-[#74191B] transition hover:bg-orange-50">Kembali ke beranda</a>
        </div>
    </div>
</section>
@endsection