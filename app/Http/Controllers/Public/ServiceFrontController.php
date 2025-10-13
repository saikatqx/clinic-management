<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Service;

class ServiceFrontController extends Controller
{
    /**
     * Display a listing of the active services.
     */
    public function index()
    {
        $services = Service::where('is_active', 1)
            ->latest()
            ->paginate(9);

        return view('frontend.services.index', compact('services'));
    }

    /**
     * Display the specified service.
     */
    public function show(Service $service)
    {
        abort_if(!$service->is_active, 404);

        return view('frontend.services.show', compact('service'));
    }
}
