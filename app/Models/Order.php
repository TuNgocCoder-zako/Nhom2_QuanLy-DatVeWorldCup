<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model
{
    protected $table = 'orders';

    protected $fillable = [
        'user_id',
        'order_code',
        'total_price',
        'total_amount',
        'status',          // 'PENDING', 'PAID', 'CANCELLED'
        'payment_status',  // 'PENDING', 'PAID', 'CANCELLED'
        'payment_method',  // 'SIMULATION'
    ];

    protected $casts = [
        'total_price'  => 'float',
        'total_amount' => 'float',
    ];

    // Đơn hàng thuộc về 1 người dùng (@ManyToOne)
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Một đơn hàng có thể chứa nhiều vé (@OneToMany)
    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class, 'order_id');
    }

    // Một đơn hàng gắn liền với 1 bản ghi thanh toán (@OneToOne)
    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class, 'order_id');
    }
}
