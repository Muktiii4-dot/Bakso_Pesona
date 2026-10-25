@extends('layouts.admin')

@section('title', 'Tambah Produk Baru')
@section('header_title', 'Tambah Produk Baru')

@section('content')
<div class="mx-auto max-w-3xl space-y-6">
    <div>
        <a href="{{ route('admin.products.index') }}" class="inline-flex items-center gap-1.5 text-xs font-bold uppercase tracking-wider text-pesona-orange hover:underline">
            &larr; Kembali ke Daftar Produk
        </a>
        <h1 class="mt-2 text-2xl font-black text-pesona-dark">Tambah Produk / Menu Baru</h1>
        <p class="text-xs text-gray-500">Masukkan rincian informasi makanan atau minuman yang akan ditampilkan pada katalog pelanggan.</p>
    </div>

    <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data" class="rounded-3xl border border-orange-100 bg-white p-6 shadow-sm sm:p-8 space-y-5">
        @csrf

        {{-- NAMA PRODUK --}}
        <div>
            <label for="name" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1.5">
                Nama Produk <span class="text-red-500">*</span>
            </label>
            <input
                type="text"
                id="name"
                name="name"
                value="{{ old('name') }}"
                required
                placeholder="Contoh: Bakso Urat Spesial"
                class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm outline-none focus:border-[#E87532] focus:ring-1 focus:ring-orange-200"
            >
            @error('name')
                <p class="mt-1 text-xs text-red-600 font-semibold">{{ $message }}</p>
            @enderror
        </div>

        <div class="grid gap-5 sm:grid-cols-2">
            {{-- KATEGORI --}}
            <div>
                <label for="category_id" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1.5">
                    Kategori <span class="text-red-500">*</span>
                </label>
                <select
                    id="category_id"
                    name="category_id"
                    required
                    class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm outline-none focus:border-[#E87532] focus:ring-1 focus:ring-orange-200"
                >
                    <option value="">Pilih Kategori...</option>
                    @foreach ($categories as $cat)
                        <option value="{{ $cat->id }}" @selected(old('category_id') == $cat->id)>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
                @error('category_id')
                    <p class="mt-1 text-xs text-red-600 font-semibold">{{ $message }}</p>
                @enderror
            </div>

            {{-- HARGA --}}
            <div>
                <label for="price" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1.5">
                    Harga Satuan (Rp) <span class="text-red-500">*</span>
                </label>
                <input
                    type="number"
                    id="price"
                    name="price"
                    value="{{ old('price') }}"
                    required
                    min="0"
                    step="1"
                    placeholder="Contoh: 25000"
                    class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm outline-none focus:border-[#E87532] focus:ring-1 focus:ring-orange-200"
                >
                @error('price')
                    <p class="mt-1 text-xs text-red-600 font-semibold">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="grid gap-5 sm:grid-cols-2">
            {{-- STOK --}}
            <div>
                <label for="stock" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1.5">
                    Jumlah Stok Porsi <span class="text-red-500">*</span>
                </label>
                <input
                    type="number"
                    id="stock"
                    name="stock"
                    value="{{ old('stock', 50) }}"
                    required
                    min="0"
                    placeholder="50"
                    class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm outline-none focus:border-[#E87532] focus:ring-1 focus:ring-orange-200"
                >
                <p class="mt-1 text-[11px] text-gray-400">Stok awal dicatat dalam riwayat mutasi. Stok ditahan saat checkout berhasil.</p>
                @error('stock')
                    <p class="mt-1 text-xs text-red-600 font-semibold">{{ $message }}</p>
                @enderror
            </div>

            {{-- BADGE PROMO / HIGHLIGHT --}}
            <div>
                <label for="badge" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1.5">
                    Label Khusus (Badge)
                </label>
                <input
                    type="text"
                    id="badge"
                    name="badge"
                    value="{{ old('badge') }}"
                    placeholder="Misal: Best Seller / Pedas / Favorit"
                    class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm outline-none focus:border-[#E87532] focus:ring-1 focus:ring-orange-200"
                >
                @error('badge')
                    <p class="mt-1 text-xs text-red-600 font-semibold">{{ $message }}</p>
                @enderror
            </div>
        </div>

        {{-- DESKRIPSI --}}
        <div>
            <label for="description" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1.5">
                Deskripsi Produk
            </label>
            <textarea
                id="description"
                name="description"
                rows="3"
                placeholder="Jelaskan isian bakso, kelezatan kuah, atau keunikan rasa produk..."
                class="w-full rounded-xl border border-gray-200 p-4 text-sm outline-none focus:border-[#E87532] focus:ring-1 focus:ring-orange-200"
            >{{ old('description') }}</textarea>
            @error('description')
                <p class="mt-1 text-xs text-red-600 font-semibold">{{ $message }}</p>
            @enderror
        </div>

        {{-- GAMBAR PRODUK --}}
        <div class="rounded-2xl border border-dashed border-orange-200 bg-orange-50/30 p-5 space-y-4">
            <p class="text-xs font-bold uppercase tracking-wider text-pesona-dark">Gambar Produk</p>

            <div>
                <label for="image_file" class="block text-xs font-semibold text-gray-600 mb-1">
                    Upload File Gambar (JPG, PNG, WEBP max 2MB)
                </label>
                <input
                    type="file"
                    id="image_file"
                    name="image_file"
                    accept="image/*"
                    class="w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-pesona-orange file:text-white hover:file:bg-[#74191B]"
                >
                @error('image_file')
                    <p class="mt-1 text-xs text-red-600 font-semibold">{{ $message }}</p>
                @enderror
            </div>

            <div class="relative flex items-center justify-center my-2">
                <span class="bg-transparent px-3 text-[10px] uppercase font-bold text-gray-400">Atau gunakan URL Gambar eksternal</span>
            </div>

            <div>
                <label for="image_url" class="block text-xs font-semibold text-gray-600 mb-1">
                    URL Gambar (Unsplash / CDN)
                </label>
                <input
                    type="url"
                    id="image_url"
                    name="image_url"
                    value="{{ old('image_url') }}"
                    placeholder="https://images.unsplash.com/..."
                    class="w-full rounded-xl border border-gray-200 bg-white px-4 py-2 text-xs outline-none focus:border-[#E87532] focus:ring-1 focus:ring-orange-200"
                >
                @error('image_url')
                    <p class="mt-1 text-xs text-red-600 font-semibold">{{ $message }}</p>
                @enderror
            </div>
        </div>

        {{-- STATUS KETERSEDIAAN --}}
        <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-2">
                Status Ketersediaan
            </label>
            <div class="flex items-center gap-6">
                <label class="flex items-center gap-2 text-xs font-semibold text-gray-700 cursor-pointer">
                    <input type="radio" name="is_available" value="1" @checked(old('is_available', '1') == '1') class="text-pesona-orange focus:ring-orange-400">
                    <span>Tersedia di Katalog (Aktif)</span>
                </label>
                <label class="flex items-center gap-2 text-xs font-semibold text-gray-700 cursor-pointer">
                    <input type="radio" name="is_available" value="0" @checked(old('is_available') == '0') class="text-pesona-orange focus:ring-orange-400">
                    <span>Habis / Nonaktif</span>
                </label>
            </div>
        </div>

        {{-- SUBMIT BUTTONS --}}
        <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
            <a href="{{ route('admin.products.index') }}" class="rounded-xl border border-gray-300 bg-white px-5 py-2.5 text-xs font-bold text-gray-600 hover:bg-gray-50 transition">
                Batal
            </a>
            <button type="submit" class="rounded-xl bg-gradient-to-r from-[#74191B] to-[#912224] hover:from-[#5C1315] hover:to-[#74191B] px-6 py-2.5 text-xs font-bold text-white shadow-md transition">
                Simpan Produk
            </button>
        </div>
    </form>
</div>
@endsection
