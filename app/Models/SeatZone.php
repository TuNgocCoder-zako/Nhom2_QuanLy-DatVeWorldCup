<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SeatZone extends Model
{
    protected $table = 'seat_zones';
    protected $primaryKey = 'id';

    protected $fillable = [
        'stadium_id',
        'ticket_type_id',
        'zone_name',
        'name',
        'type',
        'total_seats',
        'base_price',
    ];

    protected $casts = ['stadium_id' => 'integer', 'ticket_type_id' => 'integer', 'total_seats' => 'integer', 'base_price' => 'decimal:2'];

    // Khán đài thuộc về 1 sân vận động (@ManyToOne)
    public function stadium(): BelongsTo
    {
        return $this->belongsTo(Stadium::class, 'stadium_id', 'id');
    }

    // Khán đài được gán với 1 hạng vé (@ManyToOne)
    public function ticketType(): BelongsTo
    {
        return $this->belongsTo(TicketType::class, 'ticket_type_id', 'id');
    }

    // Khán đài chứa nhiều ghế ngồi theo từng trận (@OneToMany)
    public function matchSeats(): HasMany
    {
        return $this->hasMany(MatchSeat::class, 'seat_zone_id', 'id');
    }

    public function scopeForStadium(Builder $query, int $stadiumId): Builder { return $query->where('stadium_id', $stadiumId); }
}
