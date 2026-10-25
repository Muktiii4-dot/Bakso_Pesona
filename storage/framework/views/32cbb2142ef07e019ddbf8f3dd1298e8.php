

<?php $__env->startSection('title', 'Riwayat Audit'); ?>
<?php $__env->startSection('header_title', 'Riwayat Audit Pesanan'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <div>
        <h1 class="text-2xl font-black text-pesona-dark sm:text-3xl">Riwayat Audit</h1>
        <p class="mt-1 text-sm text-gray-600">Perubahan pesanan, instruksi pembayaran, verifikasi, pembatalan, refund, stok, dan moderasi yang tercatat.</p>
    </div>

    <form method="GET" action="<?php echo e(route('admin.audit.index')); ?>" class="flex flex-col gap-3 rounded-2xl border border-orange-100 bg-white p-4 shadow-sm sm:flex-row">
        <label for="search" class="sr-only">Cari nomor pesanan atau jenis peristiwa</label>
        <input
            id="search"
            name="search"
            value="<?php echo e(request('search')); ?>"
            maxlength="255"
            placeholder="Cari nomor pesanan atau jenis peristiwa"
            class="min-w-0 flex-1 rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:border-pesona-orange focus:outline-none focus:ring-1 focus:ring-pesona-orange"
        >
        <button type="submit" class="rounded-xl bg-pesona-dark px-5 py-2.5 text-sm font-bold text-white hover:bg-black">Cari audit</button>
    </form>

    <section class="overflow-hidden rounded-2xl border border-orange-100 bg-white shadow-sm">
        <?php if($events->isEmpty()): ?>
            <div class="px-6 py-12 text-center text-sm text-gray-500">Belum ada catatan audit yang sesuai.</div>
        <?php else: ?>
            <ol class="divide-y divide-gray-100">
                <?php $__currentLoopData = $events; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $event): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li class="p-5 sm:p-6">
                        <div class="flex flex-col gap-2 sm:flex-row sm:items-start sm:justify-between">
                            <div>
                                <p class="text-sm font-bold text-pesona-dark"><?php echo e(str($event->event_type)->replace('_', ' ')->title()); ?></p>
                                <p class="mt-1 text-xs text-gray-600">
                                    Pesanan:
                                    <?php if($event->order): ?>
                                        <a href="<?php echo e(route('admin.orders.show', $event->order)); ?>" class="font-bold text-pesona-orange hover:underline"><?php echo e($event->order->order_number); ?></a>
                                    <?php else: ?>
                                        tidak tersedia
                                    <?php endif; ?>
                                    · Pelaku: <?php echo e($event->actor?->name ?? ucfirst($event->actor_type)); ?>

                                </p>
                            </div>
                            <time class="shrink-0 text-xs text-gray-500"><?php echo e($event->created_at->timezone('Asia/Jakarta')->format('d M Y, H:i:s')); ?> WIB</time>
                        </div>
                        <?php if($event->old_values || $event->new_values || $event->metadata): ?>
                            <details class="mt-3 text-xs text-gray-600">
                                <summary class="cursor-pointer font-semibold">Lihat rincian perubahan</summary>
                                <div class="mt-2 space-y-1 break-words rounded-lg bg-gray-50 p-3">
                                    <?php if($event->old_values): ?>
                                        <p><strong>Sebelum:</strong> <?php echo e(json_encode($event->old_values, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)); ?></p>
                                    <?php endif; ?>
                                    <?php if($event->new_values): ?>
                                        <p><strong>Sesudah:</strong> <?php echo e(json_encode($event->new_values, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)); ?></p>
                                    <?php endif; ?>
                                    <?php if($event->metadata): ?>
                                        <p><strong>Catatan:</strong> <?php echo e(json_encode($event->metadata, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)); ?></p>
                                    <?php endif; ?>
                                </div>
                            </details>
                        <?php endif; ?>
                    </li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ol>
            <div class="border-t border-gray-100 px-4 py-3"><?php echo e($events->links()); ?></div>
        <?php endif; ?>
    </section>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\pc1\Documents\Bakso_Pesona\resources\views/admin/audit/index.blade.php ENDPATH**/ ?>