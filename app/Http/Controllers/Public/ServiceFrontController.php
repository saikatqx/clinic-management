<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Service;

class ServiceFrontController extends Controller
{
    /**
     * Display a listing of the active services.
     */
    // App\Http\Controllers\Frontend\ServiceFrontController.php
    public function index(Request $request)
    {
        $q = trim($request->get('q', ''));
        $services = Service::query()
            ->when($q, fn($qry) => $qry->where('name', 'like', "%{$q}%")
                ->orWhere('description', 'like', "%{$q}%"))
            ->orderBy('name')
            ->paginate(9);

        return view('frontend.services.index', compact('services'));
    }


    /**
     * Display the specified service.
     */
    public function show($id)
    {
        $service = Service::findOrFail($id);
        return view('frontend.services.show', compact('service'));
    }
}
