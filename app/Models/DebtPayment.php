<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DebtPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'debt_id',
        'amount',
        'payment_date',
        'note',
    ];

    public function debt()
    {
        return $this->belongsTo(Debt::class);
    }

    protected $casts = [
        'payment_date' => 'date',
    ];
}