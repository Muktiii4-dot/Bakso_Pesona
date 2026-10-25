<?php $__env->startSection('title', $product['name'] . ' - Bakso Pesona'); ?>

<?php $__env->startSection('content'); ?>

<section class="bg-pesona-cream py-10">

    <div class="mx-auto max-w-7xl px-5 lg:px-8">

        
        <nav class="mb-8 text-sm">

            <ol class="flex flex-wrap items-center gap-2 text-gray-500">

                <li>
                    <a
                        href="<?php echo e(route('home')); ?>"
                        class="hover:text-pesona-red"
                    >
                        Home
                    </a>
                </li>

                <li>
                    /
                </li>

                <li>
                    <a
                        href="<?php echo e(route('products.index')); ?>"
                        class="hover:text-pesona-red"
                    >
                        Produk
                    </a>
                </li>

                <li>
                    /
                </li>

                <li class="font-semibold text-gray-900">
                    <?php echo e($product['name']); ?>

                </li>

            </ol>

        </nav>


        
        <div class="grid overflow-hidden rounded-3xl bg-white shadow-sm lg:grid-cols-2">


            
            <div class="min-h-[420px] bg-orange-50">

                <?php if($product['image']): ?>

                    <img
                        src="<?php echo e(filter_var($product['image'], FILTER_VALIDATE_URL) ? $product['image'] : asset($product['image'])); ?>"
                        alt="<?php echo e($product['name']); ?>"
                        class="h-full min-h-[420px] w-full object-cover"
                    >

                <?php else: ?>

                    <div
                        class="flex h-full min-h-[420px] items-center justify-center bg-gradient-to-br from-pesona-red to-pesona-orange"
                    >

                        <span class="text-[150px]">
                            🍲
                        </span>

                    </div>

                <?php endif; ?>

            </div>


            
            <div class="flex flex-col justify-center p-7 md:p-10">

                
                <?php if($product['badge']): ?>

                    <div>

                        <span
                            class="inline-flex rounded-full bg-orange-100 px-4 py-2 text-xs font-bold text-pesona-red"
                        >
                            <?php echo e($product['badge']); ?>

                        </span>

                    </div>

                <?php endif; ?>


                
                <p class="mt-5 text-sm font-semibold text-pesona-orange">
                    <?php echo e($product['category']); ?>

                </p>


                
                <h1 class="mt-2 text-3xl font-black text-gray-900 md:text-4xl">
                    <?php echo e($product['name']); ?>

                </h1>


                
                <p class="mt-5 text-3xl font-black text-pesona-red">
                    Rp <?php echo e(number_format($product['price'], 0, ',', '.')); ?>

                </p>


                
                <p class="mt-6 leading-7 text-gray-600">
                    <?php echo e($product['description']); ?>

                </p>


                
                <div class="mt-6 flex items-center gap-2">

                    <span
                        class="h-2.5 w-2.5 rounded-full <?php echo e($product['is_available'] ? 'bg-green-500' : 'bg-red-500'); ?>"
                    ></span>

                    <span class="text-sm font-medium text-gray-600">

                        <?php if($product['is_available']): ?>
                            Tersedia
                        <?php else: ?>
                            Tidak tersedia
                        <?php endif; ?>

                    </span>

                </div>

                
                <form
                    action="<?php echo e(route('cart.store', $product['id'])); ?>"
                    method="POST"
                    class="mt-8"
                >
                    <?php echo csrf_field(); ?>

                    
                    <div>
                        <label
                            for="quantity"
                            class="text-sm font-bold text-gray-900"
                        >
                            Jumlah
                        </label>

                        <div class="mt-3 flex items-center">
                            <button
                                type="button"
                                onclick="const input = this.parentElement.querySelector('input[name=quantity]'); input.value = Math.max(1, Number(input.value || 1) - 1);"
                                class="flex h-11 w-11 items-center justify-center rounded-l-xl border border-gray-200 text-xl font-bold hover:bg-gray-50"
                                aria-label="Kurangi jumlah"
                            >
                                −
                            </button>

                            <input
                                type="number"
                                name="quantity"
                                id="quantity"
                                value="<?php echo e(old('quantity', 1)); ?>"
                                min="1"
                                max="99"
                                required
                                class="h-11 w-16 border-y border-gray-200 text-center text-sm font-bold outline-none"
                            >

                            <button
                                type="button"
                                onclick="const input = this.parentElement.querySelector('input[name=quantity]'); input.value = Math.min(99, Number(input.value || 1) + 1);"
                                class="flex h-11 w-11 items-center justify-center rounded-r-xl border border-gray-200 text-xl font-bold hover:bg-gray-50"
                                aria-label="Tambah jumlah"
                            >
                                +
                            </button>
                        </div>

                        <?php $__errorArgs = ['quantity'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="mt-2 text-sm text-red-600">
                                <?php echo e($message); ?>

                            </p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    
                    <button
                        type="submit"
                        <?php if(!$product['is_available'] ?? false): echo 'disabled'; endif; ?>
                        class="mt-7 flex w-full items-center justify-center gap-3 rounded-xl bg-pesona-orange px-6 py-4 font-bold text-white transition hover:bg-pesona-red disabled:cursor-not-allowed disabled:opacity-50"
                    >
                        <svg
                            class="h-5 w-5"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                            aria-hidden="true"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13 5.4 5M7 13l-2 2h12m-9 4a1 1 0 1 1-2 0m10 0a1 1 0 1 1-2 0"
                            />
                        </svg>

                        <?php echo e($product['is_available'] ?? false ? 'Tambah ke Keranjang' : 'Produk Tidak Tersedia'); ?>

                    </button>
                </form>

                
                <a
                    href="<?php echo e(route('products.index')); ?>"
                    class="mt-4 text-center text-sm font-semibold text-gray-500 hover:text-pesona-red"
                >
                    ← Kembali ke Produk
                </a>

            </div>

        </div>

        <section class="mt-10 rounded-3xl border border-orange-100 bg-white p-6 shadow-sm sm:p-8" aria-labelledby="product-ratings-title">
            <div class="flex flex-wrap items-end justify-between gap-3">
                <div>
                    <p class="text-xs font-bold uppercase tracking-[0.18em] text-pesona-orange">Cerita Pelanggan</p>
                    <h2 id="product-ratings-title" class="mt-1 text-2xl font-black text-pesona-dark">Rating & Ulasan</h2>
                </div>
                <?php if($averageRating !== null): ?>
                    <p class="text-sm font-bold text-amber-700"><?php echo e(number_format($averageRating, 1)); ?> / 5 ★</p>
                <?php endif; ?>
            </div>

            <?php if($ratings->isEmpty()): ?>
                <p class="mt-5 text-sm text-gray-500">Belum ada ulasan yang disetujui untuk produk ini.</p>
            <?php else: ?>
                <div class="mt-5 divide-y divide-orange-100">
                    <?php $__currentLoopData = $ratings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rating): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <article class="py-4 first:pt-0 last:pb-0">
                            <div class="flex flex-wrap items-center justify-between gap-2">
                                <p class="font-bold text-gray-900">Pelanggan Bakso Pesona</p>
                                <p class="text-sm font-bold text-amber-700"><?php echo e($rating->rating); ?> / 5 ★</p>
                            </div>
                            <?php if($rating->original_comment || $rating->edited_comment): ?>
                                <p class="mt-2 whitespace-pre-line text-sm leading-6 text-gray-700"><?php echo e($rating->edited_comment ?? $rating->original_comment); ?></p>
                            <?php endif; ?>
                            <p class="mt-2 text-xs text-gray-400"><?php echo e($rating->created_at->format('d M Y')); ?></p>
                        </article>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
                <div class="mt-5"><?php echo e($ratings->links()); ?></div>
            <?php endif; ?>
        </section>

    </div>

</section>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\pc1\Downloads\Bakso_Pesona\resources\views/products/show.blade.php ENDPATH**/ ?>