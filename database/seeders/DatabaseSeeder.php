<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Enums\Role;
use Illuminate\Support\Facades\Hash;


class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. The Admin
        User::create([
            'name'     => 'Admin User',
            'email'    => 'admin@caresmile.com',
            'password' => Hash::make('password'),
            'role'     => Role::Admin,
        ]);

        // 2. The Dentist
        User::create([
            'name'     => 'Dr. Smith',
            'email'    => 'dentist@caresmile.com',
            'password' => Hash::make('password'),
            'role'     => Role::Dentist,
        ]);

        // 3. The Patient
        User::create([
            'name'     => 'John Doe',
            'email'    => 'patient@caresmile.com',
            'password' => Hash::make('password'),
            'role'     => Role::Patient,
        ]);
    }
}