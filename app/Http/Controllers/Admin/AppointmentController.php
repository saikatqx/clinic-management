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
        $doctors = Doctor::where('is_active', 1)->orderBy('name', 'asc')->get();
        return view('admin.appointments.index', compact('doctors'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'doctor_id' => 'required|exists:doctors,id',
            'patient_name' => 'required|string|max:255',
            'patient_email' => 'nullable|email|max:255',
            'patient_phone' => 'required|string|max:20',
            'appointment_date' => 'required|date',
            'status' => 'required|in:Pending,Confirmed,Cancelled',
            'payment_status' => 'required|in:Pending,Paid',
            'notes' => 'nullable|string',
        ]);

        // Double booking check for exact datetime
        $exists = Appointment::where('doctor_id', $request->doctor_id)
            ->where('appointment_date', $request->appointment_date)
            ->exists();

        if ($exists) {
            return response()->json([
                'message' => 'The selected time is already booked. Please choose another slot.'
            ], 409);
        }

        $appointment = Appointment::create([
            'doctor_id' => $request->doctor_id,
            'patient_name' => $request->patient_name,
            'patient_email' => $request->patient_email,
            'patient_phone' => $request->patient_phone,
            'appointment_date' => $request->appointment_date,
            'status' => $request->status,
            'payment_status' => $request->payment_status,
            'notes' => $request->notes,
        ]);

        if ($appointment->patient_email) {
            try {
                \Illuminate\Support\Facades\Mail::to($appointment->patient_email)
                    ->send(new \App\Mail\AppointmentStatusMail($appointment));
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Mail sending failed on walk-in booking: ' . $e->getMessage());
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Walk-in appointment booked successfully!'
        ]);
    }

    public function data(Request $request)
    {
        $appointments = Appointment::with('doctor')->orderBy('appointment_date', 'desc')->get();

        $data = [];
        foreach ($appointments as $a) {
            $payBadge = match ($a->payment_status) {
                'Paid' => '<span class="badge bg-success ms-1"><i class="fa fa-check-circle"></i> Paid</span>',
                default => '<span class="badge bg-secondary ms-1"><i class="fa-solid fa-clock"></i> Unpaid</span>',
            };

            $statusBadge = match ($a->status) {
                'Confirmed' => '<span class="badge bg-success">Confirmed</span>',
                'Cancelled' => '<span class="badge bg-danger">Cancelled</span>',
                default => '<span class="badge bg-warning text-dark">Pending</span>',
            } . $payBadge;

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
        $appointment = Appointment::with('doctor')->findOrFail($request->id);
        $appointment->status = $request->status;
        $appointment->confirmed_at = $request->status == 'Confirmed' ? Carbon::now() : null;
        $appointment->save();

        if ($appointment->patient_email) {
            try {
                \Illuminate\Support\Facades\Mail::to($appointment->patient_email)
                    ->send(new \App\Mail\AppointmentStatusMail($appointment));
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Mail sending failed: ' . $e->getMessage());
            }
        }

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

        $appointment->load('doctor');
        if ($appointment->patient_email) {
            try {
                \Illuminate\Support\Facades\Mail::to($appointment->patient_email)
                    ->send(new \App\Mail\PrescriptionMail($appointment));
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Prescription mail sending failed: ' . $e->getMessage());
            }
        }

        return redirect()->route('admin.appointments.index')
            ->with('success', 'Prescription generated successfully.');
    }

    public function calendarEvents(Request $request)
    {
        $start = $request->input('start');
        $end = $request->input('end');

        $query = Appointment::with('doctor');

        if ($start && $end) {
            $query->whereBetween('appointment_date', [$start, $end]);
        }

        $appointments = $query->get();

        $events = [];
        foreach ($appointments as $a) {
            $color = match ($a->status) {
                'Confirmed' => '#28a745',
                'Cancelled' => '#dc3545',
                default => '#ffc107',
            };
            $textColor = ($a->status === 'Pending') ? '#212529' : '#ffffff';

            $startDt = Carbon::parse($a->appointment_date);
            $endDt = (clone $startDt)->addMinutes(30);

            $events[] = [
                'id' => $a->id,
                'title' => ($a->doctor ? $docName = $a->doctor->name : 'No Doc') . ' - ' . $a->patient_name,
                'start' => $startDt->toIso8601String(),
                'end' => $endDt->toIso8601String(),
                'backgroundColor' => $color,
                'borderColor' => $color,
                'textColor' => $textColor,
                'extendedProps' => [
                    'patient_name' => $a->patient_name,
                    'doctor_name' => $a->doctor ? $a->doctor->name : '-',
                    'phone' => $a->patient_phone ?? '-',
                    'status' => $a->status,
                ],
            ];
        }

        return response()->json($events);
    }

    public function reschedule(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:appointments,id',
            'appointment_date' => 'required|date',
        ]);

        $appointment = Appointment::findOrFail($request->id);
        
        $exists = Appointment::where('doctor_id', $appointment->doctor_id)
            ->where('appointment_date', $request->appointment_date)
            ->where('id', '!=', $appointment->id)
            ->exists();

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'This doctor is already booked at the selected time.'
            ], 409);
        }

        $appointment->appointment_date = $request->appointment_date;
        $appointment->save();

        $appointment->load('doctor');
        if ($appointment->patient_email) {
            try {
                \Illuminate\Support\Facades\Mail::to($appointment->patient_email)
                    ->send(new \App\Mail\AppointmentStatusMail($appointment));
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Mail sending failed on reschedule: ' . $e->getMessage());
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Appointment rescheduled successfully to ' . Carbon::parse($request->appointment_date)->format('d M Y, h:i A')
        ]);
    }
}
