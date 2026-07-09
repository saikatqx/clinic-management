@extends('layouts.admin')

@section('title', $type === 'path' ? 'Pathology Tests' : 'Diagnostic Tests')
@section('page-title', $type === 'path' ? 'Pathology' : 'Diagnostics')

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
    <a href="{{ route('admin.diagnostics.indexDiag') }}" class="tab-item {{ $type === 'diag' ? 'active' : '' }}">
        Diagnostics
    </a>
    <a href="{{ route('admin.diagnostics.indexPath') }}" class="tab-item {{ $type === 'path' ? 'active' : '' }}">
        Pathology
    </a>
    <a href="{{ route('admin.diagnostic-categories.index') }}" class="tab-item">
        Diagnostic Categories
    </a>
</div>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="mb-0 fw-bold">{{ $type === 'path' ? '🧪 Pathology Tests Directory' : '🦥 Diagnostic Tests Directory' }}</h3>
    @can('create diagnostic tests')
    <a href="{{ route('admin.diagnostics.create', ['type' => $type]) }}" class="btn btn-primary shadow-sm px-4 py-2 fw-semibold">
        <i class="fa fa-plus me-1"></i> Add {{ $type === 'path' ? 'Pathology' : 'Diagnostic' }}
    </a>
    @endcan
</div>

<div style="position: relative;" class="card border-0 shadow-sm p-3">
    <div id="loader" class="table-loader" style="display:none;">
        <div class="spinner"></div>
    </div>

    <table id="diagnostics_table" class="display stripe hover" style="width:100%">
        <thead>
            <tr>
                <th style="width: 80px;">Icon</th>
                <th>Name</th>
                <th>Category</th>
                <th>Price</th>
                <th style="width: 100px;">Status</th>
                <th style="width: 120px;">Action</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    var table = $('#diagnostics_table').DataTable({
        "serverSide": true,
        "processing": false,
        "ajax": {
            "url": "{{ route('admin.diagnostics.data', ['type' => $type]) }}",
            "type": "GET",
            "beforeSend": function() { $('#loader').show(); },
            "complete": function() { $('#loader').hide(); }
        }
    });

    $(document).on('change', '.toggle-status', function() {
        let diagnosticId = $(this).data('id');
        let status = $(this).is(':checked') ? 1 : 0;

        $.ajax({
            url: "{{ route('admin.diagnostics.toggleStatus') }}",
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                id: diagnosticId,
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
