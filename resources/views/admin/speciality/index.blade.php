@extends('layouts.admin')

@section('title', 'Specialties')
@section('page-title', 'Specialties')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="mb-0">Specialties</h2>
    <a href="{{ route('admin.specialties.create') }}" class="btn btn-primary">
        ➕ Add New Specialty
    </a>
</div>

<div style="position: relative;">
    <!-- Loader positioned inside the table container -->
    <div id="loader" class="table-loader" style="display:none;">
        <div class="spinner"></div>
    </div>

    <table id="specialties_table" class="display stripe hover" style="width:100%">
        <thead>
            <tr>
                <th>Name</th>
                <th>Description</th>
                <th>Status</th>
                <th>Created At</th>
                <th style="width: 120px;">Action</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>

@endsection

@push('scripts')
<script>
    /*----------------- AJAX Table ---------------*/
    $(document).ready(function() {
        var specialtiesTbl = $('#specialties_table').DataTable({
            "lengthMenu": [
                [10, 25, 50, 100, 500, 1000],
                [10, 25, 50, 100, 500, 1000]
            ],
            "processing": false,
            "serverSide": true,
            "ajax": {
                "url": "{{ route('admin.specialties.data') }}",
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

    $(document).on('change', '.toggle-status', function() {
        let specialtyId = $(this).data('id');
        let isActive = $(this).is(':checked') ? 1 : 0;

        $.ajax({
            url: "{{ route('admin.specialties.toggleStatus') }}", // create this route
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                id: specialtyId,
                is_active: isActive
            },
            success: function(response) {
                toastr.success(response.message);
            },
            error: function(xhr) {
                alert("Error updating status");
            }
        });
    });
</script>
@endpush