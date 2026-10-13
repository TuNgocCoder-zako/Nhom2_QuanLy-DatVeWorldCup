<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MatchGame extends Model
{
    protected $table = 'matches';

    protected $fillable = [
        'match_number',
        'stage',
        'stadium_id',
        'home_team_id',
        'away_team_id',
        'match_time',
        'status',
    ];

    public function homeTeam(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'home_team_id');
    }

    public function awayTeam(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'away_team_id');
    }

    public function stadium(): BelongsTo
    {
        return $this->belongsTo(Stadium::class, 'stadium_id');
    }

    public function seats(): HasMany
    {
        return $this->hasMany(MatchSeat::class, 'match_id');
    }
}
