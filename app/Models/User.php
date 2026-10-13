<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    protected $table = 'users';

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

    // Một người dùng có thể có nhiều đơn đặt vé (@OneToMany)
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'user_id');
    }

    // Hàm tiện ích kiểm tra xem tài khoản có phải Admin không
    public function isAdmin(): bool
    {
        return strtoupper($this->role) === 'ADMIN';
    }
}
