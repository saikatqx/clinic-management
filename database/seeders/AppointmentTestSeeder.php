<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Doctor;
use App\Models\Specialty;
use App\Models\Appointment;
use Carbon\Carbon;

class AppointmentTestSeeder extends Seeder
{
    public function run(): void
    {
        $specialty = Specialty::firstOrCreate(['name' => 'Cardiology']);
        $doctor = Doctor::firstOrCreate(
            ['email' => 'doctor1@clinic.com'],
            [
                'specialty_id' => $specialty->id,
                'name' => 'Dr. Ahmed Khan',
                'phone' => '03001234567',
                'qualification' => 'MBBS, MD',
            ]
        );

        // Create test appointments
        Appointment::create([
            'doctor_id' => $doctor->id,
            'patient_name' => 'Ali Hassan',
            'patient_email' => 'ali@example.com',
            'patient_phone' => '03009876543',
            'appointment_date' => Carbon::now()->addDays(1)->setTime(10, 0),
            'status' => 'Confirmed',
            'notes' => 'Regular checkup',
        ]);

        Appointment::create([
            'doctor_id' => $doctor->id,
            'patient_name' => 'Fatima Ahmed',
            'patient_email' => 'fatima@example.com',
            'patient_phone' => '03008765432',
            'appointment_date' => Carbon::now()->addDays(2)->setTime(14, 30),
            'status' => 'Pending',
            'notes' => 'Follow-up visit',
        ]);

        Appointment::create([
            'doctor_id' => $doctor->id,
            'patient_name' => 'Hassan Khan',
            'patient_email' => 'hassan@example.com',
            'patient_phone' => '03007654321',
            'appointment_date' => Carbon::now()->addDays(3)->setTime(11, 0),
            'status' => 'Confirmed',
            'notes' => 'General consultation',
        ]);

        Appointment::create([
            'doctor_id' => $doctor->id,
            'patient_name' => 'Ayesha Ali',
            'patient_email' => 'ayesha@example.com',
            'patient_phone' => '03006543210',
            'appointment_date' => Carbon::now()->subDays(1)->setTime(9, 0),
            'status' => 'Cancelled',
            'notes' => 'Cancelled by patient',
        ]);

        $this->command->info('✅ Test appointments created successfully!');
    }
}
