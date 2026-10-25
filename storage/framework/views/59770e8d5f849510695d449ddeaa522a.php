<?php $__env->startSection('title', 'Manajemen Produk'); ?>
<?php $__env->startSection('header_title', 'Kelola Produk & Menu'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-black text-pesona-dark sm:text-3xl">Daftar Produk</h1>
            <p class="mt-1 text-xs text-gray-500">Kelola katalog menu, harga, stok, dan ketersediaan makanan & minuman.</p>
        </div>

        <a
            href="<?php echo e(route('admin.products.create')); ?>"
            class="inline-flex items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-[#74191B] to-[#912224] hover:from-[#5C1315] hover:to-[#74191B] px-4 py-2.5 text-xs font-bold text-white shadow-md shadow-red-950/10 transition"
        >
            <span class="text-base font-bold leading-none">+</span>
            <span>Tambah Produk Baru</span>
        </a>
    </div>

    
    <div class="rounded-2xl border border-orange-100 bg-white p-4 shadow-sm">
        <form action="<?php echo e(route('admin.products.index')); ?>" method="GET" class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
            <?php if(request()->boolean('low_stock')): ?>
                <input type="hidden" name="low_stock" value="1">
            <?php endif; ?>
            <div>
                <label for="search" class="mb-1 block text-xs font-semibold text-gray-600">Cari Produk</label>
                <input
                    type="text"
                    id="search"
                    name="search"
                    value="<?php echo e(request('search')); ?>"
                    placeholder="Nama menu..."
                    class="w-full rounded-xl border border-gray-200 px-3 py-2 text-xs outline-none focus:border-[#E87532] focus:ring-1 focus:ring-orange-200"
                >
            </div>

            <div>
                <label for="category_id" class="mb-1 block text-xs font-semibold text-gray-600">Kategori</label>
                <select
                    id="category_id"
                    name="category_id"
                    class="w-full rounded-xl border border-gray-200 px-3 py-2 text-xs outline-none focus:border-[#E87532] focus:ring-1 focus:ring-orange-200"
                >
                    <option value="">Semua Kategori</option>
                    <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($cat->id); ?>" <?php if(request('category_id') == $cat->id): echo 'selected'; endif; ?>>
                            <?php echo e($cat->name); ?>

                        </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>

            <div>
                <label for="status" class="mb-1 block text-xs font-semibold text-gray-600">Status Ketersediaan</label>
                <select
                    id="status"
                    name="status"
                    class="w-full rounded-xl border border-gray-200 px-3 py-2 text-xs outline-none focus:border-[#E87532] focus:ring-1 focus:ring-orange-200"
                >
                    <option value="">Semua Status</option>
                    <option value="available" <?php if(request('status') === 'available'): echo 'selected'; endif; ?>>Aktif / Tersedia</option>
                    <option value="unavailable" <?php if(request('status') === 'unavailable'): echo 'selected'; endif; ?>>Habis / Nonaktif</option>
                </select>
            </div>

            <div class="flex items-end gap-2">
                <button
                    type="submit"
                    class="flex-1 rounded-xl bg-gray-800 px-4 py-2 text-xs font-bold text-white transition hover:bg-gray-900"
                >
                    Terapkan
                </button>
                <?php if(request()->hasAny(['search', 'category_id', 'status', 'low_stock'])): ?>
                    <a
                        href="<?php echo e(route('admin.products.index')); ?>"
                        class="rounded-xl border border-gray-200 px-3 py-2 text-xs font-semibold text-gray-600 hover:bg-gray-50"
                        title="Reset Filter"
                    >
                        Reset
                    </a>
                <?php endif; ?>
            </div>
        </form>
        <?php if(request()->boolean('low_stock')): ?>
            <p class="mt-3 text-xs font-semibold text-rose-700">Menampilkan produk aktif dengan stok 5 atau kurang.</p>
        <?php endif; ?>
    </div>

    
    <div class="overflow-hidden rounded-2xl border border-orange-100 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-gray-600">
                <thead class="border-b border-gray-100 bg-gray-50/60 text-[11px] font-bold uppercase tracking-wider text-gray-500">
                    <tr>
                        <th class="px-4 py-3">Produk</th>
                        <th class="px-4 py-3">Kategori</th>
                        <th class="px-4 py-3">Harga</th>
                        <th class="px-4 py-3">Stok</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php $__empty_1 = true; $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="hover:bg-orange-50/20 transition">
                            <td class="px-4 py-3.5">
                                <div class="flex items-center gap-3">
                                    <div class="h-12 w-12 shrink-0 overflow-hidden rounded-xl border border-gray-200 bg-gray-100">
                                        <?php if($product->image): ?>
                                            <img
                                                src="<?php echo e(filter_var($product->image, FILTER_VALIDATE_URL) ? $product->image : asset($product->image)); ?>"
                                                alt="<?php echo e($product->name); ?>"
                                                class="h-full w-full object-cover"
                                                onerror="this.src='https://placehold.co/100x100?text=Bakso';"
                                            >
                                        <?php else: ?>
                                            <div class="flex h-full w-full items-center justify-center bg-orange-100 text-lg">
                                                🍜
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="min-w-0">
                                        <p class="font-bold text-gray-900 truncate"><?php echo e($product->name); ?></p>
                                        <p class="text-[11px] text-gray-400 font-mono truncate">/produk/<?php echo e($product->slug); ?></p>
                                        <?php if($product->badge): ?>
                                            <span class="inline-block mt-0.5 rounded bg-orange-100 px-1.5 py-0.5 text-[9px] font-bold text-orange-800">
                                                <?php echo e($product->badge); ?>

                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3.5">
                                <span class="rounded-lg bg-gray-100 px-2 py-1 font-semibold text-gray-700">
                                    <?php echo e($product->category->name ?? '-'); ?>

                                </span>
                            </td>
                            <td class="px-4 py-3.5 font-bold text-gray-900">
                                Rp <?php echo e(number_format((float) $product->price, 0, ',', '.')); ?>

                            </td>
                            <td class="px-4 py-3.5">
                                <span class="font-semibold <?php echo e($product->stock <= 5 ? 'text-red-600 font-bold' : 'text-gray-700'); ?>">
                                    <?php echo e($product->stock); ?> porsi
                                </span>
                            </td>
                            <td class="px-4 py-3.5">
                                <?php if($product->is_available): ?>
                                    <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2.5 py-0.5 font-bold text-emerald-700 border border-emerald-200">
                                        <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                                        Tersedia
                                    </span>
                                <?php else: ?>
                                    <span class="inline-flex items-center gap-1 rounded-full bg-gray-100 px-2.5 py-0.5 font-bold text-gray-600 border border-gray-200">
                                        <span class="h-1.5 w-1.5 rounded-full bg-gray-400"></span>
                                        Nonaktif
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-3.5 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a
                                        href="<?php echo e(route('admin.products.edit', $product)); ?>"
                                        class="rounded-lg border border-gray-200 bg-white px-2.5 py-1 text-xs font-semibold text-gray-700 hover:bg-gray-50 transition"
                                        title="Ubah Produk"
                                    >
                                        Edit
                                    </a>

                                    <form
                                        action="<?php echo e(route('admin.products.destroy', $product)); ?>"
                                        method="POST"
                                        onsubmit="return confirm('Apakah kamu yakin ingin menghapus / menonaktifkan produk <?php echo e($product->name); ?>?');"
                                    >
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('DELETE'); ?>
                                        <button
                                            type="submit"
                                            class="rounded-lg border border-red-200 bg-white px-2.5 py-1 text-xs font-semibold text-red-600 hover:bg-red-50 transition"
                                            title="Hapus / Nonaktifkan"
                                        >
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-gray-400">
                                <p class="text-sm font-semibold">Belum ada produk yang cocok dengan pencarian.</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        
        <?php if($products->hasPages()): ?>
            <div class="border-t border-gray-100 p-4">
                <?php echo e($products->links()); ?>

            </div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\pc1\Documents\Bakso_Pesona\resources\views/admin/products/index.blade.php ENDPATH**/ ?>