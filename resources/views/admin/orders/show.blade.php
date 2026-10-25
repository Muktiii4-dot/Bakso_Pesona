@extends('layouts.admin')

@section('title', 'Detail Pesanan ' . $order->order_number)
@section('header_title', 'Kelola Pesanan ' . $order->order_number)

@section('content')
<div class="space-y-6">
    {{-- NAVIGASI KEMBALI & HEADER UTAMA --}}
    <div>
        <a
            href="{{ route('admin.orders.index', request()->query()) }}"
            class="inline-flex items-center gap-1.5 text-xs font-bold uppercase tracking-wider text-pesona-orange hover:underline"
        >
            &larr; Kembali ke Daftar Pesanan
        </a>

        <div class="mt-3 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-pesona-orange">Detail Pesanan</span>
                <h1 class="text-2xl font-black text-pesona-dark sm:text-3xl">
                    {{ $order->order_number }}
                </h1>
                <p class="mt-1 text-xs text-gray-500">
                    Dibuat pada {{ $order->created_at->timezone('Asia/Jakarta')->format('d F Y, H:i') }} WIB
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-2">
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

                    $paymentClasses = [
                        'awaiting_confirmation' => 'bg-gray-100 text-gray-700',
                        'awaiting_payment' => 'bg-blue-50 text-blue-700 border border-blue-200',
                        'under_verification' => 'bg-amber-50 text-amber-700 border border-amber-200',
                        'paid' => 'bg-emerald-50 text-emerald-700 border border-emerald-200',
                        'rejected' => 'bg-red-50 text-red-700 border border-red-200',
                        'unpaid' => 'bg-gray-100 text-gray-600',
                        'refunded' => 'bg-pink-50 text-pink-700 border border-pink-200',
                    ];
                    $pBadgeClass = $paymentClasses[$order->payment_status] ?? 'bg-gray-100 text-gray-700';
                @endphp

                <div class="flex items-center gap-2 rounded-2xl bg-white p-2 shadow-sm border border-orange-100">
                    <span class="text-xs font-semibold text-gray-500">Status:</span>
                    <span class="inline-flex rounded-full px-3 py-1 text-xs font-bold {{ $badgeClass }}">
                        {{ $order->status_label }}
                    </span>
                    <span class="inline-flex rounded-full px-3 py-1 text-xs font-bold {{ $pBadgeClass }}">
                        {{ $order->payment_status_label }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    {{-- KONTEN DETAIL 2 KOLOM --}}
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">

        {{-- KOLOM KIRI: INFORMASI PELANGGAN & AKSI ADMIN --}}
        <div class="space-y-6 lg:col-span-1">
            {{-- DATA PELANGGAN --}}
            <section class="rounded-2xl border border-orange-100 bg-white p-5 shadow-sm">
                <h2 class="text-base font-bold text-pesona-dark border-b border-gray-100 pb-3">
                    Informasi Pelanggan
                </h2>

                <div class="mt-4 space-y-3.5 text-sm">
                    <div>
                        <p class="text-xs font-medium text-gray-400 uppercase tracking-wider">Akun Terdaftar</p>
                        <p class="mt-0.5 font-bold text-gray-900">
                            {{ $order->user->name ?? 'Tamu / Akun Dihapus' }}
                        </p>
                        <p class="text-xs text-gray-500">{{ $order->user->email ?? '-' }}</p>
                    </div>

                    <div>
                        <p class="text-xs font-medium text-gray-400 uppercase tracking-wider">Nama Penerima</p>
                        <p class="mt-0.5 font-bold text-gray-900">{{ $order->recipient_name }}</p>
                    </div>

                    <div>
                        <p class="text-xs font-medium text-gray-400 uppercase tracking-wider">Nomor Telepon / WhatsApp</p>
                        <p class="mt-0.5 font-bold text-gray-900">{{ $order->phone }}</p>
                        @php
                            $cleanPhone = preg_replace('/[^0-9]/', '', $order->phone);
                            if (str_starts_with($cleanPhone, '0')) {
                                $cleanPhone = '62' . substr($cleanPhone, 1);
                            } elseif (str_starts_with($cleanPhone, '8')) {
                                $cleanPhone = '62' . $cleanPhone;
                            }
                            $whatsappPhoneIsValid = strlen($cleanPhone) >= 10 && strlen($cleanPhone) <= 15;
                            $whatsappMessage = rawurlencode('Halo, saya menghubungi terkait pesanan ' . $order->order_number . '.');
                        @endphp
                        @if ($whatsappPhoneIsValid)
                            <a
                                href="https://wa.me/{{ $cleanPhone }}?text={{ $whatsappMessage }}"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="mt-1.5 inline-flex items-center gap-1.5 rounded-lg bg-emerald-50 px-2.5 py-1 text-xs font-bold text-emerald-700 transition hover:bg-emerald-100"
                            >
                                <span>Hubungi via WhatsApp</span> &rarr;
                            </a>
                        @else
                            <p class="mt-1 text-xs text-red-600">Nomor WhatsApp tidak tersedia atau tidak valid.</p>
                        @endif
                    </div>

                    <div>
                        <p class="text-xs font-medium text-gray-400 uppercase tracking-wider">Alamat Pengiriman</p>
                        <p class="mt-0.5 whitespace-pre-line text-gray-700 leading-relaxed bg-gray-50 rounded-xl p-3 border border-gray-100">
                            {{ $order->shipping_address }}
                        </p>
                    </div>

                    @if ($order->delivery_landmark)
                        <div>
                            <p class="text-xs font-medium text-gray-400 uppercase tracking-wider">Patokan Lokasi</p>
                            <p class="mt-0.5 whitespace-pre-line text-gray-700 leading-relaxed">{{ $order->delivery_landmark }}</p>
                        </div>
                    @endif

                    <div>
                        <p class="text-xs font-medium text-gray-400 uppercase tracking-wider">Catatan Pelanggan</p>
                        <p class="mt-0.5 italic text-gray-600 bg-orange-50/40 rounded-xl p-3 border border-orange-100">
                            {{ $order->customer_note ?: 'Tidak ada catatan khusus.' }}
                        </p>
                    </div>
                </div>
            </section>

            <section class="rounded-2xl border border-orange-200 bg-white p-5 shadow-sm space-y-4">
                <h2 class="text-base font-bold text-pesona-dark border-b border-gray-100 pb-3">Aksi Admin</h2>

                @if ($order->status === \App\Models\Order::STATUS_PENDING_CONFIRMATION && !in_array($order->payment_status, [\App\Models\Order::PAYMENT_UNDER_VERIFICATION, \App\Models\Order::PAYMENT_PAID, \App\Models\Order::PAYMENT_REFUNDED], true) && ! $order->payment_review_required && ! ($order->payment_due_at && $order->payment_due_at->isPast()))
                    <div class="rounded-xl border border-amber-200 bg-amber-50/60 p-4">
                        <p class="text-xs font-bold uppercase tracking-wider text-amber-800">Tetapkan Ongkir</p>
                        <form action="{{ route('admin.orders.confirm-shipping', $order) }}" method="POST" class="mt-3 space-y-3">
                            @csrf
                            @method('PUT')
                            <label for="shipping_cost" class="block text-xs font-semibold text-gray-700">Nominal Ongkos Kirim (Rp)</label>
                            <input id="shipping_cost" name="shipping_cost" type="number" min="0" step="1" value="{{ old('shipping_cost', $order->shipping_cost) }}" required class="w-full rounded-xl border border-gray-300 px-3 py-2 text-sm">
                            @error('shipping_cost')
                                <p class="text-xs text-red-600">{{ $message }}</p>
                            @enderror
                            <button type="submit" class="w-full rounded-xl bg-pesona-orange px-4 py-2.5 text-xs font-bold text-white hover:bg-pesona-red">
                                Simpan Ongkir & Hitung Ulang Total
                            </button>
                            @if ($hasPreviouslySentInstructions)
                                <p class="text-xs text-amber-900">Jika ongkir berubah, instruksi lama akan ditandai tidak berlaku. Admin harus mengirim instruksi baru.</p>
                            @endif
                            @if ($order->status === \App\Models\Order::STATUS_PENDING_CONFIRMATION && ($order->payment_review_required || ($order->payment_due_at && $order->payment_due_at->isPast())))
                                <p class="rounded-xl border border-orange-200 bg-orange-50 p-4 text-xs leading-5 text-orange-900">Ongkir tidak dapat diubah sebelum pembayaran yang melewati tenggat ditinjau. Selesaikan pemeriksaan transaksi terlebih dahulu.</p>
                            @endif
                        </form>
                        @if (!empty($whatsappTemplates['confirmation']['url']))
                            <a href="{{ $whatsappTemplates['confirmation']['url'] }}" target="_blank" rel="noopener noreferrer" class="mt-3 inline-flex text-xs font-bold text-emerald-700 hover:underline">Buka template konfirmasi WhatsApp</a>
                        @endif
                    </div>
                @endif

                @if ($order->status === \App\Models\Order::STATUS_PENDING_CONFIRMATION && $order->total_amount !== null && in_array($order->payment_status, [\App\Models\Order::PAYMENT_AWAITING_CONFIRMATION, \App\Models\Order::PAYMENT_AWAITING_PAYMENT, \App\Models\Order::PAYMENT_REJECTED], true) && ! $order->payment_review_required && ! ($order->payment_due_at && $order->payment_due_at->isPast()))
                    <div class="rounded-xl border border-blue-200 bg-blue-50/50 p-4">
                        <p class="text-xs font-bold uppercase tracking-wider text-blue-900">Instruksi Pembayaran QRIS</p>
                        @if (! $qrisConfigured)
                            <p class="mt-2 text-xs text-red-700">Pengiriman dinonaktifkan sampai QRIS statis resmi, nama merchant, dan nomor WhatsApp usaha dikonfigurasi oleh pemilik.</p>
                        @else
                            @if (!empty($whatsappTemplates['payment_instruction']['url']))
                                <a href="{{ $whatsappTemplates['payment_instruction']['url'] }}" target="_blank" rel="noopener noreferrer" class="mt-3 inline-flex w-full items-center justify-center rounded-xl border border-emerald-300 bg-white px-4 py-2.5 text-xs font-bold text-emerald-800 hover:bg-emerald-50">
                                    Buka WhatsApp dengan teks terisi
                                </a>
                            @else
                                <p class="mt-2 text-xs text-red-700">Nomor WhatsApp pelanggan tidak valid. Periksa data pesanan sebelum mengirim instruksi.</p>
                            @endif

                            @if (!empty($whatsappTemplates['payment_instruction']['url']))
                            <form action="{{ route('admin.orders.issue-payment-instructions', $order) }}" method="POST" class="mt-3 space-y-3" onsubmit="return confirm('Catat hanya setelah pesan WhatsApp benar-benar dikirim ke pelanggan. Waktu server akan memulai tenggat 24 jam.');">
                                @csrf
                                @method('PUT')
                                @if ($hasPreviouslySentInstructions)
                                    <label for="resend_reason" class="block text-xs font-semibold text-gray-700">Alasan pengiriman ulang</label>
                                    <textarea id="resend_reason" name="resend_reason" required maxlength="1000" rows="2" class="w-full rounded-lg border border-blue-200 p-2 text-sm">{{ old('resend_reason') }}</textarea>
                                    @error('resend_reason')
                                        <p class="text-xs text-red-600">{{ $message }}</p>
                                    @enderror
                                @endif
                                <label class="flex items-start gap-2 text-xs leading-5 text-gray-700">
                                    <input type="checkbox" name="confirmed_sent" value="1" required class="mt-0.5 rounded border-gray-300 text-pesona-orange focus:ring-pesona-orange">
                                    <span>Saya sudah benar-benar mengirim pesan WhatsApp kepada pelanggan. Membuka WhatsApp saja tidak dihitung.</span>
                                </label>
                                @error('confirmed_sent')
                                    <p class="text-xs text-red-600">{{ $message }}</p>
                                @enderror
                                <button type="submit" @disabled(! $qrisConfigured) class="w-full rounded-xl bg-blue-700 px-4 py-2.5 text-xs font-bold text-white hover:bg-blue-800 disabled:cursor-not-allowed disabled:opacity-50">
                                    {{ $hasPreviouslySentInstructions ? 'Catat Pengiriman Ulang & Mulai Tenggat Baru' : 'Catat Pesan Terkirim & Mulai Tenggat 24 Jam' }}
                                </button>
                            </form>
                            @endif
                        @endif
                        <p class="mt-2 text-xs text-gray-600">Pembukaan WhatsApp tidak mengubah status atau tenggat. Gunakan konfirmasi terpisah hanya setelah pesan dikirim.</p>
                    </div>
                @endif

                @if (($order->payment_review_required || ($order->payment_due_at && $order->payment_due_at->isPast())) && in_array($order->payment_status, [\App\Models\Order::PAYMENT_AWAITING_PAYMENT, \App\Models\Order::PAYMENT_UNDER_VERIFICATION], true))
                    <div class="rounded-xl border border-orange-200 bg-orange-50 p-4">
                        <p class="text-xs font-bold uppercase tracking-wider text-orange-900">Pemeriksaan Transaksi Terlambat</p>
                        <p class="mt-1 text-xs text-orange-800">Cocokkan transaksi aktual, bukan klaim atau gambar saja. Tenggat lewat tidak otomatis melunasi atau membatalkan pesanan.</p>
                        <form action="{{ route('admin.orders.resolve-payment-review', $order) }}" method="POST" class="mt-3 space-y-3" onsubmit="return confirm('Pastikan hasil pemeriksaan sesuai mutasi transaksi aktual.');">
                            @csrf
                            @method('PUT')
                            <label for="late_actual_amount" class="block text-xs font-semibold text-gray-700">Nominal transaksi aktual (Rp; wajib untuk diterima/tidak cocok)</label>
                            <input id="late_actual_amount" name="actual_amount" type="number" min="0" step="1" value="{{ old('actual_amount') }}" class="w-full rounded-lg border border-orange-200 p-2 text-sm">
                            <label for="late_transaction_reference" class="block text-xs font-semibold text-gray-700">Referensi transaksi (jika tersedia)</label>
                            <input id="late_transaction_reference" name="transaction_reference" maxlength="255" value="{{ old('transaction_reference') }}" class="w-full rounded-lg border border-orange-200 p-2 text-sm">
                            <label for="review_note" class="block text-xs font-semibold text-gray-700">Catatan pemeriksaan</label>
                            <textarea id="review_note" name="review_note" required maxlength="1000" rows="2" class="w-full rounded-lg border border-orange-200 p-2 text-sm">{{ old('review_note') }}</textarea>
                            @error('review_note')
                                <p class="text-xs text-red-600">{{ $message }}</p>
                            @enderror
                            @error('actual_amount')
                                <p class="text-xs text-red-600">{{ $message }}</p>
                            @enderror
                            <div class="grid gap-2">
                                <button name="result" value="received" class="rounded-lg bg-emerald-600 px-3 py-2 text-xs font-bold text-white">Dana masuk sesuai total — tandai lunas</button>
                                <button name="result" value="mismatch" class="rounded-lg border border-amber-300 bg-white px-3 py-2 text-xs font-bold text-amber-900">Dana masuk tetapi nominal tidak cocok</button>
                                <button name="result" value="not_received" class="rounded-lg border border-red-200 bg-white px-3 py-2 text-xs font-bold text-red-700">Tidak ditemukan transaksi masuk</button>
                            </div>
                        </form>
                    </div>
                @elseif ($order->payment_instructions_issued_at && $order->payment_due_at && in_array($order->payment_status, [\App\Models\Order::PAYMENT_AWAITING_PAYMENT, \App\Models\Order::PAYMENT_UNDER_VERIFICATION], true))
                    <div class="rounded-xl border border-blue-200 bg-blue-50/50 p-4 space-y-3">
                        <p class="text-xs font-bold uppercase tracking-wider text-blue-900">Verifikasi Pembayaran Manual</p>
                        <p class="text-xs text-blue-800">Instruksi terkirim: {{ $order->payment_instructions_issued_at->timezone('Asia/Jakarta')->format('d M Y, H:i') }} WIB · Tenggat: {{ $order->payment_due_at->timezone('Asia/Jakarta')->format('d M Y, H:i') }} WIB</p>
                        <p class="text-xs text-gray-600">Tidak ada bukti unggah dalam sistem saat ini. Cocokkan nominal dan transaksi aktual pada kanal QRIS.</p>
                        @if ($order->payment_status === \App\Models\Order::PAYMENT_AWAITING_PAYMENT)
                            <form action="{{ route('admin.orders.update-payment-status', $order) }}" method="POST" class="space-y-2">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="payment_status" value="under_verification">
                                <label for="start_verification_note" class="block text-xs font-semibold text-gray-700">Catatan awal pemeriksaan</label>
                                <textarea id="start_verification_note" name="verification_note" required maxlength="1000" rows="2" class="w-full rounded-lg border border-amber-200 p-2 text-sm">{{ old('verification_note') }}</textarea>
                                <button class="w-full rounded-xl border border-amber-300 bg-amber-50 px-3 py-2 text-xs font-bold text-amber-800">Mulai pemeriksaan transaksi</button>
                            </form>
                        @endif
                        @if ($order->payment_status === \App\Models\Order::PAYMENT_UNDER_VERIFICATION)
                            <form action="{{ route('admin.orders.update-payment-status', $order) }}" method="POST" class="space-y-2" onsubmit="return confirm('Tandai lunas hanya jika dana aktual masuk dengan nominal yang sama persis.');">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="payment_status" value="paid">
                                <label for="actual_amount" class="block text-xs font-semibold text-gray-700">Nominal transaksi aktual (Rp)</label>
                                <input id="actual_amount" name="actual_amount" type="number" min="0" step="1" required value="{{ old('actual_amount') }}" class="w-full rounded-lg border border-emerald-200 p-2 text-sm">
                                <label for="transaction_reference" class="block text-xs font-semibold text-gray-700">Referensi transaksi (jika tersedia)</label>
                                <input id="transaction_reference" name="transaction_reference" maxlength="255" value="{{ old('transaction_reference') }}" class="w-full rounded-lg border border-emerald-200 p-2 text-sm">
                                <label for="verification_note" class="block text-xs font-semibold text-gray-700">Catatan pencocokan</label>
                                <textarea id="verification_note" name="verification_note" required maxlength="1000" rows="2" class="w-full rounded-lg border border-emerald-200 p-2 text-sm">{{ old('verification_note') }}</textarea>
                                <button class="w-full rounded-xl bg-emerald-600 px-3 py-2.5 text-xs font-bold text-white">Verifikasi nominal & tandai lunas</button>
                            </form>
                            <form action="{{ route('admin.orders.update-payment-status', $order) }}" method="POST" class="space-y-2" onsubmit="return confirm('Tolak transaksi ini setelah memastikan bukti/mutasi tidak sesuai?');">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="payment_status" value="rejected">
                                <label for="rejection_reason" class="block text-xs font-semibold text-gray-700">Alasan penolakan</label>
                                <textarea id="rejection_reason" name="rejection_reason" required maxlength="1000" rows="2" class="w-full rounded-lg border border-red-200 p-2 text-sm">{{ old('rejection_reason') }}</textarea>
                                <button class="w-full rounded-xl border border-red-200 bg-white px-3 py-2 text-xs font-bold text-red-700">Tolak hasil pembayaran</button>
                            </form>
                        @endif
                    </div>
                @endif

                @if ($order->status === \App\Models\Order::STATUS_PENDING_CONFIRMATION && $order->payment_status === \App\Models\Order::PAYMENT_PAID)
                    <form action="{{ route('admin.orders.update-status', $order) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <button name="status" value="processing" class="w-full rounded-xl bg-purple-700 px-3 py-2.5 text-xs font-bold text-white">Mulai Dikemas</button>
                    </form>
                @elseif ($order->status === \App\Models\Order::STATUS_PROCESSING)
                    <form action="{{ route('admin.orders.update-status', $order) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <button name="status" value="shipped" class="w-full rounded-xl bg-indigo-600 px-3 py-2.5 text-xs font-bold text-white">Tandai Diserahkan ke Pengiriman</button>
                    </form>
                @elseif ($order->status === \App\Models\Order::STATUS_SHIPPED)
                    <form action="{{ route('admin.orders.update-status', $order) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <button name="status" value="completed" class="w-full rounded-xl bg-emerald-600 px-3 py-2.5 text-xs font-bold text-white">Tandai Selesai</button>
                    </form>
                    @if (!empty($whatsappTemplates['completed']['url']))
                        <a href="{{ $whatsappTemplates['completed']['url'] }}" target="_blank" rel="noopener noreferrer" class="inline-flex text-xs font-bold text-emerald-700 hover:underline">Buka template pesanan selesai</a>
                    @endif
                @endif

                @foreach ($order->cancellationRequests as $cancellationRequest)
                    <div class="rounded-xl border border-amber-200 bg-amber-50 p-4 text-xs">
                        <p class="font-bold text-amber-900">Permintaan pembatalan · {{ ucfirst($cancellationRequest->status) }}</p>
                        <p class="mt-1 text-gray-700">{{ $cancellationRequest->reason }}</p>
                        @if ($cancellationRequest->status === 'pending')
                            <form action="{{ route('admin.orders.decide-cancellation', [$order, $cancellationRequest]) }}" method="POST" class="mt-3 space-y-2">
                                @csrf
                                @method('PUT')
                                <textarea name="decision_note" maxlength="1000" rows="2" class="w-full rounded-lg border border-amber-200 p-2" placeholder="Catatan keputusan">{{ old('decision_note') }}</textarea>
                                <div class="flex gap-2">
                                    <button name="decision" value="approve" class="rounded-lg bg-red-700 px-3 py-2 font-bold text-white">Setujui pembatalan</button>
                                    <button name="decision" value="reject" class="rounded-lg border border-gray-300 bg-white px-3 py-2 font-bold text-gray-700">Tolak permintaan</button>
                                </div>
                            </form>
                        @endif
                    </div>
                @endforeach

                @if (!in_array($order->status, [\App\Models\Order::STATUS_SHIPPED, \App\Models\Order::STATUS_COMPLETED, \App\Models\Order::STATUS_CANCELLED], true) && $order->payment_status !== \App\Models\Order::PAYMENT_UNDER_VERIFICATION && ! $order->payment_review_required)
                    <form action="{{ route('admin.orders.cancel', $order) }}" method="POST" class="rounded-xl border border-red-200 bg-red-50/50 p-4">
                        @csrf
                        <label for="admin_cancel_reason" class="block text-xs font-bold text-red-900">Pembatalan oleh admin</label>
                        <textarea id="admin_cancel_reason" name="reason" maxlength="1000" required rows="2" class="mt-2 w-full rounded-lg border border-red-200 p-2 text-sm" placeholder="Alasan pembatalan">{{ old('reason') }}</textarea>
                        <button class="mt-2 w-full rounded-lg border border-red-200 bg-white px-3 py-2 text-xs font-bold text-red-700">Catat pembatalan</button>
                    </form>
                @endif

                @if ($order->refund && $order->refund->status === 'pending')
                    <div class="rounded-xl border border-pink-200 bg-pink-50 p-4">
                        <p class="text-xs font-bold text-pink-900">Refund manual · Rp{{ number_format($order->refund->amount, 0, ',', '.') }}</p>
                        <p class="mt-1 text-xs text-pink-800">Catat refund selesai hanya setelah dana benar-benar ditransfer kembali.</p>
                        <form action="{{ route('admin.orders.complete-refund', $order) }}" method="POST" class="mt-3 space-y-2">
                            @csrf
                            @method('PUT')
                            <label for="transfer_reference" class="block text-xs font-semibold text-gray-700">Referensi transfer</label>
                            <input id="transfer_reference" name="transfer_reference" required maxlength="255" value="{{ old('transfer_reference') }}" class="w-full rounded-lg border border-pink-200 p-2 text-sm">
                            <button class="w-full rounded-lg bg-pink-600 px-3 py-2 text-xs font-bold text-white">Catat refund selesai</button>
                        </form>
                    </div>
                @endif

                @if ($order->status === \App\Models\Order::STATUS_CANCELLED)
                    <form action="{{ route('admin.orders.restock-cancelled', $order) }}" method="POST" class="rounded-xl border border-gray-200 bg-gray-50 p-4">
                        @csrf
                        @method('PUT')
                        <label for="restock_reason" class="block text-xs font-bold text-gray-800">Pengembalian stok (tindakan terpisah)</label>
                        <textarea id="restock_reason" name="reason" required maxlength="1000" rows="2" class="mt-2 w-full rounded-lg border border-gray-300 p-2 text-sm" placeholder="Alasan restock"></textarea>
                        <button class="mt-2 w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-xs font-bold text-gray-700">Catat pengembalian stok</button>
                    </form>
                @endif
            </section>
        </div>

        {{-- KOLOM KANAN: PRODUK YANG DIPESAN & RINCIAN BIAYA --}}
        <div class="space-y-6 lg:col-span-2">
            {{-- TABEL PRODUK --}}
            <section class="overflow-hidden rounded-2xl border border-orange-100 bg-white shadow-sm">
                <div class="border-b border-gray-100 px-6 py-4">
                    <h2 class="text-base font-bold text-pesona-dark">
                        Item Produk Dipesan
                    </h2>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-gray-50/80 text-xs uppercase tracking-wider text-gray-500">
                            <tr>
                                <th class="px-5 py-3 font-semibold">Produk</th>
                                <th class="px-5 py-3 text-right font-semibold">Harga Satuan</th>
                                <th class="px-5 py-3 text-center font-semibold">Jumlah</th>
                                <th class="px-5 py-3 text-right font-semibold">Subtotal</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-100">
                            @forelse ($order->items as $item)
                                <tr>
                                    <td class="px-5 py-4 font-bold text-pesona-dark">
                                        {{ $item->product_name }}
                                        @if ($item->product)
                                            <span class="block text-xs font-normal text-gray-500">
                                                Kategori: {{ $item->product->category?->name ?? '-' }}
                                            </span>
                                        @endif
                                    </td>

                                    <td class="whitespace-nowrap px-5 py-4 text-right text-gray-600">
                                        Rp{{ number_format(\App\Models\Order::rupiahToInt($item->unit_price), 0, ',', '.') }}
                                    </td>

                                    <td class="px-5 py-4 text-center font-bold text-gray-800">
                                        {{ $item->quantity }}
                                    </td>

                                    <td class="whitespace-nowrap px-5 py-4 text-right font-bold text-pesona-dark">
                                        Rp{{ number_format(\App\Models\Order::rupiahToInt($item->subtotal), 0, ',', '.') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-5 py-8 text-center text-sm text-gray-500">Tidak ada item pesanan yang tersimpan.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>

            {{-- RINCIAN BIAYA PEMBAYARAN --}}
            <section class="rounded-2xl border border-orange-100 bg-white p-6 shadow-sm">
                <h2 class="text-base font-bold text-pesona-dark border-b border-gray-100 pb-3">
                    Rincian Pembayaran
                </h2>

                <div class="mt-4 space-y-3 text-sm">
                    <div class="flex items-center justify-between text-gray-600">
                        <span>Subtotal Produk</span>
                        <span class="font-semibold text-gray-900">
                            Rp{{ number_format(\App\Models\Order::rupiahToInt($order->subtotal), 0, ',', '.') }}
                        </span>
                    </div>

                    <div class="flex items-center justify-between text-gray-600">
                        <span>Diskon</span>
                        <span class="font-semibold text-gray-900">
                            -Rp{{ number_format(\App\Models\Order::rupiahToInt($order->discount_amount ?? 0), 0, ',', '.') }}
                        </span>
                    </div>

                    <div class="flex items-center justify-between text-gray-600">
                        <span>Ongkos Kirim</span>
                        <span class="font-semibold text-gray-900">
                            @if ($order->shipping_cost !== null)
                                Rp{{ number_format(\App\Models\Order::rupiahToInt($order->shipping_cost), 0, ',', '.') }}
                            @else
                                <span class="text-amber-700 font-medium">Belum Dikonfirmasi</span>
                            @endif
                        </span>
                    </div>

                    <div class="border-t border-gray-100 pt-3 flex items-center justify-between">
                        <span class="text-base font-bold text-pesona-dark">Total Tagihan Final</span>
                        <span class="text-xl font-black text-pesona-red">
                            @if ($order->total_amount !== null)
                                Rp{{ number_format(\App\Models\Order::rupiahToInt($order->total_amount), 0, ',', '.') }}
                            @else
                                <span class="text-sm font-semibold text-amber-600">Menunggu Konfirmasi Ongkir</span>
                            @endif
                        </span>
                    </div>

                    <div class="mt-4 rounded-xl bg-orange-50/60 p-3.5 text-xs text-orange-950 flex items-center justify-between">
                        <div>
                            <span class="font-bold">Metode Pembayaran:</span>
                            {{ $order->payment_method === 'qris' ? 'QRIS statis' : 'Riwayat metode lama · '.strtoupper($order->payment_method) }}
                        </div>
                        <span class="rounded-full bg-white px-2.5 py-0.5 text-xs font-bold text-pesona-orange border border-orange-200">
                            {{ $order->payment_status_label }}
                        </span>
                    </div>
                </div>
            </section>
        </div>

    </div>

    <section class="mt-6 rounded-2xl border border-orange-100 bg-white p-6 shadow-sm">
        <h2 class="text-base font-bold text-pesona-dark">Riwayat Perubahan & Audit</h2>
        @if ($order->events->isEmpty())
            <p class="mt-3 text-sm text-gray-500">Belum ada perubahan yang tercatat.</p>
        @else
            <ol class="mt-4 space-y-4 border-l-2 border-orange-100 pl-4">
                @foreach ($order->events as $event)
                    <li>
                        <div class="flex flex-wrap items-center justify-between gap-2">
                            <p class="text-sm font-bold text-gray-800">{{ str($event->event_type)->replace('_', ' ')->title() }}</p>
                            <time class="text-xs text-gray-500">{{ $event->created_at->timezone('Asia/Jakarta')->format('d M Y, H:i:s') }} WIB</time>
                        </div>
                        <p class="mt-1 text-xs text-gray-600">{{ $event->actor?->name ?? ucfirst($event->actor_type) }}</p>
                        @if ($event->old_values || $event->new_values || $event->metadata)
                            <details class="mt-2 text-xs text-gray-600">
                                <summary class="cursor-pointer font-semibold">Lihat detail catatan</summary>
                                <div class="mt-2 space-y-1 rounded-lg bg-gray-50 p-3">
                                    @if ($event->old_values)
                                        <p><strong>Sebelum:</strong> {{ json_encode($event->old_values, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) }}</p>
                                    @endif
                                    @if ($event->new_values)
                                        <p><strong>Sesudah:</strong> {{ json_encode($event->new_values, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) }}</p>
                                    @endif
                                    @if ($event->metadata)
                                        <p><strong>Metadata:</strong> {{ json_encode($event->metadata, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) }}</p>
                                    @endif
                                </div>
                            </details>
                        @endif
                    </li>
                @endforeach
            </ol>
        @endif
    </section>
</div>
@endsection
