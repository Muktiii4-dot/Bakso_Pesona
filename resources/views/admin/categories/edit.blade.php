@extends('layouts.admin')

@section('title', 'Edit Kategori')
@section('header_title', 'Edit Kategori Produk')

@section('content')
<div class="mx-auto max-w-2xl space-y-6">
    <div class="flex items-center gap-3">
        <a href="{{ route('admin.categories.index') }}"
           class="inline-flex items-center gap-1.5 rounded-lg border border-gray-200 bg-white px-3 py-1.5 text-xs font-semibold text-gray-600 transition hover:bg-gray-50">
            &larr; Kembali ke Kategori
        </a>
        <h1 class="text-xl font-black text-pesona-dark">Edit Kategori</h1>
    </div>

    @if ($errors->any())
        <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
            <ul class="list-disc list-inside space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="rounded-2xl border border-orange-100 bg-white p-6 shadow-sm">
        <form action="{{ route('admin.categories.update', $category) }}" method="POST" class="space-y-5">
            @csrf
            @method('PUT')

            <div>
                <label for="name" class="mb-1.5 block text-sm font-semibold text-gray-700">
                    Nama Kategori <span class="text-red-500">*</span>
                </label>
                <input
                    type="text"
                    id="name"
                    name="name"
                    value="{{ old('name', $category->name) }}"
                    placeholder="Nama kategori..."
                    class="w-full rounded-xl border @error('name') border-red-400 bg-red-50 @else border-gray-200 bg-gray-50 @enderror px-4 py-2.5 text-sm text-gray-800 placeholder-gray-400 transition focus:border-pesona-primary focus:bg-white focus:outline-none focus:ring-2 focus:ring-pesona-primary/20"
                />
                @error('name')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-xs text-gray-400">
                    Slug saat ini: <code class="rounded bg-gray-100 px-1 py-0.5">{{ $category->slug }}</code>
                    (akan diperbarui otomatis jika nama berubah)
                </p>
            </div>

            <div>
                <label for="description" class="mb-1.5 block text-sm font-semibold text-gray-700">
                    Deskripsi <span class="text-gray-400 font-normal">(opsional)</span>
                </label>
                <textarea
                    id="description"
                    name="description"
                    rows="4"
                    placeholder="Deskripsi singkat kategori..."
                    class="w-full resize-none rounded-xl border border-gray-200 bg-gray-50 px-4 py-2.5 text-sm text-gray-800 placeholder-gray-400 transition focus:border-pesona-primary focus:bg-white focus:outline-none focus:ring-2 focus:ring-pesona-primary/20"
                >{{ old('description', $category->description) }}</textarea>
            </div>

            <div class="flex gap-3 pt-2">
                <a href="{{ route('admin.categories.index') }}"
                   class="flex-1 rounded-xl border border-gray-200 bg-white px-4 py-2.5 text-center text-sm font-semibold text-gray-600 transition hover:bg-gray-50">
                    Batal
                </a>
                <button type="submit"
                        class="flex-1 rounded-xl bg-gradient-to-r from-[#74191B] to-[#912224] px-4 py-2.5 text-sm font-bold text-white shadow-md transition hover:from-[#5C1315] hover:to-[#74191B]">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>

    @if ($category->products_count ?? $category->products()->count() > 0)
        <div class="rounded-xl border border-blue-100 bg-blue-50 px-4 py-3 text-xs text-blue-700">
            <strong>Info:</strong> Kategori ini memiliki {{ $category->products()->count() }} produk terkait.
            Mengubah nama kategori tidak akan mempengaruhi produk di dalamnya.
        </div>
    @endif
</div>
@endsection