@extends('layouts.admin')

@section('title', 'Appointments')
@section('page-title', 'Appointments')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="mb-0">Appointments</h2>
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
});
</script>
@endpush
