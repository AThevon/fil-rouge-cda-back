<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Enums\UserRole;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'athevon',
            'email' => 'athevon@gmail.com',
            'password' => Hash::make('admin123'),
            'role' => UserRole::ADMIN,
        ]);
        User::create([
            'name' => 'test',
            'email' => 'test@gmail.com',
            'password' => Hash::make('teste123'),
            'role' => UserRole::USER, 
        ]);
    }
}