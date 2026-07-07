<?php

namespace Tests\Feature;

use App\Models\Doctor;
use App\Models\Specialty;
use App\Models\DoctorAvailability;
use App\Models\Appointment;
use Carbon\Carbon;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AppointmentSlotsTest extends TestCase
{
    use RefreshDatabase;

    protected $doctor;

    public function setUp(): void
    {
        parent::setUp();

        $specialty = Specialty::firstOrCreate(['name' => 'Cardiology']);
        $this->doctor = Doctor::create([
            'specialty_id' => $specialty->id,
            'name' => 'Dr. John Doe',
            'email' => 'john@clinic.com',
            'phone' => '555-1234',
        ]);

        // Add availability for Monday (1)
        DoctorAvailability::create([
            'doctor_id' => $this->doctor->id,
            'day_of_week' => 1,
            'start_time' => '09:00',
            'end_time' => '12:00',
            'slot_minutes' => 30,
        ]);
    }

    public function test_can_fetch_available_slots()
    {
        // Get next Monday
        $date = Carbon::now()->next(Carbon::MONDAY);

        $response = $this->get('/appointment/slots?doctor_id=' . $this->doctor->id . '&date=' . $date->toDateString());
        $response->assertStatus(200);
        $response->assertJsonStructure(['slots']);
    }

    public function test_slot_generation_respects_availability_window()
    {
        $date = Carbon::now()->next(Carbon::MONDAY);

        $response = $this->get('/appointment/slots?doctor_id=' . $this->doctor->id . '&date=' . $date->toDateString());
        $data = $response->json();

        // Should have slots between 9 AM and 12 PM in 30-min intervals = 6 slots
        $this->assertCount(6, $data['slots']);
    }

    public function test_booked_slot_is_not_available()
    {
        $date = Carbon::now()->next(Carbon::MONDAY);
        $appointmentTime = $date->clone()->setTime(10, 0);

        Appointment::create([
            'doctor_id' => $this->doctor->id,
            'patient_name' => 'John Patient',
            'patient_phone' => '555-5555',
            'appointment_date' => $appointmentTime,
            'status' => 'Confirmed',
        ]);

        $response = $this->get('/appointment/slots?doctor_id=' . $this->doctor->id . '&date=' . $date->toDateString());
        $data = $response->json();

        // Should have one less slot (5 instead of 6)
        $this->assertCount(5, $data['slots']);

        // Verify 10:00 AM is NOT in the slots
        $times = array_map(fn($s) => $s['time'], $data['slots']);
        $this->assertNotContains('10:00 AM', $times);
    }

    public function test_prevent_double_booking()
    {
        $date = Carbon::now()->next(Carbon::MONDAY);
        $appointmentTime = $date->clone()->setTime(11, 0);

        $appointment1 = Appointment::create([
            'doctor_id' => $this->doctor->id,
            'patient_name' => 'Patient One',
            'patient_phone' => '555-1111',
            'appointment_date' => $appointmentTime,
            'status' => 'Pending',
        ]);

        $response = $this->post('/appointments', [
            'doctor_id' => $this->doctor->id,
            'patient_name' => 'Patient Two',
            'patient_phone' => '555-2222',
            'appointment_date' => $appointmentTime->toDateTimeString(),
        ]);

        $this->assertEquals(409, $response->status());
        $this->assertStringContainsString('already booked', $response->json('message'));
    }

    public function test_can_book_appointment_successfully()
    {
        $date = Carbon::now()->next(Carbon::MONDAY);
        $appointmentTime = $date->clone()->setTime(9, 30);

        $response = $this->post('/appointments', [
            'doctor_id' => $this->doctor->id,
            'patient_name' => 'New Patient',
            'patient_email' => 'patient@example.com',
            'patient_phone' => '555-9999',
            'appointment_date' => $appointmentTime->toDateTimeString(),
            'notes' => 'Regular checkup',
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure(['message', 'appointment_id']);
        $this->assertDatabaseHas('appointments', [
            'doctor_id' => $this->doctor->id,
            'patient_name' => 'New Patient',
        ]);
    }
}
