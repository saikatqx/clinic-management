<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\Doctor;
use App\Models\Specialty;
use App\Models\Banner;

class HomeController extends Controller
{
    public function index()
    {
        $banners = Banner::where('is_active', 1)->orderBy('order')->get();
        $services = Service::where('is_active', 1)->get();
        $specialties = Specialty::all();
        $doctors = Doctor::where('is_active', 1)->get();

        return view('frontend.home', compact('banners', 'services', 'specialties', 'doctors'));
    }

    public function getBySpecialty($id)
    {
        try {
            // Validate ID
            if (!is_numeric($id)) {
                return response()->json(['error' => 'Invalid specialty ID'], 400);
            }

            // Fetch active doctors for that specialty
            $doctors = Doctor::where('specialty_id', $id)
                ->where('is_active', 1)
                ->select('id', 'name')
                ->orderBy('name')
                ->get();

            // If no doctors found, still return an empty array
            return response()->json($doctors);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
