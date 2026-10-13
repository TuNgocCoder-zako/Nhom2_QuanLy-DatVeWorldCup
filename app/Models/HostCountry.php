<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class HostCountry extends Model
{
    protected $table = 'host_countries';

    protected $fillable = [
        'country_name',
        'country_code',
    ];

    // Một quốc gia đăng cai có nhiều thành phố tổ chức (@OneToMany)
    public function cities(): HasMany
    {
        return $this->hasMany(City::class, 'country_id');
    }
}
