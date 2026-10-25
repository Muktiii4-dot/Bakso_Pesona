<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Bakso Pesona</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-[#FFF8F0] text-[#3D2420]">
    <main class="flex min-h-screen items-center justify-center px-4 py-10">
        <div class="w-full max-w-md rounded-3xl bg-white p-6 shadow-xl sm:p-9">
            <a href="{{ route('home') }}"
                class="text-sm font-semibold text-[#8A2929] hover:underline">
                &larr; Kembali ke Beranda
            </a>

            <div class="mt-7 text-center ">
                <p class="text-sm font-bold uppercase tracking-[0.2em] text-[#E87532]">
                    Bakso Pesona
                </p>
                <h1 class="mt-2 text-3xl font-extrabold">
                    Selamat Datang
                </h1>
                <p class="mt-2 text-sm text-gray-500">
                    Login untuk melanjutkan perjalanan kulinermu.
                </p>
            </div>

            @if (session('success'))
                <div class="mt-5 rounded-xl bg-green-50 py-3 text-sm text-green-700">
                    {{ session('success') }}
                </div>
            @endif

            <form action="{{ route('login.store') }}" method="POST" class="mt-7 space-y-5">
                @csrf

                <div>
                    <label for="email" class="mb-2 block text-sm font-semibold">
                        Email
                    </label>
                    <input id="email" name="email" type="email"
                        value="{{ old('email') }}" required autocomplete="email"
                        autofocus
                        class="w-full rounded-xl border border-gray-200 px-4 py-3 outline-none transition focus:border-[#E87532] focus:ring-2 focus:ring-orange-100">
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password" class="mb-2 block text-sm font-semibold">
                        Password
                    </label>
                    <input id="password" name="password" type="password"
                        required autocomplete="current-password"
                        autofocus
                        class="w-full rounded-xl border border-gray-200 px-4 py-3 outline-none transition focus:border-[#E87532] focus:ring-2 focus:ring-orange-100">
                    @error('password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <label class="flex items-center gap-2 text-sm text-gray-600">
                    <input type="checkbox" name="remember" value="1"
                        class="rounded border-gray-300 text-[#8A2929] focus:ring-[#E87532]">
                    Ingat Saya
                </label>

                <button type="submit"
                    class="w-full rounded-xl bg-[#8A2929] px-5 py-3 font-bold text-white transition hover:bg-[#6F1E20] focus:outline-none focus:ring-2 focus:ring-[#E87532] focus:ring-offset-2">
                    Login
                </button>
            </form>

            <p class="mt-6 text-center text-sm text-gray-600">
                Belum punya akun?
                <a href="{{ route('register') }}"
                    class="font-bold text-[#8A2929] hover:underline">
                    Daftar Sekarang
                </a>
            </p>
        </div>
    </main>
</body>
</html>