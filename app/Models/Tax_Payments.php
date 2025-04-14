<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TaxPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'tax_id',
        'amount',
        'tax_invoice_img',
        'payment_date',
        'tax_invoice_number',
        'payment_method',
        'note'
    ];

    public function tax()
    {
        return $this->belongsTo(Tax::class);
    }

    protected static function booted(): void
    {
        static::created(function ($payment) {
            $payment->tax->updateStatus();
        });

        static::deleted(function ($payment) {
            $payment->tax->updateStatus();
        });
    }
}