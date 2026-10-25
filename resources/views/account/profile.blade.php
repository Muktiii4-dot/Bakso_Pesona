
@extends('layouts.app')

@section('title', 'Profil Saya - Bakso Pesona')

@section('content')
<section class="bg-pesona-cream py-12">
    <div class="mx-auto max-w-4xl px-5 lg:px-8">
        <div class="rounded-3xl border border-orange-100 bg-white p-6 shadow-sm sm:p-8">
            <span class="text-sm font-bold text-pesona-orange">
                Akun Pelanggan
            </span>

            <h1 class="mt-2 text-3xl font-black text-gray-900">
                Profil Saya
            </h1>

            <p class="mt-2 text-sm leading-6 text-gray-500">
                Kelola informasi dasar akun Bakso Pesona kamu.
            </p>

            @if (session('success'))
                <div class="mt-6 rounded-xl border border-green-200 bg-green-50 p-4 text-sm text-green-800">
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mt-6 rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700">
                    <p class="font-bold">Periksa kembali data berikut:</p>
                    <ul class="mt-2 list-inside list-disc">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('account.profile.update') }}"
                  method="POST"
                  class="mt-8 space-y-5">
                @csrf
                @method('PUT')

                <div>
                    <label for="name" class="mb-2 block text-sm font-bold text-gray-700">
                        Nama lengkap
                    </label>
                    <input
                        id="name"
                        name="name"
                        type="text"
                        value="{{ old('name', $user->name) }}"
                        required
                        maxlength="255"
                        autocomplete="name"
                        class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm outline-none transition focus:border-pesona-orange focus:ring-2 focus:ring-pesona-orange/20"
                    >
                </div>

                <div>
                    <label for="email" class="mb-2 block text-sm font-bold text-gray-700">
                        Email
                    </label>
                    <input
                        id="email"
                        name="email"
                        type="email"
                        value="{{ old('email', $user->email) }}"
                        required
                        maxlength="255"
                        autocomplete="email"
                        class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm outline-none transition focus:border-pesona-orange focus:ring-2 focus:ring-pesona-orange/20"
                    >
                </div>

                <div class="flex flex-wrap gap-3 pt-2">
                    <button
                        type="submit"
                        class="rounded-xl bg-pesona-orange px-6 py-3 text-sm font-bold text-white transition hover:bg-pesona-red"
                    >
                        Simpan Perubahan
                    </button>

                    <a
                        href="{{ route('orders.index') }}"
                        class="rounded-xl border border-gray-200 px-6 py-3 text-sm font-bold text-gray-700 transition hover:bg-gray-50"
                    >
                        Riwayat Pesanan
                    </a>
                </div>
            </form>
        </div>
    </div>
</section>
@endsection
