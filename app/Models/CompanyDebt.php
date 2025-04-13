<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CompanyDebt extends Model
{
    use HasFactory;

    protected $fillable = ['creditor_name', 'amount', 'paid', 'due_date', 'note'];

    public function payments()
    {
        return $this->hasMany(CompanyDebtPayment::class);
    }

    public function remainingAmount()
    {
        return $this->amount - $this->paid;
    }
}