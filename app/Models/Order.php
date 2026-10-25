<?php

namespace App\Models;

use Database\Factories\OrderFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use InvalidArgumentException;

class Order extends Model
{
    /** @use HasFactory<OrderFactory> */
    use HasFactory;

    public const STATUS_PENDING_CONFIRMATION = 'pending_confirmation';

    public const STATUS_AWAITING_PAYMENT = 'awaiting_payment';

    public const STATUS_PROCESSING = 'processing';

    public const STATUS_SHIPPED = 'shipped';

    public const STATUS_COMPLETED = 'completed';

    public const STATUS_CANCELLED = 'cancelled';

    public const PAYMENT_AWAITING_CONFIRMATION = 'awaiting_confirmation';

    public const PAYMENT_AWAITING_PAYMENT = 'awaiting_payment';

    public const PAYMENT_UNDER_VERIFICATION = 'under_verification';

    public const PAYMENT_PAID = 'paid';

    public const PAYMENT_REJECTED = 'rejected';

    public const PAYMENT_UNPAID = 'unpaid';

    public const PAYMENT_REFUNDED = 'refunded';

    public const PAYMENT_EXPIRED = 'expired';

    protected $fillable = [
        'order_number', 'user_id', 'recipient_name', 'phone', 'shipping_address',
        'customer_note', 'subtotal', 'discount_amount', 'shipping_cost', 'total_amount', 'status',
        'payment_method', 'payment_status', 'delivery_landmark', 'checkout_token',
        'total_approved_at', 'payment_instructions_issued_at', 'payment_due_at',
        'payment_review_required', 'cancelled_at',
    ];

    protected function casts(): array
    {
        return [
            'subtotal' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'shipping_cost' => 'decimal:2',
            'total_amount' => 'decimal:2',
            'total_approved_at' => 'datetime',
            'payment_instructions_issued_at' => 'datetime',
            'payment_due_at' => 'datetime',
            'payment_review_required' => 'boolean',
            'cancelled_at' => 'datetime',
        ];
    }

    public static function statusLabels(): array
    {
        return [
            self::STATUS_PENDING_CONFIRMATION => 'Menunggu Konfirmasi',
            self::STATUS_PROCESSING => 'Dikemas',
            self::STATUS_SHIPPED => 'Dikirim',
            self::STATUS_COMPLETED => 'Selesai',
            self::STATUS_CANCELLED => 'Dibatalkan',
        ];
    }

    public static function paymentStatusLabels(): array
    {
        return [
            self::PAYMENT_AWAITING_CONFIRMATION => 'Belum Ditagihkan',
            self::PAYMENT_AWAITING_PAYMENT => 'Menunggu Pembayaran',
            self::PAYMENT_UNDER_VERIFICATION => 'Sedang Diverifikasi',
            self::PAYMENT_PAID => 'Sudah Dibayar',
            self::PAYMENT_REJECTED => 'Pembayaran Ditolak',
            self::PAYMENT_UNPAID => 'Belum Dibayar',
            self::PAYMENT_REFUNDED => 'Dana Dikembalikan (Refund)',
            self::PAYMENT_EXPIRED => 'Kedaluwarsa',
        ];
    }

    public static function rupiahToInt(string|int|float $amount): int
    {
        $amount = (string) $amount;

        if (! preg_match('/^([0-9]+)(?:\\.0{1,2})?$/D', $amount, $matches)) {
            throw new InvalidArgumentException('Nominal Rupiah harus berupa bilangan bulat non-negatif.');
        }

        $normalizedAmount = ltrim($matches[1], '0') ?: '0';
        $maximumAmount = (string) PHP_INT_MAX;

        if (
            strlen($normalizedAmount) > strlen($maximumAmount)
            || (strlen($normalizedAmount) === strlen($maximumAmount) && strcmp($normalizedAmount, $maximumAmount) > 0)
        ) {
            throw new InvalidArgumentException('Nominal Rupiah melebihi batas bilangan bulat.');
        }

        return (int) $normalizedAmount;
    }

    public function getStatusLabelAttribute(): string
    {
        return self::statusLabels()[$this->status] ?? str($this->status)->replace('_', ' ')->title();
    }

