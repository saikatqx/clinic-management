<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DoctorAvailability;
use App\Models\Doctor;
use Illuminate\Http\Request;

class AvailabilityController extends Controller
{
    public function index()
    {
        $doctors = Doctor::all();
        $availabilities = DoctorAvailability::with('doctor')->get();
        return view('admin.availabilities.index', compact('doctors', 'availabilities'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'doctor_id' => 'required|exists:doctors,id',
            'day_of_week' => 'required|integer|min:0|max:6',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'slot_minutes' => 'required|integer|min:15|max:120',
        ]);

        DoctorAvailability::create($request->only(['doctor_id', 'day_of_week', 'start_time', 'end_time', 'slot_minutes']));

        return back()->with('success', 'Availability slot added successfully!');
    }

    public function destroy($id)
    {
        $availability = DoctorAvailability::findOrFail($id);
        $availability->delete();

        return back()->with('success', 'Availability slot deleted.');
    }

    public function data(Request $request)
    {
        $availabilities = DoctorAvailability::with('doctor')->get();
        $days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];

        $data = [];
        foreach ($availabilities as $av) {
            $data[] = [
                $av->id,
                $av->doctor->name ?? '-',
                $days[$av->day_of_week],
                $av->start_time,
                $av->end_time,
                $av->slot_minutes . ' min',
                '<button class="btn btn-danger btn-sm delete-availability" data-id="' . $av->id . '">Delete</button>'
            ];
        }

        return response()->json(['data' => $data]);
    }
}
