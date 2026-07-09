<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\HealthPackage;
use App\Models\HealthPackageBooking;
use App\Models\HealthPackageBookingItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PublicHealthPackageController extends Controller
{
    public function index()
    {
        $packages = HealthPackage::where('status', 1)->with('diagnostics')->orderBy('name', 'asc')->get();
        return view('frontend.packages.index', compact('packages'));
    }

    public function book(Request $request)
    {
        $request->validate([
            'health_package_id' => 'required|exists:health_packages,id'
        ]);

        $package = HealthPackage::with('diagnostics')->findOrFail($request->health_package_id);
        return view('frontend.packages.book', compact('package'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'patient_name' => 'required|string|max:255',
            'mobile' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'booking_date' => 'required|date|after_or_equal:today',
            'booking_time' => 'required|string',
            'collection_type' => 'nullable|string',
            'payment_method' => 'required|string',
            'health_package_id' => 'required|exists:health_packages,id',
        ]);

        $package = HealthPackage::with('diagnostics')->findOrFail($request->health_package_id);

        $bookingNo = 'PKG-' . strtoupper(uniqid());

        $booking = HealthPackageBooking::create([
            'booking_no' => $bookingNo,
            'user_id' => Auth::id(),
            'patient_name' => $request->patient_name,
            'mobile' => $request->mobile,
            'email' => $request->email,
            'address' => null,
            'booking_date' => $request->booking_date,
            'booking_time' => $request->booking_time,
            'collection_type' => 'clinic',
            'subtotal' => $package->package_price,
            'discount' => 0,
            'total_amount' => $package->package_price,
            'payment_status' => ($request->payment_method === 'Cash') ? 'pending' : 'paid',
            'payment_method' => $request->payment_method,
            'booking_status' => 'pending',
        ]);

        HealthPackageBookingItem::create([
            'health_package_booking_id' => $booking->id,
            'health_package_id' => $package->id,
            'package_name' => $package->name,
            'actual_price' => $package->actual_price,
            'package_price' => $package->package_price,
            'tests_json' => $package->diagnostics->pluck('name')->toArray(),
        ]);

        return redirect()->route('packages.success', ['booking_no' => $bookingNo]);
    }

    public function success(Request $request)
    {
        $bookingNo = $request->query('booking_no');
        $booking = HealthPackageBooking::where('booking_no', $bookingNo)->firstOrFail();
        return view('frontend.packages.success', compact('booking'));
    }
}
