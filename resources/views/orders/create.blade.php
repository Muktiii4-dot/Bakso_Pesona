@extends('layouts.app')

@section('title', 'Checkout — Bakso Pesona')

@section('content')
<section class="min-h-[70vh] bg-[#FFF9F3] px-4 py-10 sm:px-6 sm:py-14 lg:px-8">
    <div class="mx-auto max-w-7xl">
        <div class="mb-8">
            <p class="text-sm font-bold uppercase tracking-[0.2em] text-[#B83B25]">Bakso Pesona</p>
            <h1 class="mt-2 text-3xl font-black tracking-tight text-[#74191B] sm:text-4xl">Checkout</h1>
            <p class="mt-3 text-sm leading-6 text-gray-600 sm:text-base">Lengkapi alamat pengiriman. Ongkir dan total dihitung otomatis berdasarkan provinsi; pembayaran QRIS tetap menunggu instruksi resmi sampai integrasi API merchant dikonfirmasi.</p>
        </div>

        @if ($errors->any())
            <div role="alert" class="mb-6 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                <p class="font-bold">Periksa kembali data berikut:</p>
                <ul class="mt-2 list-inside list-disc">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
            </div>
        @endif

        <form action="{{ route('checkout.store') }}" method="POST" class="grid items-start gap-6 lg:grid-cols-[1fr_360px]">
            @csrf
            <input type="hidden" name="checkout_token" value="{{ $checkoutToken }}">
            <div class="rounded-2xl border border-orange-100 bg-white p-5 shadow-sm sm:p-7">
                <h2 class="text-xl font-extrabold text-[#74191B]">Informasi Penerima</h2>
                <div class="mt-6 grid gap-5">
                    <div>
                        <label for="recipient_name" class="mb-2 block text-sm font-bold text-gray-700">Nama penerima <span class="text-red-600">*</span></label>
                        <input id="recipient_name" name="recipient_name" value="{{ old('recipient_name', auth('customer')->user()->name) }}" required maxlength="255" autocomplete="name" class="w-full rounded-xl border border-orange-200 px-4 py-3 text-sm text-gray-800 outline-none transition focus:border-[#B83B25] focus:ring-2 focus:ring-orange-100">
                        @error('recipient_name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="phone" class="mb-2 block text-sm font-bold text-gray-700">Nomor HP/WhatsApp <span class="text-red-600">*</span></label>
                        <input id="phone" name="phone" type="tel" value="{{ old('phone') }}" required maxlength="30" autocomplete="tel" placeholder="Contoh: 08xxxxxxxxxx" class="w-full rounded-xl border border-orange-200 px-4 py-3 text-sm text-gray-800 outline-none transition focus:border-[#B83B25] focus:ring-2 focus:ring-orange-100">
                        @error('phone')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="shipping_province" class="mb-2 block text-sm font-bold text-gray-700">Provinsi <span class="text-red-600">*</span></label>
                        <select id="shipping_province" name="shipping_province" required class="w-full rounded-xl border border-orange-200 bg-white px-4 py-3 text-sm text-gray-800 outline-none focus:border-[#B83B25] focus:ring-2 focus:ring-orange-100">
                            <option value="">Pilih provinsi</option>
                            @foreach ($provinces as $province)
                                <option value="{{ $province }}" @selected(old('shipping_province') === $province)>{{ $province }}</option>
                            @endforeach
                        </select>
                        @error('shipping_province')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div class="grid gap-5 sm:grid-cols-2">
                        <div>
                            <label for="shipping_city" class="mb-2 block text-sm font-bold text-gray-700">Kota/kabupaten <span class="text-red-600">*</span></label>
                            <input id="shipping_city" name="shipping_city" value="{{ old('shipping_city') }}" required maxlength="150" autocomplete="address-level2" placeholder="Contoh: Kabupaten Indramayu" class="w-full rounded-xl border border-orange-200 px-4 py-3 text-sm text-gray-800 outline-none focus:border-[#B83B25] focus:ring-2 focus:ring-orange-100">
                            @error('shipping_city')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="shipping_postal_code" class="mb-2 block text-sm font-bold text-gray-700">Kode pos <span class="text-red-600">*</span></label>
                            <input id="shipping_postal_code" name="shipping_postal_code" value="{{ old('shipping_postal_code') }}" required inputmode="numeric" pattern="[0-9]{5}" minlength="5" maxlength="5" autocomplete="postal-code" placeholder="5 digit kode pos" class="w-full rounded-xl border border-orange-200 px-4 py-3 text-sm text-gray-800 outline-none focus:border-[#B83B25] focus:ring-2 focus:ring-orange-100">
                            @error('shipping_postal_code')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                    </div>
                    <div>
                        <label for="shipping_address" class="mb-2 block text-sm font-bold text-gray-700">Alamat lengkap <span class="text-red-600">*</span></label>
                        <textarea id="shipping_address" name="shipping_address" rows="4" required maxlength="2000" autocomplete="street-address" placeholder="Jalan, nomor rumah, RT/RW, desa/kelurahan, kecamatan, kota/kabupaten, provinsi, kode pos" class="w-full rounded-xl border border-orange-200 px-4 py-3 text-sm text-gray-800 outline-none transition focus:border-[#B83B25] focus:ring-2 focus:ring-orange-100">{{ old('shipping_address') }}</textarea>
                        @error('shipping_address')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="delivery_landmark" class="mb-2 block text-sm font-bold text-gray-700">Patokan lokasi <span class="font-normal text-gray-400">(opsional)</span></label>
                        <textarea id="delivery_landmark" name="delivery_landmark" rows="2" maxlength="1000" placeholder="Contoh: dekat gerbang atau toko tertentu" class="w-full rounded-xl border border-orange-200 px-4 py-3 text-sm text-gray-800 outline-none transition focus:border-[#B83B25] focus:ring-2 focus:ring-orange-100">{{ old('delivery_landmark') }}</textarea>
                        @error('delivery_landmark')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="customer_note" class="mb-2 block text-sm font-bold text-gray-700">Catatan pesanan <span class="font-normal text-gray-400">(opsional)</span></label>
                        <textarea id="customer_note" name="customer_note" rows="3" maxlength="1000" placeholder="Petunjuk alamat atau catatan lainnya" class="w-full rounded-xl border border-orange-200 px-4 py-3 text-sm text-gray-800 outline-none transition focus:border-[#B83B25] focus:ring-2 focus:ring-orange-100">{{ old('customer_note') }}</textarea>
                        @error('customer_note')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>

            <aside class="rounded-2xl border border-orange-100 bg-white p-5 shadow-sm sm:p-6 lg:sticky lg:top-24">
                <h2 class="text-lg font-extrabold text-[#74191B]">Ringkasan Pesanan</h2>
                <div class="mt-5 space-y-4">
                    @foreach ($items as $item)
                        <div class="flex items-start justify-between gap-3 text-sm">
                            <div class="min-w-0">
                                <p class="font-bold text-gray-800">{{ $item['product']->name }}</p>
                                <p class="mt-1 text-xs text-gray-500">{{ $item['quantity'] }} × Rp{{ number_format((float) $item['product']->price, 0, ',', '.') }}</p>
                            </div>
                            <span class="shrink-0 font-bold text-gray-800">Rp{{ number_format($item['subtotal'], 0, ',', '.') }}</span>
                        </div>
                    @endforeach
                </div>
                <div class="my-5 border-t border-dashed border-orange-200"></div>
                <div class="flex items-start justify-between gap-3">
                    <span class="text-sm font-bold text-gray-700">Subtotal produk</span>
                    <span class="text-right text-lg font-black text-[#B83B25]">Rp{{ number_format($subtotal, 0, ',', '.') }}</span>
                </div>
                <div class="mt-3 rounded-xl bg-orange-50 p-3 text-xs leading-5 text-gray-700">
                    <div class="flex justify-between gap-3"><span>Ongkos kirim</span><strong id="shipping-preview">Pilih provinsi</strong></div>
                    <p class="mt-2">Pulau Jawa Rp3.000 · Luar Pulau Jawa Rp5.000. Perhitungan final selalu divalidasi ulang oleh server.</p>
                </div>
                <div class="mt-3 flex justify-between gap-3"><span class="text-sm font-bold text-gray-700">Perkiraan total</span><strong id="total-preview" class="text-lg font-black text-[#B83B25]">Pilih provinsi</strong></div>
                <button type="submit" class="mt-6 w-full rounded-full bg-[#B83B25] px-5 py-3.5 text-sm font-extrabold text-white transition hover:bg-[#74191B] focus:outline-none focus:ring-2 focus:ring-[#B83B25] focus:ring-offset-2">Buat Pesanan</button>
                <a href="{{ route('cart.index') }}" class="mt-3 flex w-full justify-center py-2 text-sm font-bold text-[#B83B25] hover:text-[#74191B]">Kembali ke keranjang</a>
            </aside>
        </form>
    </div>
    <script>
        (() => {
            const province = document.getElementById('shipping_province');
            const shippingPreview = document.getElementById('shipping-preview');
            const totalPreview = document.getElementById('total-preview');
            const java = @json($javaProvinces);
            const subtotal = {{ (int) $subtotal }};
            const formatRupiah = value => 'Rp' + new Intl.NumberFormat('id-ID').format(value);
            const updatePreview = () => {
                if (!province.value) {
                    shippingPreview.textContent = 'Pilih provinsi';
                    totalPreview.textContent = 'Pilih provinsi';
                    return;
                }
                const shipping = java.includes(province.value) ? {{ (int) $shippingRates['java'] }} : {{ (int) $shippingRates['outside_java'] }};
                shippingPreview.textContent = formatRupiah(shipping);
                totalPreview.textContent = formatRupiah(subtotal + shipping);
            };
            province.addEventListener('change', updatePreview);
            updatePreview();
        })();
    </script>
</section>
@endsection