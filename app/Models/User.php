<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    protected $table = 'users';
    protected $primaryKey = 'id';

    protected $fillable = [
        'name',
        'full_name',
        'email',
        'password',
        'phone',
        'role', // 'USER', 'ADMIN'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = ['role' => 'string', 'password' => 'hashed'];

    // Một người dùng có thể có nhiều đơn đặt vé (@OneToMany)
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'user_id', 'id');
    }

    // Hàm tiện ích kiểm tra xem tài khoản có phải Admin không
    public function isAdmin(): bool
    {
        return strtoupper((string) $this->role) === 'ADMIN';
    }

    public function scopeAdmins(Builder $query): Builder { return $query->where('role', 'ADMIN'); }
}
