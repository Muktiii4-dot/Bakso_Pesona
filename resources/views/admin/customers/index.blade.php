@extends('layouts.admin')

@section('title', 'Pelanggan')
@section('header_title', 'Manajemen Pelanggan')

@section('content')
<div class="space-y-6">
    <section class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="text-xs font-bold uppercase tracking-[0.18em] text-[#E87532]">Data pelanggan</p>
            <h1 class="mt-1 text-2xl font-black tracking-tight text-[#74191B] sm:text-3xl">Manajemen Pelanggan</h1>
            <p class="mt-2 max-w-2xl text-sm leading-6 text-gray-500">Cari akun pelanggan dan lihat aktivitas pesanan mereka. Halaman ini hanya menampilkan data, tanpa mengubah kata sandi atau hak akses akun.</p>
        </div>
        <div class="rounded-2xl border border-orange-100 bg-white px-5 py-3 shadow-sm">
            <p class="text-xs font-semibold text-gray-500">Total pelanggan</p>
            <p class="mt-1 text-2xl font-black text-[#74191B]">{{ number_format($totalCustomers) }}</p>
        </div>
    </section>

    <section class="rounded-2xl border border-orange-100 bg-white p-4 shadow-sm sm:p-5">
        <form method="GET" action="{{ route('admin.customers.index') }}" class="grid gap-3 sm:grid-cols-[minmax(0,1fr)_220px_auto_auto] sm:items-end">
            <div>
                <label for="search" class="mb-1.5 block text-xs font-bold text-gray-600">Cari pelanggan</label>
                <input id="search" name="search" type="search" value="{{ request('search') }}" placeholder="Nama atau email pelanggan" class="w-full rounded-xl border border-gray-200 px-3.5 py-2.5 text-sm outline-none transition focus:border-[#E87532] focus:ring-2 focus:ring-orange-100">
            </div>
            <div>
                <label for="sort" class="mb-1.5 block text-xs font-bold text-gray-600">Urutkan</label>
                <select id="sort" name="sort" class="w-full rounded-xl border border-gray-200 bg-white px-3.5 py-2.5 text-sm outline-none focus:border-[#E87532] focus:ring-2 focus:ring-orange-100">
                    <option value="latest" @selected(request('sort', 'latest') === 'latest')>Terbaru terdaftar</option>
                    <option value="oldest" @selected(request('sort') === 'oldest')>Terlama terdaftar</option>
                    <option value="name" @selected(request('sort') === 'name')>Nama A–Z</option>
                </select>
            </div>
            <button class="rounded-xl bg-[#74191B] px-5 py-2.5 text-sm font-bold text-white transition hover:bg-[#5C1315]" type="submit">Terapkan</button>
            <a href="{{ route('admin.customers.index') }}" class="rounded-xl border border-orange-200 px-5 py-2.5 text-center text-sm font-bold text-[#74191B] transition hover:bg-orange-50">Reset</a>
        </form>
    </section>

    <section class="overflow-hidden rounded-2xl border border-orange-100 bg-white shadow-sm">
        <div class="flex items-center justify-between border-b border-gray-100 px-5 py-4 sm:px-6">
            <div><h2 class="font-black text-[#74191B]">Daftar akun</h2><p class="mt-1 text-xs text-gray-500">{{ $customers->total() }} hasil ditemukan</p></div>
        </div>
        @if ($customers->isEmpty())
            <div class="px-6 py-14 text-center"><p class="font-bold text-gray-700">Pelanggan tidak ditemukan</p><p class="mt-1 text-sm text-gray-500">Coba kata kunci lain atau reset filter pencarian.</p></div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full text-left text-sm">
                    <thead class="bg-[#FFF9F4] text-xs uppercase tracking-wide text-gray-500"><tr><th class="px-5 py-3.5">Pelanggan</th><th class="px-5 py-3.5">Email</th><th class="px-5 py-3.5 text-center">Jumlah pesanan</th><th class="px-5 py-3.5">Terdaftar</th></tr></thead>
                    <tbody class="divide-y divide-gray-100">
                    @foreach ($customers as $customer)
                        <tr class="transition hover:bg-orange-50/40">
                            <td class="px-5 py-4"><div class="flex items-center gap-3"><span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-orange-100 font-black text-[#74191B]">{{ strtoupper(substr($customer->name, 0, 1)) }}</span><span class="font-bold text-gray-800">{{ $customer->name }}</span></div></td>
                            <td class="whitespace-nowrap px-5 py-4 text-gray-600">{{ $customer->email }}</td>
                            <td class="px-5 py-4 text-center"><span class="inline-flex min-w-10 justify-center rounded-full bg-orange-50 px-3 py-1 font-bold text-[#74191B]">{{ $customer->orders_count }}</span></td>
                            <td class="whitespace-nowrap px-5 py-4 text-gray-500">{{ $customer->created_at?->format('d M Y') ?? '—' }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            @if ($customers->hasPages())<div class="border-t border-gray-100 px-5 py-4">{{ $customers->links() }}</div>@endif
        @endif
    </section>
</div>
@endsection
