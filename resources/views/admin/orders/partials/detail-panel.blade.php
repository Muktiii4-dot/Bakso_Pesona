@php
    $statusClasses = [
        'pending_confirmation' => 'bg-amber-100 text-amber-800 border-amber-200',
        'processing' => 'bg-purple-100 text-purple-800 border-purple-200',
        'shipped' => 'bg-indigo-100 text-indigo-800 border-indigo-200',
        'completed' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
        'cancelled' => 'bg-red-100 text-red-800 border-red-200',
    ];
    $paymentClasses = [
        'awaiting_confirmation' => 'bg-gray-100 text-gray-700 border-gray-200',
        'awaiting_payment' => 'bg-blue-50 text-blue-700 border-blue-200',
        'under_verification' => 'bg-amber-50 text-amber-800 border-amber-300',
        'paid' => 'bg-emerald-50 text-emerald-700 border-emerald-300',
        'rejected' => 'bg-red-50 text-red-700 border-red-200',
        'unpaid' => 'bg-gray-100 text-gray-600 border-gray-200',
        'refunded' => 'bg-pink-50 text-pink-700 border-pink-200',
        'expired' => 'bg-red-50 text-red-700 border-red-200',
    ];
@endphp

<div class="rounded-2xl border border-orange-100 bg-white p-5 shadow-sm space-y-6">
    <div class="border-b border-gray-100 pb-4">
        <p class="text-[11px] font-bold uppercase tracking-wider text-pesona-orange">Detail Pesanan Terpilih</p>
        <h2 class="mt-1 break-all text-xl font-black text-pesona-dark">{{ $order->order_number }}</h2>
        <p class="mt-1 text-xs text-gray-500">Dibuat pada {{ $order->created_at->timezone('Asia/Jakarta')->format('d M Y, H:i') }} WIB</p>
        <div class="mt-3 flex flex-wrap gap-2">
            <span class="inline-flex rounded-full border px-2.5 py-0.5 text-xs font-bold {{ $statusClasses[$order->status] ?? 'border-gray-200 bg-gray-100 text-gray-700' }}">{{ $order->status_label }}</span>
            <span class="inline-flex rounded-full border px-2.5 py-0.5 text-xs font-bold {{ $paymentClasses[$order->payment_status] ?? 'border-gray-200 bg-gray-100 text-gray-700' }}">{{ $order->payment_status_label }}</span>
        </div>
    </div>

    <div class="rounded-xl border border-gray-100 bg-gray-50/50 p-4 space-y-3 text-xs">
        <p class="font-bold uppercase tracking-wider text-gray-500 text-[10px]">Data Penerima & Kontak</p>
        <div>
            <span class="text-gray-400">Nama penerima</span>
            <p class="font-bold text-gray-900 text-sm">{{ $order->recipient_name }}</p>
            @if ($order->user)
                <p class="text-[11px] text-gray-500">Akun: {{ $order->user->name }} ({{ $order->user->email }})</p>
            @endif
        </div>
        <div>
            <span class="text-gray-400">Nomor telepon</span>
            <p class="font-bold text-gray-900">{{ $order->phone }}</p>
        </div>
        <div>
            <span class="text-gray-400">Alamat pengiriman</span>
            <p class="mt-1 whitespace-pre-line rounded-lg border border-gray-100 bg-white p-2.5 font-medium leading-relaxed text-gray-800">{{ $order->shipping_address }}</p>
        </div>
        @if ($order->delivery_landmark)
            <div>
                <span class="text-gray-400">Patokan lokasi</span>
                <p class="mt-1 whitespace-pre-line text-gray-800">{{ $order->delivery_landmark }}</p>
            </div>
        @endif
        @if ($order->customer_note)
            <div>
                <span class="text-gray-400">Catatan pelanggan</span>
                <p class="mt-1 whitespace-pre-line text-gray-800">{{ $order->customer_note }}</p>
            </div>
        @endif
    </div>

    <div>
        <p class="mb-2 font-bold uppercase tracking-wider text-gray-500 text-[10px]">Item dipesan ({{ $order->items->count() }})</p>
        <div class="divide-y divide-gray-100 rounded-xl border border-gray-100 bg-white">
            @foreach ($order->items as $item)
                <div class="flex items-center justify-between gap-2 p-3 text-xs">
                    <div class="min-w-0 flex-1">
                        <p class="truncate font-bold text-gray-900">{{ $item->product_name }}</p>
                        <p class="text-gray-400">{{ $item->quantity }} × Rp{{ number_format(\App\Models\Order::rupiahToInt($item->unit_price), 0, ',', '.') }}</p>
                    </div>
                    <span class="whitespace-nowrap font-bold text-pesona-dark">Rp{{ number_format(\App\Models\Order::rupiahToInt($item->subtotal), 0, ',', '.') }}</span>
                </div>
            @endforeach
        </div>
    </div>

    <div class="rounded-xl border border-orange-100 bg-orange-50/30 p-4 space-y-2 text-xs">
        <div class="flex justify-between text-gray-600">
            <span>Subtotal produk</span>
            <span class="font-semibold text-gray-900">Rp{{ number_format(\App\Models\Order::rupiahToInt($order->subtotal), 0, ',', '.') }}</span>
        </div>
        <div class="flex justify-between text-gray-600">
            <span>Diskon</span>
            <span class="font-semibold text-gray-900">-Rp{{ number_format(\App\Models\Order::rupiahToInt($order->discount_amount ?? 0), 0, ',', '.') }}</span>
        </div>
        <div class="flex justify-between text-gray-600">
            <span>Ongkos kirim</span>
            <span class="font-semibold text-gray-900">{{ $order->shipping_cost === null ? 'Belum ditetapkan' : 'Rp'.number_format(\App\Models\Order::rupiahToInt($order->shipping_cost), 0, ',', '.') }}</span>
        </div>
        <div class="flex justify-between border-t border-orange-200 pt-2 font-bold text-pesona-dark">
            <span>Total akhir</span>
            <span>{{ $order->total_amount === null ? 'Menunggu ongkir' : 'Rp'.number_format(\App\Models\Order::rupiahToInt($order->total_amount), 0, ',', '.') }}</span>
        </div>
        @if ($order->payment_instructions_issued_at)
            <p class="text-gray-600">Instruksi terkirim: {{ $order->payment_instructions_issued_at->timezone('Asia/Jakarta')->format('d M Y, H:i') }} WIB</p>
            <p class="text-gray-600">Tenggat: {{ $order->payment_due_at ? $order->payment_due_at->timezone('Asia/Jakarta')->format('d M Y, H:i').' WIB' : 'Tidak tersedia' }}</p>
        @endif
        @if ($order->payment_review_required)
            <p class="rounded-lg bg-amber-100 p-2 font-semibold text-amber-900">Perlu pemeriksaan mutasi; stok tetap ditahan.</p>
        @endif
    </div>

    <a href="{{ route('admin.orders.show', ['order' => $order->id] + request()->query()) }}" class="flex w-full items-center justify-center rounded-xl bg-pesona-dark px-4 py-3 text-xs font-bold text-white transition hover:bg-black">
        Buka detail & tindakan pesanan
    </a>
</div>
