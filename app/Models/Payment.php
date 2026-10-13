<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    protected $table = 'payments';

    protected $fillable = [
        'order_id',
        'payment_method',
        'transaction_id',
        'amount',
        'status', // 'PENDING', 'SUCCESS', 'FAILED'
    ];

    protected $casts = [
        'amount' => 'float',
    ];

    // Giao dịch thanh toán thuộc về 1 đơn hàng (@BelongsTo)
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id');
    }
}
