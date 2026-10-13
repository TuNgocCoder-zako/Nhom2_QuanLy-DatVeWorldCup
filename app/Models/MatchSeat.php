<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class MatchSeat extends Model
{
    protected $table = 'match_seats';

    protected $fillable = [
        'match_id',
        'seat_zone_id',
        'seat_code',
        'price',
        'status',
    ];

    public function match(): BelongsTo
    {
        return $this->belongsTo(MatchGame::class, 'match_id');
    }

    public function zone(): BelongsTo
    {
        return $this->belongsTo(SeatZone::class, 'seat_zone_id');
    }

    public function scopeAvailable(Builder $query): Builder
    {
        return $query->where('status', 'AVAILABLE');
    }
}
