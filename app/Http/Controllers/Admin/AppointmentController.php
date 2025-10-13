<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Doctor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

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
            $statusBadge = match($a->status) {
                'Confirmed' => '<span class="badge bg-success">Confirmed</span>',
                'Cancelled' => '<span class="badge bg-danger">Cancelled</span>',
                default => '<span class="badge bg-warning text-dark">Pending</span>',
            };

            $actions = '
                <button class="btn btn-success btn-sm update-status" data-id="'.$a->id.'" data-status="Confirmed">✅ Confirm</button>
                <button class="btn btn-danger btn-sm update-status" data-id="'.$a->id.'" data-status="Cancelled">❌ Cancel</button>
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
        $appointment->save();

        return response()->json(['message' => 'Appointment marked as '.$request->status]);
    }
}
