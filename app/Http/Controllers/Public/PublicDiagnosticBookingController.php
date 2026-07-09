<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Diagnostic;
use App\Models\DiagnosticCategory;
use App\Models\DiagnosticBooking;
use App\Models\DiagnosticBookingItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PublicDiagnosticBookingController extends Controller
{
    public function index(Request $request)
    {
        $categories = DiagnosticCategory::where('status', 1)->where('type', 'diag')->orderBy('name', 'asc')->get();
        
        $query = Diagnostic::where('status', 1)->whereHas('category', function($q) {
            $q->where('type', 'diag');
        })->with('category');
        
        if ($request->has('category') && $request->category != '') {
            $query->where('diagnostic_category_id', $request->category);
        }
        
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where('name', 'like', "%$search%");
        }
        
        $tests = $query->orderBy('name', 'asc')->get();
        
        return view('frontend.diagnostics.index', compact('categories', 'tests'));
    }

    public function book(Request $request)
    {
        $request->validate([
            'test_ids' => 'required|string'
        ]);

        $ids = explode(',', $request->test_ids);
        $tests = Diagnostic::whereIn('id', $ids)->where('status', 1)->get();

        if ($tests->isEmpty()) {
            return redirect()->route('diagnostics.index.public')->with('error', 'Select at least one test to book.');
        }

        return view('frontend.diagnostics.book', compact('tests'));
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
            'test_ids' => 'required|array|min:1',
            'test_ids.*' => 'exists:diagnostics,id',
        ]);

        $subtotal = 0;
        $tests = Diagnostic::whereIn('id', $request->test_ids)->get();
        foreach ($tests as $t) {
            $subtotal += $t->price;
        }

        $bookingNo = 'DIAG-' . strtoupper(uniqid());

        $booking = DiagnosticBooking::create([
            'booking_no' => $bookingNo,
            'type' => 'diag',
            'user_id' => Auth::id(),
            'patient_id' => Auth::id(),
            'patient_name' => $request->patient_name,
            'mobile' => $request->mobile,
            'email' => $request->email,
            'address' => null,
            'booking_date' => $request->booking_date,
            'booking_time' => $request->booking_time,
            'collection_type' => 'clinic',
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

        return redirect()->route('diagnostics.success', ['booking_no' => $bookingNo]);
    }

    public function success(Request $request)
    {
        $bookingNo = $request->query('booking_no');
        $booking = DiagnosticBooking::where('booking_no', $bookingNo)->firstOrFail();
        return view('frontend.diagnostics.success', compact('booking'));
    }
}
