<?php

namespace App\Models;

use Database\Factories\ProductStockMovementFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductStockMovement extends Model
{
    /** @use HasFactory<ProductStockMovementFactory> */
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'product_id',
        'order_id',
        'order_item_id',
        'actor_user_id',
        'movement_type',
        'direction',
        'quantity',
        'metadata',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'direction' => 'integer',
            'quantity' => 'integer',
            'metadata' => 'array',
            'created_at' => 'datetime',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function orderItem(): BelongsTo
    {
        return $this->belongsTo(OrderItem::class);
    }

    public function actor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'actor_user_id');
    }
}
