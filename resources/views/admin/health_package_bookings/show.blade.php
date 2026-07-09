@extends('layouts.admin')

@section('title', 'Package Booking Details - ' . $booking->booking_no)
@section('page-title', 'Package Booking Details')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="mb-0">Package Booking #{{ $booking->booking_no }}</h2>
    <a href="{{ route('admin.health-package-bookings.index') }}" class="btn btn-secondary shadow-sm">
        <i class="fa fa-arrow-left me-1"></i> Back to Bookings
    </a>
</div>

<div class="row g-4">
    <!-- Patient and Booking Info Card -->
    <div class="col-lg-8">
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 text-primary"><i class="fas fa-user-injured me-2"></i> Patient & Schedule Information</h5>
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
                        <span class="badge bg-secondary fs-6">🏢 Clinic Visit</span>
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

        <!-- Booked Packages table -->
        <div class="card shadow-sm">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 text-primary"><i class="fas fa-heartbeat me-2"></i> Booked Health Package Details</h5>
            </div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Package Name & Included Tests</th>
                            <th class="text-end" style="width: 150px;">Actual Price</th>
                            <th class="text-end" style="width: 150px;">Offer Price</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($booking->items as $item)
                            <tr>
                                <td>
                                    <strong class="fs-5 text-dark">{{ $item->package_name }}</strong>
                                    <div class="mt-2">
                                        <span class="text-muted small d-block mb-1">Included Diagnostic Tests:</span>
                                        @if(is_array($item->tests_json))
                                            @foreach($item->tests_json as $testName)
                                                <span class="badge bg-light text-dark border me-1 mb-1"><i class="fa fa-vial me-1 text-primary"></i> {{ $testName }}</span>
                                            @endforeach
                                        @else
                                            <span class="text-muted small">No specific tests logged.</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="text-end text-muted"><del>₹{{ number_format($item->actual_price, 2) }}</del></td>
                                <td class="text-end fw-semibold text-success fs-5">₹{{ number_format($item->package_price, 2) }}</td>
                            </tr>
                        @endforeach
                        <tr class="table-light">
                            <td class="text-end fw-bold">Subtotal:</td>
                            <td colspan="2" class="text-end fw-bold">₹{{ number_format($booking->subtotal, 2) }}</td>
                        </tr>
                        @if($booking->discount > 0)
                            <tr class="table-light">
                                <td class="text-end text-success fw-bold">Discount:</td>
                                <td colspan="2" class="text-end text-success fw-bold">-₹{{ number_format($booking->discount, 2) }}</td>
                            </tr>
                        @endif
                        <tr class="table-primary">
                            <td class="text-end fw-bold fs-5">Total Paid Amount:</td>
                            <td colspan="2" class="text-end fw-bold fs-5 text-primary">₹{{ number_format($booking->total_amount, 2) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Right Sidebar Controls -->
    <div class="col-lg-4">
        <!-- Status Panel -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 text-primary"><i class="fas fa-tasks me-2"></i> Update Status</h5>
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

                    <button type="submit" class="btn btn-primary w-100" id="update_status_btn">
                        <i class="fa fa-save me-1"></i> Save Changes
                    </button>
                </form>
            </div>
        </div>

        <!-- Report Upload Panel -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 text-primary"><i class="fas fa-file-pdf me-2"></i> Test Report PDF</h5>
            </div>
            <div class="card-body">
                @if($booking->report_pdf_path)
                    <div class="alert alert-success text-center p-3 mb-3">
                        <i class="fa fa-check-circle fa-2x d-block mb-2"></i>
                        <strong>Report Uploaded Successfully!</strong>
                        <div class="small text-muted mt-1">Uploaded by: {{ optional($booking->reportUploadedBy)->name ?? 'Admin' }}</div>
                        <div class="small text-muted">Date: {{ $booking->report_uploaded_at ? $booking->report_uploaded_at->format('d M Y, h:i A') : '-' }}</div>
                    </div>
                    <a href="{{ asset('reports/packages/' . $booking->report_pdf_path) }}" target="_blank" class="btn btn-outline-success w-100 mb-3">
                        <i class="fa fa-download me-1"></i> Download PDF Report
                    </a>
                @else
                    <div class="alert alert-warning text-center small mb-3">
                        <i class="fa fa-exclamation-triangle me-1"></i> No report uploaded yet. Upload a PDF to automatically set the status to Completed.
                    </div>
                @endif

                <form action="{{ route('admin.health-package-bookings.uploadReport') }}" method="POST" enctype="multipart/form-data">
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
            </div>
        </div>

        <!-- Reschedule Panel -->
        <div class="card shadow-sm">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 text-primary"><i class="fas fa-calendar-day me-2"></i> Reschedule Booking</h5>
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
            url: "{{ route('admin.health-package-bookings.updateStatus') }}",
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
            url: "{{ route('admin.health-package-bookings.reschedule') }}",
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
