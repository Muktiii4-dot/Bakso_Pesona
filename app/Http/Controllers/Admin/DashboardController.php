<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderRating;
use App\Models\Product;
use App\Models\User;
use Illuminate\Contracts\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $totalOrders = Order::count();

        $pendingConfirmationOrders = Order::where(
            'status',
            Order::STATUS_PENDING_CONFIRMATION
        )->count();

        $awaitingPaymentOrders = Order::query()
            ->where('status', '!=', Order::STATUS_CANCELLED)
            ->where('payment_status', Order::PAYMENT_AWAITING_PAYMENT)
            ->count();

        $processingOrders = Order::where(
            'status',
            Order::STATUS_PROCESSING
        )->count();

        $ordersAwaitingShipping = Order::query()
            ->where('status', Order::STATUS_PENDING_CONFIRMATION)
            ->whereNull('shipping_cost')
            ->count();

        $ordersAwaitingInstructions = Order::query()
            ->where('status', Order::STATUS_PENDING_CONFIRMATION)
            ->whereNotNull('total_amount')
            ->whereIn('payment_status', [
                Order::PAYMENT_AWAITING_CONFIRMATION,
                Order::PAYMENT_REJECTED,
            ])
            ->whereNull('payment_instructions_issued_at')
            ->count();

        $paymentsUnderVerification = Order::where(
            'payment_status',
            Order::PAYMENT_UNDER_VERIFICATION
        )->count();

        $paymentsNeedingReview = Order::query()
            ->where(function ($query) {
                $query->where('payment_review_required', true)
                    ->orWhere(function ($deadlineQuery) {
                        $deadlineQuery
                            ->whereNotNull('payment_due_at')
                            ->where('payment_due_at', '<=', now())
                            ->whereIn('payment_status', [
                                Order::PAYMENT_AWAITING_PAYMENT,
                                Order::PAYMENT_UNDER_VERIFICATION,
                            ]);
                    });
            })
            ->count();

        $ordersReadyToPack = Order::query()
            ->where('status', Order::STATUS_PENDING_CONFIRMATION)
            ->where('payment_status', Order::PAYMENT_PAID)
            ->count();

        $totalProducts = Product::count();
        $lowStockProducts = Product::query()
            ->where('is_available', true)
            ->where('stock', '<=', 5)
            ->count();
        $pendingRatings = OrderRating::where('moderation_status', 'pending')->count();

        $totalCustomers = User::where(
            'role',
            'customer'
        )->count();

        $latestOrders = Order::with('user')
            ->latest()
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'totalOrders',
            'pendingConfirmationOrders',
            'awaitingPaymentOrders',
            'processingOrders',
            'ordersAwaitingShipping',
            'ordersAwaitingInstructions',
            'paymentsUnderVerification',
            'paymentsNeedingReview',
            'ordersReadyToPack',
            'totalProducts',
            'lowStockProducts',
            'totalCustomers',
            'pendingRatings',
            'latestOrders'
        ));
    }
}
