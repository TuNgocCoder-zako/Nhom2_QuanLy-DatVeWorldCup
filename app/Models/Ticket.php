<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Ticket extends Model
{
    protected $table = 'tickets';
    protected $primaryKey = 'id';

    protected $fillable = [
        'order_id',
        'match_id',
        'match_seat_id',
        'ticket_code',
        'price',
        'status', // 'BOOKED', 'VALID', 'CANCELLED'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'order_id' => 'integer', 'match_id' => 'integer', 'match_seat_id' => 'integer',
        'check_in_at' => 'datetime',
    ];

    // Vé thuộc về đơn hàng nào (@ManyToOne)
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id', 'id');
    }

    // Vé thuộc về trận đấu nào (@ManyToOne)
    public function match(): BelongsTo
    {
        return $this->belongsTo(MatchGame::class, 'match_id', 'id');
    }

    // Vé gắn liền với chiếc ghế nào (@ManyToOne / @OneToOne)
    public function matchSeat(): BelongsTo
    {
        return $this->belongsTo(MatchSeat::class, 'match_seat_id', 'id');
    }

    public function scopeValid(Builder $query): Builder { return $query->where('status', 'VALID'); }
    public function scopeForOrder(Builder $query, int $orderId): Builder { return $query->where('order_id', $orderId); }
}
