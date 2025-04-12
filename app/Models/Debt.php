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
        'note',
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
}
