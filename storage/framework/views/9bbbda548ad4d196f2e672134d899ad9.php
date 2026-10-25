<?php $__env->startSection('title', 'Keranjang Belanja — Bakso Pesona'); ?>

<?php $__env->startSection('content'); ?>
<section class="min-h-[70vh] bg-[#FFF9F3] px-4 py-10 sm:px-6 sm:py-14 lg:px-8">
    <div class="mx-auto max-w-7xl">

        <a href="<?php echo e(route('home')); ?>"
            class="mb-6 inline-flex items-center gap-2 rounded-full border border-orange-200 bg-white px-4 py-2.5 text-sm font-bold text-[#74191B] transition hover:border-[#B83B25] hover:bg-orange-50 focus:outline-none focus:ring-2 focus:ring-[#B83B25] focus:ring-offset-2">
            <span aria-hidden="true">&larr;</span>
            Kembali ke Beranda
        </a>

        
        <div class="mb-8">
            <p class="text-sm font-bold uppercase tracking-[0.2em] text-[#B83B25]">
                Bakso Pesona
            </p>

            <h1 class="mt-2 text-3xl font-black tracking-tight text-[#74191B] sm:text-4xl">
                Keranjang Belanja
            </h1>

            <p class="mt-3 text-sm leading-6 text-gray-600 sm:text-base">
                Periksa pilihan produkmu sebelum melanjutkan ke tahap pemesanan.
            </p>
        </div>

        
        <?php if(session('success')): ?>
            <div role="status" class="mb-5 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm font-medium text-green-800">
                <?php echo e(session('success')); ?>

            </div>
        <?php endif; ?>

        <?php if(session('error')): ?>
            <div role="alert" class="mb-5 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-800">
                <?php echo e(session('error')); ?>

            </div>
        <?php endif; ?>

        <?php if($errors->any()): ?>
            <div role="alert" class="mb-5 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                <p class="font-bold">Periksa kembali input kamu:</p>
                <ul class="mt-2 list-inside list-disc">
                    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li><?php echo e($error); ?></li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            </div>
        <?php endif; ?>

        <?php if($items->isEmpty()): ?>
            
            <div class="rounded-3xl border border-orange-100 bg-white px-5 py-14 text-center shadow-sm sm:py-20">
                <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-full bg-orange-50 text-[#B83B25]">
                    <svg xmlns="http://ww.w3.org/2000/svg" class="h-10 w-10" fill="none"
                        viewbox="0 0 24 24" stroke="currentColor" stroke-width="1.7">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M3 3h212.2 11.2a2 2 0 002 1.6h8.9a2 2 0 002-1.6L22 7H6" />
                        <circle cx="10" cy="20" r="1" />
                        <circle cx="18" cy="20" r="1" />
                    </svg>
                </div>

                <h2 class="mt-6 text-xl font-extrabold text-[#74191B] sm:text-2xl">
                    Keranjangmu masih kosong
                </h2>

                <p class="mx-auto mt-3 max-w-md text-sm leading-6 text-gray-500">
                    Yuk, temukan bakso favoritmu dan tambakan ke keranjang.
                </p>

                <a href="<?php echo e(route('products.index')); ?>"
                    class="mt-7 inline-flex items-center justify-center rounded-full bg-[#B83B25] px-6 py-3 text-sm font-bold text-white transition hover:bg-[#74191B] focus:outline-noen focus:ring-2 focus:ring-[#B83B25] focus:ring-offset-2">
                    Lihat produk
                </a>
            </div>
        <?php else: ?>
            <div class="grid items-start gap-6 lg:grid-cols-[1fr_340px]">
                
                <div class="space-y-4">
                    <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php
                            $product = $item['product'];
                            $quantity = $item['quantity'];
                            $subtotal = $item['subtotal'];
                        ?>

                        <article wire:key="cart-product-<?php echo e($product->id); ?>"
                            class="rounded-2xl border border-orange-100 bg-white p-4 shadow-sm sm:p-5">
                        
                            <div class="flex gap-4 sm:gap-5">
                                
                                <a href="<?php echo e(route('products.show', $product->slug)); ?>"
                                    class="h-24 w-24 shrink-0 overflow-hidden rounded-xl bg-orange-50 sm:h-32 sm:w-32">
                                    <?php if($product->image): ?>
                                        <img src="<?php echo e($product->image); ?>"
                                            alt="<?php echo e($product->name); ?>"
                                            class="h-full w-full object-cover"
                                            loading="lazy"
                                            onerror="this.style.display='none'">
                                    <?php else: ?>
                                        <div class="flex h-full items-center justify-center px-2 text-center text-xs font-medium text-[#B83B25]">
                                            Bakso Pesona
                                        </div>
                                    <?php endif; ?>
                                </a>

                                
                                <div class="min-w-0 flex-1">
                                    <div class="flex flex-wrap items-start justify-between gap-2">
                                        <div class="min-w-0">
                                            <a href="<?php echo e(route('products.show', $product->slug)); ?>"
                                                class="font-extrabold leading-5 text-[#74191B] transition hover:text-[#B83B25] sm:text-lg">
                                                <?php echo e($product->name); ?>

                                            </a>

                                            <?php if($product->badge): ?>
                                                <span class="mt-2 inline-block rounded-full bg-orange-50 px-2.5 py-1 text-xs font-bold text-[#B83B25]">
                                                    <?php echo e($product->badge); ?>

                                                </span>
                                            <?php endif; ?>
                                        </div>

                                        <p class="whitespace-nowrap text-sm font-extrabold text-[#B83B25] sm:text-base">
                                            Rp<?php echo e(number_format((float) $product->price, 0, ',', '.')); ?>

                                        </p>
                                    </div>

                                    <?php if(!$product->is_available): ?>
                                        <p class="mt-2 text-xs font-semibold text-red-600">
                                            Produk sedang tidak tersedia. Hapus dari keranjang untuk melanjutkan.
                                        </p>
                                    <?php endif; ?>

                                    <div class="mt-4 flex flex-wrap items-center justify-between gap-3">
                                        
                                        <?php if($product->is_available): ?>
                                            <form action="<?php echo e(route('cart.update', $product->id)); ?>"
                                                method="POST"
                                                class="flex items-center gap-2">
                                                <?php echo csrf_field(); ?>
                                                <?php echo method_field('PATCH'); ?>

                                                <span
                                                    class="text-xs font-medium text-gray-500">
                                                    Jumlah
                                                </span>

                                                <button
                                                    type="submit"
                                                    name="quantity"
                                                    value="<?php echo e(max(1, $quantity - 1)); ?>"
                                                    <?php if($quantity <= 1): echo 'disabled'; endif; ?>
                                                    aria-label="Kurangi jumlah <?php echo e($product->name); ?>"
                                                    class="flex h-9 w-9 items-center justify-center rounded-full border border-orange-200 text-lg font-bold text-[#74191B] transition hover:bg-orange-50 disabled:cursor-not-allowed disabled:opacity-40"
                                                >&minus;</button>

                                                <span
                                                    class="min-w-8 text-center text-sm font-extrabold text-[#74191B]"
                                                    aria-live="polite"
                                                ><?php echo e($quantity); ?></span>

                                                <button
                                                    type="submit"
                                                    name="quantity"
                                                    value="<?php echo e(min(99, (int) $product->stock, $quantity + 1)); ?>"
                                                    <?php if($quantity >= min(99, (int) $product->stock)): echo 'disabled'; endif; ?>
                                                    aria-label="Tambah jumlah <?php echo e($product->name); ?>"
                                                    class="flex h-9 w-9 items-center justify-center rounded-full border border-orange-200 text-lg font-bold text-[#74191B] transition hover:bg-orange-50 disabled:cursor-not-allowed disabled:opacity-40"
                                                >&plus;</button>
                                            </form>
                                        <?php else: ?>
                                            <span class="text-xs text-gray-500">
                                                Jumlah: <?php echo e($quantity); ?>

                                            </span>
                                        <?php endif; ?>

                                        
                                        <form action="<?php echo e(route('cart.destroy', $product->id)); ?>"
                                            method="POST"
                                            onsubmit="return confirm('Hapus produk ini dari keranjang?')">
                                            <?php echo csrf_field(); ?>
                                            <?php echo method_field('DELETE'); ?>

                                            <button type="submit"
                                                class="text-xs font-bold text-red-600 underline-offset-4 transition hover:underline">
                                                Hapus
                                            </button>
                                        </form>
                                    </div>

                                    <div class="mt-4 border-t border-orange-50 pt-3 text-right">
                                        <span class="text-xs text-gray-500">Subtotal Produk</span>
                                        <p class="mt-1 font-extrabold text-[#74191B]">
                                            Rp<?php echo e(number_format($subtotal, 0, ',', '.')); ?>

                                        </p>
                                    </div>
                                </div>
                            </div>
                        </article>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                    <a href="<?php echo e(route('products.index')); ?>"
                        class="inline-flex items-center gap-2 py-2 text-sm font-bold text-[#B83B25] transition hover:text-[#74191B]">
                        <span aria-hidden="true">←</span>
                        Lanjut Belanja
                    </a>
                </div>

                
                <aside class="rounded-2xl border border-orange-100 bg-white p-5 shadow-sm sm:p-6 lg:sticky lg:top-24">
                    <h2 class="text-lg font-extrabold text-[#74191B]">Ringkasan Belanja</h2>

                    <div class="mt-5 flex items-center justify-between gap-3 text-sm text-gray-600">
                        <span>Jumlah jenis produk</span>
                        <span class="font-bold text-gray-800"><?php echo e($items->count()); ?> produk</span>
                    </div>

                    <div class="mt-3 flex items-center justify-between gap-3 text-sm text-gray-600">
                        <span>Total unit</span>
                        <span class="font-bold text-gray-800"><?php echo e($items->sum('quantity')); ?> unit</span>
                    </div>

                    <div class="my-5 border-t border-dashed border-orange-200"></div>

                    <div class="flex items-start justify-between gap-3">
                        <span class="text-sm font-bold text-gray-700">Total sementar</span>
                        <span class="text-right text-xl font-black text-[#B83B25]">
                            Rp<?php echo e(number_format($total, 0, ',', '.')); ?>

                        </span>
                    </div>

                    <p class="mt-3 text-xs leading-5 text-gray-500">
                        Total ini belum termasuk ongkos kirim. Biaya pengiriman akan ditentukan pada tahap pemesanan.
                    </p>

                    <a href="<?php echo e(route('checkout.create')); ?>"
                        class="mt-6 flex w-full items-center justify-center rounded-full bg-[#B83B25] px-5 py-3.5 text-sm font-extrabold text-white transition hover:bg-[#74191B] focus:outline-none focus:ring-2 focus:ring-[#B83B25] focus:ring-offset-2">
                        Lanjut ke Checkout
                    </a>

                    <p class="mt-3 text-center text-xs leading-5 text-gray-500">
                        Ongkos kirim dan instruksi pembayaran akan dikonfirmasi admin setelah pesanan diterima.
                    </p>
                </aside>
            </div>
        <?php endif; ?>
    </div>
</section>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\pc1\Downloads\Bakso_Pesona\resources\views/cart/index.blade.php ENDPATH**/ ?>