@extends('layouts.admin')

@section('title', 'Diagnostic Categories')
@section('page-title', 'Diagnostic Categories')

@section('content')
<!-- Custom premium CSS styles for peerless tabs -->
<style>
.tab-container {
    border-bottom: 2px solid #eef2f5;
    margin-bottom: 25px;
    display: flex;
    gap: 20px;
}
.tab-item {
    font-size: 16px;
    font-weight: 600;
    color: #8c9ba5;
    padding: 10px 5px 15px;
    text-decoration: none;
    position: relative;
    transition: color 0.2s ease;
}
.tab-item:hover {
    color: #4a5d6e;
}
.tab-item.active {
    color: #4f46e5;
}
.tab-item.active::after {
    content: '';
    position: absolute;
    bottom: -2px;
    left: 0;
    right: 0;
    height: 3px;
    background-color: #4f46e5;
    border-radius: 3px;
}
</style>

<!-- Top navigation tabs -->
<div class="tab-container">
    <a href="{{ route('admin.diagnostics.indexDiag') }}" class="tab-item">
        Diagnostics
    </a>
    <a href="{{ route('admin.diagnostics.indexPath') }}" class="tab-item">
        Pathology
    </a>
    <a href="{{ route('admin.diagnostic-categories.index') }}" class="tab-item active">
        Diagnostic Categories
    </a>
</div>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="mb-0 fw-bold">📁 Diagnostic Categories</h3>
    <a href="{{ route('admin.diagnostic-categories.create') }}" class="btn btn-primary shadow-sm px-4 py-2 fw-semibold">
        <i class="fa fa-plus me-1"></i> Add Category
    </a>
</div>

<div style="position: relative;" class="card border-0 shadow-sm p-3">
    <div id="loader" class="table-loader" style="display:none;">
        <div class="spinner"></div>
    </div>

    <table id="categories_table" class="display stripe hover" style="width:100%">
        <thead>
            <tr>
                <th style="width: 60px;">Image</th>
                <th>Name</th>
                <th>Type</th>
                <th>Description</th>
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
    var table = $('#categories_table').DataTable({
        "serverSide": true,
        "processing": false,
        "ajax": {
            "url": "{{ route('admin.diagnostic-categories.data') }}",
            "type": "GET",
            "beforeSend": function() { $('#loader').show(); },
            "complete": function() { $('#loader').hide(); }
        }
    });

    $(document).on('change', '.toggle-status', function() {
        let categoryId = $(this).data('id');
        let status = $(this).is(':checked') ? 1 : 0;

        $.ajax({
            url: "{{ route('admin.diagnostic-categories.toggleStatus') }}",
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                id: categoryId,
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
