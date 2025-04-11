<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Roles;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UnitSeeder::class,
            RoleSeeder::class,
        ]);

        $ownerRole = Roles::where('name', 'Owner')->first();
        $adminRole = Roles::where('name', 'Admin')->first();
        $cashierRole = Roles::where('name', 'Cashier')->first();

        User::create([
            'name' => 'Owner User',
            'email' => 'owner@example.com',
            'password' => Hash::make('password'),
            'role_id' => $ownerRole->id,
        ]);

        User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role_id' => $adminRole->id,
        ]);

        User::create([
            'name' => 'Cashier User',
            'email' => 'kasir@example.com',
            'password' => Hash::make('password'),
            'role_id' => $cashierRole->id,
        ]);
    }
}