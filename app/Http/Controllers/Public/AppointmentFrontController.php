<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Appointment;

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

        Appointment::create([
            'doctor_id' => $request->doctor_id,
            'patient_name' => $request->patient_name,
            'patient_email' => $request->patient_email,
            'patient_phone' => $request->patient_phone,
            'appointment_date' => $request->appointment_date,
            'notes' => $request->notes,
            'status' => 'Pending',
        ]);

        return response()->json(['message' => 'Appointment booked successfully!']);
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
}
