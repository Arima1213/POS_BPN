<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Unit;
use App\Models\Units;

class UnitSeeder extends Seeder
{
    public function run(): void
    {
        $units = [
            ['name' => 'Kilogram', 'short' => 'kg'],
            ['name' => 'Gram', 'short' => 'g'],
            ['name' => 'Liter', 'short' => 'l'],
            ['name' => 'Mililiter', 'short' => 'ml'],
            ['name' => 'Meter', 'short' => 'm'],
            ['name' => 'Centimeter', 'short' => 'cm'],
            ['name' => 'Milimeter', 'short' => 'mm'],
            ['name' => 'Jam', 'short' => 'h'],
            ['name' => 'Menit', 'short' => 'min'],
            ['name' => 'Detik', 'short' => 's'],
            ['name' => 'Hari', 'short' => 'd'],
            ['name' => 'Minggu', 'short' => 'w'],
            ['name' => 'Bulan', 'short' => 'mo'],
            ['name' => 'Tahun', 'short' => 'y'],
            ['name' => 'Unit', 'short' => 'unit'],
            ['name' => 'Orang', 'short' => 'org'],
            ['name' => 'Paket', 'short' => 'pkt'],
            ['name' => 'Set', 'short' => 'set'],
            ['name' => 'Lembar', 'short' => 'lbr'],
            ['name' => 'Dus', 'short' => 'dus'],
        ];

        foreach ($units as $unit) {
            Units::create($unit);
        }
    }
}
