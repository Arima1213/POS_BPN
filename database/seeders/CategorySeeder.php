<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'Beras', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Benih Padi', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Benih Jagung', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Pupuk Organik', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Pupuk Kimia', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Pestisida Cair', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Pestisida Bubuk', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Alat Pertanian', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Sistem Irigasi', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Bibit Sayuran', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Bibit Buah', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Pakan Ternak', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Obat Ternak', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Peralatan Kebun', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Kompos', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Mulsa Plastik', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Jaring Tanaman', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Media Tanam', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Bibit Tanaman Hias', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Pupuk Hayati', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Alat Semprot', 'created_at' => now(), 'updated_at' => now()],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}