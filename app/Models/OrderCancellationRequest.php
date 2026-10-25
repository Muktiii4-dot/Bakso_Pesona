<?php

namespace App\Models;

use Database\Factories\OrderCancellationRequestFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderCancellationRequest extends Model
{
    /** @use HasFactory<OrderCancellationRequestFactory> */
    use HasFactory;

    protected $fillable = [
        'order_id',
        'requested_by_user_id',
        'requested_by_role',
        'reason',
        'status',
        'decided_by_user_id',
        'decision_note',
        'decided_at',
    ];

    protected function casts(): array
    {
        return [
            'decided_at' => 'datetime',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by_user_id');
    }

    public function decider(): BelongsTo
    {
        return $this->belongsTo(User::class, 'decided_by_user_id');
    }
}
