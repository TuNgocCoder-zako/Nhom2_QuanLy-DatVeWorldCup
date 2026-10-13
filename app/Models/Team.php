<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    protected $table = 'teams';

    protected $fillable = [
        'name',
        'code',
        'flag_url',
        'group_name',
    ];
}
