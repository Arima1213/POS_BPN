<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class assets extends Model
{
    protected $fillable = [
        'asset_name',
        'asset_code',
        'category',
        'description',
        'purchase_price',
        'purchase_date',
        'useful_life_years',
        'residual_value',
        'location',
        'status',
        'journal_entry_id',
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'purchase_price' => 'decimal:2',
        'residual_value' => 'decimal:2',
    ];

    public function journalEntry()
    {
        return $this->belongsTo(JournalEntry::class);
    }
}