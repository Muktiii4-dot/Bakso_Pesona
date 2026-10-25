<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'name',
    'description',
    'price',
    'image' => null,
    'badge' => null,
    'slug' => null,
]));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter(([
    'name',
    'description',
    'price',
    'image' => null,
    'badge' => null,
    'slug' => null,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<article class="group flex h-full min-w-0 flex-col overflow-hidden rounded-2xl border border-[#F0DED4] bg-white shadow-[0_4px_16px_rgba(83,37,24,0.045)] transition duration-300 hover:-translate-y-1 hover:border-[#E9B89E] hover:shadow-[0_12px_28px_rgba(83,37,24,0.11)]">

    
    <a
        href="<?php echo e($slug ? route('products.show', $slug) : route('products.index')); ?>"
        class="relative block aspect-[1.18/1] overflow-hidden bg-[#F6E6DC]"
        aria-label="Lihat <?php echo e($name); ?>"
    >
        <?php if($image): ?>
            <img
                src="<?php echo e(filter_var($image, FILTER_VALIDATE_URL) ? $image : asset($image)); ?>"
                alt="<?php echo e($name); ?>"
                class="h-full w-full object-cover transition duration-500 group-hover:scale-105"
                loading="lazy"
                onerror="this.style.display='none'; this.nextElementSibling?.classList.remove('hidden'); this.nextElementSibling?.classList.add('flex')"
            >

            <div class="hidden absolute inset-0 items-center justify-center bg-gradient-to-br from-[#B83B25] to-[#E87532] text-5xl" aria-hidden="true">
                🍜
            </div>
        <?php else: ?>
            <div class="flex h-full w-full items-center justify-center bg-gradient-to-br from-[#B83B25] to-[#E87532] text-5xl" aria-hidden="true">
                🍜
            </div>
        <?php endif; ?>

        <?php if($badge): ?>
            <span class="absolute left-2.5 top-2.5 rounded-full bg-[#E87532] px-2.5 py-1 text-[9px] font-extrabold tracking-wide text-white shadow-sm sm:left-3 sm:top-3 sm:text-[10px]">
                <?php echo e($badge); ?>

            </span>
        <?php endif; ?>
    </a>

    
    <div class="flex flex-1 flex-col p-3 sm:p-4">
        <h3 class="text-xs font-extrabold leading-5 text-[#351B18] transition group-hover:text-[#B83B25] sm:text-sm">
            <a href="<?php echo e($slug ? route('products.show', $slug) : route('products.index')); ?>">
                <?php echo e($name); ?>

            </a>
        </h3>

        <p class="mt-1.5 line-clamp-2 text-[10px] leading-4 text-[#8B7770] sm:text-xs sm:leading-5">
            <?php echo e($description); ?>

        </p>

        
        <div class="mt-auto flex items-end justify-between gap-2 pt-4">
            <div class="min-w-0">
                <span class="block text-[9px] font-medium text-[#A18E86] sm:text-[10px]">
                    Harga
                </span>

                <p class="mt-0.5 text-xs font-black text-[#B83B25] sm:text-sm">
                    Rp <?php echo e(number_format((float) $price, 0, ',', '.')); ?>

                </p>
            </div>

            <a
                href="<?php echo e($slug ? route('products.show', $slug) : route('products.index')); ?>"
                class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-[#E87532] text-lg font-medium leading-none text-white transition hover:bg-[#7F1D1D] focus:outline-none focus:ring-2 focus:ring-[#E87532] focus:ring-offset-2 sm:h-8 sm:w-8"
                aria-label="Lihat <?php echo e($name); ?>"
            >
                +
            </a>
        </div>
    </div>
</article><?php /**PATH C:\Users\pc1\Documents\Bakso_Pesona\resources\views/components/product-card.blade.php ENDPATH**/ ?>