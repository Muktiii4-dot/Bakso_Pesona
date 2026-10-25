
<?php
    $cartCount = collect(session('bakso_pesona_cart', []))
        ->sum();

    $customer = auth('customer')->user();
    $user = $customer?->role === 'customer' ? $customer : null;
    $initials = '';

    if ($user) {
        $nameParts = preg_split('/\s+/', trim($user->name));

        $initials = collect($nameParts)
            ->filter()
            ->take(2)
            ->map(fn ($part) => mb_strtoupper(mb_substr($part, 0, 1)))
            ->implode('');
    }
?>

<header
    class="sticky top-0 z-50 border-b border-white/10 bg-[#74191B]/95 text-white shadow-sm backdrop-blur-md"
    x-data="{ open: false }"
>
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-10">

        
        <div class="flex h-[84px] items-center justify-between gap-6">

            
            <a
                href="<?php echo e(route('home')); ?>"
                class="flex h-[120px] w-[120px] shrink-0 items-center justify-center"
                aria-label="Bakso Pesona - Beranda"
            >
                <img
                    src="<?php echo e(asset('images/logo_navbar.png')); ?>"
                    alt="Bakso Pesona"
                    class="block h-auto w-full max-h-[100px] object-contain"
                >
            </a>

            
            <nav
                class="relative left-8 hidden shrink-0 items-center gap-7 whitespace-nowrap lg:flex"
                aria-label="Navigasi utama"
            >
                <a
                    href="<?php echo e(route('home')); ?>"
                    class="text-xs font-bold text-white/90 transition hover:text-[#FFB45E]"
                >
                    Home
                </a>

                <a
                    href="<?php echo e(route('home')); ?>#tentang"
                    class="text-xs font-semibold text-white/80 transition hover:text-[#FFB45E]"
                >
                    Tentang
                </a>

                <a
                    href="<?php echo e(route('home')); ?>#produk"
                    class="text-xs font-semibold text-white/80 transition hover:text-[#FFB45E]"
                >
                    Produk
                </a>

                <a
                    href="<?php echo e(route('home')); ?>#kontak"
                    class="text-xs font-semibold text-white/80 transition hover:text-[#FFB45E]"
                >
                    Kontak
                </a>
            </nav>

            
            <form
                action="<?php echo e(route('products.index')); ?>"
                method="GET"
                role="search"
                class="relative left-50 hidden h-10 w-44 shrink-0 items-center gap-2 rounded-full border border-white/30 bg-white px-4 xl:flex"
            >
                <svg
                    class="h-4 w-4 shrink-0 text-[#9A8179]"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                    aria-hidden="true"
                >
                    <circle cx="11" cy="11" r="7" stroke-width="1.8"/>
                    <path d="m16 16 4 4" stroke-width="1.8" stroke-linecap="round"/>
                </svg>

                <input
                    type="search"
                    name="q"
                    value="<?php echo e(request('q')); ?>"
                    placeholder="Cari produk..."
                    class="w-full min-w-0 border-0 bg-transparent text-xs text-[#351B18] outline-none placeholder:text-[#A99993] focus:ring-0"
                >
            </form>

            
            <div class="hidden shrink-0 translate-x-12 items-center gap-3 lg:flex">

                
                <?php if($user): ?>
                    <div class="relative" x-data="{ accountOpen: false }">
                        <button
                            type="button"
                            @click="accountOpen = !accountOpen"
                            @click.outside="accountOpen = false"
                            @keydown.escape.window="accountOpen = false"
                            aria-label="Menu akun pengguna"
                            aria-haspopup="true"
                            :aria-expanded="accountOpen"
                            class="flex h-10 w-10 items-center justify-center rounded-full border border-[#FFB45E]/70 bg-[#FFB45E] font-bold text-[#74191B] transition hover:bg-white focus:outline-none focus:ring-2 focus:ring-[#FFB45E]"
                        >
                            <?php echo e($initials ?: 'U'); ?>

                        </button>

                        <div
                            x-cloak
                            x-show="accountOpen"
                            x-transition.origin.top.right
                            class="absolute right-0 z-50 mt-3 w-64 rounded-2xl border border-gray-100 bg-white p-2 text-gray-800 shadow-xl"
                        >
                            <div class="border-b border-gray-100 px-3 py-3">
                                <p class="truncate font-bold">
                                    <?php echo e($user->name); ?>

                                </p>
                                <p class="truncate text-sm text-gray-500">
                                    <?php echo e($user->email); ?>

                                </p>
                            </div>

                            <a
                                href="<?php echo e(route('home')); ?>"
                                class="flex items-center gap-3 rounded-xl px-3 py-3 text-sm transition hover:bg-orange-50"
                            >
                                <span aria-hidden="true">⌂</span>
                                Beranda
                            </a>

                            <a
                                href="<?php echo e(route('account.profile')); ?>"
                                class="flex items-center gap-3 rounded-xl px-3 py-3 text-sm transition hover:bg-orange-50"
                            >
                                <span aria-hidden="true">♙</span>
                                Profil Saya
                            </a>

                            <a
                                href="<?php echo e(route('orders.index')); ?>"
                                class="flex items-center gap-3 rounded-xl px-3 py-3 text-sm transition hover:bg-orange-50"
                            >
                                <span aria-hidden="true">▤</span>
                                Riwayat Pesanan
                            </a>

                            <form action="<?php echo e(route('logout')); ?>" method="POST">
                                <?php echo csrf_field(); ?>
                                <button
                                    type="submit"
                                    class="flex w-full items-center gap-3 rounded-xl px-3 py-3 text-left text-sm text-red-700 transition hover:bg-red-50"
                                >
                                    <span aria-hidden="true">↪</span>
                                    Logout
                                </button>
                            </form>
                        </div>
                    </div>
                <?php else: ?>
                    <a
                        href="<?php echo e(route('login')); ?>"
                        aria-label="Login akun"
                        title="Login akun"
                        class="flex h-10 w-10 items-center justify-center rounded-full border border-white/25 text-white transition hover:border-[#FFB45E] hover:bg-white/10 hover:text-[#FFB45E]"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            width="21"
                            height="21"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.8"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            aria-hidden="true"
                        >
                            <circle cx="12" cy="8" r="4" />
                            <path d="M5 21v-2a7 7 0 0 1 14 0v2" />
                        </svg>
                    </a>
                <?php endif; ?>

                
                <a
                    href="<?php echo e(route('cart.index')); ?>"
                    aria-label="Keranjang belanja, <?php echo e($cartCount); ?> item"
                    title="Keranjang belanja"
                    class="relative flex h-10 w-10 items-center justify-center rounded-full border border-white/25 text-white transition hover:border-[#FFB45E] hover:bg-white/10 hover:text-[#FFB45E]"
                >
                    <svg
                        class="h-5 w-5"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                        aria-hidden="true"
                    >
                        <path
                            d="M3 4h2l2.2 10.2a2 2 0 0 0 2 1.6h7.9a2 2 0 0 0 1.9-1.4L21 8H6"
                            stroke-width="1.7"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                        />
                        <circle cx="10" cy="20" r="1" fill="currentColor"/>
                        <circle cx="18" cy="20" r="1" fill="currentColor"/>
                    </svg>

                    <?php if($cartCount > 0): ?>
                        <span
                            class="absolute -right-1 -top-1 flex h-[17px] min-w-[17px] items-center justify-center rounded-full bg-[#FFB45E] px-1 text-[10px] font-extrabold text-[#74191B]"
                            aria-label="<?php echo e($cartCount); ?> item di keranjang"
                        ><?php echo e($cartCount > 99 ? '99+' : $cartCount); ?></span>
                    <?php endif; ?>
                </a>
            </div>

            <div class="flex shrink-0 items-center gap-2 lg:hidden">
                
                <a
                    href="<?php echo e(route('cart.index')); ?>"
                    aria-label="Keranjang belanja, <?php echo e($cartCount); ?> item"
                    title="Keranjang belanja"
                    class="relative flex h-10 w-10 items-center justify-center rounded-full border border-white/25 text-white transition hover:border-[#FFB45E] hover:bg-white/10 hover:text-[#FFB45E]"
                >
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path
                            d="M3 4h2l2.2 10.2a2 2 0 0 0 2 1.6h7.9a2 2 0 0 0 1.9-1.4L21 8H6"
                            stroke-width="1.7"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                        />
                        <circle cx="10" cy="20" r="1" fill="currentColor"/>
                        <circle cx="18" cy="20" r="1" fill="currentColor"/>
                    </svg>
                    <?php if($cartCount > 0): ?>
                        <span
                            class="absolute -right-1 -top-1 flex h-[17px] min-w-[17px] items-center justify-center rounded-full bg-[#FFB45E] px-1 text-[10px] font-extrabold text-[#74191B]"
                            aria-label="<?php echo e($cartCount); ?> item di keranjang"
                        ><?php echo e($cartCount > 99 ? '99+' : $cartCount); ?></span>
                    <?php endif; ?>
                </a>

                
                <button
                    type="button"
                    @click="open = !open"
                    class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-xl border border-white/15 text-white transition hover:bg-white/10"
                    :aria-expanded="open.toString()"
                    aria-label="Buka menu navigasi"
                >
                    <svg
                        x-show="!open"
                        class="h-5 w-5"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                        aria-hidden="true"
                    >
                        <path d="M4 6h16M4 12h16M4 18h16" stroke-width="1.8" stroke-linecap="round"/>
                    </svg>

                    <svg
                        x-show="open"
                        x-cloak
                        class="h-5 w-5"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                        aria-hidden="true"
                    >
                        <path d="m6 6 12 12M18 6 6 18" stroke-width="1.8" stroke-linecap="round"/>
                    </svg>
                </button>
            </div>
        </div>

        
        <div
            x-show="open"
            x-transition
            x-cloak
            class="border-t border-white/10 px-5 pt-4 pb-5 lg:hidden"
        >
            
            <form
                action="<?php echo e(route('products.index')); ?>"
                method="GET"
                role="search"
                class="flex h-11 items-center gap-2 rounded-xl bg-white px-3"
            >
                <svg
                    class="h-4 w-4 shrink-0 text-[#9A8179]"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                    aria-hidden="true"
                >
                    <circle cx="11" cy="11" r="7" stroke-width="1.8"/>
                    <path d="m16 16 4 4" stroke-width="1.8" stroke-linecap="round"/>
                </svg>

                <input
                    type="search"
                    name="q"
                    value="<?php echo e(request('q')); ?>"
                    placeholder="Cari produk..."
                    class="w-full min-w-0 border-0 bg-transparent text-sm text-[#351B18] outline-none focus:ring-0"
                >

                <button
                    class="shrink-0 text-xs font-bold text-[#B83B25]"
                    type="submit"
                >
                    Cari
                </button>
            </form>

            
            <nav
                class="mt-3 grid gap-1"
                aria-label="Navigasi mobile"
            >
                <a
                    href="<?php echo e(route('home')); ?>"
                    @click="open = false"
                    class="rounded-xl px-3 py-3 text-sm font-semibold text-white/90 transition hover:bg-white/10"
                >
                    Home
                </a>

                <a
                    href="<?php echo e(route('home')); ?>#produk"
                    @click="open = false"
                    class="rounded-xl px-3 py-3 text-sm font-semibold text-white/90 transition hover:bg-white/10"
                >
                    Produk
                </a>

                <a
                    href="<?php echo e(route('home')); ?>#tentang"
                    @click="open = false"
                    class="rounded-xl px-3 py-3 text-sm font-semibold text-white/90 transition hover:bg-white/10"
                >
                    Tentang
                </a>

                <a
                    href="<?php echo e(route('home')); ?>#kontak"
                    @click="open = false"
                    class="rounded-xl px-3 py-3 text-sm font-semibold text-white/90 transition hover:bg-white/10"
                >
                    Kontak
                </a>

            </nav>

            
            <a
                href="<?php echo e(route('cart.index')); ?>"
                @click="open = false"
                class="mt-3 flex items-center justify-between rounded-xl border border-white/15 px-4 py-3 text-sm font-bold text-white transition hover:bg-white/10"
            >
                <span class="flex items-center gap-3">
                    <svg
                        class="h-5 w-5"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                        aria-hidden="true"
                    >
                        <path
                            d="M3 4h2l2.2 10.2a2 2 0 0 0 2 1.6h7.9a2 2 0 0 0 1.9-1.4L21 8H6"
                            stroke-width="1.7"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                        />
                        <circle cx="10" cy="20" r="1" fill="currentColor"/>
                        <circle cx="18" cy="20" r="1" fill="currentColor"/>
                    </svg>

                    Keranjang Belanja
                </span>

                <?php if($cartCount > 0): ?>
                    <span
                        class="flex h-6 min-w-6 items-center justify-center rounded-full bg-[#FFB45E] px-2 text-xs font-extrabold text-[#74191B]"
                        aria-label="<?php echo e($cartCount); ?> item di keranjang"
                    ><?php echo e($cartCount > 99 ? '99+' : $cartCount); ?></span>
                <?php endif; ?>
            </a>

            
            <a
                href="<?php echo e(route('products.index')); ?>"
                @click="open = false"
                class="mt-3 flex w-full items-center justify-center gap-2 rounded-xl bg-[#E87532] px-4 py-3 text-sm font-extrabold text-white transition hover:bg-[#F28A42]"
            >
                Lihat Menu
                <span aria-hidden="true">→</span>
            </a>
        </div>
    </div>

</header>
<?php /**PATH C:\Users\pc1\Downloads\Bakso_Pesona\resources\views/components/navbar.blade.php ENDPATH**/ ?>