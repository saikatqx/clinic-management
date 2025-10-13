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
}
