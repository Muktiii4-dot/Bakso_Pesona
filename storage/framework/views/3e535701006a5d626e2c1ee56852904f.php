<?php $__env->startSection('title', 'Checkout — Bakso Pesona'); ?>

<?php $__env->startSection('content'); ?>
<section class="min-h-[70vh] bg-[#FFF9F3] px-4 py-10 sm:px-6 sm:py-14 lg:px-8">
    <div class="mx-auto max-w-7xl">
        <div class="mb-8">
            <p class="text-sm font-bold uppercase tracking-[0.2em] text-[#B83B25]">Bakso Pesona</p>
            <h1 class="mt-2 text-3xl font-black tracking-tight text-[#74191B] sm:text-4xl">Checkout</h1>
            <p class="mt-3 text-sm leading-6 text-gray-600 sm:text-base">Lengkapi alamat pengiriman. Ongkir dan total dihitung otomatis berdasarkan provinsi; pembayaran QRIS tetap menunggu instruksi resmi sampai integrasi API merchant dikonfirmasi.</p>
        </div>

        <?php if($errors->any()): ?>
            <div role="alert" class="mb-6 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                <p class="font-bold">Periksa kembali data berikut:</p>
                <ul class="mt-2 list-inside list-disc"><?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><li><?php echo e($error); ?></li><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></ul>
            </div>
        <?php endif; ?>

        <form action="<?php echo e(route('checkout.store')); ?>" method="POST" class="grid items-start gap-6 lg:grid-cols-[1fr_360px]">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="checkout_token" value="<?php echo e($checkoutToken); ?>">
            <div class="rounded-2xl border border-orange-100 bg-white p-5 shadow-sm sm:p-7">
                <h2 class="text-xl font-extrabold text-[#74191B]">Informasi Penerima</h2>
                <div class="mt-6 grid gap-5">
                    <div>
                        <label for="recipient_name" class="mb-2 block text-sm font-bold text-gray-700">Nama penerima <span class="text-red-600">*</span></label>
                        <input id="recipient_name" name="recipient_name" value="<?php echo e(old('recipient_name', auth('customer')->user()->name)); ?>" required maxlength="255" autocomplete="name" class="w-full rounded-xl border border-orange-200 px-4 py-3 text-sm text-gray-800 outline-none transition focus:border-[#B83B25] focus:ring-2 focus:ring-orange-100">
                        <?php $__errorArgs = ['recipient_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    <div>
                        <label for="phone" class="mb-2 block text-sm font-bold text-gray-700">Nomor HP/WhatsApp <span class="text-red-600">*</span></label>
                        <input id="phone" name="phone" type="tel" value="<?php echo e(old('phone')); ?>" required maxlength="30" autocomplete="tel" placeholder="Contoh: 08xxxxxxxxxx" class="w-full rounded-xl border border-orange-200 px-4 py-3 text-sm text-gray-800 outline-none transition focus:border-[#B83B25] focus:ring-2 focus:ring-orange-100">
                        <?php $__errorArgs = ['phone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    <div>
                        <label for="shipping_province" class="mb-2 block text-sm font-bold text-gray-700">Provinsi <span class="text-red-600">*</span></label>
                        <select id="shipping_province" name="shipping_province" required class="w-full rounded-xl border border-orange-200 bg-white px-4 py-3 text-sm text-gray-800 outline-none focus:border-[#B83B25] focus:ring-2 focus:ring-orange-100">
                            <option value="">Pilih provinsi</option>
                            <?php $__currentLoopData = $provinces; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $province): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($province); ?>" <?php if(old('shipping_province') === $province): echo 'selected'; endif; ?>><?php echo e($province); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                        <?php $__errorArgs = ['shipping_province'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    <div class="grid gap-5 sm:grid-cols-2">
                        <div>
                            <label for="shipping_city" class="mb-2 block text-sm font-bold text-gray-700">Kota/kabupaten <span class="text-red-600">*</span></label>
                            <input id="shipping_city" name="shipping_city" value="<?php echo e(old('shipping_city')); ?>" required maxlength="150" autocomplete="address-level2" placeholder="Contoh: Kabupaten Indramayu" class="w-full rounded-xl border border-orange-200 px-4 py-3 text-sm text-gray-800 outline-none focus:border-[#B83B25] focus:ring-2 focus:ring-orange-100">
                            <?php $__errorArgs = ['shipping_city'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        <div>
                            <label for="shipping_postal_code" class="mb-2 block text-sm font-bold text-gray-700">Kode pos <span class="text-red-600">*</span></label>
                            <input id="shipping_postal_code" name="shipping_postal_code" value="<?php echo e(old('shipping_postal_code')); ?>" required inputmode="numeric" pattern="[0-9]{5}" minlength="5" maxlength="5" autocomplete="postal-code" placeholder="5 digit kode pos" class="w-full rounded-xl border border-orange-200 px-4 py-3 text-sm text-gray-800 outline-none focus:border-[#B83B25] focus:ring-2 focus:ring-orange-100">
                            <?php $__errorArgs = ['shipping_postal_code'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                    </div>
                    <div>
                        <label for="shipping_address" class="mb-2 block text-sm font-bold text-gray-700">Alamat lengkap <span class="text-red-600">*</span></label>
                        <textarea id="shipping_address" name="shipping_address" rows="4" required maxlength="2000" autocomplete="street-address" placeholder="Jalan, nomor rumah, RT/RW, desa/kelurahan, kecamatan, kota/kabupaten, provinsi, kode pos" class="w-full rounded-xl border border-orange-200 px-4 py-3 text-sm text-gray-800 outline-none transition focus:border-[#B83B25] focus:ring-2 focus:ring-orange-100"><?php echo e(old('shipping_address')); ?></textarea>
                        <?php $__errorArgs = ['shipping_address'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    <div>
                        <label for="delivery_landmark" class="mb-2 block text-sm font-bold text-gray-700">Patokan lokasi <span class="font-normal text-gray-400">(opsional)</span></label>
                        <textarea id="delivery_landmark" name="delivery_landmark" rows="2" maxlength="1000" placeholder="Contoh: dekat gerbang atau toko tertentu" class="w-full rounded-xl border border-orange-200 px-4 py-3 text-sm text-gray-800 outline-none transition focus:border-[#B83B25] focus:ring-2 focus:ring-orange-100"><?php echo e(old('delivery_landmark')); ?></textarea>
                        <?php $__errorArgs = ['delivery_landmark'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    <div>
                        <label for="customer_note" class="mb-2 block text-sm font-bold text-gray-700">Catatan pesanan <span class="font-normal text-gray-400">(opsional)</span></label>
                        <textarea id="customer_note" name="customer_note" rows="3" maxlength="1000" placeholder="Petunjuk alamat atau catatan lainnya" class="w-full rounded-xl border border-orange-200 px-4 py-3 text-sm text-gray-800 outline-none transition focus:border-[#B83B25] focus:ring-2 focus:ring-orange-100"><?php echo e(old('customer_note')); ?></textarea>
                        <?php $__errorArgs = ['customer_note'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                </div>
            </div>

            <aside class="rounded-2xl border border-orange-100 bg-white p-5 shadow-sm sm:p-6 lg:sticky lg:top-24">
                <h2 class="text-lg font-extrabold text-[#74191B]">Ringkasan Pesanan</h2>
                <div class="mt-5 space-y-4">
                    <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="flex items-start justify-between gap-3 text-sm">
                            <div class="min-w-0">
                                <p class="font-bold text-gray-800"><?php echo e($item['product']->name); ?></p>
                                <p class="mt-1 text-xs text-gray-500"><?php echo e($item['quantity']); ?> × Rp<?php echo e(number_format((float) $item['product']->price, 0, ',', '.')); ?></p>
                            </div>
                            <span class="shrink-0 font-bold text-gray-800">Rp<?php echo e(number_format($item['subtotal'], 0, ',', '.')); ?></span>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
                <div class="my-5 border-t border-dashed border-orange-200"></div>
                <div class="flex items-start justify-between gap-3">
                    <span class="text-sm font-bold text-gray-700">Subtotal produk</span>
                    <span class="text-right text-lg font-black text-[#B83B25]">Rp<?php echo e(number_format($subtotal, 0, ',', '.')); ?></span>
                </div>
                <div class="mt-3 rounded-xl bg-orange-50 p-3 text-xs leading-5 text-gray-700">
                    <div class="flex justify-between gap-3"><span>Ongkos kirim</span><strong id="shipping-preview">Pilih provinsi</strong></div>
                    <p class="mt-2">Pulau Jawa Rp3.000 · Luar Pulau Jawa Rp5.000. Perhitungan final selalu divalidasi ulang oleh server.</p>
                </div>
                <div class="mt-3 flex justify-between gap-3"><span class="text-sm font-bold text-gray-700">Perkiraan total</span><strong id="total-preview" class="text-lg font-black text-[#B83B25]">Pilih provinsi</strong></div>
                <button type="submit" class="mt-6 w-full rounded-full bg-[#B83B25] px-5 py-3.5 text-sm font-extrabold text-white transition hover:bg-[#74191B] focus:outline-none focus:ring-2 focus:ring-[#B83B25] focus:ring-offset-2">Buat Pesanan</button>
                <a href="<?php echo e(route('cart.index')); ?>" class="mt-3 flex w-full justify-center py-2 text-sm font-bold text-[#B83B25] hover:text-[#74191B]">Kembali ke keranjang</a>
            </aside>
        </form>
    </div>
    <script>
        (() => {
            const province = document.getElementById('shipping_province');
            const shippingPreview = document.getElementById('shipping-preview');
            const totalPreview = document.getElementById('total-preview');
            const java = <?php echo json_encode($javaProvinces, 15, 512) ?>;
            const subtotal = <?php echo e((int) $subtotal); ?>;
            const formatRupiah = value => 'Rp' + new Intl.NumberFormat('id-ID').format(value);
            const updatePreview = () => {
                if (!province.value) {
                    shippingPreview.textContent = 'Pilih provinsi';
                    totalPreview.textContent = 'Pilih provinsi';
                    return;
                }
                const shipping = java.includes(province.value) ? <?php echo e((int) $shippingRates['java']); ?> : <?php echo e((int) $shippingRates['outside_java']); ?>;
                shippingPreview.textContent = formatRupiah(shipping);
                totalPreview.textContent = formatRupiah(subtotal + shipping);
            };
            province.addEventListener('change', updatePreview);
            updatePreview();
        })();
    </script>
</section>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\pc1\Downloads\Bakso_Pesona\resources\views/orders/create.blade.php ENDPATH**/ ?>