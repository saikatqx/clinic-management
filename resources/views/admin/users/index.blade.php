@extends('layouts.admin')

@section('title', 'User Management')
@section('page-title', 'User Management')

@section('content')
<div class="container-fluid py-3">

    {{-- Alerts --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 mb-3" role="alert">
            <i class="fa fa-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0 mb-3" role="alert">
            <i class="fa fa-exclamation-circle me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-0 fw-bold">👤 User Management</h2>
            <p class="text-muted mb-0 small">Manage system users and their assigned roles</p>
        </div>
        @can('create users')
        <a href="{{ route('admin.users.create') }}" class="btn btn-primary px-4 py-2 shadow-sm fw-semibold">
            <i class="fa fa-plus-circle me-1"></i> Add New User
        </a>
        @endcan
    </div>

    {{-- Table Card --}}
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div style="position: relative;">
                <div id="loader" class="table-loader" style="display:none;">
                    <div class="spinner"></div>
                </div>
                <div class="p-3">
                    <table id="users_table" class="display stripe hover w-100">
                        <thead class="table-light">
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Roles</th>
                                <th>Email Status</th>
                                <th>Created At</th>
                                <th style="width:120px; text-align:right;">Action</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
$(document).ready(function () {
    $('#users_table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ route("admin.users.data") }}',
            beforeSend: function () { $('#loader').show(); },
            complete:   function () { $('#loader').hide(); },
        },
        columns: [
            { data: 0, name: 'name' },
            { data: 1, name: 'email' },
            { data: 2, name: 'roles', orderable: false },
            { data: 3, name: 'email_verified_at', orderable: false },
            { data: 4, name: 'created_at' },
            { data: 5, name: 'action', orderable: false, searchable: false, className: 'text-end' },
        ],
        order: [[4, 'desc']],
        pageLength: 10,
        responsive: true,
        language: {
            processing: '<div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div>',
            emptyTable: 'No users found.',
            zeroRecords: 'No matching users found.',
        },
    });
});
</script>
@endpush
