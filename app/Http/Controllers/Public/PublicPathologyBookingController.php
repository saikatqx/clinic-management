<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Diagnostic;
use App\Models\DiagnosticCategory;
use App\Models\DiagnosticBooking;
use App\Models\DiagnosticBookingItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PublicPathologyBookingController extends Controller
{
    public function index(Request $request)
    {
        $categories = DiagnosticCategory::where('status', 1)->where('type', 'path')->orderBy('name', 'asc')->get();
        
        $query = Diagnostic::where('status', 1)->whereHas('category', function($q) {
            $q->where('type', 'path');
        })->with('category');
        
        if ($request->has('category') && $request->category != '') {
            $query->where('diagnostic_category_id', $request->category);
        }
        
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where('name', 'like', "%$search%");
        }
        
        $tests = $query->orderBy('name', 'asc')->get();
        
        return view('frontend.pathology.index', compact('categories', 'tests'));
    }

    public function book(Request $request)
    {
        $request->validate([
            'test_ids' => 'required|string'
        ]);

        $ids = explode(',', $request->test_ids);
        $tests = Diagnostic::whereIn('id', $ids)->where('status', 1)->get();

        if ($tests->isEmpty()) {
            return redirect()->route('pathology.index.public')->with('error', 'Select at least one test to book.');
        }

        return view('frontend.pathology.book', compact('tests'));
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
            'collection_type' => 'required|in:home,clinic',
            'payment_method' => 'required|string',
            'test_ids' => 'required|array|min:1',
            'test_ids.*' => 'exists:diagnostics,id',
        ]);

        $subtotal = 0;
        $tests = Diagnostic::whereIn('id', $request->test_ids)->get();
        foreach ($tests as $t) {
            $subtotal += $t->price;
        }

        $bookingNo = 'PATH-' . strtoupper(uniqid());

        $booking = DiagnosticBooking::create([
            'booking_no' => $bookingNo,
            'type' => 'path',
            'user_id' => Auth::id(),
            'patient_id' => Auth::id(),
            'patient_name' => $request->patient_name,
            'mobile' => $request->mobile,
            'email' => $request->email,
            'address' => $request->address,
            'booking_date' => $request->booking_date,
            'booking_time' => $request->booking_time,
            'collection_type' => $request->collection_type,
            'subtotal' => $subtotal,
            'discount' => 0,
            'total_amount' => $subtotal,
            'payment_status' => ($request->payment_method === 'Cash') ? 'pending' : 'paid',
            'payment_method' => $request->payment_method,
            'booking_status' => 'pending',
        ]);

        foreach ($tests as $t) {
            DiagnosticBookingItem::create([
                'diagnostic_booking_id' => $booking->id,
                'diagnostic_id' => $t->id,
                'test_name' => $t->name,
                'price' => $t->price,
                'qty' => 1,
                'amount' => $t->price,
            ]);
        }

        return redirect()->route('pathology.success', ['booking_no' => $bookingNo]);
    }

    public function success(Request $request)
    {
        $bookingNo = $request->query('booking_no');
        $booking = DiagnosticBooking::where('booking_no', $bookingNo)->firstOrFail();
        return view('frontend.pathology.success', compact('booking'));
    }
}
