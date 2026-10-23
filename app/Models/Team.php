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

    public function getFlagUrlAttribute($value): ?string
    {
        if (!empty($value)) {
            return $value;
        }

        // FIFA 3-letter to ISO 2-letter mapping for high-definition flag assets
        $isoMap = [
            'ESP' => 'es', 'POR' => 'pt', 'MAR' => 'ma', 'VIE' => 'vn',
            'JPN' => 'jp', 'KOR' => 'kr', 'AUS' => 'au', 'KSA' => 'sa',
            'FRA' => 'fr', 'GER' => 'de', 'ITA' => 'it', 'NED' => 'nl',
            'ENG' => 'gb-eng', 'BEL' => 'be', 'CRO' => 'hr', 'SUI' => 'ch',
            'BRA' => 'br', 'ARG' => 'ar', 'URU' => 'uy', 'COL' => 'co',
            'USA' => 'us', 'MEX' => 'mx', 'CAN' => 'ca', 'CRC' => 'cr',
            'SEN' => 'sn', 'NGA' => 'ng', 'EGY' => 'eg', 'GHA' => 'gh',
            'DEN' => 'dk', 'AUT' => 'at', 'POL' => 'pl', 'SWE' => 'se',
            'ECU' => 'ec', 'CHI' => 'cl', 'PER' => 'pe', 'PAR' => 'py',
            'IRN' => 'ir', 'IRQ' => 'iq', 'UZB' => 'uz', 'QAT' => 'qa',
            'CMR' => 'cm', 'MLI' => 'ml', 'CIV' => 'ci', 'TUN' => 'tn',
            'ALG' => 'dz', 'TUR' => 'tr', 'UKR' => 'ua', 'SRB' => 'rs',
        ];

        $code = strtoupper(trim($this->code ?? ''));
        $iso = $isoMap[$code] ?? strtolower(substr($code, 0, 2));

        return "https://flagcdn.com/w80/{$iso}.png";
    }

    public function homeMatches(): HasMany { return $this->hasMany(MatchGame::class, 'home_team_id', 'id'); }
    public function awayMatches(): HasMany { return $this->hasMany(MatchGame::class, 'away_team_id', 'id'); }
    public function scopeInGroup(Builder $query, string $group): Builder { return $query->where('group_name', strtoupper($group)); }
    public function scopeWithCode(Builder $query, string $code): Builder { return $query->where('code', strtoupper($code)); }
}
