<?php

namespace Tests\Unit;

use App\Models\Doctor;
use App\Models\DoctorAvailability;
use App\Models\Specialty;
use Tests\TestCase;

class DoctorAvailabilityTest extends TestCase
{
    public function test_doctor_has_availabilities()
    {
        $specialty = Specialty::firstOrCreate(['name' => 'Cardiology']);
        $doctor = Doctor::create([
            'specialty_id' => $specialty->id,
            'name' => 'Dr. Test',
            'email' => 'test@clinic.com',
            'phone' => '123456789',
        ]);

        DoctorAvailability::create([
            'doctor_id' => $doctor->id,
            'day_of_week' => 1,
            'start_time' => '09:00',
            'end_time' => '17:00',
            'slot_minutes' => 30,
        ]);

        $this->assertCount(1, $doctor->availabilities);
        $this->assertEquals(30, $doctor->availabilities->first()->slot_minutes);
    }

    public function test_availability_validation()
    {
        $specialty = Specialty::firstOrCreate(['name' => 'Cardiology']);
        $doctor = Doctor::create([
            'specialty_id' => $specialty->id,
            'name' => 'Dr. Test',
            'email' => 'test@clinic.com',
            'phone' => '123456789',
        ]);

        $av = DoctorAvailability::create([
            'doctor_id' => $doctor->id,
            'day_of_week' => 2,
            'start_time' => '09:00',
            'end_time' => '17:00',
            'slot_minutes' => 30,
        ]);

        $this->assertTrue($av->day_of_week >= 0 && $av->day_of_week <= 6);
    }
}
