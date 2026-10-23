<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
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
        'match_date', 'home_score', 'away_score', 'match_status',
    ];

    protected $primaryKey = 'id';
    protected $casts = ['stadium_id' => 'integer', 'home_team_id' => 'integer', 'away_team_id' => 'integer', 'match_time' => 'datetime', 'match_date' => 'datetime', 'home_score' => 'integer', 'away_score' => 'integer'];

    public function homeTeam(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'home_team_id', 'id');
    }

    public function awayTeam(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'away_team_id', 'id');
    }

    public function stadium(): BelongsTo
    {
        return $this->belongsTo(Stadium::class, 'stadium_id', 'id');
    }

    public function seats(): HasMany
    {
        return $this->hasMany(MatchSeat::class, 'match_id', 'id');
    }

    public function matchSeats(): HasMany { return $this->hasMany(MatchSeat::class, 'match_id', 'id'); }
    public function tickets(): HasMany { return $this->hasMany(Ticket::class, 'match_id', 'id'); }

    public function getAvailableSeatsCountAttribute(): int
    {
        return $this->matchSeats()->where('status', 'AVAILABLE')->count();
    }

    public function getLowestPriceAttribute(): float
    {
        $min = $this->matchSeats()->where('status', 'AVAILABLE')->min('price');
        return $min ? (float)$min : 1200000.0;
    }

    public function getHomeTeamNameAttribute(): string
    {
        return $this->homeTeam?->name ?? 'Chờ xác định';
    }

    public function getAwayTeamNameAttribute(): string
    {
        return $this->awayTeam?->name ?? 'Chờ xác định';
    }

    public function getHomeTeamCodeAttribute(): string
    {
        return $this->homeTeam?->code ?? 'TBD';
    }

    public function getAwayTeamCodeAttribute(): string
    {
        return $this->awayTeam?->code ?? 'TBD';
    }

    public function getHomeTeamFlagUrlAttribute(): string
    {
        return $this->homeTeam?->flag_url ?? 'https://flagcdn.com/w80/un.png';
    }

    public function getAwayTeamFlagUrlAttribute(): string
    {
        return $this->awayTeam?->flag_url ?? 'https://flagcdn.com/w80/un.png';
    }
    public function scopeUpcoming(Builder $query): Builder { return $query->where('status', 'UPCOMING')->where('match_time', '>=', now()); }
    public function scopeLive(Builder $query): Builder { return $query->where('status', 'LIVE'); }
    public function scopeFinished(Builder $query): Builder { return $query->where('status', 'FINISHED'); }
    public function scopeByStage(Builder $query, string $stage): Builder { return $query->where('stage', strtoupper($stage)); }
}
