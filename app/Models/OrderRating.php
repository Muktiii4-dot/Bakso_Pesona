<?php

namespace App\Models;

use Database\Factories\OrderRatingFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderRating extends Model
{
    /** @use HasFactory<OrderRatingFactory> */
    use HasFactory;

    protected $fillable = [
        'order_id',
        'customer_user_id',
        'rating',
        'original_comment',
        'edited_comment',
        'moderation_status',
        'moderated_by_user_id',
        'moderation_note',
        'moderated_at',
    ];

    protected function casts(): array
    {
        return [
            'rating' => 'integer',
            'moderated_at' => 'datetime',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_user_id');
    }

    public function moderator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'moderated_by_user_id');
    }
}
