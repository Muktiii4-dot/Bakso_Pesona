<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AccountController extends Controller
{
    public function profile(): View
    {
        return view('account.profile', [
            'user' => Auth::guard('customer')->user(),
        ]);
    }

    public function updateProfile(Request $request): RedirectResponse
    {
        $user = $request->user('customer');

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
        ]);

        $user->update($validated);

        return back()->with('success', 'Profil berhasil diperbarui.');
    }

    public function orders(Request $request): View
    {
        $orders = $request->user('customer')
            ->orders()
            ->withCount('items')
            ->latest()
            ->paginate(10);

        return view('orders.index', compact('orders'));
    }

    public function showOrder(Request $request, Order $order): View
    {
        abort_unless(
            $order->user_id === $request->user('customer')->id,
            404
        );

        $order->load(['items', 'events', 'cancellationRequests', 'refund', 'rating']);

        return view('orders.show', compact('order'));
    }

    public function requestCancellation(Request $request, Order $order): RedirectResponse
    {
        $validated = $request->validate([
            'reason' => ['required', 'string', 'max:1000'],
        ]);
        $customer = $request->user('customer');

        DB::transaction(function () use ($order, $validated, $customer) {
            $lockedOrder = Order::query()->lockForUpdate()->findOrFail($order->id);
            abort_unless($lockedOrder->user_id === $customer->id, 404);

            if (
                in_array($lockedOrder->status, [Order::STATUS_PROCESSING, Order::STATUS_SHIPPED, Order::STATUS_COMPLETED], true)
                || $lockedOrder->status === Order::STATUS_CANCELLED
            ) {
                throw ValidationException::withMessages([
                    'reason' => 'Pesanan ini tidak dapat dibatalkan mandiri. Hubungi admin melalui WhatsApp untuk penanganan lebih lanjut.',
                ]);
            }

            if ($lockedOrder->stockReservations()->where('state', 'legacy_unreconciled')->exists()) {
                throw ValidationException::withMessages([
                    'reason' => 'Riwayat stok pesanan ini belum direkonsiliasi. Hubungi admin untuk penanganan manual.',
                ]);
            }

            if ($lockedOrder->cancellationRequests()->where('status', 'pending')->exists()) {
                throw ValidationException::withMessages([
                    'reason' => 'Permintaan pembatalan pesanan ini sedang ditinjau admin.',
                ]);
            }

            $requiresAdminReview = in_array(
                $lockedOrder->payment_status,
                [Order::PAYMENT_UNDER_VERIFICATION, Order::PAYMENT_PAID],
                true,
            ) || $lockedOrder->payment_review_required;

            $cancellation = $lockedOrder->cancellationRequests()->create([
                'requested_by_user_id' => $customer->id,
                'requested_by_role' => 'customer',
                'reason' => $validated['reason'],
                'status' => $requiresAdminReview ? 'pending' : 'approved',
                'decision_note' => $requiresAdminReview ? null : 'Pembatalan mandiri sebelum pembayaran diverifikasi.',
                'decided_at' => $requiresAdminReview ? null : now(),
            ]);

            if ($requiresAdminReview) {
                $lockedOrder->recordEvent(
                    'cancellation_requested',
                    $customer,
                    newValues: ['request_id' => $cancellation->id, 'status' => 'pending'],
                    metadata: ['reason' => $validated['reason']],
                );

                return;
            }

            $oldValues = [
                'status' => $lockedOrder->status,
                'payment_status' => $lockedOrder->payment_status,
            ];
            $lockedOrder->status = Order::STATUS_CANCELLED;
            $lockedOrder->payment_status = Order::PAYMENT_UNPAID;
            $lockedOrder->cancelled_at = now();
            $lockedOrder->releaseReservedStock($customer, 'customer_cancelled_before_payment');
            $lockedOrder->save();
            $lockedOrder->recordEvent(
                'customer_cancelled_order',
                $customer,
                oldValues: $oldValues,
                newValues: [
                    'status' => $lockedOrder->status,
                    'payment_status' => $lockedOrder->payment_status,
                ],
                metadata: ['request_id' => $cancellation->id, 'reason' => $validated['reason']],
            );
        }, 3);

        return redirect()
            ->route('orders.show', $order)
            ->with('success', 'Permintaan pembatalan berhasil dicatat.');
    }

    public function storeRating(Request $request, Order $order): RedirectResponse
    {
        $validated = $request->validate([
            'rating' => ['required', 'integer', 'between:1,5'],
            'comment' => ['nullable', 'string', 'max:2000'],
        ]);
        $customer = $request->user('customer');

        DB::transaction(function () use ($order, $validated, $customer) {
            $lockedOrder = Order::query()->lockForUpdate()->findOrFail($order->id);
            abort_unless($lockedOrder->user_id === $customer->id, 404);

            if ($lockedOrder->status !== Order::STATUS_COMPLETED || $lockedOrder->rating()->exists()) {
                throw ValidationException::withMessages([
                    'rating' => 'Rating hanya dapat dikirim satu kali untuk pesanan milikmu yang sudah selesai.',
                ]);
            }

            $rating = $lockedOrder->rating()->create([
                'customer_user_id' => $customer->id,
                'rating' => $validated['rating'],
                'original_comment' => $validated['comment'] ?? null,
                'moderation_status' => 'pending',
            ]);
            $lockedOrder->recordEvent(
                'rating_submitted',
                $customer,
                newValues: [
                    'rating_id' => $rating->id,
                    'rating' => $rating->rating,
                    'moderation_status' => $rating->moderation_status,
                ],
            );
        }, 3);

        return redirect()
            ->route('orders.show', $order)
            ->with('success', 'Terima kasih! Rating menunggu moderasi admin sebelum tampil di halaman produk.');
    }
}
