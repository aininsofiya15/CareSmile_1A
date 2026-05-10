<?php

namespace Database\Seeders;

use App\Models\DoctorSchedule;
use App\Models\Appointment;
use Illuminate\Database\Seeder;

class ClinicalSeeder extends Seeder
{
    public function run(): void
{
    $dentistIds = [2, 3, 4]; // Smith, Sarah, Ahmad
    $patientIds = [5, 6, 7, 8]; // John, Siti, Wahidah, Najihah
    $services = ['Scaling & Polishing', 'Teeth Whitening', 'Dental Filling'];

    foreach ($dentistIds as $dId) {
        // Create 3 days of schedules for each dentist
        for ($i = 0; $i < 3; $i++) {
            $date = now()->addDays($i)->toDateString();
            
            \App\Models\DoctorSchedule::create([
                'doctor_id' => $dId, 
                'working_date' => $date,
                'start_time' => '09:00:00',
                'end_time' => '17:00:00',
                'break_start' => '13:00:00',
                'break_end' => '14:00:00',
                'slot_duration' => 30,
                'is_active' => 1
            ]);

            // Assign a random patient to a random time for this dentist
            \App\Models\Appointment::create([
                'patient_id' => $patientIds[array_rand($patientIds)],
                'doctor_id' => $dId,
                'appointment_date' => $date,
                'appointment_time' => '10:00:00',
                'end_time' => '10:30:00',
                'service' => $services[array_rand($services)],
                'status' => 'scheduled'
            ]);
        }
    }

    // Add a specific "Busy" morning for Dr. Sarah (ID 3)
    \App\Models\Appointment::create([
        'patient_id' => 6,
        'doctor_id' => 3,
        'appointment_date' => now()->toDateString(),
        'appointment_time' => '11:00:00',
        'end_time' => '11:30:00',
        'service' => 'Teeth Whitening',
        'status' => 'scheduled'
    ]);
}
}