    public function getPaymentStatusLabelAttribute(): string
    {
        return self::paymentStatusLabels()[$this->payment_status] ?? str($this->payment_status)->replace('_', ' ')->title();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function events(): HasMany
    {
        return $this->hasMany(OrderEvent::class)->orderBy('created_at')->orderBy('id');
    }

    public function stockReservations(): HasMany
    {
        return $this->hasMany(OrderStockReservation::class);
    }

    public function cancellationRequests(): HasMany
    {
        return $this->hasMany(OrderCancellationRequest::class)->latest();
    }

    public function refund(): HasOne
    {
        return $this->hasOne(OrderRefund::class);
    }

    public function rating(): HasOne
    {
        return $this->hasOne(OrderRating::class);
    }

    public function recordEvent(
        string $eventType,
        ?User $actor = null,
        ?array $oldValues = null,
        ?array $newValues = null,
        array $metadata = [],
    ): OrderEvent {
        return $this->events()->create([
            'actor_user_id' => $actor?->id,
            'actor_type' => $actor?->role ?? 'system',
            'event_type' => $eventType,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'metadata' => $metadata === [] ? null : $metadata,
        ]);
    }

    public function consumeReservedStock(?User $actor = null): void
    {
        $reservations = $this->stockReservations()
            ->where('state', 'reserved')
            ->lockForUpdate()
            ->get();

        foreach ($reservations as $reservation) {
            $reservation->update([
                'state' => 'consumed',
                'consumed_at' => now(),
            ]);

            ProductStockMovement::create([
                'product_id' => $reservation->product_id,
                'order_id' => $this->id,
                'order_item_id' => $reservation->order_item_id,
                'actor_user_id' => $actor?->id,
                'movement_type' => 'reservation_consumed',
                'direction' => 0,
                'quantity' => $reservation->quantity,
            ]);
        }
    }

    public function releaseReservedStock(?User $actor, string $reason): int
    {
        $reservations = $this->stockReservations()
            ->where('state', 'reserved')
            ->lockForUpdate()
            ->get();

        $releasedQuantity = 0;

        foreach ($reservations as $reservation) {
            if ($reservation->product_id !== null) {
                $product = Product::query()
                    ->lockForUpdate()
                    ->find($reservation->product_id);

                if ($product) {
                    $product->increment('stock', $reservation->quantity);
                }
            }

            $reservation->update([
                'state' => 'released',
                'released_at' => now(),
            ]);

            ProductStockMovement::create([
                'product_id' => $reservation->product_id,
                'order_id' => $this->id,
                'order_item_id' => $reservation->order_item_id,
                'actor_user_id' => $actor?->id,
                'movement_type' => 'reservation_released',
                'direction' => 1,
                'quantity' => $reservation->quantity,
                'metadata' => ['reason' => $reason],
            ]);

            $releasedQuantity += $reservation->quantity;
        }

        if ($releasedQuantity > 0) {
            $this->recordEvent(
                'stock_reservations_released',
                $actor,
                metadata: ['reason' => $reason, 'quantity' => $releasedQuantity],
            );
        }

        return $releasedQuantity;
    }

    public function restockConsumedStock(User $actor, string $reason): int
    {
        $reservations = $this->stockReservations()
            ->where('state', 'consumed')
            ->lockForUpdate()
            ->get();

        $restockedQuantity = 0;

        foreach ($reservations as $reservation) {
            if ($reservation->product_id !== null) {
                $product = Product::query()
                    ->lockForUpdate()
                    ->find($reservation->product_id);

                if ($product) {
                    $product->increment('stock', $reservation->quantity);
                }
            }

            $reservation->update([
                'state' => 'released',
                'released_at' => now(),
            ]);

            ProductStockMovement::create([
                'product_id' => $reservation->product_id,
                'order_id' => $this->id,
                'order_item_id' => $reservation->order_item_id,
                'actor_user_id' => $actor->id,
                'movement_type' => 'cancelled_order_restock',
                'direction' => 1,
                'quantity' => $reservation->quantity,
                'metadata' => ['reason' => $reason],
            ]);

            $restockedQuantity += $reservation->quantity;
        }

        if ($restockedQuantity > 0) {
            $this->recordEvent(
                'cancelled_order_stock_restocked',
                $actor,
                metadata: ['reason' => $reason, 'quantity' => $restockedQuantity],
            );
        }

        return $restockedQuantity;
    }
}
