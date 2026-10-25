<?php

namespace App\Console\Commands;

use App\Models\Order;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

#[Signature('app:mark-expired-payment-reviews')]
#[Description('Flag payments past their deadline for manual bank-statement review')]
class MarkExpiredPaymentReviews extends Command
{
    public function handle(): int
    {
        $updatedCount = 0;

        Order::query()
            ->whereNotNull('payment_due_at')
            ->where('payment_due_at', '<=', now())
            ->where('payment_review_required', false)
            ->whereIn('payment_status', [
                Order::PAYMENT_AWAITING_PAYMENT,
                Order::PAYMENT_UNDER_VERIFICATION,
            ])
            ->orderBy('id')
            ->chunkById(100, function ($orders) use (&$updatedCount): void {
                foreach ($orders as $candidate) {
                    $wasUpdated = DB::transaction(function () use ($candidate): bool {
                        $order = Order::query()->lockForUpdate()->find($candidate->id);

                        if (
                            ! $order
                            || $order->payment_review_required
                            || $order->payment_due_at === null
                            || $order->payment_due_at->isFuture()
                            || ! in_array($order->payment_status, [
                                Order::PAYMENT_AWAITING_PAYMENT,
                                Order::PAYMENT_UNDER_VERIFICATION,
                            ], true)
                        ) {
                            return false;
                        }

                        $order->payment_review_required = true;
                        $order->save();
                        $order->recordEvent(
                            'payment_deadline_review_required',
                            oldValues: ['payment_review_required' => false],
                            newValues: ['payment_review_required' => true],
                            metadata: ['payment_status' => $order->payment_status],
                        );

                        return true;
                    }, 3);

                    $updatedCount += (int) $wasUpdated;
                }
            });

        $this->info("Flagged {$updatedCount} payment(s) for manual review.");

        return self::SUCCESS;
    }
}
