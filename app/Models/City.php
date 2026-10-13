<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class City extends Model
{
    protected $table = 'cities';

    protected $fillable = [
        'country_id',
        'city_name',
    ];

    // Thành phố thuộc về 1 quốc gia (@ManyToOne)
    public function country(): BelongsTo
    {
        return $this->belongsTo(HostCountry::class, 'country_id');
    }

    // Một thành phố có thể có nhiều sân vận động (@OneToMany)
    public function stadiums(): HasMany
    {
        return $this->hasMany(Stadium::class, 'city_id');
    }
}
