<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Service;
use App\Models\DiagnosticBooking;
use App\Models\HealthPackageBooking;

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

        // Lab & package bookings counts
        $totalDiagBookings = DiagnosticBooking::where('type', 'diag')->count();
        $totalPathBookings = DiagnosticBooking::where('type', 'path')->count();
        $totalPkgBookings = HealthPackageBooking::count();

        $recentLabBookings = DiagnosticBooking::with('items.diagnostic')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $recentPkgBookings = HealthPackageBooking::with('items.package')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // 1. Appointments Trend (Last 15 days)
        $daysRange = [];
        $appointmentTrends = [];
        for ($i = 14; $i >= 0; $i--) {
            $dateStr = now()->subDays($i)->format('Y-m-d');
            $daysRange[] = now()->subDays($i)->format('d M');
            $appointmentTrends[] = Appointment::whereDate('appointment_date', $dateStr)->count();
        }

        // 2. Doctor Workloads
        $doctorStats = Appointment::join('doctors', 'appointments.doctor_id', '=', 'doctors.id')
            ->select('doctors.name', \Illuminate\Support\Facades\DB::raw('count(appointments.id) as count'))
            ->groupBy('doctors.name')
            ->get()
            ->pluck('count', 'name')
            ->toArray();

        // 3. Specialty Popularity
        $specialtyStats = Appointment::join('doctors', 'appointments.doctor_id', '=', 'doctors.id')
            ->join('specialties', 'doctors.specialty_id', '=', 'specialties.id')
            ->select('specialties.name', \Illuminate\Support\Facades\DB::raw('count(appointments.id) as count'))
            ->groupBy('specialties.name')
            ->get()
            ->pluck('count', 'name')
            ->toArray();

        return view('admin.dashboard', compact(
            'totalAppointments', 'confirmed', 'pending', 'cancelled', 'upcoming', 'doctors', 'services',
            'daysRange', 'appointmentTrends', 'doctorStats', 'specialtyStats',
            'totalDiagBookings', 'totalPathBookings', 'totalPkgBookings', 'recentLabBookings', 'recentPkgBookings'
        ));
    }
}
