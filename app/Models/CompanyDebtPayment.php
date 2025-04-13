<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CompanyDebtPayment extends Model
{
    use HasFactory;

    protected $fillable = ['company_debt_id', 'amount', 'payment_date', 'note'];

    public function companyDebt()
    {
        return $this->belongsTo(CompanyDebt::class);
    }
}
