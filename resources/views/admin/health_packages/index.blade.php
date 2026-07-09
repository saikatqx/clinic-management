@extends('layouts.admin')

@section('title', 'Health Packages')
@section('page-title', 'Health Packages')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="mb-0">Health Packages</h2>
    @can('create health packages')
    <a href="{{ route('admin.health-packages.create') }}" class="btn btn-primary shadow-sm">
        <i class="fa fa-plus me-1"></i> Add New Package
    </a>
    @endcan
</div>

<div style="position: relative;">
    <div id="loader" class="table-loader" style="display:none;">
        <div class="spinner"></div>
    </div>

    <table id="packages_table" class="display stripe hover" style="width:100%">
        <thead>
            <tr>
                <th style="width: 60px;">Image</th>
                <th>Package Name</th>
                <th>Gender</th>
                <th>Actual Price</th>
                <th>Package Price</th>
                <th style="width: 80px;">Status</th>
                <th style="width: 100px;">Action</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    var table = $('#packages_table').DataTable({
        "serverSide": true,
        "processing": false,
        "ajax": {
            "url": "{{ route('admin.health-packages.data') }}",
            "type": "GET",
            "beforeSend": function() { $('#loader').show(); },
            "complete": function() { $('#loader').hide(); }
        }
    });

    $(document).on('change', '.toggle-status', function() {
        let packageId = $(this).data('id');
        let status = $(this).is(':checked') ? 1 : 0;

        $.ajax({
            url: "{{ route('admin.health-packages.toggleStatus') }}",
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                id: packageId,
                status: status
            },
            success: function(res) {
                toastr.success(res.message);
            },
            error: function() {
                toastr.error("Error updating status");
            }
        });
    });
});
</script>
@endpush
