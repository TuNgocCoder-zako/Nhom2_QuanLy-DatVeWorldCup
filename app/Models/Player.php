<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Player extends Model
{
    protected $table = 'players';
    protected $primaryKey = 'id';

    protected $fillable = [
        'team_id',
        'name',
        'shirt_number',
        'position',
    ];

    protected $casts = [
        'team_id'      => 'integer',
        'name'         => 'string',
        'shirt_number' => 'integer',
        'position'     => 'string',
    ];

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'team_id', 'id');
    }

    /**
     * Vị trí hiển thị tiếng Việt
     */
    public function getPositionTextAttribute(): string
    {
        return match($this->position) {
            'GK' => 'Thủ môn',
            'DF' => 'Hậu vệ',
            'MF' => 'Tiền vệ',
            'FW' => 'Tiền đạo',
            default => $this->position ?? 'Cầu thủ',
        };
    }
}
