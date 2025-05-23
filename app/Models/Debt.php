<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Debt extends Model
{
    protected $fillable = [
        'customer_id',
        'transaction_id',
        'amount',
        'paid',
        'due_date',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transactions::class);
    }

    public function getRemainingAttribute(): float
    {
        return $this->amount - $this->paid;
    }

    public function isPaidOff(): bool
    {
        return $this->remaining <= 0;
    }

    public function payments()
    {
        return $this->hasMany(DebtPayment::class, 'debt_id');
    }
}
