<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderRating;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class OrderRatingController extends Controller
{
    public function index(Request $request): View
    {
        $validated = $request->validate([
            'status' => ['sometimes', 'string', Rule::in(['pending', 'approved', 'rejected', 'all'])],
        ]);
        $status = $validated['status'] ?? 'pending';

        $ratings = OrderRating::query()
            ->with(['order', 'customer', 'moderator'])
            ->when($status !== 'all', fn ($query) => $query->where('moderation_status', $status))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.ratings.index', compact('ratings', 'status'));
    }

    public function moderate(Request $request, OrderRating $rating): RedirectResponse
    {
        $validated = $request->validate([
            'decision' => ['required', Rule::in(['approve', 'reject'])],
            'edited_comment' => ['nullable', 'string', 'max:2000'],
            'moderation_note' => ['nullable', 'string', 'max:1000'],
        ]);
        $actor = $request->user('admin');
        abort_unless($actor instanceof User, 403);

        DB::transaction(function () use ($rating, $validated, $actor): void {
            $lockedRating = OrderRating::query()->lockForUpdate()->findOrFail($rating->id);
            $order = Order::query()->lockForUpdate()->findOrFail($lockedRating->order_id);

            if ($lockedRating->moderation_status !== 'pending') {
                throw ValidationException::withMessages([
                    'decision' => 'Rating yang sudah dimoderasi tidak dapat diproses kembali.',
                ]);
            }

            $oldValues = [
                'moderation_status' => $lockedRating->moderation_status,
                'edited_comment' => $lockedRating->edited_comment,
            ];
            $lockedRating->moderation_status = $validated['decision'] === 'approve' ? 'approved' : 'rejected';
            $lockedRating->moderated_by_user_id = $actor->id;
            $lockedRating->moderation_note = $validated['moderation_note'] ?? null;
            $lockedRating->moderated_at = now();

            if ($validated['decision'] === 'approve') {
                $lockedRating->edited_comment = $validated['edited_comment'] ?? null;
            }

            $lockedRating->save();
            $order->recordEvent(
                'rating_moderated',
                $actor,
                oldValues: $oldValues,
                newValues: [
                    'rating_id' => $lockedRating->id,
                    'moderation_status' => $lockedRating->moderation_status,
                    'edited_comment' => $lockedRating->edited_comment,
                ],
                metadata: ['moderation_note' => $lockedRating->moderation_note],
            );
        }, 3);

        return redirect()
            ->route('admin.ratings.index')
            ->with('success', 'Moderasi rating berhasil dicatat.');
    }
}
