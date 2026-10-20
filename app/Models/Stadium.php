<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Stadium extends Model
{
    protected $table = 'stadiums';
    protected $primaryKey = 'id';

    protected $fillable = [
        'city_id',
        'name',
        'stadium_name',
        'location',
        'capacity',
    ];

    protected $casts = ['city_id' => 'integer', 'capacity' => 'integer'];

    // Sân vận động thuộc về 1 thành phố (@ManyToOne)
    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class, 'city_id', 'id');
    }

    // Một sân có nhiều khu vực khán đài (@OneToMany)
    public function seatZones(): HasMany
    {
        return $this->hasMany(SeatZone::class, 'stadium_id', 'id');
    }

    // Một sân có nhiều trận đấu diễn ra (@OneToMany)
    public function matches(): HasMany
    {
        return $this->hasMany(MatchGame::class, 'stadium_id', 'id');
    }

    public function scopeInCity(Builder $query, int $cityId): Builder { return $query->where('city_id', $cityId); }
}
