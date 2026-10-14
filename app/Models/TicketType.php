<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TicketType extends Model
{
    protected $table = 'ticket_types';
    protected $primaryKey = 'id';

    protected $fillable = [
        'type_name',
        'description',
    ];

    protected $casts = ['type_name' => 'string', 'description' => 'string'];

    // Một loại vé có thể áp dụng cho nhiều khu khán đài (@OneToMany)
    public function seatZones(): HasMany
    {
        return $this->hasMany(SeatZone::class, 'ticket_type_id', 'id');
    }

    public function scopeNamed(Builder $query, string $name): Builder { return $query->where('type_name', $name); }
}
