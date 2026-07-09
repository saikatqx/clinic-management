<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HealthPackageBooking;
use App\Models\HealthPackageBookingItem;
use App\Models\HealthPackage;
use App\Models\Doctor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Auth;

class HealthPackageBookingController extends Controller
{
    public function index()
    {
        $doctors = Doctor::where('is_active', 1)->orderBy('name', 'asc')->get();
        $packages = HealthPackage::where('status', 1)->orderBy('name', 'asc')->get();
        return view('admin.health_package_bookings.index', compact('doctors', 'packages'));
    }

    public function show(string $id)
    {
        $booking = HealthPackageBooking::with(['items.package', 'referredDoctor', 'user'])->findOrFail($id);
        return view('admin.health_package_bookings.show', compact('booking'));
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
            'payment_status' => 'required|in:pending,paid',
            'payment_method' => 'required|string',
            'health_package_id' => 'required|exists:health_packages,id',
            'remarks' => 'nullable|string',
        ]);

        $package = HealthPackage::with('diagnostics')->findOrFail($request->health_package_id);

        $bookingNo = 'PKG-' . strtoupper(uniqid());

        $booking = HealthPackageBooking::create([
            'booking_no' => $bookingNo,
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
            'payment_status' => $request->payment_status,
            'payment_method' => $request->payment_method,
            'booking_status' => 'confirmed', // immediately confirmed for admin bookings
            'remarks' => $request->remarks,
        ]);

        HealthPackageBookingItem::create([
            'health_package_booking_id' => $booking->id,
            'health_package_id' => $package->id,
            'package_name' => $package->name,
            'actual_price' => $package->actual_price,
            'package_price' => $package->package_price,
            'tests_json' => $package->diagnostics->pluck('name')->toArray(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Health Package booking created successfully!',
            'booking_id' => $booking->id
        ]);
    }

    public function updateStatus(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:health_package_bookings,id',
            'booking_status' => 'nullable|string',
            'payment_status' => 'nullable|string',
        ]);

        $booking = HealthPackageBooking::findOrFail($request->id);

        if ($request->has('booking_status')) {
            $booking->booking_status = $request->booking_status;
        }
        if ($request->has('payment_status')) {
            $booking->payment_status = $request->payment_status;
        }

        $booking->save();

        return response()->json([
            'success' => true,
            'message' => 'Booking status updated successfully!'
        ]);
    }

    public function reschedule(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:health_package_bookings,id',
            'booking_date' => 'required|date',
            'booking_time' => 'required|string',
        ]);

        $booking = HealthPackageBooking::findOrFail($request->id);
        $booking->update([
            'booking_date' => $request->booking_date,
            'booking_time' => $request->booking_time,
            'is_rescheduled' => true,
            'rescheduled_at' => now(),
            'rescheduled_by' => Auth::id(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Booking rescheduled successfully!'
        ]);
    }

    public function uploadReport(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:health_package_bookings,id',
            'report' => 'required|mimes:pdf|max:5120',
        ]);

        $booking = HealthPackageBooking::findOrFail($request->id);

        if ($request->hasFile('report')) {
            if ($booking->report_pdf_path && file_exists(public_path('reports/packages/' . $booking->report_pdf_path))) {
                unlink(public_path('reports/packages/' . $booking->report_pdf_path));
            }

            $file = $request->file('report');
            $filename = 'report_' . $booking->booking_no . '_' . time() . '.pdf';
            $destinationPath = public_path('reports/packages');

            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }

            $file->move($destinationPath, $filename);

            $booking->update([
                'report_pdf_path' => $filename,
                'report_uploaded_by' => Auth::id(),
                'report_uploaded_at' => now(),
                'booking_status' => 'completed'
            ]);
        }

        return redirect()->back()->with('success', 'Health Package report uploaded and marked as Completed!');
    }

    public function data(Request $request)
    {
        $columns = ['booking_no', 'patient_name', 'mobile', 'booking_date', 'total_amount', 'booking_status', 'payment_status', 'id'];

        $query = HealthPackageBooking::select('id', 'booking_no', 'patient_name', 'mobile', 'booking_date', 'booking_time', 'total_amount', 'booking_status', 'payment_status');

        if ($search = strtoupper($request->input('search.value'))) {
            $query->where(function ($q) use ($search) {
                $q->whereRaw('UPPER(booking_no) LIKE ?', ["%$search%"])
                    ->orWhereRaw('UPPER(patient_name) LIKE ?', ["%$search%"])
                    ->orWhereRaw('UPPER(mobile) LIKE ?', ["%$search%"]);
            });
        }

        $totalRecords = $query->count();

        $orderByColumn = $columns[$request->input('order.0.column', 0)];
        $orderByDir = $request->input('order.0.dir', 'asc');
        $query->orderBy($orderByColumn, $orderByDir);

        $limit = $request->input('length');
        $offset = $request->input('start');
        $query->limit($limit)->offset($offset);

        $results = $query->get();

        $data = [];
        foreach ($results as $value) {
            $showUrl = route('admin.health-package-bookings.show', $value->id);
            $action = '
            <a href="' . $showUrl . '" class="btn btn-sm btn-info text-white">
                <i class="fas fa-eye"></i> View Details
            </a>';

            $statusBadge = match ($value->booking_status) {
                'pending' => '<span class="badge bg-warning text-dark">Pending</span>',
                'confirmed' => '<span class="badge bg-primary">Confirmed</span>',
                'sample_collected' => '<span class="badge bg-info">Sample Collected</span>',
                'completed' => '<span class="badge bg-success">Completed</span>',
                default => '<span class="badge bg-danger">Cancelled</span>',
            };

            $payBadge = match ($value->payment_status) {
                'paid' => '<span class="badge bg-success ms-1"><i class="fa fa-check-circle"></i> Paid</span>',
                default => '<span class="badge bg-secondary ms-1">Unpaid</span>',
            };

            $row = [];
            $row[] = $value->booking_no;
            $row[] = $value->patient_name;
            $row[] = $value->mobile;
            $row[] = date('d M Y', strtotime($value->booking_date)) . ' (' . $value->booking_time . ')';
            $row[] = '₹' . number_format($value->total_amount, 2);
            $row[] = $statusBadge;
            $row[] = $payBadge;
            $row[] = $action;

            $data[] = $row;
        }

        return Response::json([
            "draw" => intval($request->input('draw')),
            "recordsTotal" => $totalRecords,
            "recordsFiltered" => $totalRecords,
            "data" => $data
        ]);
    }
}
