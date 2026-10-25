<?php

namespace App\Models;

use Database\Factories\OrderRefundFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderRefund extends Model
{
    /** @use HasFactory<OrderRefundFactory> */
    use HasFactory;

    protected $fillable = [
        'order_id',
        'amount',
        'reason',
        'status',
        'processed_by_user_id',
        'transfer_reference',
        'requested_at',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'integer',
            'requested_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function processor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by_user_id');
    }
}
