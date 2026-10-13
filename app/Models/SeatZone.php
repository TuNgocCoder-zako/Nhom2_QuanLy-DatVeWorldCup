<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SeatZone extends Model
{
    protected $table = 'seat_zones';

    protected $fillable = [
        'stadium_id',
        'ticket_type_id',
        'zone_name',
        'type',
        'total_seats',
        'base_price',
    ];

    // Khán đài thuộc về 1 sân vận động (@ManyToOne)
    public function stadium(): BelongsTo
    {
        return $this->belongsTo(Stadium::class, 'stadium_id');
    }

    // Khán đài được gán với 1 hạng vé (@ManyToOne)
    public function ticketType(): BelongsTo
    {
        return $this->belongsTo(TicketType::class, 'ticket_type_id');
    }

    // Khán đài chứa nhiều ghế ngồi theo từng trận (@OneToMany)
    public function matchSeats(): HasMany
    {
        return $this->hasMany(MatchSeat::class, 'seat_zone_id');
    }
}
