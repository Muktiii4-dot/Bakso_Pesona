<?php $__env->startSection('title', 'Manajemen Kategori'); ?>
<?php $__env->startSection('header_title', 'Kelola Kategori Produk'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <div>
        <h1 class="text-2xl font-black text-pesona-dark sm:text-3xl">Manajemen Kategori</h1>
        <p class="mt-1 text-xs text-gray-500">Kelola kategori produk untuk mengorganisir menu Bakso Pesona.</p>
    </div>

    <?php if(session('success')): ?>
        <div class="flex items-start gap-3 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
            <?php echo e(session('success')); ?>

        </div>
    <?php endif; ?>
    <?php if(session('error')): ?>
        <div class="flex items-start gap-3 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
            <?php echo e(session('error')); ?>

        </div>
    <?php endif; ?>
    <?php if(session('warning')): ?>
        <div class="flex items-start gap-3 rounded-xl border border-yellow-200 bg-yellow-50 px-4 py-3 text-sm text-yellow-800">
            <?php echo e(session('warning')); ?>

        </div>
    <?php endif; ?>

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="lg:col-span-1">
            <div class="rounded-2xl border border-orange-100 bg-white p-5 shadow-sm">
                <h2 class="mb-4 text-sm font-bold text-pesona-dark">+ Tambah Kategori Baru</h2>
                <form action="<?php echo e(route('admin.categories.store')); ?>" method="POST" class="space-y-4">
                    <?php echo csrf_field(); ?>
                    <div>
                        <label for="name" class="mb-1 block text-xs font-semibold text-gray-600">
                            Nama Kategori <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="text"
                            id="name"
                            name="name"
                            value="<?php echo e(old('name')); ?>"
                            placeholder="cth: Bakso Spesial, Minuman..."
                            class="w-full rounded-lg border <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-400 bg-red-50 <?php else: ?> border-gray-200 bg-gray-50 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?> px-3 py-2 text-sm text-gray-800 placeholder-gray-400 transition focus:border-pesona-primary focus:bg-white focus:outline-none focus:ring-2 focus:ring-pesona-primary/20"
                        />
                        <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="mt-1 text-xs text-red-500"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    <div>
                        <label for="description" class="mb-1 block text-xs font-semibold text-gray-600">
                            Deskripsi <span class="text-gray-400">(opsional)</span>
                        </label>
                        <textarea
                            id="description"
                            name="description"
                            rows="3"
                            placeholder="Deskripsi singkat kategori..."
                            class="w-full resize-none rounded-lg border border-gray-200 bg-gray-50 px-3 py-2 text-sm text-gray-800 placeholder-gray-400 transition focus:border-pesona-primary focus:bg-white focus:outline-none"
                        ><?php echo e(old('description')); ?></textarea>
                    </div>
                    <button type="submit" class="w-full rounded-xl bg-gradient-to-r from-[#74191B] to-[#912224] px-4 py-2.5 text-xs font-bold text-white shadow-md transition hover:from-[#5C1315] hover:to-[#74191B]">
                        Simpan Kategori
                    </button>
                </form>
            </div>
        </div>

        <div class="lg:col-span-2">
            <div class="rounded-2xl border border-orange-100 bg-white shadow-sm overflow-hidden">
                <div class="border-b border-gray-100 px-5 py-4">
                    <h2 class="text-sm font-bold text-pesona-dark">Daftar Kategori (<?php echo e($categories->count()); ?>)</h2>
                </div>
                <?php if($categories->isEmpty()): ?>
                    <div class="flex flex-col items-center justify-center gap-2 py-16 text-gray-400">
                        <p class="text-sm font-medium">Belum ada kategori</p>
                        <p class="text-xs">Tambahkan kategori pertama melalui form di sebelah kiri.</p>
                    </div>
                <?php else: ?>
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead class="bg-gray-50 text-xs font-semibold uppercase tracking-wide text-gray-500">
                                <tr>
                                    <th class="px-5 py-3 text-left">Nama Kategori</th>
                                    <th class="px-5 py-3 text-left">Slug</th>
                                    <th class="px-5 py-3 text-left">Deskripsi</th>
                                    <th class="px-5 py-3 text-center">Jml. Produk</th>
                                    <th class="px-5 py-3 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr class="hover:bg-orange-50/30 transition-colors">
                                        <td class="px-5 py-3.5">
                                            <span class="font-semibold text-pesona-dark"><?php echo e($category->name); ?></span>
                                        </td>
                                        <td class="px-5 py-3.5">
                                            <code class="rounded bg-gray-100 px-1.5 py-0.5 text-xs text-gray-500"><?php echo e($category->slug); ?></code>
                                        </td>
                                        <td class="px-5 py-3.5 max-w-xs">
                                            <span class="line-clamp-2 text-xs text-gray-500"><?php echo e($category->description ?? '-'); ?></span>
                                        </td>
                                        <td class="px-5 py-3.5 text-center">
                                            <?php if($category->products_count > 0): ?>
                                                <span class="inline-flex items-center justify-center rounded-full bg-orange-100 px-2.5 py-0.5 text-xs font-bold text-orange-700">
                                                    <?php echo e($category->products_count); ?>

                                                </span>
                                            <?php else: ?>
                                                <span class="text-xs text-gray-400">0</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="px-5 py-3.5">
                                            <div class="flex items-center justify-center gap-2">
                                                <a href="<?php echo e(route('admin.categories.edit', $category)); ?>"
                                                   class="inline-flex items-center gap-1 rounded-lg bg-blue-50 px-3 py-1.5 text-xs font-semibold text-blue-600 transition hover:bg-blue-100">
                                                    Edit
                                                </a>
                                                <?php if($category->products_count === 0): ?>
                                                    <form action="<?php echo e(route('admin.categories.destroy', $category)); ?>"
                                                          method="POST"
                                                          onsubmit="return confirm('Hapus kategori \'<?php echo e($category->name); ?>\'? Tindakan ini tidak dapat dibatalkan.')">
                                                        <?php echo csrf_field(); ?>
                                                        <?php echo method_field('DELETE'); ?>
                                                        <button type="submit"
                                                                class="inline-flex items-center gap-1 rounded-lg bg-red-50 px-3 py-1.5 text-xs font-semibold text-red-600 transition hover:bg-red-100">
                                                            Hapus
                                                        </button>
                                                    </form>
                                                <?php else: ?>
                                                    <span title="Tidak dapat dihapus karena masih memiliki <?php echo e($category->products_count); ?> produk"
                                                          class="inline-flex items-center gap-1 rounded-lg bg-gray-100 px-3 py-1.5 text-xs font-semibold text-gray-400 cursor-not-allowed">
                                                        Hapus
                                                    </span>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\pc1\Documents\Bakso_Pesona\resources\views/admin/categories/index.blade.php ENDPATH**/ ?>