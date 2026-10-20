<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Team extends Model
{
    protected $table = 'teams';
    protected $primaryKey = 'id';

    protected $fillable = [
        'name',
        'team_name',
        'code',
        'flag_url',
        'group_name',
    ];

    protected $casts = ['name' => 'string', 'team_name' => 'string', 'code' => 'string', 'flag_url' => 'string', 'group_name' => 'string'];

    public function homeMatches(): HasMany { return $this->hasMany(MatchGame::class, 'home_team_id', 'id'); }
    public function awayMatches(): HasMany { return $this->hasMany(MatchGame::class, 'away_team_id', 'id'); }
    public function scopeInGroup(Builder $query, string $group): Builder { return $query->where('group_name', strtoupper($group)); }
    public function scopeWithCode(Builder $query, string $code): Builder { return $query->where('code', strtoupper($code)); }
}
