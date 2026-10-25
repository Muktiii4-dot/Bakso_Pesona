<?php
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
?>

<div class="rounded-2xl border border-orange-100 bg-white p-5 shadow-sm space-y-6">
    <div class="border-b border-gray-100 pb-4">
        <p class="text-[11px] font-bold uppercase tracking-wider text-pesona-orange">Detail Pesanan Terpilih</p>
        <h2 class="mt-1 break-all text-xl font-black text-pesona-dark"><?php echo e($order->order_number); ?></h2>
        <p class="mt-1 text-xs text-gray-500">Dibuat pada <?php echo e($order->created_at->timezone('Asia/Jakarta')->format('d M Y, H:i')); ?> WIB</p>
        <div class="mt-3 flex flex-wrap gap-2">
            <span class="inline-flex rounded-full border px-2.5 py-0.5 text-xs font-bold <?php echo e($statusClasses[$order->status] ?? 'border-gray-200 bg-gray-100 text-gray-700'); ?>"><?php echo e($order->status_label); ?></span>
            <span class="inline-flex rounded-full border px-2.5 py-0.5 text-xs font-bold <?php echo e($paymentClasses[$order->payment_status] ?? 'border-gray-200 bg-gray-100 text-gray-700'); ?>"><?php echo e($order->payment_status_label); ?></span>
        </div>
    </div>

    <div class="rounded-xl border border-gray-100 bg-gray-50/50 p-4 space-y-3 text-xs">
        <p class="font-bold uppercase tracking-wider text-gray-500 text-[10px]">Data Penerima & Kontak</p>
        <div>
            <span class="text-gray-400">Nama penerima</span>
            <p class="font-bold text-gray-900 text-sm"><?php echo e($order->recipient_name); ?></p>
            <?php if($order->user): ?>
                <p class="text-[11px] text-gray-500">Akun: <?php echo e($order->user->name); ?> (<?php echo e($order->user->email); ?>)</p>
            <?php endif; ?>
        </div>
        <div>
            <span class="text-gray-400">Nomor telepon</span>
            <p class="font-bold text-gray-900"><?php echo e($order->phone); ?></p>
        </div>
        <div>
            <span class="text-gray-400">Alamat pengiriman</span>
            <p class="mt-1 whitespace-pre-line rounded-lg border border-gray-100 bg-white p-2.5 font-medium leading-relaxed text-gray-800"><?php echo e($order->shipping_address); ?><?php if($order->shipping_city || $order->shipping_province): ?><?php echo e("\n"); ?><?php echo e($order->shipping_city); ?><?php echo e($order->shipping_province ? ', '.$order->shipping_province : ''); ?><?php echo e($order->shipping_postal_code ? ' '.$order->shipping_postal_code : ''); ?><?php endif; ?></p>
        </div>
        <?php if($order->delivery_landmark): ?>
            <div>
                <span class="text-gray-400">Patokan lokasi</span>
                <p class="mt-1 whitespace-pre-line text-gray-800"><?php echo e($order->delivery_landmark); ?></p>
            </div>
        <?php endif; ?>
        <?php if($order->customer_note): ?>
            <div>
                <span class="text-gray-400">Catatan pelanggan</span>
                <p class="mt-1 whitespace-pre-line text-gray-800"><?php echo e($order->customer_note); ?></p>
            </div>
        <?php endif; ?>
    </div>

    <div>
        <p class="mb-2 font-bold uppercase tracking-wider text-gray-500 text-[10px]">Item dipesan (<?php echo e($order->items->count()); ?>)</p>
        <div class="divide-y divide-gray-100 rounded-xl border border-gray-100 bg-white">
            <?php $__currentLoopData = $order->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="flex items-center justify-between gap-2 p-3 text-xs">
                    <div class="min-w-0 flex-1">
                        <p class="truncate font-bold text-gray-900"><?php echo e($item->product_name); ?></p>
                        <p class="text-gray-400"><?php echo e($item->quantity); ?> × Rp<?php echo e(number_format(\App\Models\Order::rupiahToInt($item->unit_price), 0, ',', '.')); ?></p>
                    </div>
                    <span class="whitespace-nowrap font-bold text-pesona-dark">Rp<?php echo e(number_format(\App\Models\Order::rupiahToInt($item->subtotal), 0, ',', '.')); ?></span>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>

    <div class="rounded-xl border border-orange-100 bg-orange-50/30 p-4 space-y-2 text-xs">
        <div class="flex justify-between text-gray-600">
            <span>Subtotal produk</span>
            <span class="font-semibold text-gray-900">Rp<?php echo e(number_format(\App\Models\Order::rupiahToInt($order->subtotal), 0, ',', '.')); ?></span>
        </div>
        <div class="flex justify-between text-gray-600">
            <span>Diskon</span>
            <span class="font-semibold text-gray-900">-Rp<?php echo e(number_format(\App\Models\Order::rupiahToInt($order->discount_amount ?? 0), 0, ',', '.')); ?></span>
        </div>
        <div class="flex justify-between text-gray-600">
            <span>Ongkos kirim</span>
            <span class="font-semibold text-gray-900"><?php echo e($order->shipping_cost === null ? 'Belum ditetapkan' : 'Rp'.number_format(\App\Models\Order::rupiahToInt($order->shipping_cost), 0, ',', '.')); ?></span>
        </div>
        <div class="flex justify-between border-t border-orange-200 pt-2 font-bold text-pesona-dark">
            <span>Total akhir</span>
            <span><?php echo e($order->total_amount === null ? 'Menunggu ongkir' : 'Rp'.number_format(\App\Models\Order::rupiahToInt($order->total_amount), 0, ',', '.')); ?></span>
        </div>
        <?php if($order->payment_instructions_issued_at): ?>
            <p class="text-gray-600">Instruksi terkirim: <?php echo e($order->payment_instructions_issued_at->timezone('Asia/Jakarta')->format('d M Y, H:i')); ?> WIB</p>
            <p class="text-gray-600">Tenggat: <?php echo e($order->payment_due_at ? $order->payment_due_at->timezone('Asia/Jakarta')->format('d M Y, H:i').' WIB' : 'Tidak tersedia'); ?></p>
        <?php endif; ?>
        <?php if($order->payment_review_required): ?>
            <p class="rounded-lg bg-amber-100 p-2 font-semibold text-amber-900">Perlu pemeriksaan mutasi; stok tetap ditahan.</p>
        <?php endif; ?>
    </div>

    <a href="<?php echo e(route('admin.orders.show', ['order' => $order->id] + request()->query())); ?>" class="flex w-full items-center justify-center rounded-xl bg-pesona-dark px-4 py-3 text-xs font-bold text-white transition hover:bg-black">
        Buka detail & tindakan pesanan
    </a>
</div>
<?php /**PATH C:\Users\pc1\Downloads\Bakso_Pesona\resources\views/admin/orders/partials/detail-panel.blade.php ENDPATH**/ ?>