@extends('layouts.app')

@section('title', 'Detail Pesanan ' . $order->order_number . ' - Bakso Pesona')

@section('content')
<section class="bg-pesona-cream py-10 sm:py-14">
    <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8 space-y-6">
        {{-- NAVIGASI KEMBALI --}}
        <div>
            <a href="{{ route('orders.index') }}"
               class="inline-flex items-center gap-1.5 text-xs font-bold uppercase tracking-wider text-pesona-red hover:text-pesona-orange transition">
                <span>&larr;</span>
                <span>Kembali ke Riwayat Pesanan</span>
            </a>
        </div>

        {{-- CARD UTAMA DETAIL PESANAN --}}
        <div class="rounded-3xl border border-orange-100 bg-white p-6 shadow-sm sm:p-9">
            {{-- HEADER PESANAN & STATUS --}}
            <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between pb-6 border-b border-gray-100">
                <div>
                    <span class="inline-block text-xs font-bold uppercase tracking-wider text-[#E87532]">
                        Bukti Transaksi Pelanggan
                    </span>
                    <h1 class="mt-1 break-all text-2xl font-black text-gray-900 sm:text-3xl">
                        {{ $order->order_number }}
                    </h1>
                    <p class="mt-1.5 text-xs text-gray-500">
                        Waktu Pemesanan: <span class="font-medium text-gray-700">{{ $order->created_at->timezone('Asia/Jakarta')->format('d F Y, H:i') }} WIB</span>
                    </p>
                </div>

                @php
                    $statusStyles = [
                        'pending_confirmation' => 'bg-amber-100 text-amber-900 border-amber-200',
                        'awaiting_payment' => 'bg-blue-100 text-blue-900 border-blue-200',
                        'processing' => 'bg-purple-100 text-purple-900 border-purple-200',
                        'shipped' => 'bg-indigo-100 text-indigo-900 border-indigo-200',
                        'completed' => 'bg-emerald-100 text-emerald-900 border-emerald-200',
                        'cancelled' => 'bg-red-100 text-red-900 border-red-200',
                    ];
                    $orderBadge = $statusStyles[$order->status] ?? 'bg-gray-100 text-gray-800 border-gray-200';

                    $paymentStyles = [
                        'awaiting_confirmation' => 'bg-amber-50 text-amber-800 border-amber-200',
                        'awaiting_payment' => 'bg-blue-50 text-blue-800 border-blue-200',
                        'under_verification' => 'bg-amber-50 text-amber-800 border-amber-200',
                        'paid' => 'bg-emerald-50 text-emerald-800 border-emerald-200',
                        'rejected' => 'bg-red-50 text-red-800 border-red-200',
                        'unpaid' => 'bg-gray-100 text-gray-600 border-gray-200',
                        'refunded' => 'bg-pink-50 text-pink-800 border-pink-200',
                    ];
                    $paymentBadge = $paymentStyles[$order->payment_status] ?? 'bg-gray-50 text-gray-700 border-gray-200';

                    $waRaw = (string) config('bakso_pesona.whatsapp_number', '');
                    $waClean = preg_replace('/[^0-9]/', '', $waRaw);
                    if (str_starts_with($waClean, '0')) {
                        $waClean = '62' . substr($waClean, 1);
                    } elseif (str_starts_with($waClean, '8')) {
                        $waClean = '62' . $waClean;
                    }
                    $waMessage = rawurlencode("Halo Admin Bakso Pesona, saya ingin konfirmasi pembayaran untuk pesanan #" . $order->order_number . ($order->total_amount ? " sebesar Rp " . number_format(\App\Models\Order::rupiahToInt($order->total_amount), 0, ',', '.') : "") . ".");
                    $waLink = strlen($waClean) >= 10 && strlen($waClean) <= 15 ? "https://wa.me/" . $waClean . "?text=" . $waMessage : null;
                @endphp

                <div class="flex flex-wrap items-center gap-2">
                    <span class="inline-flex rounded-full border px-3 py-1 text-xs font-bold {{ $orderBadge }}">
                        Pesanan: {{ $order->status_label }}
                    </span>
                    <span class="inline-flex rounded-full border px-3 py-1 text-xs font-bold {{ $paymentBadge }}">
                        Bayar: {{ $order->payment_status_label }}
                    </span>
                </div>
            </div>

            {{-- BANNER PETUNJUK & INFORMASI PEMBAYARAN SESUAI STATUS --}}
            <div class="mt-6">
                @if ($order->status === 'pending_confirmation' && $order->total_amount === null)
                    <div class="rounded-2xl border border-amber-200 bg-amber-50/70 p-5 text-sm text-amber-900 flex items-start gap-3.5">
                        <span class="text-2xl shrink-0">⏳</span>
                        <div>
                            <p class="font-bold">Pesananmu telah tercatat dan sedang menunggu konfirmasi ongkir.</p>
                            <p class="mt-1 text-xs text-amber-800 leading-5">
                                Tim Bakso Pesona sedang memeriksa alamat pengirimanmu untuk menghitung ongkos kirim terbaik. Halaman ini akan otomatis menampilkan rincian total pembayaran setelah ongkir dikonfirmasi.
                            </p>
                        </div>
                    </div>

                @elseif ($order->status === 'pending_confirmation' && $order->payment_instructions_issued_at === null)
                    <div class="rounded-2xl border border-blue-200 bg-blue-50 p-5 text-sm text-blue-900">
                        <p class="font-bold">{{ $order->total_amount === null ? 'Pesanan menunggu penetapan ongkir.' : 'Total akhir sudah dihitung oleh admin.' }}</p>
                        <p class="mt-1 text-xs leading-5">Anda tidak perlu menyetujui ongkir. Admin akan mengirim instruksi pembayaran QRIS melalui WhatsApp. Tenggat 24 jam belum dimulai sampai pengiriman dicatat.</p>
                    </div>

                @elseif ($order->payment_review_required || ($order->payment_due_at && $order->payment_due_at->isPast()))
                    <div class="rounded-2xl border border-orange-200 bg-orange-50 p-5 text-sm text-orange-900">
                        <p class="font-bold">Pembayaran memerlukan pemeriksaan admin.</p>
                        <p class="mt-1 text-xs leading-5">Pesanan dan stok tetap ditahan sampai admin memeriksa mutasi. Tenggat yang lewat tidak membatalkan pesanan secara otomatis.</p>
                    </div>

                @elseif ($order->payment_status === 'awaiting_payment' && $order->payment_instructions_issued_at && $order->status !== 'cancelled')
                    <div class="rounded-2xl border-2 border-[#E87532] bg-gradient-to-br from-orange-50 via-white to-amber-50 p-6 shadow-sm">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-5 border-b border-orange-100">
                            <div>
                                <span class="inline-block rounded-full bg-[#E87532] px-3 py-0.5 text-[11px] font-black uppercase tracking-wider text-white">
                                    Siap Dibayar
                                </span>
                                <h2 class="mt-2 text-xl font-black text-pesona-dark">
                                    Instruksi Pembayaran {{ $order->payment_method === 'qris' ? 'QRIS Statis' : 'Pesanan Lama' }}
                                </h2>
                                <p class="text-xs text-gray-600 mt-1">
                                    Ongkir telah ditetapkan. Silakan selesaikan pembayaran sebesar nominal total di bawah ini.
                                </p>
                            </div>

                            <div class="text-left sm:text-right">
                                <span class="text-xs font-semibold text-gray-500">Total Tagihan Final</span>
                                <p class="text-2xl font-black text-pesona-red">
                                    Rp {{ number_format(\App\Models\Order::rupiahToInt($order->total_amount), 0, ',', '.') }}
                                </p>
                            </div>
                        </div>

                        @if ($order->payment_method === 'qris')
                            <div class="mt-6 grid gap-6 sm:grid-cols-2">
                                {{-- QRIS RESMI --}}
                            <div class="rounded-2xl border border-orange-200 bg-white p-5 text-center flex flex-col items-center justify-center shadow-sm">
                                <div class="flex items-center gap-2 mb-2">
                                    <span class="text-xs font-black tracking-wider text-gray-800 uppercase">QRIS Statis Resmi</span>
                                </div>
                                <div class="h-44 w-44 rounded-xl border border-dashed border-orange-300 bg-orange-50/50 flex flex-col items-center justify-center p-3 text-center">
                                    @if ($order->payment_method === 'qris' && config('bakso_pesona.qris.image') && is_file(public_path(config('bakso_pesona.qris.image'))) && config('bakso_pesona.qris.merchant_name'))
                                        <img src="{{ asset(config('bakso_pesona.qris.image')) }}" alt="QRIS resmi {{ config('bakso_pesona.qris.merchant_name') }}" class="max-h-full max-w-full rounded-lg object-contain">
                                    @else
                                        <span class="text-xs font-semibold text-gray-600">QRIS resmi belum tersedia. Hubungi admin melalui kontak resmi.</span>
                                    @endif
                                </div>
                                @if (config('bakso_pesona.qris.merchant_name'))
                                    <p class="mt-3 text-xs text-gray-500 font-medium">{{ config('bakso_pesona.qris.merchant_name') }}</p>
                                @endif
                            </div>

                            <div class="space-y-4 flex flex-col justify-between">
                                <div class="rounded-2xl bg-orange-100/60 p-4 text-xs text-gray-700 space-y-1.5">
                                    <p class="font-bold text-pesona-dark">Petunjuk Pembayaran:</p>
                                    <ol class="list-decimal pl-4 space-y-1">
                                        <li>Pindai QRIS statis resmi dan pastikan nama merchant sesuai.</li>
                                        <li>Masukkan nominal tepat sesuai total akhir: Rp{{ number_format(\App\Models\Order::rupiahToInt($order->total_amount), 0, ',', '.') }}.</li>
                                        <li>Jika ada kendala, hubungi admin. Pembayaran lunas hanya setelah transaksi aktual diverifikasi.</li>
                                    </ol>
                                </div>

                                @if ($order->payment_due_at)
                                    <p class="text-xs font-semibold text-red-700">Batas pembayaran: {{ $order->payment_due_at->timezone('Asia/Jakarta')->format('d M Y, H:i') }} WIB</p>
                                @endif
                                @if ($waLink)
                                    <a href="{{ $waLink }}" target="_blank" rel="noopener noreferrer" class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-emerald-600 px-5 py-3 text-sm font-bold text-white shadow-md shadow-emerald-900/10 transition hover:bg-emerald-700">
                                        Hubungi Admin via WhatsApp
                                    </a>
                                @endif
                            </div>
                        </div>
                        @else
                            <div class="mt-6 rounded-2xl border border-amber-200 bg-amber-50 p-5 text-sm text-amber-900">
                                <p class="font-bold">Instruksi pembayaran pesanan lama perlu dikonfirmasi.</p>
                                <p class="mt-1 text-xs leading-5">Jangan melakukan pembayaran berdasarkan metode lama yang tampil sebelumnya. Hubungi admin melalui kontak resmi untuk rekonsiliasi pesanan ini.</p>
                                @if ($waLink)
                                    <a href="{{ $waLink }}" target="_blank" rel="noopener noreferrer" class="mt-3 inline-flex items-center gap-2 rounded-xl bg-emerald-600 px-4 py-2 text-xs font-bold text-white hover:bg-emerald-700">Hubungi Admin via WhatsApp</a>
                                @endif
                            </div>
                        @endif
                    </div>

                @elseif ($order->payment_status === 'under_verification')
                    <div class="rounded-2xl border border-amber-200 bg-amber-50 p-5 text-sm text-amber-900 flex items-start gap-3.5">
                        <span class="text-2xl shrink-0">🔍</span>
                        <div>
                            <p class="font-bold">Bukti pembayaranmu sedang diverifikasi oleh admin.</p>
                            <p class="mt-1 text-xs text-amber-800 leading-5">
                                Admin sedang mencocokkan nominal dan transaksi aktual. Pesanan baru disiapkan setelah dana diverifikasi.
                            </p>
                        </div>
                    </div>

                @elseif ($order->payment_status === 'paid' && $order->status !== 'cancelled')
                    <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-5 text-sm text-emerald-900 flex items-start gap-3.5">
                        <span class="text-2xl shrink-0">✅</span>
                        <div>
                            <p class="font-bold">Pembayaran telah diverifikasi.</p>
                            <p class="mt-1 text-xs text-emerald-800 leading-5">
                                Status pesanan saat ini: {{ $order->status_label }}.
                            </p>
                        </div>
                    </div>

                @elseif ($order->payment_status === 'rejected')
                    <div class="rounded-2xl border border-red-200 bg-red-50 p-5 text-sm text-red-900 flex items-start gap-3.5">
                        <span class="text-2xl shrink-0">⚠️</span>
                        <div>
                            <p class="font-bold">Verifikasi pembayaran belum berhasil / ditolak.</p>
                            <p class="mt-1 text-xs text-red-800 leading-5">
                                Transaksi belum dapat diverifikasi. Baca catatan admin dan hubungi tim kami melalui WhatsApp jika diperlukan.
                            </p>
                            @if ($waLink)
                            <a
                                href="{{ $waLink }}"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="mt-3 inline-flex items-center gap-1.5 rounded-lg bg-red-600 px-3 py-1.5 text-xs font-bold text-white hover:bg-red-700 transition"
                            >
                                Hubungi Admin via WhatsApp &rarr;
                            </a>
                            @endif
                        </div>
                    </div>

                @elseif ($order->status === 'cancelled')
                    <div class="rounded-2xl border border-gray-200 bg-gray-50 p-5 text-sm text-gray-700 flex items-start gap-3.5">
                        <span class="text-2xl shrink-0">❌</span>
                        <div>
                            <p class="font-bold">Pesanan ini telah dibatalkan.</p>
                            <p class="mt-1 text-xs text-gray-600 leading-5">
                                @if ($order->payment_status === 'refunded')
                                    Pesanan telah dibatalkan dan pengembalian dana (refund) telah diproses oleh admin.
                                @elseif ($order->refund?->status === 'pending')
                                    Pembatalan disetujui. Pengembalian dana penuh sedang diproses secara manual oleh admin.
                                @else
                                    Pesanan telah dibatalkan. Jika ada pertanyaan, silakan hubungi layanan pelanggan kami.
                                @endif
                            </p>
                        </div>
                    </div>
                @endif
            </div>

            {{-- INFORMASI PENERIMA & ALAMAT --}}
            <div class="mt-8 border-t border-gray-100 pt-6">
                <h3 class="text-base font-extrabold text-gray-900">
                    Informasi Pengiriman
                </h3>

                <dl class="mt-4 grid gap-4 sm:grid-cols-2 rounded-2xl bg-[#FFF9F5] p-5 border border-orange-100/60">
                    <div>
                        <dt class="text-xs font-semibold text-gray-500">Nama Penerima</dt>
                        <dd class="mt-0.5 text-sm font-bold text-gray-800">
                            {{ $order->recipient_name }}
                        </dd>
                    </div>

                    <div>
                        <dt class="text-xs font-semibold text-gray-500">Nomor Telepon</dt>
                        <dd class="mt-0.5 text-sm font-bold text-gray-800">
                            {{ $order->phone }}
                        </dd>
                    </div>

                    <div class="sm:col-span-2">
                        <dt class="text-xs font-semibold text-gray-500">Alamat Lengkap</dt>
                        <dd class="mt-0.5 text-xs leading-5 text-gray-800 whitespace-pre-line font-medium">
                            {{ $order->shipping_address }}
                        </dd>
                    </div>

                    @if ($order->delivery_landmark)
                        <div class="sm:col-span-2">
                            <dt class="text-xs font-semibold text-gray-500">Patokan Lokasi</dt>
                            <dd class="mt-0.5 text-xs leading-5 text-gray-800 whitespace-pre-line font-medium">{{ $order->delivery_landmark }}</dd>
                        </div>
                    @endif

                    @if ($order->customer_note)
                        <div class="sm:col-span-2 border-t border-orange-100 pt-3">
                            <dt class="text-xs font-semibold text-gray-500">Catatan Khusus</dt>
                            <dd class="mt-0.5 text-xs italic text-gray-700 whitespace-pre-line">
                                "{{ $order->customer_note }}"
                            </dd>
                        </div>
                    @endif
                </dl>
            </div>

            {{-- RINCIAN ITEM PESANAN --}}
            <div class="mt-8 border-t border-gray-100 pt-6">
                <h3 class="text-base font-extrabold text-gray-900">
                    Daftar Item Menu ({{ $order->items->count() }})
                </h3>

                <div class="mt-4 divide-y divide-gray-100 border-y border-gray-100">
                    @foreach ($order->items as $item)
                        <div class="flex items-center justify-between py-3.5 text-sm">
                            <div class="min-w-0 pr-4">
                                <p class="font-bold text-gray-900 truncate">
                                    {{ $item->product_name }}
                                </p>
                                <p class="text-xs text-gray-500 mt-0.5">
                                    {{ $item->quantity }} × Rp {{ number_format(\App\Models\Order::rupiahToInt($item->unit_price), 0, ',', '.') }}
                                </p>
                            </div>

                            <p class="shrink-0 font-bold text-gray-900">
                                Rp {{ number_format(\App\Models\Order::rupiahToInt($item->subtotal), 0, ',', '.') }}
                            </p>
                        </div>
                    @endforeach
                </div>

                {{-- REKAP BIAYA --}}
                <div class="mt-6 rounded-2xl bg-gray-50 p-5 space-y-3 text-sm">
                    <div class="flex justify-between text-gray-600">
                        <span>Subtotal Produk</span>
                        <span class="font-bold text-gray-800">Rp {{ number_format(\App\Models\Order::rupiahToInt($order->subtotal), 0, ',', '.') }}</span>
                    </div>

                    <div class="flex justify-between text-gray-600">
                        <span>Diskon</span>
                        <span class="font-bold text-gray-800">-Rp {{ number_format(\App\Models\Order::rupiahToInt($order->discount_amount ?? 0), 0, ',', '.') }}</span>
                    </div>

                    <div class="flex justify-between text-gray-600">
                        <span>Ongkos Kirim</span>
                        @if ($order->shipping_cost === null)
                            <span class="inline-flex rounded-full bg-amber-100 px-2.5 py-0.5 text-xs font-bold text-amber-800">
                                Menunggu konfirmasi admin
                            </span>
                        @else
                            <span class="font-bold text-gray-800">Rp {{ number_format(\App\Models\Order::rupiahToInt($order->shipping_cost), 0, ',', '.') }}</span>
                        @endif
                    </div>

                    <div class="flex justify-between border-t border-gray-200 pt-3 text-base">
                        <span class="font-black text-gray-900">Total Pembayaran</span>
                        @if ($order->total_amount === null)
                            <span class="font-bold text-amber-700">Menunggu konfirmasi ongkir</span>
                        @else
                            <span class="font-black text-pesona-red text-lg">
                                Rp {{ number_format(\App\Models\Order::rupiahToInt($order->total_amount), 0, ',', '.') }}
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            <section id="rating" class="mt-8 border-t border-gray-100 pt-6">
                <h3 class="text-base font-extrabold text-gray-900">Tindakan & Riwayat Pesanan</h3>

                @php
                    $pendingCancellation = $order->cancellationRequests->firstWhere('status', 'pending');
                    $eventLabels = [
                        'shipping_total_updated' => 'Admin memperbarui ongkir dan total akhir',
                        'total_approved_by_customer' => 'Anda menyetujui total akhir',
                        'payment_instructions_issued' => 'Catatan lama: instruksi pembayaran diterbitkan',
                        'payment_instructions_sent' => 'Admin mencatat instruksi WhatsApp terkirim',
                        'payment_instructions_resent' => 'Admin mencatat pengiriman ulang instruksi WhatsApp',
                        'payment_instructions_invalidated' => 'Instruksi pembayaran sebelumnya tidak berlaku',
                        'payment_review_started' => 'Admin mulai memeriksa transaksi pembayaran',
                        'payment_verified' => 'Admin memverifikasi pembayaran',
                        'payment_rejected' => 'Admin menolak pembayaran dengan alasan tercatat',
                        'late_payment_amount_mismatch' => 'Transaksi terlambat dengan nominal tidak cocok ditinjau',
                        'payment_status_changed' => 'Status pembayaran diperbarui',
                        'order_status_changed' => 'Status pesanan diperbarui',
                        'cancellation_requested' => 'Permintaan pembatalan dicatat',
                        'cancellation_approved' => 'Pembatalan pesanan disetujui',
                        'refund_requested' => 'Pengembalian dana dicatat untuk diproses',
                        'refund_completed' => 'Pengembalian dana selesai dicatat',
                        'rating_submitted' => 'Rating Anda dikirim untuk moderasi',
                    ];
                @endphp

                @if ($pendingCancellation)
                    <div class="mt-4 rounded-xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-900">
                        Permintaan pembatalan sedang ditinjau admin. Stok tidak dilepas sebelum pemeriksaan pembayaran selesai.
                    </div>
                @elseif (!in_array($order->status, ['processing', 'shipped', 'completed', 'cancelled'], true))
                    <form action="{{ route('orders.cancel', $order) }}" method="POST" class="mt-4 rounded-2xl border border-red-100 bg-red-50/50 p-4">
                        @csrf
                        <label for="cancellation_reason" class="block text-sm font-bold text-gray-800">Ajukan pembatalan sebelum pesanan diproses</label>
                        <textarea id="cancellation_reason" name="reason" rows="2" maxlength="1000" required class="mt-2 w-full rounded-lg border border-red-200 p-3 text-sm" placeholder="Tuliskan alasan pembatalan"></textarea>
                        @error('reason')
                            <p class="mt-1 text-xs text-red-700">{{ $message }}</p>
                        @enderror
                        <button type="submit" class="mt-3 rounded-lg border border-red-200 bg-white px-4 py-2 text-xs font-bold text-red-700 hover:bg-red-100">Ajukan Pembatalan</button>
                    </form>
                @elseif (in_array($order->status, ['processing', 'shipped'], true))
                    <p class="mt-3 text-sm text-gray-600">Pesanan mulai diproses. Untuk permintaan pembatalan, silakan hubungi admin melalui WhatsApp.</p>
                @endif

                @if ($order->refund)
                    <div class="mt-4 rounded-xl border border-pink-200 bg-pink-50 p-4 text-sm text-pink-900">
                        Status refund: <strong>{{ $order->refund->status === 'completed' ? 'Selesai' : 'Menunggu diproses admin' }}</strong>
                        · Rp {{ number_format(\App\Models\Order::rupiahToInt($order->refund->amount), 0, ',', '.') }}
                    </div>
                @endif

                @if ($order->status === 'completed' && ! $order->rating)
                    <form action="{{ route('orders.rating.store', $order) }}" method="POST" class="mt-4 rounded-2xl border border-orange-100 bg-orange-50/40 p-4">
                        @csrf
                        <label for="rating_value" class="block text-sm font-bold text-gray-800">Bagaimana pengalaman pesanan ini?</label>
                        <select id="rating_value" name="rating" required class="mt-2 rounded-lg border border-orange-200 bg-white px-3 py-2 text-sm">
                            <option value="">Pilih rating</option>
                            @foreach (range(5, 1) as $ratingValue)
                                <option value="{{ $ratingValue }}" @selected(old('rating') == $ratingValue)>{{ $ratingValue }} bintang</option>
                            @endforeach
                        </select>
                        @error('rating')
                            <p class="mt-1 text-xs text-red-700">{{ $message }}</p>
                        @enderror
                        <label for="rating_comment" class="mt-3 block text-sm font-bold text-gray-800">Komentar (opsional)</label>
                        <textarea id="rating_comment" name="comment" rows="3" maxlength="2000" class="mt-2 w-full rounded-lg border border-orange-200 p-3 text-sm" placeholder="Bagikan pengalaman Anda">{{ old('comment') }}</textarea>
                        @error('comment')
                            <p class="mt-1 text-xs text-red-700">{{ $message }}</p>
                        @enderror
                        <button type="submit" class="mt-3 rounded-lg bg-pesona-orange px-4 py-2 text-xs font-bold text-white hover:bg-pesona-red">Kirim Rating</button>
                        <p class="mt-2 text-xs text-gray-500">Rating akan ditinjau admin sebelum ditampilkan secara publik.</p>
                    </form>
                @elseif ($order->rating)
                    <div class="mt-4 rounded-xl border border-orange-100 bg-orange-50/40 p-4 text-sm text-gray-700">
                        Rating Anda: {{ $order->rating->rating }} / 5 · {{ ucfirst($order->rating->moderation_status === 'pending' ? 'Menunggu Moderasi' : $order->rating->moderation_status) }}
                    </div>
                @endif

                @if ($order->events->isNotEmpty())
                    <ol class="mt-5 space-y-3 border-l-2 border-orange-100 pl-4">
                        @foreach ($order->events as $event)
                            <li class="text-sm">
                                <p class="font-semibold text-gray-800">{{ $eventLabels[$event->event_type] ?? 'Aktivitas pesanan dicatat' }}</p>
                                <p class="mt-0.5 text-xs text-gray-500">{{ $event->created_at->format('d M Y, H:i') }} WIB</p>
                            </li>
                        @endforeach
                    </ol>
                @endif
            </section>
        </div>
    </div>
</section>
@endsection
