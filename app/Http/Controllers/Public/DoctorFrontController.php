<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Doctor;
use App\Models\Specialty;

class DoctorFrontController extends Controller
{
    public function index(Request $request)
    {
        $specialtyId = $request->query('specialty');
        $doctorId    = $request->query('doctor');

        // Dropdown sources
        $specialties   = Specialty::orderBy('name')->get(['id', 'name']);
        $doctorsMaster = Doctor::where('is_active', 1)
            ->orderBy('name')->get(['id', 'name', 'specialty_id']);

        // Results (show all first; filter when query present)
        $results = Doctor::with('specialty')
            ->where('is_active', 1)
            ->when($specialtyId, fn($q) => $q->where('specialty_id', $specialtyId))
            ->when($doctorId,    fn($q) => $q->where('id', $doctorId))
            ->orderBy('name')
            ->paginate(12)
            ->withQueryString();

        return view('frontend.doctors', compact(
            'specialties',
            'doctorsMaster',
            'results',
            'specialtyId',
            'doctorId'
        ));
    }
}
