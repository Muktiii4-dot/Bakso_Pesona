@extends('layouts.admin')

@section('title', 'Moderasi Rating')
@section('header_title', 'Moderasi Rating Pelanggan')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-2xl font-black text-pesona-dark sm:text-3xl">Rating Pelanggan</h1>
        <p class="mt-1 text-sm text-gray-600">Rating hanya ditampilkan pada katalog setelah disetujui admin.</p>
    </div>

    <form method="GET" action="{{ route('admin.ratings.index') }}" class="rounded-2xl border border-orange-100 bg-white p-4 shadow-sm">
        <label for="status" class="mr-2 text-sm font-bold text-gray-700">Status moderasi</label>
        <select id="status" name="status" class="rounded-lg border border-orange-200 px-3 py-2 text-sm" onchange="this.form.submit()">
            <option value="pending" @selected($status === 'pending')>Menunggu Moderasi</option>
            <option value="approved" @selected($status === 'approved')>Disetujui</option>
            <option value="rejected" @selected($status === 'rejected')>Ditolak</option>
            <option value="all" @selected($status === 'all')>Semua</option>
        </select>
    </form>

    @if ($ratings->isEmpty())
        <div class="rounded-2xl border border-dashed border-orange-200 bg-white p-10 text-center text-sm text-gray-500">
            Tidak ada rating untuk status ini.
        </div>
    @else
        <div class="space-y-4">
            @foreach ($ratings as $rating)
                <article class="rounded-2xl border border-orange-100 bg-white p-5 shadow-sm sm:p-6">
                    <div class="flex flex-wrap items-start justify-between gap-3">
                        <div>
                            <p class="font-black text-pesona-dark">{{ $rating->customer->name }}</p>
                            <p class="mt-1 text-xs text-gray-500">
                                Pesanan <a class="font-bold text-pesona-orange hover:underline" href="{{ route('admin.orders.show', $rating->order) }}">{{ $rating->order->order_number }}</a>
                                · {{ $rating->created_at->format('d M Y, H:i') }}
                            </p>
                        </div>
                        <span class="rounded-full bg-amber-50 px-3 py-1 text-sm font-black text-amber-700">{{ $rating->rating }} / 5 ★</span>
                    </div>

                    <div class="mt-4 grid gap-4 md:grid-cols-2">
                        <div class="rounded-xl bg-gray-50 p-4">
                            <p class="text-xs font-bold uppercase tracking-wide text-gray-500">Komentar asli</p>
                            <p class="mt-2 whitespace-pre-line text-sm text-gray-800">{{ $rating->original_comment ?: 'Tanpa komentar.' }}</p>
                        </div>

                        @if ($rating->moderation_status === 'pending')
                            <div class="rounded-xl border border-orange-100 bg-orange-50/40 p-4">
                                <p class="text-xs font-bold uppercase tracking-wide text-pesona-dark">Setujui atau tolak</p>
                                <p class="mt-1 text-xs text-gray-600">Jika menyunting, pertahankan makna penilaian pelanggan. Komentar asli tetap tersimpan.</p>

                                <form action="{{ route('admin.ratings.moderate', $rating) }}" method="POST" class="mt-3 space-y-3">
                                    @csrf
                                    @method('PUT')
                                    <label class="block text-xs font-bold text-gray-700" for="edited_comment_{{ $rating->id }}">Komentar yang ditampilkan</label>
                                    <textarea id="edited_comment_{{ $rating->id }}" name="edited_comment" rows="3" maxlength="2000" class="w-full rounded-lg border border-orange-200 p-2 text-sm">{{ old('edited_comment', $rating->original_comment) }}</textarea>
                                    <label class="block text-xs font-bold text-gray-700" for="moderation_note_{{ $rating->id }}">Catatan moderasi (opsional)</label>
                                    <input id="moderation_note_{{ $rating->id }}" name="moderation_note" maxlength="1000" class="w-full rounded-lg border border-orange-200 p-2 text-sm">
                                    <div class="flex flex-wrap gap-2">
                                        <button name="decision" value="approve" class="rounded-lg bg-emerald-600 px-4 py-2 text-xs font-bold text-white hover:bg-emerald-700">Setujui rating</button>
                                        <button name="decision" value="reject" class="rounded-lg border border-red-200 bg-white px-4 py-2 text-xs font-bold text-red-700 hover:bg-red-50">Tolak rating</button>
                                    </div>
                                </form>
                            </div>
                        @else
                            <div class="rounded-xl bg-gray-50 p-4">
                                <p class="text-xs font-bold uppercase tracking-wide text-gray-500">Status moderasi</p>
                                <p class="mt-2 text-sm font-bold text-gray-800">{{ ucfirst($rating->moderation_status) }}</p>
                                <p class="mt-1 text-xs text-gray-500">
                                    {{ $rating->moderator?->name ?? 'Admin tidak tersedia' }}
                                    @if ($rating->moderated_at)
                                        · {{ $rating->moderated_at->format('d M Y, H:i') }}
                                    @endif
                                </p>
                                @if ($rating->edited_comment !== null)
                                    <p class="mt-3 text-xs font-bold text-gray-500">Komentar publik</p>
                                    <p class="mt-1 whitespace-pre-line text-sm text-gray-800">{{ $rating->edited_comment }}</p>
                                @endif
                            </div>
                        @endif
                    </div>
                </article>
            @endforeach
        </div>
        {{ $ratings->links() }}
    @endif
</div>
@endsection
