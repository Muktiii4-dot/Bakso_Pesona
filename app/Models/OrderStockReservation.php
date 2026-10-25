<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderStockReservation extends Model
{
    protected $fillable = [
        'order_id',
        'order_item_id',
        'product_id',
        'quantity',
        'state',
        'reserved_at',
        'consumed_at',
        'released_at',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'reserved_at' => 'datetime',
            'consumed_at' => 'datetime',
            'released_at' => 'datetime',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function orderItem(): BelongsTo
    {
        return $this->belongsTo(OrderItem::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
