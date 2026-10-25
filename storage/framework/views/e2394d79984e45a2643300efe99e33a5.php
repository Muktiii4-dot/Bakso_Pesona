<?php $__env->startSection('title', 'Pesanan Berhasil — Bakso Pesona'); ?>

<?php $__env->startSection('content'); ?>
<section class="min-h-[70vh] bg-[#FFF9F3] px-4 py-12 sm:px-6 sm:py-16 lg:px-8">
    <div class="mx-auto max-w-3xl rounded-3xl border border-orange-100 bg-white p-6 shadow-sm sm:p-10">
        <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-green-50 text-green-700">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="m5 12 4 4L19 6" /></svg>
        </div>
        <p class="mt-5 text-center text-sm font-bold uppercase tracking-[0.2em] text-[#B83B25]">Bakso Pesona</p>
        <h1 class="mt-2 text-center text-3xl font-black text-[#74191B]">Pesanan berhasil dibuat!</h1>
        <p class="mt-3 text-center text-sm leading-6 text-gray-600">Simpan nomor pesanan berikut. Ongkir dan total dihitung otomatis. Kamu dapat menampilkan QRIS statis di bawah ini dan memasukkan nominal pembayaran secara manual. Pembayaran tetap perlu diverifikasi admin.</p>

        <div class="mt-8 rounded-2xl bg-orange-50 p-5 text-center">
            <p class="text-sm text-gray-600">Nomor pesanan</p>
            <p class="mt-1 break-all text-xl font-black text-[#74191B]"><?php echo e($order->order_number); ?></p>
            <p class="mt-3 text-sm font-bold text-[#B83B25]">Menunggu instruksi pembayaran</p>
        </div>

        <div class="mt-6 space-y-3">
            <?php $__currentLoopData = $order->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="flex justify-between gap-4 text-sm">
                    <span class="text-gray-600"><?php echo e($item->product_name); ?> × <?php echo e($item->quantity); ?></span>
                    <span class="shrink-0 font-bold text-gray-800">Rp<?php echo e(number_format(\App\Models\Order::rupiahToInt($item->subtotal), 0, ',', '.')); ?></span>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        <div class="my-5 border-t border-dashed border-orange-200"></div>
        <div class="flex justify-between gap-4">
            <span class="font-bold text-gray-700">Subtotal produk</span>
            <span class="text-lg font-black text-[#B83B25]">Rp<?php echo e(number_format(\App\Models\Order::rupiahToInt($order->subtotal), 0, ',', '.')); ?></span>
        </div>
        <div class="mt-4 space-y-3 rounded-xl bg-orange-50 p-4">
            <div class="flex justify-between gap-4 text-sm"><span class="text-gray-600">Provinsi tujuan</span><strong class="text-right"><?php echo e($order->shipping_province); ?></strong></div>
            <div class="flex justify-between gap-4 text-sm"><span class="text-gray-600">Kota/kabupaten</span><strong class="text-right"><?php echo e($order->shipping_city); ?></strong></div>
            <?php if($order->shipping_cost !== null && $order->total_amount !== null): ?>
                <div class="flex justify-between gap-4 text-sm"><span class="text-gray-600">Ongkos kirim</span><strong>Rp<?php echo e(number_format(\App\Models\Order::rupiahToInt($order->shipping_cost), 0, ',', '.')); ?></strong></div>
                <div class="flex justify-between gap-4 border-t border-orange-200 pt-3"><span class="font-bold text-gray-700">Total pembayaran</span><strong class="text-lg font-black text-[#B83B25]">Rp<?php echo e(number_format(\App\Models\Order::rupiahToInt($order->total_amount), 0, ',', '.')); ?></strong></div>
            <?php else: ?>
                <p class="text-sm text-amber-800">Pesanan lama ini belum memiliki total final. Admin akan meninjau ongkirnya sesuai alur sebelumnya.</p>
            <?php endif; ?>
        </div>
        <p class="mt-3 text-xs leading-5 text-gray-500">Jangan menganggap pesanan telah dibayar sebelum pembayaran diverifikasi. QRIS ini bersifat statis, bukan QR dinamis dengan verifikasi otomatis.</p>

        <?php
            $qrisImage = config('bakso_pesona.qris.image');
            $qrisMerchant = config('bakso_pesona.qris.merchant_name');
            $qrisAvailable = is_string($qrisImage)
                && $qrisImage !== ''
                && ! str_contains($qrisImage, '..')
                && is_file(public_path($qrisImage))
                && is_string($qrisMerchant)
                && $qrisMerchant !== '';
        ?>

        <?php if($order->payment_method === 'qris' && $order->total_amount !== null && $qrisAvailable): ?>
            <div class="mt-6 rounded-2xl border border-orange-200 bg-orange-50 p-5 text-center" x-data="{}">
                <p class="text-sm font-bold text-[#74191B]">Pembayaran QRIS</p>
                <p class="mt-1 text-sm text-gray-600"><?php echo e($qrisMerchant); ?></p>
                <button
                    type="button"
                    @click="$refs.qrisSuccessDialog.showModal()"
                    class="mt-4 inline-flex w-full items-center justify-center rounded-full bg-[#B83B25] px-5 py-3 text-sm font-bold text-white transition hover:bg-[#74191B] focus:outline-none focus:ring-2 focus:ring-[#B83B25] focus:ring-offset-2 sm:w-auto"
                >
                    Tampilkan QRIS untuk membayar
                </button>
                <p class="mt-3 text-xs leading-5 text-gray-500">Masukkan nominal persis sesuai total pembayaran. Status pesanan tidak otomatis berubah setelah memindai QRIS.</p>

                <dialog
                    x-ref="qrisSuccessDialog"
                    @click.self="$refs.qrisSuccessDialog.close()"
                    aria-labelledby="qris-success-dialog-title"
                    class="m-auto max-h-[90vh] w-[calc(100%-2rem)] max-w-lg overflow-y-auto rounded-2xl bg-white p-5 shadow-2xl backdrop:bg-black/70 sm:p-7"
                >
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <h2 id="qris-success-dialog-title" class="text-lg font-black text-[#74191B]">Bayar dengan QRIS Bakso Pesona</h2>
                            <p class="mt-1 text-sm text-gray-600">Pastikan nama merchant yang muncul adalah <?php echo e($qrisMerchant); ?>.</p>
                        </div>
                        <button type="button" @click="$refs.qrisSuccessDialog.close()" aria-label="Tutup QRIS" class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-full text-2xl text-gray-500 hover:bg-gray-100">&times;</button>
                    </div>
                    <img src="<?php echo e(asset($qrisImage)); ?>" alt="Kode QRIS resmi <?php echo e($qrisMerchant); ?>" class="mx-auto mt-5 max-h-[55vh] w-auto max-w-full rounded-lg object-contain">
                    <div class="mt-5 rounded-xl bg-orange-50 p-4 text-center">
                        <p class="text-xs font-semibold text-gray-600">Nominal yang harus dibayar</p>
                        <p class="mt-1 text-2xl font-black text-[#B83B25]">Rp<?php echo e(number_format(\App\Models\Order::rupiahToInt($order->total_amount), 0, ',', '.')); ?></p>
                        <p class="mt-1 text-xs text-gray-500">Nomor pesanan: <?php echo e($order->order_number); ?></p>
                    </div>
                    <ol class="mt-4 list-decimal space-y-2 pl-5 text-left text-sm leading-5 text-gray-600">
                        <li>Buka aplikasi pembayaran yang mendukung QRIS dan pindai kode di atas.</li>
                        <li>Masukkan nominal persis seperti yang tertera, lalu periksa nama merchant sebelum membayar.</li>
                        <li>Simpan bukti transaksi dan ikuti instruksi konfirmasi dari admin.</li>
                    </ol>
                    <p class="mt-4 rounded-xl border border-amber-200 bg-amber-50 p-3 text-xs leading-5 text-amber-900">QRIS ini statis. Sistem belum memverifikasi pembayaran secara otomatis; pesanan baru dianggap lunas setelah admin mencocokkan transaksi.</p>
                    <button type="button" @click="$refs.qrisSuccessDialog.close()" class="mt-5 w-full rounded-full border border-orange-200 px-5 py-3 text-sm font-bold text-[#74191B] hover:bg-orange-50">Tutup</button>
                </dialog>
            </div>
        <?php elseif($order->payment_method === 'qris' && $order->total_amount !== null): ?>
            <p class="mt-5 rounded-xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-900">Gambar QRIS belum tersedia. Silakan hubungi admin melalui kontak resmi sebelum melakukan pembayaran.</p>
        <?php endif; ?>

        <div class="mt-8 flex flex-col gap-3 sm:flex-row">
            <a href="<?php echo e(route('products.index')); ?>" class="inline-flex flex-1 justify-center rounded-full bg-[#B83B25] px-5 py-3 text-sm font-bold text-white transition hover:bg-[#74191B]">Lanjut belanja</a>
            <a href="<?php echo e(route('home')); ?>" class="inline-flex flex-1 justify-center rounded-full border border-orange-200 px-5 py-3 text-sm font-bold text-[#74191B] transition hover:bg-orange-50">Kembali ke beranda</a>
        </div>
    </div>
</section>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\pc1\Downloads\Bakso_Pesona\resources\views/orders/success.blade.php ENDPATH**/ ?>