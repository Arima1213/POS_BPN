<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Asset extends Model
{
    protected $fillable = [
        'asset_name',
        'asset_code',
        'description',
        'purchase_price',
        'purchase_date',
        'useful_life_years',
        'residual_value',
        'location',
        'category',
        'status',
        'journal_entry_id',
        'accumulated_depreciation',
        'depreciation_start_date',
        'is_fully_depreciated',
        'depreciation_method',
        'book_value',
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'purchase_price' => 'decimal:2',
        'residual_value' => 'decimal:2',
    ];

    public function calculateMonthlyDepreciation(): float
    {
        $cost = $this->purchase_price;
        $residual = $this->residual_value ?? 0;
        $usefulLifeMonths = $this->useful_life_years * 12;

        return ($cost - $residual) / $usefulLifeMonths;
    }

    public function journalEntry()
    {
        return $this->belongsTo(JournalEntry::class);
    }
}