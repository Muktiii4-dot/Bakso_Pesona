<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Administrator | Bakso Pesona</title>
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
</head>
<body class="min-h-screen bg-gradient-to-br from-[#3B0A0C] via-[#5C1315] to-[#74191B] text-[#3D2420] antialiased flex flex-col justify-between selection:bg-[#E87532] selection:text-white">
    
    <div class="fixed inset-0 pointer-events-none overflow-hidden">
        <div class="absolute -top-32 -left-32 h-96 w-96 rounded-full bg-[#E87532]/10 blur-3xl"></div>
        <div class="absolute -bottom-32 -right-32 h-96 w-96 rounded-full bg-[#FFB45E]/10 blur-3xl"></div>
    </div>

    
    <header class="relative z-10 px-6 py-6 flex items-center justify-between">
        <a href="<?php echo e(route('home')); ?>" class="inline-flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-white/80 hover:text-white transition">
            <span>&larr;</span>
            <span>Kembali ke Website Toko</span>
        </a>

        <div class="inline-flex items-center gap-1.5 rounded-full bg-white/10 backdrop-blur-md px-3 py-1 text-xs font-semibold text-white/90 border border-white/15">
            <span class="h-2 w-2 rounded-full bg-[#E87532] animate-pulse"></span>
            Area Khusus Pengelola
        </div>
    </header>

    
    <main class="relative z-10 flex flex-1 items-center justify-center px-4 py-8">
        <div class="w-full max-w-md rounded-3xl bg-white p-6 shadow-2xl sm:p-10 border border-white/20">
            <div class="text-center">
                
                <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-gradient-to-br from-[#74191B] to-[#B83B25] text-white shadow-md shadow-red-950/20">
                    <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                </div>

                <div class="mt-4">
                    <span class="inline-block rounded-full bg-orange-100 px-3 py-0.5 text-[11px] font-black uppercase tracking-wider text-[#E87532]">
                        Admin Portal
                    </span>
                    <h1 class="mt-2 text-2xl font-black text-[#74191B] sm:text-3xl">
                        Bakso Pesona
                    </h1>
                    <p class="mt-1.5 text-xs text-gray-500 sm:text-sm">
                        Masuk dengan akun administrator untuk mengelola pesanan dan operasional toko.
                    </p>
                </div>
            </div>

            
            <?php if(session('success')): ?>
                <div class="mt-5 rounded-2xl bg-emerald-50 border border-emerald-200 p-3.5 text-xs font-semibold text-emerald-800 flex items-center gap-2">
                    <svg class="h-4 w-4 shrink-0 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    <span><?php echo e(session('success')); ?></span>
                </div>
            <?php endif; ?>

            <?php if(session('error')): ?>
                <div class="mt-5 rounded-2xl bg-red-50 border border-red-200 p-3.5 text-xs font-semibold text-red-800 flex items-center gap-2">
                    <svg class="h-4 w-4 shrink-0 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    <span><?php echo e(session('error')); ?></span>
                </div>
            <?php endif; ?>

            
            <form action="<?php echo e(route('admin.login.store')); ?>" method="POST" class="mt-6 space-y-4">
                <?php echo csrf_field(); ?>

                <div>
                    <label for="email" class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-gray-700">
                        Email Administrator
                    </label>
                    <input
                        id="email"
                        name="email"
                        type="email"
                        value="<?php echo e(old('email')); ?>"
                        required
                        autocomplete="email"
                        autofocus
                        placeholder="admin@baksopesona.com"
                        class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm outline-none transition focus:border-[#E87532] focus:ring-2 focus:ring-orange-100"
                    >
                    <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="mt-1.5 text-xs font-semibold text-red-600"><?php echo e($message); ?></p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label for="password" class="block text-xs font-bold uppercase tracking-wider text-gray-700">
                            Password
                        </label>
                    </div>
                    <input
                        id="password"
                        name="password"
                        type="password"
                        required
                        autocomplete="current-password"
                        placeholder="••••••••"
                        class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm outline-none transition focus:border-[#E87532] focus:ring-2 focus:ring-orange-100"
                    >
                    <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="mt-1.5 text-xs font-semibold text-red-600"><?php echo e($message); ?></p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div class="flex items-center justify-between pt-1">
                    <label class="flex items-center gap-2 text-xs text-gray-600 cursor-pointer select-none">
                        <input
                            type="checkbox"
                            name="remember"
                            value="1"
                            class="rounded border-gray-300 text-[#74191B] focus:ring-[#E87532]"
                        >
                        <span>Ingat sesi masuk</span>
                    </label>
                </div>

                <button
                    type="submit"
                    class="w-full mt-2 rounded-xl bg-gradient-to-r from-[#74191B] to-[#912224] hover:from-[#5C1315] hover:to-[#74191B] px-5 py-3.5 text-sm font-bold text-white transition shadow-md shadow-red-950/20 focus:outline-none focus:ring-2 focus:ring-[#E87532] focus:ring-offset-2 flex items-center justify-center gap-2"
                >
                    <span>Masuk ke Panel Admin</span>
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                    </svg>
                </button>
            </form>

            <div class="mt-8 border-t border-gray-100 pt-5 text-center">
                <p class="text-xs text-gray-500">
                    Bukan administrator?
                    <a href="<?php echo e(route('login')); ?>" class="font-bold text-[#E87532] hover:underline">
                        Masuk sebagai Pelanggan
                    </a>
                </p>
            </div>
        </div>
    </main>

    
    <footer class="relative z-10 px-6 py-4 text-center text-xs text-white/50">
        &copy; <?php echo e(date('Y')); ?> Bakso Pesona. Hak Cipta Dilindungi.
    </footer>
</body>
</html>
<?php /**PATH C:\Users\pc1\Documents\Bakso_Pesona\resources\views/admin/auth/login.blade.php ENDPATH**/ ?>