<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Pemilik Rental',
            'email' => 'admin@rental.com',
            'password' => Hash::make('admin123'),
            'role' => 'admin'
        ]);

        User::create([
            'name' => 'Kasir Jaga',
            'email' => 'kasir@rental.com',
            'password' => Hash::make('kasir123'),
            'role' => 'kasir'
        ]);
    }
}