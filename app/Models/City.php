<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/** A host city and its stadiums. */
class City extends Model
{
    protected $table = 'cities';
    protected $primaryKey = 'id';
    protected $fillable = ['country_id', 'city_name'];
    protected $casts = ['country_id' => 'integer'];

    public function country(): BelongsTo
    {
        return $this->belongsTo(HostCountry::class, 'country_id', 'id');
    }
    public function stadiums(): HasMany
    {
        return $this->hasMany(Stadium::class, 'city_id', 'id');
    }
    public function scopeForCountry(Builder $query, int $countryId): Builder
    {
        return $query->where('country_id', $countryId);
    }
}
