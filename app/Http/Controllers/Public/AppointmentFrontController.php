<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Appointment;
use App\Models\Doctor;
use Carbon\Carbon;

class AppointmentFrontController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'doctor_id' => 'required|exists:doctors,id',
            'patient_name' => 'required|string|max:255',
            'patient_email' => 'nullable|email|max:255',
            'patient_phone' => 'required|string|max:20',
            'appointment_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        // Prevent double-booking for exact datetime for the same doctor
        $exists = Appointment::where('doctor_id', $request->doctor_id)
            ->where('appointment_date', $request->appointment_date)
            ->exists();

        if ($exists) {
            return response()->json(['message' => 'The selected time is already booked. Please choose another slot.'], 409);
        }

        $appt = Appointment::create([
            'doctor_id' => $request->doctor_id,
            'patient_name' => $request->patient_name,
            'patient_email' => $request->patient_email,
            'patient_phone' => $request->patient_phone,
            'appointment_date' => $request->appointment_date,
            'notes' => $request->notes,
            'status' => 'Pending',
        ]);

        return response()->json(['message' => 'Appointment booked successfully!', 'appointment_id' => $appt->id]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
    public function status()
    {
        return view('frontend.appointment.status');
    }

    // Handle form submission and display result
    public function checkStatus(Request $request)
    {
        $request->validate([
            'appointment_no' => 'required'
        ]);

        $appointment = Appointment::where('id', $request->appointment_no)->first();

        if (!$appointment) {
            return back()->with('error', 'No appointment found with this number.');
        }

        return view('frontend.appointment.status', compact('appointment'));
    }
    public function downloadPrescription($id)
    {
        $appt = Appointment::findOrFail($id);
        abort_unless($appt->prescription_file && file_exists(public_path('prescriptions/' . $appt->prescription_file)), 404);

        return response()->download(public_path('prescriptions/' . $appt->prescription_file));
    }

    /**
     * Return available slots for a doctor on a given date.
     * Expects `doctor_id` and `date` (Y-m-d).
     */
    public function slots(Request $request)
    {
        $request->validate([
            'doctor_id' => 'required|exists:doctors,id',
            'date' => 'required|date',
        ]);

        $doctor = Doctor::with('availabilities')->findOrFail($request->doctor_id);
        $date = Carbon::parse($request->date);
        $dayOfWeek = $date->dayOfWeek; // 0 (Sunday) - 6 (Saturday)

        $availabilities = $doctor->availabilities()->where('day_of_week', $dayOfWeek)->get();

        if ($availabilities->isEmpty()) {
            return response()->json(['slots' => []]);
        }

        $existing = Appointment::where('doctor_id', $doctor->id)
            ->whereDate('appointment_date', $date->toDateString())
            ->get()
            ->map(fn($a) => Carbon::parse($a->appointment_date)->format('Y-m-d H:i'))
            ->toArray();

        $slots = [];

        foreach ($availabilities as $av) {
            $start = Carbon::createFromFormat('H:i', $av->start_time)
                ->setDate($date->year, $date->month, $date->day);
            $end = Carbon::createFromFormat('H:i', $av->end_time)
                ->setDate($date->year, $date->month, $date->day);

            while ($start->lt($end)) {
                $slotKey = $start->format('Y-m-d H:i');
                if (! in_array($slotKey, $existing)) {
                    $slots[] = [
                        'datetime' => $start->format('Y-m-d H:i:s'),
                        'time' => $start->format('h:i A')
                    ];
                }
                $start->addMinutes($av->slot_minutes);
            }
        }

        return response()->json(['slots' => $slots]);
    }
}
