<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model
{
    protected $table = 'orders';
    protected $primaryKey = 'id';

    protected $fillable = [
        'user_id',
        'order_code',
        'total_price',
        'total_amount',
        'status',          // 'PENDING', 'PAID', 'CANCELLED'
        'payment_status',  // 'PENDING', 'PAID', 'CANCELLED'
        'payment_method',  // 'SIMULATION'
        'hold_expires_at',
    ];

    protected $casts = [
        'total_price'  => 'decimal:2',
        'total_amount' => 'decimal:2',
        'hold_expires_at' => 'datetime',
    ];

    // Đơn hàng thuộc về 1 người dùng (@ManyToOne)
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    // Một đơn hàng có thể chứa nhiều vé (@OneToMany)
    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class, 'order_id', 'id');
    }

    // Một đơn hàng gắn liền với 1 bản ghi thanh toán (@OneToOne)
    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class, 'order_id', 'id');
    }

    public function payments(): HasMany { return $this->hasMany(Payment::class, 'order_id', 'id'); }
    public function scopePending(Builder $query): Builder { return $query->where('status', 'PENDING'); }
    public function scopePaid(Builder $query): Builder { return $query->where('status', 'PAID'); }
    public function scopeCancelled(Builder $query): Builder { return $query->where('status', 'CANCELLED'); }
}
