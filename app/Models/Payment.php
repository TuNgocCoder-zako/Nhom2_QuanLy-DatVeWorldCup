<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    protected $table = 'payments';
    protected $primaryKey = 'id';

    protected $fillable = [
        'order_id',
        'payment_method',
        'transaction_id',
        'amount',
        'status', // 'PENDING', 'SUCCESS', 'FAILED'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'order_id' => 'integer',
    ];

    // Giao dịch thanh toán thuộc về 1 đơn hàng (@BelongsTo)
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id', 'id');
    }

    public function scopePending(Builder $query): Builder { return $query->where('status', 'PENDING'); }
    public function scopeSuccessful(Builder $query): Builder { return $query->where('status', 'SUCCESS'); }
}
