@extends('layouts.admin')

@section('title', 'Permissions')
@section('page-title', 'Permissions')

@section('content')
<div class="container-fluid py-3">
    <!-- Success / Error Alerts -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 mb-3" role="alert">
            <i class="fa fa-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0 mb-3" role="alert">
            <i class="fa fa-exclamation-circle me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-0 fw-bold">Permission Listing</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 mt-1 small">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item text-muted">Permissions</li>
                    <li class="breadcrumb-item active" aria-current="page">All Permissions</li>
                </ol>
            </nav>
        </div>
        @can('create permissions')
        <button class="btn btn-primary px-4 py-2 shadow-sm fw-semibold" data-bs-toggle="modal" data-bs-target="#addPermissionModal">
            <i class="fa fa-plus-circle me-1"></i> Add Permission
        </button>
        @endcan
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 80px;">S.No.</th>
                            <th>Name</th>
                            <th>Group</th>
                            <th>Created At</th>
                            <th style="width: 150px; text-align: right;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($permissions as $index => $perm)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    <span class="text-dark fw-bold text-capitalize">
                                        {{ str_replace('manage ', '', $perm->name) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border px-3 py-1 fw-semibold">
                                        {{ $perm->group_name ?? 'General' }}
                                    </span>
                                </td>
                                <td>{{ $perm->created_at ? $perm->created_at->format('d-m-Y h:i A') : '-' }}</td>
                                <td style="text-align: right;">
                                    @can('edit permissions')
                                    <!-- Edit Button -->
                                    <button class="btn btn-sm btn-primary me-1" data-bs-toggle="modal" data-bs-target="#editPermissionModal-{{ $perm->id }}" title="Edit Permission">
                                        <i class="fa fa-edit"></i>
                                    </button>
                                    @endcan

                                    @can('delete permissions')
                                    <!-- Delete Button -->
                                    <form action="{{ route('admin.permissions.destroy', $perm->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this permission?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" title="Delete Permission">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </form>
                                    @endcan
                                </td>
                            </tr>

                            <!-- Edit Permission Modal -->
                            <div class="modal fade" id="editPermissionModal-{{ $perm->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog">
                                    <form action="{{ route('admin.permissions.update', $perm->id) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-content text-start">
                                            <div class="modal-header">
                                                <h5 class="modal-title fw-bold">Edit Permission</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <label class="form-label fw-bold">Permission Name <span class="text-danger">*</span></label>
                                                    <input type="text" name="name" class="form-control" value="{{ $perm->name }}" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label fw-bold">Group <span class="text-danger">*</span></label>
                                                    <input type="text" name="group_name" class="form-control" value="{{ $perm->group_name }}" required>
                                                </div>
                                            </div>
                                            <div class="modal-footer bg-light">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                <button type="submit" class="btn btn-primary px-4">Update Permission</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Add Permission Modal -->
<div class="modal fade" id="addPermissionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('admin.permissions.store') }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Add Permission</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-start">
                    <div class="mb-3">
                        <label for="perm_name" class="form-label fw-bold">Permission Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="perm_name" class="form-control" placeholder="e.g. manage specialties" required>
                    </div>
                    <div class="mb-3">
                        <label for="perm_group" class="form-label fw-bold">Group / Module <span class="text-danger">*</span></label>
                        <input type="text" name="group_name" id="perm_group" class="form-control" placeholder="e.g. Specialties" required>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4">Create Permission</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
