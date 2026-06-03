<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\Doctor;
use App\Models\Appointment;

Route::get('/doctors', function () {
    return response()->json(Doctor::where('status', 'Active')->get());
});

Route::post('/appointments', function (Request $request) {
    $request->validate([
        'doctor_id' => 'required|exists:doctors,id',
        'patient_name' => 'required|string|max:255',
        'patient_email' => 'nullable|email|max:255',
        'patient_phone' => 'required|string|max:20',
        'appointment_date' => 'required|date',
    ]);

    // Simple conflict check
    $exists = Appointment::where('doctor_id', $request->doctor_id)
        ->where('appointment_date', $request->appointment_date)
        ->exists();

    if ($exists) {
        return response()->json(['message' => 'Selected slot already booked'], 409);
    }

    $appt = Appointment::create([
        'doctor_id' => $request->doctor_id,
        'patient_name' => $request->patient_name,
        'patient_email' => $request->patient_email,
        'patient_phone' => $request->patient_phone,
        'appointment_date' => $request->appointment_date,
        'status' => 'Pending',
    ]);

    return response()->json(['message' => 'Appointment created', 'id' => $appt->id]);
});
