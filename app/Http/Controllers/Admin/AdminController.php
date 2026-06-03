<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Service;

class AdminController extends Controller
{
    public function index()
    {
        $totalAppointments = Appointment::count();
        $confirmed = Appointment::where('status', 'Confirmed')->count();
        $pending = Appointment::where('status', 'Pending')->count();
        $cancelled = Appointment::where('status', 'Cancelled')->count();

        $upcoming = Appointment::with('doctor')
            ->where('appointment_date', '>=', now())
            ->orderBy('appointment_date')
            ->limit(10)
            ->get();

        $doctors = Doctor::count();
        $services = Service::count();

        return view('admin.dashboard', compact(
            'totalAppointments', 'confirmed', 'pending', 'cancelled', 'upcoming', 'doctors', 'services'
        ));
    }
}
