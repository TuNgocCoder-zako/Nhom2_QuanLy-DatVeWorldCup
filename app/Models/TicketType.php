<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TicketType extends Model
{
    protected $table = 'ticket_types';

    protected $fillable = [
        'type_name',
        'description',
    ];

    // Một loại vé có thể áp dụng cho nhiều khu khán đài (@OneToMany)
    public function seatZones(): HasMany
    {
        return $this->hasMany(SeatZone::class, 'ticket_type_id');
    }
}
