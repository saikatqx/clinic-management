@extends('layouts.admin')

@section('title', 'Doctors')
@section('page-title', 'Doctors')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="mb-0">Doctors</h2>
    <a href="{{ route('admin.doctors.create') }}" class="btn btn-primary">
        ➕ Add New Doctor
    </a>
</div>

<div style="position: relative;">
    <!-- Loader positioned inside the table container -->
    <div id="loader" class="table-loader" style="display:none;">
        <div class="spinner"></div>
    </div>

    <table id="doctors_table" class="display stripe hover" style="width:100%">
        <thead>
            <tr>
                <th>Name</th>
                <th>Specialty</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Status</th>
                <th>Created At</th>
                <th style="width: 150px;">Action</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>
@endsection

@push('scripts')
<script>
    /*----------------- AJAX Table for Doctors ---------------*/
    $(document).ready(function() {
        var doctorsTbl = $('#doctors_table').DataTable({
            "lengthMenu": [
                [10, 25, 50, 100, 500, 1000],
                [10, 25, 50, 100, 500, 1000]
            ],
            "processing": false,
            "serverSide": true,
            "ajax": {
                "url": "{{ route('admin.doctors.data') }}", // ✅ New route for doctors data
                "type": "GET",
                "dataType": "json",
                "beforeSend": function() {
                    $('#loader').show();
                },
                "complete": function() {
                    $('#loader').hide();
                },
                "dataSrc": function(result) {
                    return result.data;
                },
                "error": function(error) {
                    console.log(error.responseText);
                }
            }
        });
    });

    /*----------------- Status Toggle (AJAX) ---------------*/
    $(document).on('change', '.toggle-status', function() {
        let doctorId = $(this).data('id');
        let isActive = $(this).is(':checked') ? 1 : 0;

        $.ajax({
            url: "{{ route('admin.doctors.toggleStatus') }}", 
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                id: doctorId,
                is_active: isActive
            },
            success: function(response) {
                toastr.success(response.message);
            },
            error: function(xhr) {
                alert("Error updating doctor status");
            }
        });
    });
</script>
@endpush
