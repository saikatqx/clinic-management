<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Barryvdh\DomPDF\Facade\Pdf;

class AppointmentController extends Controller
{
    public function index()
    {
        return view('admin.appointments.index');
    }

    public function data(Request $request)
    {
        $appointments = Appointment::with('doctor')->orderBy('appointment_date', 'desc')->get();

        $data = [];
        foreach ($appointments as $a) {
            $statusBadge = match ($a->status) {
                'Confirmed' => '<span class="badge bg-success">Confirmed</span>',
                'Cancelled' => '<span class="badge bg-danger">Cancelled</span>',
                default => '<span class="badge bg-warning text-dark">Pending</span>',
            };

            $actions = '
                <a href="' . route('admin.appointments.prescription', $a->id) . '" class="btn btn-info btn-sm">
                🩺 Prescription
                </a>
                <button class="btn btn-success btn-sm update-status" data-id="' . $a->id . '" data-status="Confirmed">✅ Confirm</button>
                <button class="btn btn-danger btn-sm update-status" data-id="' . $a->id . '" data-status="Cancelled">❌ Cancel</button>
            ';

            $data[] = [
                $a->id,
                $a->doctor->name ?? '-',
                $a->patient_name,
                $a->appointment_date ? date('d M Y h:i A', strtotime($a->appointment_date)) : '-',
                $statusBadge,
                $actions
            ];
        }

        return Response::json(['data' => $data]);
    }

    public function updateStatus(Request $request)
    {
        $appointment = Appointment::findOrFail($request->id);
        $appointment->status = $request->status;
        $appointment->confirmed_at = $request->status == 'Confirmed' ? Carbon::now() : null;
        $appointment->save();

        return response()->json(['message' => 'Appointment marked as ' . $request->status]);
    }

    public function createPrescription($id)
    {
        $appointment = Appointment::with('doctor')->findOrFail($id);
        return view('admin.appointments.prescription', compact('appointment'));
    }

    // Store prescription and generate PDF
    public function storePrescription(Request $request, $id)
    {
        $request->validate([
            'medicine_name' => 'required|string|max:255',
            'checkup_name' => 'nullable|string|max:255',
            'eating_time' => 'required|string|max:255',
            'short_note' => 'nullable|string|max:500',
        ]);

        $appointment = Appointment::findOrFail($id);
        $settings = Setting::first(); // contains clinic info

        // Prepare PDF content
        $data = [
            'appointment' => $appointment,
            'medicine_name' => $request->medicine_name,
            'checkup_name' => $request->checkup_name,
            'eating_time' => $request->eating_time,
            'short_note' => $request->short_note,
            'generated_at' => now()->format('d M Y, h:i A'),
            'settings' => $settings
        ];

        $pdf = Pdf::loadView('admin.appointments.prescription_pdf', $data);

        // Save PDF to public/prescriptions
        $fileName = 'prescription_' . $appointment->id . '_' . time() . '.pdf';
        $filePath = public_path('prescriptions/' . $fileName);
        $pdf->save($filePath);

        // Update appointment record
        $appointment->update([
            'prescription_file' => $fileName,
            'prescription_generated_at' => now(),
            'status' => 'Confirmed',
        ]);

        return redirect()->route('admin.appointments.index')
            ->with('success', 'Prescription generated successfully.');
    }
}
