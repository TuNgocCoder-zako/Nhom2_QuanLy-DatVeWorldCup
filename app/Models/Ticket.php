<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Ticket extends Model
{
    protected $table = 'tickets';

    protected $fillable = [
        'order_id',
        'match_id',
        'match_seat_id',
        'ticket_code',
        'price',
        'status', // 'BOOKED', 'VALID', 'CANCELLED'
    ];

    protected $casts = [
        'price' => 'float',
    ];

    // Vé thuộc về đơn hàng nào (@ManyToOne)
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    // Vé thuộc về trận đấu nào (@ManyToOne)
    public function match(): BelongsTo
    {
        return $this->belongsTo(MatchGame::class, 'match_id');
    }

    // Vé gắn liền với chiếc ghế nào (@ManyToOne / @OneToOne)
    public function matchSeat(): BelongsTo
    {
        return $this->belongsTo(MatchSeat::class, 'match_seat_id');
    }
}
