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
    // --- ADMIN ---
    User::create([
        'name'     => 'Admin User',
        'email'    => 'admin@caresmile.com',
        'password' => Hash::make('password'),
        'role'     => Role::Admin,
    ]);

    // --- DENTISTS ---
    // Dr. Smith (ID: 2)
    User::create([
        'name'     => 'Dr. Smith',
        'email'    => 'smith@caresmile.com',
        'password' => Hash::make('password'),
        'role'     => Role::Dentist,
    ]);

    // Dr. Sarah (ID: 3)
    User::create([
        'name'     => 'Dr. Sarah',
        'email'    => 'sarah@caresmile.com',
        'password' => Hash::make('password'),
        'role'     => Role::Dentist,
    ]);

    // Dr. Ahmad (ID: 4)
    User::create([
        'name'     => 'Dr. Ahmad',
        'email'    => 'ahmad@caresmile.com',
        'password' => Hash::make('password'),
        'role'     => Role::Dentist,
    ]);

    // --- PATIENTS ---
    $patients = [
        ['name' => 'John Doe', 'email' => 'john@gmail.com'],
        ['name' => 'Siti Aminah', 'email' => 'siti@gmail.com'],
        ['name' => 'Wahidah Ali', 'email' => 'wahidah@gmail.com'],
        ['name' => 'Najihah Bakar', 'email' => 'najihah@gmail.com'],
    ];

    foreach ($patients as $p) {
        User::create([
            'name'     => $p['name'],
            'email'    => $p['email'],
            'password' => Hash::make('password'),
            'role'     => Role::Patient,
        ]);
    }

    $this->call([
        ServiceSeeder::class,
        ClinicalSeeder::class,
    ]);
}
}

