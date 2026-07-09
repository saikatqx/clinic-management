@extends('layouts.admin')

@section('title', 'Appointments')
@section('page-title', 'Appointments')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="mb-0">Appointments</h2>
    <button type="button" class="btn btn-primary btn-sm px-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#bookWalkInModal">
        <i class="fa fa-plus me-1"></i> Book Walk-In Patient
    </button>
</div>

<div style="position: relative;">
    <div id="loader" class="table-loader" style="display:none;">
        <div class="spinner"></div>
    </div>

    <table id="appointments_table" class="display stripe hover" style="width:100%">
        <thead>
            <tr>
                <th>ID</th>
                <th>Doctor</th>
                <th>Patient</th>
                <th>Date & Time</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>

<!-- Book Walk-in Patient Modal -->
<div class="modal fade" id="bookWalkInModal" tabindex="-1" aria-labelledby="bookWalkInModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="bookWalkInModalLabel"><i class="fa fa-clinic-medical me-2"></i> Book Walk-In Patient</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-start">
                <form id="bookWalkInForm">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Doctor <span class="text-danger">*</span></label>
                            <select name="doctor_id" id="walkin_doctor_id" class="form-select" required data-placeholder="Select Doctor">
                                <option value="">Select Doctor</option>
                                @foreach($doctors as $doctor)
                                    <option value="{{ $doctor->id }}">
                                        {{ $doctor->name }} ({{ optional($doctor->specialty)->name ?? 'No Specialty' }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Appointment Date <span class="text-danger">*</span></label>
                            <input type="date" id="walkin_booking_date" class="form-control" required min="{{ date('Y-m-d') }}">
                        </div>

                        <input type="hidden" name="appointment_date" id="walkin_appointment_date">

                        <div class="col-12 text-start">
                            <label class="form-label fw-semibold d-block">Available Time Slots <span class="text-danger">*</span></label>
                            <div id="walkin_slots_container" class="d-flex flex-wrap gap-2 p-3 bg-light rounded-3 border" style="min-height: 58px;">
                                <span class="text-muted small">Please select a doctor and date to load available time slots.</span>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Patient Name <span class="text-danger">*</span></label>
                            <input type="text" name="patient_name" class="form-control" placeholder="Enter patient name" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Patient Phone <span class="text-danger">*</span></label>
                            <input type="text" name="patient_phone" class="form-control" placeholder="Enter phone number" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Patient Email (Optional)</label>
                            <input type="email" name="patient_email" class="form-control" placeholder="patient@example.com">
                            <small class="text-muted">A confirmation/update email will be sent if provided.</small>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Status <span class="text-danger">*</span></label>
                            <select name="status" class="form-select" required>
                                <option value="Confirmed" selected>Confirmed</option>
                                <option value="Pending">Pending</option>
                                <option value="Cancelled">Cancelled</option>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Payment Status <span class="text-danger">*</span></label>
                            <select name="payment_status" class="form-select" required>
                                <option value="Paid" selected>Paid</option>
                                <option value="Pending">Pending</option>
                            </select>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">Internal Notes (Optional)</label>
                            <textarea name="notes" class="form-control" rows="2" placeholder="Symptom details, triage information, etc."></textarea>
                        </div>
                    </div>

                    <div class="text-end mt-4">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary" id="walkin_submit_btn">
                            <i class="fa fa-check me-1"></i> Book Appointment
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
    var table = $('#appointments_table').DataTable({
        ajax: {
            url: "{{ route('admin.appointments.data') }}",
            type: "GET",
            dataSrc: "data",
            beforeSend: function() { $('#loader').show(); },
            complete: function() { $('#loader').hide(); }
        }
    });

    // AJAX status update
    $(document).on('click', '.update-status', function() {
        let id = $(this).data('id');
        let status = $(this).data('status');

        $.ajax({
            url: "{{ route('admin.appointments.updateStatus') }}",
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                id: id,
                status: status
            },
            success: function(res) {
                toastr.success(res.message);
                table.ajax.reload();
            },
            error: function(err) {
                toastr.error('Error updating status');
            }
        });
    });

    // Initialize Select2 inside Modal
    $('#bookWalkInModal').on('shown.bs.modal', function () {
        $('#walkin_doctor_id').select2({
            dropdownParent: $('#bookWalkInModal'),
            width: '100%',
            placeholder: 'Select Doctor',
            allowClear: true
        });
    });

    // Reset modal form on hide
    $('#bookWalkInModal').on('hidden.bs.modal', function () {
        $('#bookWalkInForm')[0].reset();
        $('#walkin_doctor_id').val(null).trigger('change');
        $('#walkin_appointment_date').val('');
        $('#walkin_slots_container').html('<span class="text-muted small">Please select a doctor and date to load available time slots.</span>');
    });

    function loadWalkInSlots() {
        const doctorId = $('#walkin_doctor_id').val();
        const date = $('#walkin_booking_date').val();
        const $container = $('#walkin_slots_container');

        if (!doctorId || !date) {
            $container.html('<span class="text-muted small">Please select a doctor and date to load available time slots.</span>');
            return;
        }

        $container.html('<span class="text-muted small"><i class="fa fa-spinner fa-spin me-1"></i> Loading available slots...</span>');
        $('#walkin_appointment_date').val('');

        $.ajax({
            url: "{{ route('appointments.slots') }}",
            type: "GET",
            data: {
                doctor_id: doctorId,
                date: date,
                include_buffers: 1
            },
            success: function (res) {
                $container.empty();
                if (res.slots && res.slots.length > 0) {
                    res.slots.forEach(function (slot) {
                        const btnClass = slot.is_buffer ? 'btn-outline-warning' : 'btn-outline-primary';
                        const labelText = slot.is_buffer ? ' (Buffer)' : '';
                        $container.append(`
                            <button type="button" class="btn ${btnClass} btn-sm walkin-slot-btn" data-datetime="${slot.datetime}" data-buffer="${slot.is_buffer ? 1 : 0}">
                                ${slot.time}${labelText}
                            </button>
                        `);
                    });
                } else {
                    $container.html('<span class="text-danger small"><i class="fa fa-exclamation-circle me-1"></i> No available slots for this day. Please select another date.</span>');
                }
            },
            error: function (xhr) {
                $container.html('<span class="text-danger small"><i class="fa fa-exclamation-circle me-1"></i> Error loading slots. Please try again.</span>');
            }
        });
    }

    // Trigger slots loading when doctor or date changes
    $(document).on('change', '#walkin_doctor_id, #walkin_booking_date', function () {
        loadWalkInSlots();
    });

    // Handle slot badge click
    $(document).on('click', '.walkin-slot-btn', function () {
        $('.walkin-slot-btn').each(function() {
            const isBuf = $(this).data('buffer') === 1;
            if (isBuf) {
                $(this).removeClass('btn-warning btn-primary text-white btn-outline-primary').addClass('btn-outline-warning');
            } else {
                $(this).removeClass('btn-warning btn-primary text-white btn-outline-warning').addClass('btn-outline-primary');
            }
        });

        const isClickedBuf = $(this).data('buffer') === 1;
        if (isClickedBuf) {
            $(this).removeClass('btn-outline-warning').addClass('btn-warning text-white');
        } else {
            $(this).removeClass('btn-outline-primary').addClass('btn-primary text-white');
        }
        $('#walkin_appointment_date').val($(this).data('datetime'));
    });

    // Form submission
    $('#bookWalkInForm').on('submit', function (e) {
        e.preventDefault();

        if (!$('#walkin_appointment_date').val()) {
            toastr.error('Please select an available time slot.');
            return;
        }

        const $btn = $('#walkin_submit_btn');
        $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin me-1"></i> Booking...');

        $.ajax({
            url: "{{ route('admin.appointments.store') }}",
            type: "POST",
            data: $(this).serialize(),
            success: function (res) {
                $btn.prop('disabled', false).html('<i class="fa fa-check me-1"></i> Book Appointment');
                $('#bookWalkInModal').modal('hide');
                toastr.success(res.message || 'Walk-in appointment booked successfully!');
                table.ajax.reload();
            },
            error: function (xhr) {
                $btn.prop('disabled', false).html('<i class="fa fa-check me-1"></i> Book Appointment');
                let message = 'An error occurred. Please try again.';
                if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                    const errors = xhr.responseJSON.errors;
                    const firstKey = Object.keys(errors)[0];
                    message = errors[firstKey][0];
                } else if (xhr.responseJSON && xhr.responseJSON.message) {
                    message = xhr.responseJSON.message;
                }
                toastr.error(message);
            }
        });
    });
});
</script>
@endpush
