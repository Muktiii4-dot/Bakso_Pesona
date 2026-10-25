@extends('layouts.admin')

@section('title', 'Manajemen Kategori')
@section('header_title', 'Kelola Kategori Produk')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-2xl font-black text-pesona-dark sm:text-3xl">Manajemen Kategori</h1>
        <p class="mt-1 text-xs text-gray-500">Kelola kategori produk untuk mengorganisir menu Bakso Pesona.</p>
    </div>

    @if (session('success'))
        <div class="flex items-start gap-3 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
            {{ session('success') }}
        </div>
    @endif
    @if (session('error'))
        <div class="flex items-start gap-3 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
            {{ session('error') }}
        </div>
    @endif
    @if (session('warning'))
        <div class="flex items-start gap-3 rounded-xl border border-yellow-200 bg-yellow-50 px-4 py-3 text-sm text-yellow-800">
            {{ session('warning') }}
        </div>
    @endif

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="lg:col-span-1">
            <div class="rounded-2xl border border-orange-100 bg-white p-5 shadow-sm">
                <h2 class="mb-4 text-sm font-bold text-pesona-dark">+ Tambah Kategori Baru</h2>
                <form action="{{ route('admin.categories.store') }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label for="name" class="mb-1 block text-xs font-semibold text-gray-600">
                            Nama Kategori <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="text"
                            id="name"
                            name="name"
                            value="{{ old('name') }}"
                            placeholder="cth: Bakso Spesial, Minuman..."
                            class="w-full rounded-lg border @error('name') border-red-400 bg-red-50 @else border-gray-200 bg-gray-50 @enderror px-3 py-2 text-sm text-gray-800 placeholder-gray-400 transition focus:border-pesona-primary focus:bg-white focus:outline-none focus:ring-2 focus:ring-pesona-primary/20"
                        />
                        @error('name')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
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
                        >{{ old('description') }}</textarea>
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
                    <h2 class="text-sm font-bold text-pesona-dark">Daftar Kategori ({{ $categories->count() }})</h2>
                </div>
                @if ($categories->isEmpty())
                    <div class="flex flex-col items-center justify-center gap-2 py-16 text-gray-400">
                        <p class="text-sm font-medium">Belum ada kategori</p>
                        <p class="text-xs">Tambahkan kategori pertama melalui form di sebelah kiri.</p>
                    </div>
                @else
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
                                @foreach ($categories as $category)
                                    <tr class="hover:bg-orange-50/30 transition-colors">
                                        <td class="px-5 py-3.5">
                                            <span class="font-semibold text-pesona-dark">{{ $category->name }}</span>
                                        </td>
                                        <td class="px-5 py-3.5">
                                            <code class="rounded bg-gray-100 px-1.5 py-0.5 text-xs text-gray-500">{{ $category->slug }}</code>
                                        </td>
                                        <td class="px-5 py-3.5 max-w-xs">
                                            <span class="line-clamp-2 text-xs text-gray-500">{{ $category->description ?? '-' }}</span>
                                        </td>
                                        <td class="px-5 py-3.5 text-center">
                                            @if ($category->products_count > 0)
                                                <span class="inline-flex items-center justify-center rounded-full bg-orange-100 px-2.5 py-0.5 text-xs font-bold text-orange-700">
                                                    {{ $category->products_count }}
                                                </span>
                                            @else
                                                <span class="text-xs text-gray-400">0</span>
                                            @endif
                                        </td>
                                        <td class="px-5 py-3.5">
                                            <div class="flex items-center justify-center gap-2">
                                                <a href="{{ route('admin.categories.edit', $category) }}"
                                                   class="inline-flex items-center gap-1 rounded-lg bg-blue-50 px-3 py-1.5 text-xs font-semibold text-blue-600 transition hover:bg-blue-100">
                                                    Edit
                                                </a>
                                                @if ($category->products_count === 0)
                                                    <form action="{{ route('admin.categories.destroy', $category) }}"
                                                          method="POST"
                                                          onsubmit="return confirm('Hapus kategori \'{{ $category->name }}\'? Tindakan ini tidak dapat dibatalkan.')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit"
                                                                class="inline-flex items-center gap-1 rounded-lg bg-red-50 px-3 py-1.5 text-xs font-semibold text-red-600 transition hover:bg-red-100">
                                                            Hapus
                                                        </button>
                                                    </form>
                                                @else
                                                    <span title="Tidak dapat dihapus karena masih memiliki {{ $category->products_count }} produk"
                                                          class="inline-flex items-center gap-1 rounded-lg bg-gray-100 px-3 py-1.5 text-xs font-semibold text-gray-400 cursor-not-allowed">
                                                        Hapus
                                                    </span>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection