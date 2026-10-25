<?php $__env->startSection('title', 'Manajemen Pesanan'); ?>
<?php $__env->startSection('header_title', 'Manajemen Pesanan'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">

    
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
            <?php echo e(number_format($totalOrders)); ?> pesanan tersimpan
        </div>
    </section>

    
    <section class="grid grid-cols-1 gap-4 sm:grid-cols-2 2xl:grid-cols-4">

        <div class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <p class="text-sm font-medium text-gray-500">Semua Pesanan</p>
                    <p class="mt-3 text-3xl font-black tracking-tight text-pesona-dark">
                        <?php echo e(number_format($totalOrders)); ?>

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
                        <?php echo e(number_format($ordersRequiringAction)); ?>

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
                        <?php echo e(number_format($awaitingPaymentOrders)); ?>

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
                        <?php echo e(number_format($paymentsUnderVerification)); ?>

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

    
    <section class="rounded-2xl border border-gray-100 bg-white p-4 shadow-sm sm:p-5">
        <div class="mb-5 flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-base font-bold text-gray-900">Cari dan filter pesanan</h2>
                <p class="mt-1 text-xs text-gray-500">
                    Gunakan filter untuk menemukan pesanan yang perlu ditangani.
                </p>
            </div>

            <?php if(request()->hasAny(['search', 'status', 'payment_status', 'attention', 'date_from', 'date_to', 'sort'])): ?>
                <a
                    href="<?php echo e(route('admin.orders.index')); ?>"
                    class="mt-2 inline-flex w-fit items-center gap-2 text-sm font-semibold text-pesona-orange hover:text-pesona-dark sm:mt-0"
                >
                    <span aria-hidden="true">&times;</span>
                    Reset semua filter
                </a>
            <?php endif; ?>
        </div>

        <form method="GET" action="<?php echo e(route('admin.orders.index')); ?>" class="space-y-4">
            <?php if(request('selected')): ?>
                <input type="hidden" name="selected" value="<?php echo e(request('selected')); ?>">
            <?php endif; ?>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">

                
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
                            value="<?php echo e(request('search')); ?>"
                            placeholder="Nomor pesanan, nama, atau nomor HP..."
                            class="w-full rounded-xl border border-gray-200 bg-gray-50 py-2.5 pl-10 pr-3 text-sm text-gray-900 placeholder:text-gray-400 transition focus:border-pesona-orange focus:bg-white focus:outline-none focus:ring-2 focus:ring-orange-100"
                        >
                    </div>
                </div>

                
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
                        <?php $__currentLoopData = $statusLabels; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($value); ?>" <?php if(request('status') === $value): echo 'selected'; endif; ?>>
                                <?php echo e($label); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>

                
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
                        <?php $__currentLoopData = $paymentStatusLabels; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pValue => $pLabel): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($pValue); ?>" <?php if(request('payment_status') === $pValue): echo 'selected'; endif; ?>>
                                <?php echo e($pLabel); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>

                
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
                        <option value="shipping" <?php if(($attention ?? request('attention')) === 'shipping'): echo 'selected'; endif; ?>>Tetapkan ongkir</option>
                        <option value="instructions" <?php if(($attention ?? request('attention')) === 'instructions'): echo 'selected'; endif; ?>>Kirim instruksi</option>
                        <option value="verification" <?php if(($attention ?? request('attention')) === 'verification'): echo 'selected'; endif; ?>>Verifikasi transaksi</option>
                        <option value="payment_review" <?php if(($attention ?? request('attention')) === 'payment_review'): echo 'selected'; endif; ?>>Review pembayaran</option>
                        <option value="ready_to_pack" <?php if(($attention ?? request('attention')) === 'ready_to_pack'): echo 'selected'; endif; ?>>Siap dikemas</option>
                    </select>
                </div>

                
                <div>
                    <label for="sort" class="mb-2 block text-sm font-semibold text-gray-700">
                        Urutkan berdasarkan
                    </label>
                    <select
                        id="sort"
                        name="sort"
                        class="w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm text-gray-800 focus:border-pesona-orange focus:outline-none focus:ring-2 focus:ring-orange-100"
                    >
                        <option value="latest" <?php if(($sort ?? request('sort', 'latest')) === 'latest'): echo 'selected'; endif; ?>>Terbaru</option>
                        <option value="oldest" <?php if(($sort ?? request('sort')) === 'oldest'): echo 'selected'; endif; ?>>Terlama</option>
                        <option value="total_high" <?php if(($sort ?? request('sort')) === 'total_high'): echo 'selected'; endif; ?>>Total tertinggi</option>
                        <option value="total_low" <?php if(($sort ?? request('sort')) === 'total_low'): echo 'selected'; endif; ?>>Total terendah</option>
                    </select>
                </div>

                
                <div>
                    <label for="date_from" class="mb-2 block text-sm font-semibold text-gray-700">
                        Dari tanggal
                    </label>
                    <input
                        type="date"
                        id="date_from"
                        name="date_from"
                        value="<?php echo e(request('date_from')); ?>"
                        class="w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm text-gray-800 focus:border-pesona-orange focus:outline-none focus:ring-2 focus:ring-orange-100"
                    >
                    <?php $__errorArgs = ['date_from'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="mt-1 text-xs text-red-600"><?php echo e($message); ?></p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div>
                    <label for="date_to" class="mb-2 block text-sm font-semibold text-gray-700">
                        Sampai tanggal
                    </label>
                    <input
                        type="date"
                        id="date_to"
                        name="date_to"
                        value="<?php echo e(request('date_to')); ?>"
                        class="w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm text-gray-800 focus:border-pesona-orange focus:outline-none focus:ring-2 focus:ring-orange-100"
                    >
                    <?php $__errorArgs = ['date_to'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="mt-1 text-xs text-red-600"><?php echo e($message); ?></p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

            </div>

            <div class="flex flex-col gap-2 border-t border-gray-100 pt-4 sm:flex-row sm:justify-end">
                <?php if(request()->hasAny(['search', 'status', 'payment_status', 'attention', 'date_from', 'date_to', 'sort'])): ?>
                    <a
                        href="<?php echo e(route('admin.orders.index')); ?>"
                        class="inline-flex items-center justify-center rounded-xl border border-gray-200 px-5 py-2.5 text-sm font-semibold text-gray-600 transition hover:bg-gray-50"
                    >
                        Reset
                    </a>
                <?php endif; ?>

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

            
            <div class="w-full min-w-0 flex-1">
                <div class="overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-sm">

                    <?php if($orders->isEmpty()): ?>
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

                            <?php if(request()->hasAny(['search', 'status', 'payment_status', 'attention', 'date_from', 'date_to', 'sort'])): ?>
                                <a
                                    href="<?php echo e(route('admin.orders.index')); ?>"
                                    class="mt-5 inline-flex items-center justify-center rounded-xl bg-pesona-dark px-4 py-2.5 text-sm font-bold text-white transition hover:bg-black"
                                >
                                    Hapus Semua Filter
                                </a>
                            <?php endif; ?>
                        </div>
                    <?php else: ?>

                        <div class="border-b border-gray-100 px-4 py-3 sm:px-5">
                            <p class="text-sm text-gray-500">
                                Menampilkan
                                <span class="font-bold text-gray-900"><?php echo e($orders->firstItem() ?? 0); ?>–<?php echo e($orders->lastItem() ?? 0); ?></span>
                                dari
                                <span class="font-bold text-gray-900"><?php echo e(number_format($orders->total())); ?></span>
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
                                    <?php $__currentLoopData = $orders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php
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
                                        ?>

                                        <tr class="transition-colors <?php echo e($isSelected ? 'bg-orange-50/70' : 'hover:bg-gray-50/80'); ?>">
                                            
                                            <td class="whitespace-nowrap border-l-4 px-4 py-4 <?php echo e($isSelected ? 'border-pesona-orange' : 'border-transparent'); ?>">
                                                <a
                                                    href="<?php echo e(route('admin.orders.index', array_merge(request()->query(), ['selected' => $order->id]))); ?>"
                                                    aria-current="<?php echo e($isSelected ? 'true' : 'false'); ?>"
                                                    class="font-bold text-pesona-dark transition hover:text-pesona-orange hover:underline focus:outline-none focus:ring-2 focus:ring-orange-200"
                                                >
                                                    <?php echo e($order->order_number); ?>

                                                </a>
                                            </td>

                                            
                                            <td class="px-4 py-4">
                                                <p class="font-semibold text-gray-900">
                                                    <?php echo e($order->user?->name ?? $order->recipient_name); ?>

                                                </p>
                                                <p class="mt-1 text-xs text-gray-500">
                                                    <?php echo e($order->phone ?: 'Nomor HP belum tersedia'); ?>

                                                </p>
                                            </td>

                                            
                                            <td class="whitespace-nowrap px-4 py-4">
                                                <?php if($order->total_amount !== null): ?>
                                                    <span class="font-bold text-gray-900">
                                                        Rp<?php echo e(number_format(\App\Models\Order::rupiahToInt($order->total_amount), 0, ',', '.')); ?>

                                                    </span>
                                                <?php else: ?>
                                                    <span class="inline-flex rounded-lg border border-amber-200 bg-amber-50 px-2.5 py-1 text-xs font-semibold text-amber-800">
                                                        Menunggu ongkir
                                                    </span>
                                                <?php endif; ?>
                                            </td>

                                            
                                            <td class="whitespace-nowrap px-4 py-4">
                                                <span class="inline-flex rounded-full border px-2.5 py-1 text-xs font-semibold <?php echo e($badgeClass); ?>">
                                                    <?php echo e($order->status_label); ?>

                                                </span>
                                            </td>

                                            
                                            <td class="whitespace-nowrap px-4 py-4">
                                                <span class="inline-flex rounded-full border px-2.5 py-1 text-xs font-semibold <?php echo e($pBadgeClass); ?>">
                                                    <?php echo e($order->payment_status_label); ?>

                                                </span>

                                                <?php if($order->payment_instructions_issued_at && $order->payment_due_at): ?>
                                                    <p class="mt-1.5 text-[11px] <?php echo e($order->payment_due_at->isPast() || $order->payment_review_required ? 'font-semibold text-red-700' : 'text-gray-500'); ?>">
                                                        Tenggat:
                                                        <?php echo e($order->payment_due_at->timezone('Asia/Jakarta')->format('d/m/Y H:i')); ?> WIB
                                                    </p>
                                                <?php endif; ?>
                                            </td>

                                            
                                            <td class="whitespace-nowrap px-4 py-4 text-gray-600">
                                                <p><?php echo e($order->created_at->timezone('Asia/Jakarta')->format('d M Y')); ?></p>
                                                <p class="mt-1 text-xs text-gray-400">
                                                    <?php echo e($order->created_at->timezone('Asia/Jakarta')->format('H:i')); ?> WIB
                                                </p>
                                            </td>

                                            
                                            <td class="min-w-44 px-4 py-4">
                                                <div class="flex items-start gap-2">
                                                    <span class="mt-1.5 h-2 w-2 shrink-0 rounded-full <?php echo e($order->payment_review_required || ($order->payment_due_at && $order->payment_due_at->isPast()) ? 'bg-red-500' : ($order->shipping_cost === null ? 'bg-amber-500' : 'bg-pesona-orange')); ?>"></span>
                                                    <span class="text-xs leading-5 text-gray-600">
                                                        <?php echo e($nextAction); ?>

                                                    </span>
                                                </div>
                                            </td>

                                            
                                            <td class="whitespace-nowrap px-4 py-4 text-right">
                                                <div class="flex items-center justify-end gap-2">
                                                    <a
                                                        href="<?php echo e(route('admin.orders.index', array_merge(request()->query(), ['selected' => $order->id]))); ?>"
                                                        aria-current="<?php echo e($isSelected ? 'true' : 'false'); ?>"
                                                        class="hidden rounded-lg px-3 py-2 text-xs font-bold transition 2xl:inline-flex <?php echo e($isSelected ? 'bg-orange-100 text-pesona-dark ring-1 ring-orange-200' : 'border border-gray-200 bg-white text-gray-600 hover:border-orange-200 hover:text-pesona-orange'); ?>"
                                                    >
                                                        <?php echo e($isSelected ? 'Terpilih' : 'Pilih'); ?>

                                                    </a>

                                                    <a
                                                        href="<?php echo e(route('admin.orders.show', ['order' => $order->id] + request()->query())); ?>"
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
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                        </div>

                        
                        <div class="border-t border-gray-100 bg-white px-4 py-4 sm:px-5">
                            <?php echo e($orders->links()); ?>

                        </div>
                    <?php endif; ?>
                </div>
            </div>

            
            <aside class="w-full shrink-0 space-y-3 2xl:sticky 2xl:top-6 2xl:w-[360px] 2xl:max-h-[calc(100vh-3rem)] 2xl:overflow-y-auto">

                <div class="flex items-center justify-between gap-3">
                    <div>
                        <h3 class="text-base font-black text-pesona-dark">Ringkasan Pesanan</h3>
                        <p class="mt-1 text-xs text-gray-500">Informasi pesanan yang dipilih</p>
                    </div>
                    <?php if($selectedOrder): ?>
                        <span class="rounded-full bg-orange-50 px-3 py-1 text-xs font-bold text-pesona-orange">
                            Terpilih
                        </span>
                    <?php endif; ?>
                </div>

                <?php if($selectedOrder): ?>
                    <?php echo $__env->make('admin.orders.partials.detail-panel', ['order' => $selectedOrder], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                <?php else: ?>
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
                <?php endif; ?>

            </aside>

        </div>
    </section>

</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\pc1\Documents\Bakso_Pesona\resources\views/admin/orders/index.blade.php ENDPATH**/ ?>