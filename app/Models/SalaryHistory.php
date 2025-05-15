<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalaryHistory extends Model
{
    protected $fillable = [
        'salary_id',
        'processed_by',
        'tanggal_pembayaran',
    ];

    // Relasi ke Salary
    public function salary()
    {
        return $this->belongsTo(Salary::class);
    }

    // Relasi ke User (Petugas gaji)
    public function processedBy()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }
}