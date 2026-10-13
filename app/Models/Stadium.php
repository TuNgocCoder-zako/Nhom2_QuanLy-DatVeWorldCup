<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Stadium extends Model
{
    protected $table = 'stadiums';

    protected $fillable = [
        'city_id',
        'name',
        'location',
        'capacity',
    ];

    // Sân vận động thuộc về 1 thành phố (@ManyToOne)
    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class, 'city_id');
    }

    // Một sân có nhiều khu vực khán đài (@OneToMany)
    public function seatZones(): HasMany
    {
        return $this->hasMany(SeatZone::class, 'stadium_id');
    }

    // Một sân có nhiều trận đấu diễn ra (@OneToMany)
    public function matches(): HasMany
    {
        return $this->hasMany(MatchGame::class, 'stadium_id');
    }
}
