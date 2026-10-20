<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Builder;

class MatchSeat extends Model
{
    protected $table = 'match_seats';
    protected $primaryKey = 'id';

    protected $fillable = [
        'match_id',
        'seat_zone_id',
        'seat_code',
        'price',
        'status',
    ];

    protected $casts = ['match_id' => 'integer', 'seat_zone_id' => 'integer', 'price' => 'decimal:2'];

    public function match(): BelongsTo
    {
        return $this->belongsTo(MatchGame::class, 'match_id', 'id');
    }

    public function seatZone(): BelongsTo
    {
        return $this->belongsTo(SeatZone::class, 'seat_zone_id', 'id');
    }

    public function zone(): BelongsTo { return $this->seatZone(); }
    public function ticket(): HasOne { return $this->hasOne(Ticket::class, 'match_seat_id', 'id'); }

    public function scopeAvailable(Builder $query): Builder
    {
        return $query->where('status', 'AVAILABLE');
    }

    public function scopeByZone(Builder $query, int $zoneId): Builder { return $query->where('seat_zone_id', $zoneId); }
}
