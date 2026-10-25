<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderCancellationRequest;
use App\Models\OrderRefund;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class OrderController extends Controller
{
    public function index(Request $request): View
    {
        $request->validate([
            'status' => ['nullable', Rule::in(array_keys(Order::statusLabels()))],
            'payment_status' => ['nullable', Rule::in(array_keys(Order::paymentStatusLabels()))],
            'attention' => ['nullable', Rule::in([
                'shipping',
                'instructions',
                'verification',
                'payment_review',
                'ready_to_pack',
            ])],
            'search' => ['nullable', 'string', 'max:255'],
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date', 'after_or_equal:date_from'],
            'sort' => ['nullable', Rule::in(['latest', 'oldest', 'total_high', 'total_low'])],
            'selected' => ['nullable', 'integer', 'min:1'],
        ]);
        $sort = $request->input('sort', 'latest');

        $ordersQuery = Order::with('user')
            ->when($request->filled('status'), function ($query) use ($request) {
                $query->where('status', $request->input('status'));
            })
            ->when($request->filled('payment_status'), function ($query) use ($request) {
                $query->where('payment_status', $request->input('payment_status'));
            })
            ->when($request->filled('date_from'), function ($query) use ($request) {
                $start = CarbonImmutable::parse($request->input('date_from'), 'Asia/Jakarta')
                    ->startOfDay()
                    ->utc();

                $query->where('created_at', '>=', $start);
            })
            ->when($request->filled('date_to'), function ($query) use ($request) {
                $end = CarbonImmutable::parse($request->input('date_to'), 'Asia/Jakarta')
                    ->addDay()
                    ->startOfDay()
                    ->utc();

                $query->where('created_at', '<', $end);
            })
            ->when($request->filled('attention'), function ($query) use ($request) {
                match ($request->input('attention')) {
                    'shipping' => $query
                        ->where('status', Order::STATUS_PENDING_CONFIRMATION)
                        ->whereNull('shipping_cost'),
                    'instructions' => $query
                        ->where('status', Order::STATUS_PENDING_CONFIRMATION)
                        ->whereNotNull('total_amount')
                        ->whereIn('payment_status', [
                            Order::PAYMENT_AWAITING_CONFIRMATION,
                            Order::PAYMENT_REJECTED,
                        ])
                        ->whereNull('payment_instructions_issued_at'),
                    'verification' => $query->where('payment_status', Order::PAYMENT_UNDER_VERIFICATION),
                    'payment_review' => $query
                        ->where(function ($paymentQuery) {
                            $paymentQuery->where('payment_review_required', true)
                                ->orWhere(function ($deadlineQuery) {
                                    $deadlineQuery
                                        ->whereNotNull('payment_due_at')
                                        ->where('payment_due_at', '<=', now())
                                        ->whereIn('payment_status', [
                                            Order::PAYMENT_AWAITING_PAYMENT,
                                            Order::PAYMENT_UNDER_VERIFICATION,
                                        ]);
                                });
                        }),
                    'ready_to_pack' => $query
                        ->where('status', Order::STATUS_PENDING_CONFIRMATION)
                        ->where('payment_status', Order::PAYMENT_PAID),
                    default => $query,
                };
            })
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = trim($request->input('search'));
                $query->where(function ($q) use ($search) {
                    $q->where('order_number', 'like', "%{$search}%")
                        ->orWhere('recipient_name', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%")
                        ->orWhereHas('user', function ($uq) use ($search) {
                            $uq->where('name', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%");
                        });
                });
            });

        if ($sort === 'oldest') {
            $ordersQuery->oldest();
        } elseif ($sort === 'total_high') {
            $ordersQuery->orderByDesc('total_amount')->orderByDesc('id');
        } elseif ($sort === 'total_low') {
            $ordersQuery->orderBy('total_amount')->orderBy('id');
        } else {
            $ordersQuery->latest();
        }

        $orders = $ordersQuery->paginate(10)->withQueryString();

        $selectedOrder = null;
        if ($request->filled('selected')) {
            $selectedOrder = Order::with($this->orderDetailRelations())->find($request->input('selected'));
        }

        if (! $selectedOrder && $orders->isNotEmpty()) {
            $selectedOrder = $orders->first();
            $selectedOrder->load($this->orderDetailRelations());
        }

        $statusLabels = Order::statusLabels();
        $paymentStatusLabels = Order::paymentStatusLabels();
        $totalOrders = Order::count();
        $ordersRequiringAction = Order::query()
            ->where(function ($query) {
                $query
                    ->where(function ($shippingQuery) {
                        $shippingQuery
                            ->where('status', Order::STATUS_PENDING_CONFIRMATION)
                            ->whereNull('shipping_cost');
                    })
                    ->orWhere(function ($instructionQuery) {
                        $instructionQuery
                            ->where('status', Order::STATUS_PENDING_CONFIRMATION)
                            ->whereNotNull('total_amount')
                            ->whereIn('payment_status', [
                                Order::PAYMENT_AWAITING_CONFIRMATION,
                                Order::PAYMENT_REJECTED,
                            ])
                            ->whereNull('payment_instructions_issued_at');
                    })
                    ->orWhere('payment_review_required', true)
                    ->orWhere(function ($expiredQuery) {
                        $expiredQuery
                            ->whereNotNull('payment_due_at')
                            ->where('payment_due_at', '<=', now())
                            ->whereIn('payment_status', [
                                Order::PAYMENT_AWAITING_PAYMENT,
                                Order::PAYMENT_UNDER_VERIFICATION,
                            ]);
                    })
                    ->orWhere('payment_status', Order::PAYMENT_UNDER_VERIFICATION)
                    ->orWhere(function ($readyQuery) {
                        $readyQuery
                            ->where('status', Order::STATUS_PENDING_CONFIRMATION)
                            ->where('payment_status', Order::PAYMENT_PAID);
                    });
            })
            ->count();
        $awaitingPaymentOrders = Order::query()
            ->where('status', '!=', Order::STATUS_CANCELLED)
            ->where('payment_status', Order::PAYMENT_AWAITING_PAYMENT)
            ->count();
        $paymentsUnderVerification = Order::query()
            ->where('payment_status', Order::PAYMENT_UNDER_VERIFICATION)
            ->count();

        return view('admin.orders.index', compact(
            'orders',
            'selectedOrder',
            'statusLabels',
            'paymentStatusLabels',
            'sort',
            'totalOrders',
            'ordersRequiringAction',
            'awaitingPaymentOrders',
            'paymentsUnderVerification',
        ) + ['attention' => $request->input('attention')]);
    }

    public function show(Order $order): View
    {
        $order->load($this->orderDetailRelations());

        $statusLabels = Order::statusLabels();
        $paymentStatusLabels = Order::paymentStatusLabels();
        $whatsappTemplates = $this->whatsappTemplates($order);
        $hasPreviouslySentInstructions = $order->payment_instructions_issued_at !== null
            || $order->events()
                ->whereIn('event_type', ['payment_instructions_sent', 'payment_instructions_resent'])
                ->exists();
        $qrisConfigured = $this->qrisIsConfigured();

        return view('admin.orders.show', compact(
            'order',
            'statusLabels',
            'paymentStatusLabels',
            'whatsappTemplates',
            'hasPreviouslySentInstructions',
            'qrisConfigured',
        ));
    }

    public function confirmShipping(Request $request, Order $order): RedirectResponse
    {
        $validated = $request->validate([
            'shipping_cost' => ['required', 'numeric', 'integer', 'min:0', 'max:1000000000'],
        ], [
            'shipping_cost.required' => 'Ongkos kirim wajib diisi.',
            'shipping_cost.numeric' => 'Ongkos kirim harus berupa angka.',
            'shipping_cost.integer' => 'Ongkos kirim harus berupa bilangan bulat.',
            'shipping_cost.min' => 'Ongkos kirim tidak boleh negatif.',
            'shipping_cost.max' => 'Ongkos kirim terlalu besar.',
        ]);

        $actor = $request->user('admin');

        DB::transaction(function () use ($order, $validated, $actor) {
            $lockedOrder = Order::query()
                ->lockForUpdate()
                ->findOrFail($order->id);

            if (
                $lockedOrder->status !== Order::STATUS_PENDING_CONFIRMATION
                || in_array($lockedOrder->payment_status, [Order::PAYMENT_UNDER_VERIFICATION, Order::PAYMENT_PAID, Order::PAYMENT_REFUNDED], true)
                || $lockedOrder->payment_review_required
                || ($lockedOrder->payment_due_at !== null && $lockedOrder->payment_due_at->isPast())
                || $lockedOrder->cancellationRequests()->where('status', 'pending')->exists()
            ) {
                throw ValidationException::withMessages([
                    'shipping_cost' => 'Ongkos kirim tidak dapat diubah pada status pesanan atau pembayaran saat ini.',
                ]);
            }

            $shippingCost = (int) $validated['shipping_cost'];
            $subtotal = Order::rupiahToInt($lockedOrder->subtotal);
            $discount = Order::rupiahToInt($lockedOrder->discount_amount ?? 0);

            if ($discount > $subtotal) {
                throw ValidationException::withMessages([
                    'shipping_cost' => 'Diskon tercatat melebihi subtotal. Perbaiki data pesanan sebelum menetapkan ongkir.',
                ]);
            }

            $total = $subtotal - $discount + $shippingCost;

            if ($total > 9_999_999_999) {
                throw ValidationException::withMessages([
                    'shipping_cost' => 'Total pesanan melebihi batas nominal yang didukung.',
                ]);
            }

            $oldValues = [
                'shipping_cost' => $lockedOrder->shipping_cost === null
                    ? null
                    : Order::rupiahToInt($lockedOrder->shipping_cost),
                'discount_amount' => Order::rupiahToInt($lockedOrder->discount_amount ?? 0),
                'total_amount' => $lockedOrder->total_amount === null
                    ? null
                    : Order::rupiahToInt($lockedOrder->total_amount),
                'payment_status' => $lockedOrder->payment_status,
                'payment_instructions_issued_at' => $lockedOrder->payment_instructions_issued_at?->toIso8601String(),
                'payment_due_at' => $lockedOrder->payment_due_at?->toIso8601String(),
            ];

            $totalChanged = $lockedOrder->shipping_cost === null
                || Order::rupiahToInt($lockedOrder->shipping_cost) !== $shippingCost;

            if ($totalChanged && $lockedOrder->payment_instructions_issued_at !== null) {
                $lockedOrder->recordEvent(
                    'payment_instructions_invalidated',
                    $actor,
                    oldValues: [
                        'total_amount' => $oldValues['total_amount'],
                        'payment_instructions_issued_at' => $oldValues['payment_instructions_issued_at'],
                        'payment_due_at' => $oldValues['payment_due_at'],
                    ],
                    newValues: ['valid' => false],
                    metadata: ['reason' => 'order_total_changed'],
                );
            }

            $lockedOrder->shipping_cost = $shippingCost;
            $lockedOrder->total_amount = $total;

            if ($totalChanged) {
                $lockedOrder->total_approved_at = null;
                $lockedOrder->payment_instructions_issued_at = null;
                $lockedOrder->payment_due_at = null;
                $lockedOrder->payment_review_required = false;
                $lockedOrder->payment_status = Order::PAYMENT_AWAITING_CONFIRMATION;
            }

            $lockedOrder->save();
            $lockedOrder->recordEvent(
                'shipping_total_updated',
                $actor,
                oldValues: $oldValues,
                newValues: [
                    'shipping_cost' => $shippingCost,
                    'discount_amount' => $discount,
                    'total_amount' => $total,
                    'payment_status' => $lockedOrder->payment_status,
                    'payment_instructions_issued_at' => $lockedOrder->payment_instructions_issued_at?->toIso8601String(),
                    'payment_due_at' => $lockedOrder->payment_due_at?->toIso8601String(),
                ],
            );
        }, 3);

        return redirect()
            ->route('admin.orders.show', $order)
            ->with('success', 'Ongkos kirim dan total akhir berhasil dihitung dan disimpan.');
    }

    public function updatePaymentStatus(Request $request, Order $order): RedirectResponse
    {
        $validated = $request->validate([
            'payment_status' => [
                'required',
                Rule::in([
                    Order::PAYMENT_UNDER_VERIFICATION,
                    Order::PAYMENT_PAID,
                    Order::PAYMENT_REJECTED,
                ]),
            ],
            'actual_amount' => [
                'required_if:payment_status,'.Order::PAYMENT_PAID,
                'nullable',
                'integer',
                'min:0',
                'max:9999999999',
            ],
            'transaction_reference' => ['nullable', 'string', 'max:255'],
            'verification_note' => [
                'required_if:payment_status,'.Order::PAYMENT_UNDER_VERIFICATION.','.Order::PAYMENT_PAID,
                'nullable',
                'string',
                'max:1000',
            ],
            'rejection_reason' => [
                'required_if:payment_status,'.Order::PAYMENT_REJECTED,
                'nullable',
                'string',
                'max:1000',
            ],
        ], [
            'payment_status.required' => 'Status pembayaran wajib dipilih.',
            'payment_status.in' => 'Status pembayaran yang dipilih tidak valid.',
            'actual_amount.required_if' => 'Nominal yang terlihat pada transaksi aktual wajib dicatat sebelum pembayaran dinyatakan lunas.',
            'verification_note.required_if' => 'Catatan pemeriksaan transaksi wajib diisi.',
            'rejection_reason.required_if' => 'Alasan penolakan pembayaran wajib diisi.',
        ]);

        $actor = $request->user('admin');

        DB::transaction(function () use ($order, $validated, $actor) {
            $lockedOrder = Order::query()
                ->lockForUpdate()
                ->findOrFail($order->id);

            $targetPayment = $validated['payment_status'];

            if (
                $lockedOrder->total_amount === null
                || $lockedOrder->payment_instructions_issued_at === null
            ) {
                throw ValidationException::withMessages([
                    'payment_status' => 'Total akhir dan pengiriman instruksi pembayaran harus tercatat terlebih dahulu.',
                ]);
            }

            if ($this->hasUnreconciledLegacyStock($lockedOrder)) {
                throw ValidationException::withMessages([
                    'payment_status' => 'Riwayat stok pesanan legacy belum direkonsiliasi. Selesaikan rekonsiliasi sebelum memverifikasi pembayaran.',
                ]);
            }

            $oldValues = [
                'payment_status' => $lockedOrder->payment_status,
                'status' => $lockedOrder->status,
                'payment_due_at' => $lockedOrder->payment_due_at?->toIso8601String(),
                'payment_review_required' => $lockedOrder->payment_review_required,
            ];

            if ($targetPayment === Order::PAYMENT_UNDER_VERIFICATION) {
                if (
                    $lockedOrder->payment_status !== Order::PAYMENT_AWAITING_PAYMENT
                    || $lockedOrder->status !== Order::STATUS_PENDING_CONFIRMATION
                    || $lockedOrder->payment_review_required
                    || $lockedOrder->payment_due_at === null
                    || $lockedOrder->payment_due_at->isPast()
                ) {
                    throw ValidationException::withMessages([
                        'payment_status' => 'Pemeriksaan transaksi hanya dapat dimulai untuk pembayaran yang belum melewati tenggat.',
                    ]);
                }

                $lockedOrder->payment_status = Order::PAYMENT_UNDER_VERIFICATION;
                $eventType = 'payment_review_started';
            } elseif ($targetPayment === Order::PAYMENT_PAID) {
                if (
                    $lockedOrder->payment_status !== Order::PAYMENT_UNDER_VERIFICATION
                    || $lockedOrder->status !== Order::STATUS_PENDING_CONFIRMATION
                    || $lockedOrder->payment_review_required
                    || $lockedOrder->payment_due_at === null
                    || $lockedOrder->payment_due_at->isPast()
                ) {
                    throw ValidationException::withMessages([
                        'payment_status' => 'Pembayaran harus berstatus sedang diverifikasi dan masih dalam tenggat.',
                    ]);
                }

                $expectedAmount = Order::rupiahToInt($lockedOrder->total_amount);
                if ((int) $validated['actual_amount'] !== $expectedAmount) {
                    throw ValidationException::withMessages([
                        'actual_amount' => 'Nominal transaksi aktual tidak sama dengan total tagihan. Tandai sebagai perlu pemeriksaan, bukan lunas.',
                    ]);
                }

                $lockedOrder->payment_status = Order::PAYMENT_PAID;
                $lockedOrder->payment_review_required = false;
                $lockedOrder->consumeReservedStock($actor);
                $eventType = 'payment_verified';
            } elseif ($targetPayment === Order::PAYMENT_REJECTED) {
                if ($lockedOrder->payment_status !== Order::PAYMENT_UNDER_VERIFICATION) {
                    throw ValidationException::withMessages([
                        'payment_status' => 'Pembayaran hanya dapat ditolak setelah transaksi aktual diperiksa.',
                    ]);
                }

                $lockedOrder->payment_status = Order::PAYMENT_REJECTED;
                $lockedOrder->payment_review_required = false;
                $lockedOrder->payment_due_at = null;
                $eventType = 'payment_rejected';
            }

            $lockedOrder->save();
            $lockedOrder->recordEvent(
                $eventType,
                $actor,
                oldValues: $oldValues,
                newValues: [
                    'payment_status' => $lockedOrder->payment_status,
                    'status' => $lockedOrder->status,
                    'payment_due_at' => $lockedOrder->payment_due_at?->toIso8601String(),
                    'payment_review_required' => $lockedOrder->payment_review_required,
                ],
                metadata: [
                    'expected_amount' => $lockedOrder->total_amount === null
                        ? null
                        : Order::rupiahToInt($lockedOrder->total_amount),
                    'actual_amount' => isset($validated['actual_amount']) ? (int) $validated['actual_amount'] : null,
                    'transaction_reference' => $validated['transaction_reference'] ?? null,
                    'verification_note' => $validated['verification_note'] ?? null,
                    'rejection_reason' => $validated['rejection_reason'] ?? null,
                    'verified_at' => $targetPayment === Order::PAYMENT_PAID ? now()->toIso8601String() : null,
                    'amount_matched' => $targetPayment === Order::PAYMENT_PAID,
                ],
            );
        }, 3);

        return redirect()
            ->route('admin.orders.show', $order)
            ->with('success', 'Status pembayaran berhasil diperbarui.');
    }

    public function updateStatus(Request $request, Order $order): RedirectResponse
    {
        $validated = $request->validate([
            'status' => [
                'required',
                Rule::in([
                    Order::STATUS_PROCESSING,
                    Order::STATUS_SHIPPED,
                    Order::STATUS_COMPLETED,
                ]),
            ],
        ], [
            'status.required' => 'Status pesanan wajib dipilih.',
            'status.in' => 'Status pesanan yang dipilih tidak valid.',
        ]);

        $actor = $request->user('admin');

        DB::transaction(function () use ($order, $validated, $actor) {
            $lockedOrder = Order::query()
                ->lockForUpdate()
                ->findOrFail($order->id);

            $targetStatus = $validated['status'];
            $oldStatus = $lockedOrder->status;

            if ($targetStatus === Order::STATUS_PROCESSING) {
                if (
                    $lockedOrder->status !== Order::STATUS_PENDING_CONFIRMATION
                    || $lockedOrder->payment_status !== Order::PAYMENT_PAID
                ) {
                    throw ValidationException::withMessages([
                        'status' => 'Pesanan hanya dapat dikemas setelah pembayaran diverifikasi dan pesanan siap diproses.',
                    ]);
                }
                $lockedOrder->status = Order::STATUS_PROCESSING;
            } elseif ($targetStatus === Order::STATUS_SHIPPED) {
                if ($lockedOrder->status !== Order::STATUS_PROCESSING) {
                    throw ValidationException::withMessages([
                        'status' => 'Pesanan hanya dapat dikirim jika status saat ini sedang diproses.',
                    ]);
                }
                $lockedOrder->status = Order::STATUS_SHIPPED;
            } elseif ($targetStatus === Order::STATUS_COMPLETED) {
                if ($lockedOrder->status !== Order::STATUS_SHIPPED) {
                    throw ValidationException::withMessages([
                        'status' => 'Pesanan hanya dapat diselesaikan setelah pesanan berstatus dikirim.',
                    ]);
                }
                $lockedOrder->status = Order::STATUS_COMPLETED;
            }

            $lockedOrder->save();
            $lockedOrder->recordEvent(
                'order_status_changed',
                $actor,
                oldValues: ['status' => $oldStatus],
                newValues: ['status' => $lockedOrder->status],
            );
        }, 3);

        return redirect()
            ->route('admin.orders.show', $order)
            ->with('success', 'Status pesanan berhasil diperbarui.');
    }

    public function issuePaymentInstructions(Request $request, Order $order): RedirectResponse
    {
        $validated = $request->validate([
            'confirmed_sent' => ['required', 'accepted'],
            'resend_reason' => ['nullable', 'string', 'max:1000'],
        ]);
        $actor = $request->user('admin');

        DB::transaction(function () use ($order, $validated, $actor) {
            $lockedOrder = Order::query()->lockForUpdate()->findOrFail($order->id);

            if (
                $lockedOrder->status !== Order::STATUS_PENDING_CONFIRMATION
                || $lockedOrder->total_amount === null
                || ! in_array($lockedOrder->payment_status, [
                    Order::PAYMENT_AWAITING_CONFIRMATION,
                    Order::PAYMENT_AWAITING_PAYMENT,
                    Order::PAYMENT_REJECTED,
                ], true)
                || $lockedOrder->payment_review_required
                || ($lockedOrder->payment_due_at !== null && $lockedOrder->payment_due_at->isPast())
            ) {
                throw ValidationException::withMessages([
                    'confirmed_sent' => 'Instruksi hanya dapat dicatat setelah total ditetapkan dan sebelum pembayaran masuk pemeriksaan.',
                ]);
            }

            if (! $this->qrisIsConfigured()) {
                throw ValidationException::withMessages([
                    'confirmed_sent' => 'QRIS resmi, identitas merchant, dan nomor WhatsApp usaha belum dikonfigurasi.',
                ]);
            }

            if ($this->customerWhatsAppUrl($lockedOrder, '') === null) {
                throw ValidationException::withMessages([
                    'confirmed_sent' => 'Nomor WhatsApp pelanggan tidak valid. Perbaiki data pesanan sebelum mengirim instruksi.',
                ]);
            }

            if ($this->hasUnreconciledLegacyStock($lockedOrder)) {
                throw ValidationException::withMessages([
                    'confirmed_sent' => 'Riwayat stok pesanan legacy belum direkonsiliasi. Selesaikan rekonsiliasi sebelum mengirim instruksi pembayaran.',
                ]);
            }

            $hasPreviouslySent = $lockedOrder->payment_instructions_issued_at !== null
                || $lockedOrder->events()
                    ->whereIn('event_type', ['payment_instructions_sent', 'payment_instructions_resent'])
                    ->exists();

            if ($hasPreviouslySent && trim((string) ($validated['resend_reason'] ?? '')) === '') {
                throw ValidationException::withMessages([
                    'resend_reason' => 'Alasan pengiriman ulang wajib diisi.',
                ]);
            }

            $now = now();
            $oldValues = [
                'payment_method' => $lockedOrder->payment_method,
                'payment_status' => $lockedOrder->payment_status,
                'total_amount' => Order::rupiahToInt($lockedOrder->total_amount),
                'payment_instructions_issued_at' => $lockedOrder->payment_instructions_issued_at?->toIso8601String(),
                'payment_due_at' => $lockedOrder->payment_due_at?->toIso8601String(),
            ];

            $lockedOrder->payment_method = 'qris';
            $lockedOrder->payment_status = Order::PAYMENT_AWAITING_PAYMENT;
            $lockedOrder->payment_instructions_issued_at = $now;
            $lockedOrder->payment_due_at = $now->copy()->addHours(24);
            $lockedOrder->payment_review_required = false;
            $lockedOrder->save();
            $lockedOrder->recordEvent(
                $hasPreviouslySent ? 'payment_instructions_resent' : 'payment_instructions_sent',
                $actor,
                oldValues: $oldValues,
                newValues: [
                    'payment_method' => $lockedOrder->payment_method,
                    'payment_status' => $lockedOrder->payment_status,
                    'total_amount' => Order::rupiahToInt($lockedOrder->total_amount),
                    'payment_instructions_issued_at' => $now->toIso8601String(),
                    'payment_due_at' => $lockedOrder->payment_due_at->toIso8601String(),
                ],
                metadata: [
                    'resend_reason' => $validated['resend_reason'] ?? null,
                    'manual_send_confirmed' => true,
                    'deadline_hours' => 24,
                ],
            );
        }, 3);

        return redirect()
            ->route('admin.orders.show', $order)
            ->with('success', 'Pengiriman instruksi pembayaran dicatat. Tenggat 24 jam dimulai dari waktu server ini.');
    }

    public function resolvePaymentReview(Request $request, Order $order): RedirectResponse
    {
        $validated = $request->validate([
            'result' => ['required', Rule::in(['received', 'mismatch', 'not_received'])],
            'actual_amount' => [
                'required_if:result,received,mismatch',
                'nullable',
                'integer',
                'min:0',
                'max:9999999999',
            ],
            'transaction_reference' => ['nullable', 'string', 'max:255'],
            'review_note' => ['required', 'string', 'max:1000'],
        ]);
        $actor = $request->user('admin');

        DB::transaction(function () use ($order, $validated, $actor) {
            $lockedOrder = Order::query()->lockForUpdate()->findOrFail($order->id);

            if (
                $lockedOrder->status !== Order::STATUS_PENDING_CONFIRMATION
                || $lockedOrder->total_amount === null
                || $lockedOrder->payment_due_at === null
                || $lockedOrder->payment_due_at->isFuture()
                || ! in_array($lockedOrder->payment_status, [Order::PAYMENT_AWAITING_PAYMENT, Order::PAYMENT_UNDER_VERIFICATION], true)
            ) {
                throw ValidationException::withMessages([
                    'result' => 'Pesanan ini belum memenuhi syarat untuk pemeriksaan kedaluwarsa.',
                ]);
            }

            if ($this->hasUnreconciledLegacyStock($lockedOrder)) {
                throw ValidationException::withMessages([
                    'result' => 'Riwayat stok pesanan legacy belum direkonsiliasi. Jangan ubah status atau lepaskan stok sebelum rekonsiliasi.',
                ]);
            }

            $oldValues = [
                'status' => $lockedOrder->status,
                'payment_status' => $lockedOrder->payment_status,
                'payment_review_required' => $lockedOrder->payment_review_required,
            ];

            if ($validated['result'] === 'received') {
                $expectedAmount = Order::rupiahToInt($lockedOrder->total_amount);

                if ((int) $validated['actual_amount'] !== $expectedAmount) {
                    throw ValidationException::withMessages([
                        'actual_amount' => 'Nominal transaksi aktual tidak sama dengan total tagihan. Catat sebagai nominal tidak cocok.',
                    ]);
                }

                $lockedOrder->payment_status = Order::PAYMENT_PAID;
                $lockedOrder->payment_review_required = false;
                $lockedOrder->consumeReservedStock($actor);
                $eventType = 'expired_payment_review_received';
            } elseif ($validated['result'] === 'mismatch') {
                $expectedAmount = Order::rupiahToInt($lockedOrder->total_amount);

                if ((int) $validated['actual_amount'] === $expectedAmount) {
                    throw ValidationException::withMessages([
                        'actual_amount' => 'Nominal transaksi sesuai total tagihan. Gunakan hasil dana diterima setelah memeriksa mutasi.',
                    ]);
                }

                $lockedOrder->payment_status = Order::PAYMENT_REJECTED;
                $lockedOrder->payment_due_at = null;
                $lockedOrder->payment_review_required = false;
                $eventType = 'late_payment_amount_mismatch';
            } else {
                $lockedOrder->payment_status = Order::PAYMENT_EXPIRED;
                $lockedOrder->status = Order::STATUS_CANCELLED;
                $lockedOrder->cancelled_at = now();
                $lockedOrder->payment_review_required = false;
                $lockedOrder->releaseReservedStock($actor, 'payment_expired_after_admin_review');

                $pendingCancellation = $lockedOrder->cancellationRequests()
                    ->where('status', 'pending')
                    ->lockForUpdate()
                    ->first();

                if ($pendingCancellation) {
                    $pendingCancellation->update([
                        'status' => 'approved',
                        'decided_by_user_id' => $actor->id,
                        'decision_note' => $validated['review_note'],
                        'decided_at' => now(),
                    ]);
                }

                $eventType = 'payment_expired_after_admin_review';
            }

            $lockedOrder->save();
            $lockedOrder->recordEvent(
                $eventType,
                $actor,
                oldValues: $oldValues,
                newValues: [
                    'status' => $lockedOrder->status,
                    'payment_status' => $lockedOrder->payment_status,
                    'payment_review_required' => false,
                ],
                metadata: [
                    'review_note' => $validated['review_note'],
                    'expected_amount' => $lockedOrder->total_amount === null
                        ? null
                        : Order::rupiahToInt($lockedOrder->total_amount),
                    'actual_amount' => isset($validated['actual_amount']) ? (int) $validated['actual_amount'] : null,
                    'transaction_reference' => $validated['transaction_reference'] ?? null,
                    'result' => $validated['result'],
                    'reviewed_at' => now()->toIso8601String(),
                ],
            );
        }, 3);

        return redirect()
            ->route('admin.orders.show', $order)
            ->with('success', 'Hasil pemeriksaan pembayaran dan mutasi berhasil dicatat.');
    }

    public function decideCancellation(
        Request $request,
        Order $order,
        OrderCancellationRequest $cancellationRequest,
    ): RedirectResponse {
        abort_unless($cancellationRequest->order_id === $order->id, 404);

        $validated = $request->validate([
            'decision' => ['required', Rule::in(['approve', 'reject'])],
            'decision_note' => ['nullable', 'string', 'max:1000'],
        ]);
        $actor = $request->user('admin');

        DB::transaction(function () use ($order, $cancellationRequest, $validated, $actor) {
            $lockedOrder = Order::query()->lockForUpdate()->findOrFail($order->id);
            $lockedRequest = OrderCancellationRequest::query()
                ->lockForUpdate()
                ->findOrFail($cancellationRequest->id);

            $this->applyCancellationDecision(
                $lockedOrder,
                $lockedRequest,
                $actor,
                $validated['decision'],
                $validated['decision_note'] ?? null,
            );
        }, 3);

        return redirect()
            ->route('admin.orders.show', $order)
            ->with('success', 'Keputusan permintaan pembatalan berhasil dicatat.');
    }

    public function cancelByAdmin(Request $request, Order $order): RedirectResponse
    {
        $validated = $request->validate([
            'reason' => ['required', 'string', 'max:1000'],
        ]);
        $actor = $request->user('admin');

        DB::transaction(function () use ($order, $validated, $actor) {
            $lockedOrder = Order::query()->lockForUpdate()->findOrFail($order->id);

            if (
                in_array($lockedOrder->status, [Order::STATUS_SHIPPED, Order::STATUS_COMPLETED, Order::STATUS_CANCELLED], true)
                || $lockedOrder->payment_status === Order::PAYMENT_UNDER_VERIFICATION
                || $lockedOrder->payment_review_required
            ) {
                throw ValidationException::withMessages([
                    'reason' => 'Pesanan tidak dapat dibatalkan sebelum status pengiriman dan pembayaran ditinjau.',
                ]);
            }

            $cancellationRequest = $lockedOrder->cancellationRequests()->create([
                'requested_by_user_id' => $actor->id,
                'requested_by_role' => 'admin',
                'reason' => $validated['reason'],
                'status' => 'pending',
            ]);

            $this->applyCancellationDecision($lockedOrder, $cancellationRequest, $actor, 'approve', null);
        }, 3);

        return redirect()
            ->route('admin.orders.show', $order)
            ->with('success', 'Pesanan dibatalkan dan dampak pembayaran/stok dicatat.');
    }

    public function completeRefund(Request $request, Order $order): RedirectResponse
    {
        $validated = $request->validate([
            'transfer_reference' => ['required', 'string', 'max:255'],
        ]);
        $actor = $request->user('admin');

        DB::transaction(function () use ($order, $validated, $actor) {
            $lockedOrder = Order::query()->lockForUpdate()->findOrFail($order->id);
            $refund = OrderRefund::query()->lockForUpdate()->where('order_id', $order->id)->first();

            if (
                ! $refund
                || $refund->status !== 'pending'
                || $lockedOrder->status !== Order::STATUS_CANCELLED
                || $lockedOrder->payment_status !== Order::PAYMENT_PAID
            ) {
                throw ValidationException::withMessages([
                    'transfer_reference' => 'Refund tidak tersedia atau sudah diselesaikan.',
                ]);
            }

            $refund->update([
                'status' => 'completed',
                'processed_by_user_id' => $actor->id,
                'transfer_reference' => $validated['transfer_reference'],
                'completed_at' => now(),
            ]);

            $lockedOrder->payment_status = Order::PAYMENT_REFUNDED;
            $lockedOrder->save();
            $lockedOrder->recordEvent(
                'refund_completed',
                $actor,
                oldValues: ['payment_status' => Order::PAYMENT_PAID, 'refund_status' => 'pending'],
                newValues: ['payment_status' => Order::PAYMENT_REFUNDED, 'refund_status' => 'completed'],
                metadata: ['amount' => $refund->amount],
            );
        }, 3);

        return redirect()
            ->route('admin.orders.show', $order)
            ->with('success', 'Refund penuh dicatat selesai setelah transfer manual dikonfirmasi.');
    }

    public function restockCancelledOrder(Request $request, Order $order): RedirectResponse
    {
        $validated = $request->validate([
            'reason' => ['required', 'string', 'max:1000'],
        ]);
        $actor = $request->user('admin');

        $quantity = DB::transaction(function () use ($order, $validated, $actor): int {
            $lockedOrder = Order::query()->lockForUpdate()->findOrFail($order->id);

            if ($lockedOrder->status !== Order::STATUS_CANCELLED) {
                throw ValidationException::withMessages([
                    'reason' => 'Stok hanya dapat dikembalikan untuk pesanan yang sudah dibatalkan.',
                ]);
            }

            return $lockedOrder->restockConsumedStock($actor, $validated['reason']);
        }, 3);

        return redirect()
            ->route('admin.orders.show', $order)
            ->with('success', $quantity > 0
                ? 'Stok produk yang dibatalkan telah dicatat kembali.'
                : 'Tidak ada stok terjual yang perlu dikembalikan.');
    }

    /**
     * @return array<int, string>
     */
    private function orderDetailRelations(): array
    {
        return [
            'user',
            'items.product.category',
            'events.actor',
            'cancellationRequests.requester',
            'cancellationRequests.decider',
            'refund.processor',
            'stockReservations.product',
        ];
    }

    private function applyCancellationDecision(
        Order $order,
        OrderCancellationRequest $cancellationRequest,
        User $actor,
        string $decision,
        ?string $decisionNote,
    ): void {
        if ($cancellationRequest->status !== 'pending') {
            throw ValidationException::withMessages([
                'decision' => 'Permintaan pembatalan ini sudah diputuskan.',
            ]);
        }

        if ($decision === 'reject') {
            $cancellationRequest->update([
                'status' => 'rejected',
                'decided_by_user_id' => $actor->id,
                'decision_note' => $decisionNote,
                'decided_at' => now(),
            ]);
            $order->recordEvent(
                'cancellation_rejected',
                $actor,
                newValues: ['request_id' => $cancellationRequest->id, 'status' => 'rejected'],
                metadata: ['reason' => $decisionNote],
            );

            return;
        }

        if ($order->stockReservations()->where('state', 'legacy_unreconciled')->exists()) {
            throw ValidationException::withMessages([
                'decision' => 'Riwayat stok pesanan ini belum direkonsiliasi. Selesaikan rekonsiliasi manual sebelum menyetujui pembatalan.',
            ]);
        }

        if (
            in_array($order->status, [Order::STATUS_SHIPPED, Order::STATUS_COMPLETED, Order::STATUS_CANCELLED], true)
            || $order->payment_status === Order::PAYMENT_UNDER_VERIFICATION
            || $order->payment_review_required
        ) {
            throw ValidationException::withMessages([
                'decision' => 'Pembatalan harus ditolak atau pemeriksaan pembayaran diselesaikan terlebih dahulu.',
            ]);
        }

        $oldValues = [
            'status' => $order->status,
            'payment_status' => $order->payment_status,
        ];
        $order->status = Order::STATUS_CANCELLED;
        $order->cancelled_at = now();

        if ($order->payment_status === Order::PAYMENT_PAID) {
            if ($order->total_amount === null) {
                throw ValidationException::withMessages([
                    'decision' => 'Total pesanan lunas tidak tersedia untuk dibuatkan refund.',
                ]);
            }

            $refund = OrderRefund::query()->firstOrCreate(
                ['order_id' => $order->id],
                [
                    'amount' => Order::rupiahToInt($order->total_amount),
                    'reason' => $cancellationRequest->reason,
                    'status' => 'pending',
                    'requested_at' => now(),
                ],
            );

            if (! $refund->wasRecentlyCreated) {
                throw ValidationException::withMessages([
                    'decision' => 'Refund penuh untuk pesanan ini sudah tercatat.',
                ]);
            }
        } else {
            $order->payment_status = Order::PAYMENT_UNPAID;
            $order->releaseReservedStock($actor, 'order_cancelled_before_payment');
        }

        $order->save();
        $cancellationRequest->update([
            'status' => 'approved',
            'decided_by_user_id' => $actor->id,
            'decision_note' => $decisionNote,
            'decided_at' => now(),
        ]);
        $order->recordEvent(
            'cancellation_approved',
            $actor,
            oldValues: $oldValues,
            newValues: [
                'status' => $order->status,
                'payment_status' => $order->payment_status,
            ],
            metadata: [
                'request_id' => $cancellationRequest->id,
                'reason' => $cancellationRequest->reason,
            ],
        );

        if ($order->payment_status === Order::PAYMENT_PAID) {
            $order->recordEvent(
                'refund_requested',
                $actor,
                newValues: ['refund_status' => 'pending'],
                metadata: ['amount' => $order->refund()->value('amount')],
            );
        }
    }

    private function qrisIsConfigured(): bool
    {
        $qris = config('bakso_pesona.qris', []);
        $imagePath = $qris['image'] ?? null;
        $whatsappNumber = $this->officialWhatsAppNumber();

        return is_string($qris['image'] ?? null)
            && trim($imagePath) !== ''
            && ! str_contains($imagePath, '..')
            && is_file(public_path($imagePath))
            && is_string($qris['merchant_name'] ?? null)
            && trim($qris['merchant_name']) !== ''
            && $whatsappNumber !== null;
    }

    private function officialWhatsAppNumber(): ?string
    {
        $number = preg_replace('/\D/', '', (string) config('bakso_pesona.whatsapp_number'));

        if (str_starts_with($number, '0')) {
            $number = '62'.substr($number, 1);
        } elseif (str_starts_with($number, '8')) {
            $number = '62'.$number;
        }

        return strlen($number) >= 10 && strlen($number) <= 15 ? $number : null;
    }

    private function hasUnreconciledLegacyStock(Order $order): bool
    {
        return $order->stockReservations()
            ->where('state', 'legacy_unreconciled')
            ->exists();
    }

    /**
     * @return array<string, array{message: string, url: ?string}>
     */
    private function whatsappTemplates(Order $order): array
    {
        $order->loadMissing('items');
        $items = $order->items
            ->map(fn ($item): string => '- '.$item->product_name.' x '.$item->quantity)
            ->implode("\n");
        $subtotal = Order::rupiahToInt($order->subtotal);
        $discount = Order::rupiahToInt($order->discount_amount ?? 0);
        $shipping = $order->shipping_cost === null ? null : Order::rupiahToInt($order->shipping_cost);
        $total = $order->total_amount === null ? null : Order::rupiahToInt($order->total_amount);
        $supportNumber = $this->officialWhatsAppNumber();
        $qrisMerchant = trim((string) config('bakso_pesona.qris.merchant_name'));

        $confirmationMessage = implode("\n", array_filter([
            'Halo Kak '.$order->recipient_name.', ini konfirmasi pesanan '.$order->order_number.' dari Bakso Pesona.',
            $items,
            'Subtotal: Rp'.number_format($subtotal, 0, ',', '.'),
            $discount > 0 ? 'Diskon: -Rp'.number_format($discount, 0, ',', '.') : null,
            $shipping === null ? null : 'Ongkir: Rp'.number_format($shipping, 0, ',', '.'),
            $total === null ? null : 'Total akhir: Rp'.number_format($total, 0, ',', '.'),
            'Ongkir dan total akhir ditetapkan oleh admin. Instruksi pembayaran menyusul setelah total dihitung.',
        ]));

        $instructionLines = [
            'Instruksi pembayaran Bakso Pesona untuk pesanan '.$order->order_number,
            $order->recipient_name !== '' ? 'Nama penerima: '.$order->recipient_name : null,
            $items,
            'Subtotal: Rp'.number_format($subtotal, 0, ',', '.'),
            $discount > 0 ? 'Diskon: -Rp'.number_format($discount, 0, ',', '.') : null,
            $shipping === null ? null : 'Ongkir: Rp'.number_format($shipping, 0, ',', '.'),
            $total === null ? null : 'Total akhir: Rp'.number_format($total, 0, ',', '.'),
            'Pindai QRIS statis resmi Bakso Pesona pada halaman detail pesanan.',
            $qrisMerchant !== '' ? 'Pastikan nama merchant yang tampil adalah '.$qrisMerchant.'.' : null,
            'Masukkan nominal pembayaran tepat sesuai total akhir. Jangan membulatkan atau menambah nominal.',
            'Tenggat 24 jam dimulai saat admin mencatat bahwa pesan ini benar-benar telah dikirim.',
            $supportNumber !== null ? 'Jika transaksi bermasalah, hubungi admin Bakso Pesona di +'.$supportNumber.'.' : 'Jika transaksi bermasalah, hubungi admin melalui kontak resmi Bakso Pesona.',
            'Pembayaran baru dianggap lunas setelah admin mencocokkan transaksi aktual.',
        ];
        $instructionMessage = implode("\n", array_filter($instructionLines));

        $completedMessage = implode("\n", [
            'Pesanan '.$order->order_number.' dari Bakso Pesona telah ditandai selesai.',
            'Apakah pesanan sudah diterima dengan baik? Kami menghargai penilaian Anda.',
            'Beri rating: '.route('orders.show', $order).'#rating',
        ]);

        return [
            'confirmation' => [
                'message' => $confirmationMessage,
                'url' => $this->customerWhatsAppUrl($order, $confirmationMessage),
            ],
            'payment_instruction' => [
                'message' => $instructionMessage,
                'url' => $total !== null && $this->qrisIsConfigured()
                    ? $this->customerWhatsAppUrl($order, $instructionMessage)
                    : null,
            ],
            'completed' => [
                'message' => $completedMessage,
                'url' => $this->customerWhatsAppUrl($order, $completedMessage),
            ],
        ];
    }

    private function customerWhatsAppUrl(Order $order, string $message): ?string
    {
        $phone = preg_replace('/\D/', '', $order->phone);

        if (str_starts_with($phone, '0')) {
            $phone = '62'.substr($phone, 1);
        } elseif (str_starts_with($phone, '8')) {
            $phone = '62'.$phone;
        }

        if (strlen($phone) < 10 || strlen($phone) > 15) {
            return null;
        }

        return 'https://wa.me/'.$phone.'?text='.rawurlencode($message);
    }
}
