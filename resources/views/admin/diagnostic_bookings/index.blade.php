@extends('layouts.admin')

@section('title', 'Diagnostic Bookings')
@section('page-title', 'Diagnostic Bookings')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="mb-0">Diagnostic Bookings</h2>
    @can('view bookings')
    <button type="button" class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#adminBookModal">
        <i class="fa fa-plus me-1"></i> Book Diagnostic Test
    </button>
    @endcan
</div>

<div style="position: relative;">
    <div id="loader" class="table-loader" style="display:none;">
        <div class="spinner"></div>
    </div>

    <table id="bookings_table" class="display stripe hover" style="width:100%">
        <thead>
            <tr>
                <th>Booking No</th>
                <th>Patient</th>
                <th>Phone</th>
                <th>Date & Time</th>
                <th>Amount</th>
                <th>Status</th>
                <th>Payment</th>
                <th style="width: 120px;">Action</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>

<!-- Admin Direct Book Modal -->
<div class="modal fade" id="adminBookModal" tabindex="-1" aria-labelledby="adminBookModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="adminBookModalLabel"><i class="fa fa-microscope me-2"></i> Book Walk-In Diagnostic Test</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-start">
                <form id="adminBookForm">
                    @csrf
                    <div class="row g-3">
                        <!-- Patient Details -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Patient Name <span class="text-danger">*</span></label>
                            <input type="text" name="patient_name" class="form-control" required placeholder="Enter patient name">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Mobile Number <span class="text-danger">*</span></label>
                            <input type="text" name="mobile" class="form-control" required placeholder="e.g. 9876543210">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Email Address (Optional)</label>
                            <input type="email" name="email" class="form-control" placeholder="patient@example.com">
                        </div>
                        <input type="hidden" name="collection_type" value="clinic">

                        <!-- Date & Time -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Preferred Date <span class="text-danger">*</span></label>
                            <input type="date" name="booking_date" class="form-control" required min="{{ date('Y-m-d') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Preferred Time <span class="text-danger">*</span></label>
                            <select name="booking_time" class="form-select select2-modal" required>
                                <option value="08:00 AM - 10:00 AM">Morning (08:00 AM - 10:00 AM)</option>
                                <option value="10:00 AM - 12:00 PM">Morning (10:00 AM - 12:00 PM)</option>
                                <option value="12:00 PM - 02:00 PM">Midday (12:00 PM - 02:00 PM)</option>
                                <option value="02:00 PM - 04:00 PM">Afternoon (02:00 PM - 04:00 PM)</option>
                                <option value="04:00 PM - 06:00 PM">Evening (04:00 PM - 06:00 PM)</option>
                            </select>
                        </div>

                        <!-- Tests -->
                        <div class="col-12">
                            <label class="form-label fw-semibold">Select Diagnostic Tests <span class="text-danger">*</span></label>
                            <select name="diagnostic_ids[]" id="select_tests" class="form-select select2-modal" multiple required data-placeholder="Select one or more tests">
                                @foreach($tests as $test)
                                    <option value="{{ $test->id }}" data-price="{{ $test->price }}">
                                        {{ $test->name }} (₹{{ number_format($test->price, 2) }})
                                    </option>
                                @endforeach
                            </select>
                            <div class="mt-2 text-end text-success fw-bold" id="total_price_label">
                                Total Amount: ₹0.00
                            </div>
                        </div>

                        <!-- Staff / Doctor Assignment -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Assign Doctor (Optional)</label>
                            <select name="assign_doctor_id" class="form-select select2-modal">
                                <option value="">Select Doctor</option>
                                @foreach($doctors as $doctor)
                                    <option value="{{ $doctor->id }}">Dr. {{ $doctor->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Payment info -->
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Payment Status <span class="text-danger">*</span></label>
                            <select name="payment_status" class="form-select" required>
                                <option value="pending">Pending</option>
                                <option value="paid" selected>Paid</option>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Payment Method <span class="text-danger">*</span></label>
                            <select name="payment_method" class="form-select" required>
                                <option value="Cash" selected>Cash</option>
                                <option value="Card">Card Checkout</option>
                                <option value="UPI">UPI / QR Code</option>
                            </select>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">Remarks (Optional)</label>
                            <textarea name="remarks" class="form-control" rows="2" placeholder="Clinical notes, symptoms, or special instructions"></textarea>
                        </div>
                    </div>

                    <div class="text-end mt-4">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary" id="submit_booking_btn">
                            <i class="fa fa-check me-1"></i> Book Diagnostic Test
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    var table = $('#bookings_table').DataTable({
        "serverSide": true,
        "processing": false,
        "order": [[0, "desc"]],
        "ajax": {
            "url": "{{ route('admin.diagnostic-bookings.data') }}",
            "type": "GET",
            "beforeSend": function() { $('#loader').show(); },
            "complete": function() { $('#loader').hide(); }
        }
    });

    // Select2 inside Modal
    $('#adminBookModal').on('shown.bs.modal', function () {
        $('.select2-modal').select2({
            dropdownParent: $('#adminBookModal'),
            width: '100%'
        });
    });

    // Reset form
    $('#adminBookModal').on('hidden.bs.modal', function () {
        $('#adminBookForm')[0].reset();
        $('#select_tests').val(null).trigger('change');
        $('#total_price_label').text('Total Amount: ₹0.00');
    });

    // Total price calculator
    $('#select_tests').on('change', function() {
        let total = 0;
        $('#select_tests option:selected').each(function() {
            let price = parseFloat($(this).data('price'));
            if (!isNaN(price)) {
                total += price;
            }
        });
        $('#total_price_label').text('Total Amount: ₹' + total.toFixed(2));
    });

    // Form submit
    $('#adminBookForm').on('submit', function(e) {
        e.preventDefault();

        const $btn = $('#submit_booking_btn');
        $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin me-1"></i> Booking...');

        $.ajax({
            url: "{{ route('admin.diagnostic-bookings.store') }}",
            type: "POST",
            data: $(this).serialize(),
            success: function(res) {
                $btn.prop('disabled', false).html('<i class="fa fa-check me-1"></i> Book Diagnostic Test');
                $('#adminBookModal').modal('hide');
                toastr.success(res.message);
                table.ajax.reload();
            },
            error: function(xhr) {
                $btn.prop('disabled', false).html('<i class="fa fa-check me-1"></i> Book Diagnostic Test');
                let message = 'Error booking test. Please try again.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    message = xhr.responseJSON.message;
                }
                toastr.error(message);
            }
        });
    });
});
</script>
@endpush
