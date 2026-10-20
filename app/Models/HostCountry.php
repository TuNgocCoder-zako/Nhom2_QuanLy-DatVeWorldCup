<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;

class HostCountry extends Model
{
    protected $table = 'host_countries';
    protected $primaryKey = 'id';

    protected $fillable = [
        'country_name',
        'country_code',
    ];

    protected $casts = ['country_name' => 'string', 'country_code' => 'string'];

    // Một quốc gia đăng cai có nhiều thành phố tổ chức (@OneToMany)
    public function cities(): HasMany
    {
        return $this->hasMany(City::class, 'country_id', 'id');
    }

    public function scopeWithCode(Builder $query, string $countryCode): Builder
    {
        return $query->where('country_code', strtoupper($countryCode));
    }
}
