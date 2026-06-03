<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Doctor;
use App\Models\DoctorAvailability;

class DoctorAvailabilitySeeder extends Seeder
{
    public function run(): void
    {
        // Get all active doctors
        $doctors = Doctor::where('is_active', true)->orWhere('is_active', 1)->get();

        foreach ($doctors as $doctor) {
            // Mon-Fri: 9 AM - 5 PM, 30-min slots
            for ($day = 1; $day <= 5; $day++) {
                DoctorAvailability::firstOrCreate([
                    'doctor_id' => $doctor->id,
                    'day_of_week' => $day,
                ], [
                    'start_time' => '09:00',
                    'end_time' => '17:00',
                    'slot_minutes' => 30,
                ]);
            }

            // Saturday: 10 AM - 2 PM (half day)
            DoctorAvailability::firstOrCreate([
                'doctor_id' => $doctor->id,
                'day_of_week' => 6,
            ], [
                'start_time' => '10:00',
                'end_time' => '14:00',
                'slot_minutes' => 30,
            ]);

            $this->command->info("Availability seeded for Dr. {$doctor->name}");
        }
    }
}
