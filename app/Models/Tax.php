<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Tax extends Model
{
    use HasFactory;

    protected $fillable = [
        'tax_type',
        'tax_period',
        'npwp',
        'amount_due',
        'amount_paid',
        'status',
        'due_date',
        'description',
    ];

    public function payments()
    {
        return $this->hasMany(TaxPayment::class);
    }

    public function getTotalPaidAttribute(): float
    {
        return $this->payments()->sum('amount_paid');
    }

    public function getRemainingAmountAttribute(): float
    {
        return $this->amount_due - $this->total_paid;
    }

    public function updateStatus(): void
    {
        $totalPaid = $this->total_paid;

        if ($totalPaid >= $this->amount_due) {
            $this->status = 'Lunas';
        } elseif ($totalPaid > 0) {
            $this->status = 'Sebagian Dibayar';
        } elseif (now()->gt($this->due_date)) {
            $this->status = 'Nunggak';
        } else {
            $this->status = 'Belum Dibayar';
        }

        $this->save();
    }
}