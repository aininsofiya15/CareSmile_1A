<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        $services = [
            [
                'name' => 'Scaling & Polishing',
                'description' => 'Deep cleaning to remove plaque and tartar buildup.',
                'price' => 120.00,
                'duration_minutes' => 30,
                'is_active' => 1
            ],
            [
                'name' => 'Teeth Whitening',
                'description' => 'Laser-based professional whitening treatment.',
                'price' => 850.00,
                'duration_minutes' => 60,
                'is_active' => 1
            ],
            [
                'name' => 'Dental Filling',
                'description' => 'Cavity restoration using composite resin.',
                'price' => 150.00,
                'duration_minutes' => 45,
                'is_active' => 1
            ],
            [
                'name' => 'Tooth Extraction',
                'description' => 'Safe surgical removal of a damaged tooth.',
                'price' => 200.00,
                'duration_minutes' => 45,
                'is_active' => 1
            ],
        ];

        foreach ($services as $service) {
            Service::create($service);
        }
    }
}