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

    public function getImageUrlAttribute(): string
    {
        $photos = [
            'Santiago Bernabeu' => 'https://images.unsplash.com/photo-1577223625816-7546f13df25d?auto=format&fit=crop&w=800&q=80',
            'Spotify Camp Nou' => 'https://images.unsplash.com/photo-1522778119026-d647f0596c20?auto=format&fit=crop&w=800&q=80',
            'Estadio La Cartuja' => 'https://images.unsplash.com/photo-1518091043644-c1d4457512c6?auto=format&fit=crop&w=800&q=80',
            'San Mames' => 'https://images.unsplash.com/photo-1574629810360-7efbbe195018?auto=format&fit=crop&w=800&q=80',
            'Estadio da Luz' => 'https://images.unsplash.com/photo-1508098682722-e99c43a406b2?auto=format&fit=crop&w=800&q=80',
            'Estadio do Dragao' => 'https://images.unsplash.com/photo-1486286701208-1d58e9338013?auto=format&fit=crop&w=800&q=80',
            'Grand Stade de Casablanca' => 'https://images.unsplash.com/photo-1516450360452-9312f5e86fc7?auto=format&fit=crop&w=800&q=80',
            'Stade Prince Moulay Abdellah' => 'https://images.unsplash.com/photo-1534438327276-14e5300c3a48?auto=format&fit=crop&w=800&q=80',
        ];

        $name = $this->stadium_name ?? ($this->name ?? '');
        return $photos[$name] ?? 'https://images.unsplash.com/photo-1508098682722-e99c43a406b2?auto=format&fit=crop&w=800&q=80';
    }

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
