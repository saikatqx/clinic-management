@extends('layouts.admin')

@section('title', 'Pathology Booking Details - ' . $booking->booking_no)
@section('page-title', 'Pathology Booking Details')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="mb-0">Booking #{{ $booking->booking_no }}</h2>
    <a href="{{ route('admin.pathology-bookings.index') }}" class="btn btn-secondary shadow-sm">
        <i class="fa fa-arrow-left me-1"></i> Back to Pathology Bookings
    </a>
</div>

<div class="row g-4">
    <!-- Patient and Booking Info Card -->
    <div class="col-lg-8">
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 text-primary"><i class="fas fa-user-injured me-2"></i> Patient & Sample Collection Details</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="text-muted small d-block">Patient Name</label>
                        <span class="fw-semibold fs-5">{{ $booking->patient_name }}</span>
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted small d-block">Mobile Number</label>
                        <span class="fw-semibold fs-5">{{ $booking->mobile }}</span>
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted small d-block">Email Address</label>
                        <span class="fs-6 text-dark">{{ $booking->email ?? 'Not provided' }}</span>
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted small d-block">Collection Type</label>
                        <span class="badge bg-secondary fs-6">{{ ($booking->collection_type === 'home') ? '🏠 Home Sample Collection' : '🏢 Clinic Visit' }}</span>
                    </div>
                    <div class="col-12">
                        <label class="text-muted small d-block">Collection/Home Address</label>
                        <span class="fs-6 text-dark">{{ $booking->address ?? 'N/A' }}</span>
                    </div>
                    
                    <hr>

                    <div class="col-md-6">
                        <label class="text-muted small d-block">Scheduled Appointment Date</label>
                        <span class="fw-semibold fs-5 text-primary"><i class="fa fa-calendar-day me-1"></i> {{ date('d M Y', strtotime($booking->booking_date)) }}</span>
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted small d-block">Preferred Time Slot</label>
                        <span class="fw-semibold fs-5 text-primary"><i class="fa fa-clock me-1"></i> {{ $booking->booking_time }}</span>
                        @if($booking->is_rescheduled)
                            <small class="d-block text-danger mt-1">Rescheduled on {{ $booking->rescheduled_at->format('d M, h:i A') }}</small>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Booked Tests table -->
        <div class="card shadow-sm">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 text-primary"><i class="fas fa-vial me-2"></i> Booked Pathology Tests</h5>
            </div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Test Name</th>
                            <th class="text-center" style="width: 100px;">Qty</th>
                            <th class="text-end" style="width: 150px;">Price</th>
                            <th class="text-end" style="width: 150px;">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($booking->items as $item)
                            <tr>
                                <td>
                                    <strong>{{ $item->test_name }}</strong>
                                    @if($item->diagnostic && $item->diagnostic->category)
                                        <small class="d-block text-muted">{{ $item->diagnostic->category->name }}</small>
                                    @endif
                                </td>
                                <td class="text-center">{{ $item->qty }}</td>
                                <td class="text-end">₹{{ number_format($item->price, 2) }}</td>
                                <td class="text-end fw-semibold">₹{{ number_format($item->amount, 2) }}</td>
                            </tr>
                        @endforeach
                        <tr class="table-light">
                            <td colspan="3" class="text-end fw-bold">Subtotal:</td>
                            <td class="text-end fw-bold">₹{{ number_format($booking->subtotal, 2) }}</td>
                        </tr>
                        @if($booking->discount > 0)
                            <tr class="table-light">
                                <td colspan="3" class="text-end text-success fw-bold">Discount:</td>
                                <td class="text-end text-success fw-bold">-₹{{ number_format($booking->discount, 2) }}</td>
                            </tr>
                        @endif
                        <tr class="table-primary">
                            <td colspan="3" class="text-end fw-bold fs-5">Total Paid Amount:</td>
                            <td class="text-end fw-bold fs-5 text-primary">₹{{ number_format($booking->total_amount, 2) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Right Sidebar Controls -->
    <div class="col-lg-4">
        @can('update booking status')
        <!-- Status Panel -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 text-primary"><i class="fas fa-tasks me-2"></i> Update Status & Doctor</h5>
            </div>
            <div class="card-body">
                <form id="statusUpdateForm">
                    @csrf
                    <input type="hidden" name="id" value="{{ $booking->id }}">

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Booking Status</label>
                        <select name="booking_status" class="form-select">
                            <option value="pending" {{ $booking->booking_status === 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="confirmed" {{ $booking->booking_status === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                            <option value="sample_collected" {{ $booking->booking_status === 'sample_collected' ? 'selected' : '' }}>Sample Collected</option>
                            <option value="completed" {{ $booking->booking_status === 'completed' ? 'selected' : '' }}>Completed</option>
                            <option value="cancelled" {{ $booking->booking_status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Payment Status</label>
                        <select name="payment_status" class="form-select">
                            <option value="pending" {{ $booking->payment_status === 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="paid" {{ $booking->payment_status === 'paid' ? 'selected' : '' }}>Paid</option>
                            <option value="failed" {{ $booking->payment_status === 'failed' ? 'selected' : '' }}>Failed</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Assigned Doctor</label>
                        <select name="assign_doctor_id" class="form-select select2">
                            <option value="">Select Doctor</option>
                            @foreach(\App\Models\Doctor::where('is_active', 1)->get() as $doctor)
                                <option value="{{ $doctor->id }}" {{ $booking->assign_doctor_id == $doctor->id ? 'selected' : '' }}>Dr. {{ $doctor->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary w-100" id="update_status_btn">
                        <i class="fa fa-save me-1"></i> Save Changes
                    </button>
                </form>
            </div>
        </div>
        @endcan

        <!-- Report Upload Panel -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 text-primary"><i class="fas fa-file-pdf me-2"></i> Pathology Report PDF</h5>
            </div>
            <div class="card-body">
                @if($booking->report_pdf_path)
                    <div class="alert alert-success text-center p-3 mb-3">
                        <i class="fa fa-check-circle fa-2x d-block mb-2"></i>
                        <strong>Report Uploaded Successfully!</strong>
                        <div class="small text-muted mt-1">Uploaded by: {{ optional($booking->reportUploadedBy)->name ?? 'Admin' }}</div>
                        <div class="small text-muted">Date: {{ $booking->report_uploaded_at ? $booking->report_uploaded_at->format('d M Y, h:i A') : '-' }}</div>
                    </div>
                    <a href="{{ asset('reports/pathology/' . $booking->report_pdf_path) }}" target="_blank" class="btn btn-outline-success w-100 mb-3">
                        <i class="fa fa-download me-1"></i> Download PDF Report
                    </a>
                @else
                    <div class="alert alert-warning text-center small mb-3">
                        <i class="fa fa-exclamation-triangle me-1"></i> No report uploaded yet. Upload a PDF to automatically set the status to Completed.
                    </div>
                @endif

                @can('upload reports')
                <form action="{{ route('admin.pathology-bookings.uploadReport') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="id" value="{{ $booking->id }}">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Select PDF File <span class="text-danger">*</span></label>
                        <input type="file" name="report" class="form-control" accept="application/pdf" required>
                        <small class="text-muted">Only PDF files up to 5MB are accepted.</small>
                    </div>
                    <button type="submit" class="btn btn-success w-100">
                        <i class="fa fa-upload me-1"></i> Upload & Complete Booking
                    </button>
                </form>
                @endcan
            </div>
        </div>

        <!-- Reschedule Panel -->
        <div class="card shadow-sm">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 text-primary"><i class="fas fa-calendar-day me-2"></i> Reschedule Appointment</h5>
            </div>
            <div class="card-body">
                <form id="rescheduleForm">
                    @csrf
                    <input type="hidden" name="id" value="{{ $booking->id }}">

                    <div class="mb-3">
                        <label class="form-label fw-semibold">New Date <span class="text-danger">*</span></label>
                        <input type="date" name="booking_date" class="form-control" required min="{{ date('Y-m-d') }}" value="{{ $booking->booking_date }}">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">New Time Slot <span class="text-danger">*</span></label>
                        <select name="booking_time" class="form-select" required>
                            <option value="08:00 AM - 10:00 AM" {{ $booking->booking_time === '08:00 AM - 10:00 AM' ? 'selected' : '' }}>Morning (08:00 AM - 10:00 AM)</option>
                            <option value="10:00 AM - 12:00 PM" {{ $booking->booking_time === '10:00 AM - 12:00 PM' ? 'selected' : '' }}>Morning (10:00 AM - 12:00 PM)</option>
                            <option value="12:00 PM - 02:00 PM" {{ $booking->booking_time === '12:00 PM - 02:00 PM' ? 'selected' : '' }}>Midday (12:00 PM - 02:00 PM)</option>
                            <option value="02:00 PM - 04:00 PM" {{ $booking->booking_time === '02:00 PM - 04:00 PM' ? 'selected' : '' }}>Afternoon (02:00 PM - 04:00 PM)</option>
                            <option value="04:00 PM - 06:00 PM" {{ $booking->booking_time === '04:00 PM - 06:00 PM' ? 'selected' : '' }}>Evening (04:00 PM - 06:00 PM)</option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-outline-danger w-100" id="reschedule_btn">
                        <i class="fa fa-clock me-1"></i> Reschedule Appointment
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Status form submit
    $('#statusUpdateForm').on('submit', function(e) {
        e.preventDefault();
        const $btn = $('#update_status_btn');
        $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin me-1"></i> Saving...');

        $.ajax({
            url: "{{ route('admin.pathology-bookings.updateStatus') }}",
            type: "POST",
            data: $(this).serialize(),
            success: function(res) {
                $btn.prop('disabled', false).html('<i class="fa fa-save me-1"></i> Save Changes');
                toastr.success(res.message);
                setTimeout(() => location.reload(), 1000);
            },
            error: function() {
                $btn.prop('disabled', false).html('<i class="fa fa-save me-1"></i> Save Changes');
                toastr.error('Error updating status.');
            }
        });
    });

    // Reschedule form submit
    $('#rescheduleForm').on('submit', function(e) {
        e.preventDefault();
        const $btn = $('#reschedule_btn');
        $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin me-1"></i> Rescheduling...');

        $.ajax({
            url: "{{ route('admin.pathology-bookings.reschedule') }}",
            type: "POST",
            data: $(this).serialize(),
            success: function(res) {
                $btn.prop('disabled', false).html('<i class="fa fa-clock me-1"></i> Reschedule Appointment');
                toastr.success(res.message);
                setTimeout(() => location.reload(), 1000);
            },
            error: function() {
                $btn.prop('disabled', false).html('<i class="fa fa-clock me-1"></i> Reschedule Appointment');
                toastr.error('Error rescheduling booking.');
            }
        });
    });
});
</script>
@endpush
