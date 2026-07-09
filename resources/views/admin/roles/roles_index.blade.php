@extends('layouts.admin')

@section('title', 'Role List')
@section('page-title', 'Role List')

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
        <h2 class="mb-0 fw-bold">Role List</h2>
        <button class="btn btn-primary px-4 py-2 shadow-sm fw-semibold" data-bs-toggle="modal" data-bs-target="#addRoleModal">
            <i class="fa fa-plus-circle me-1"></i> Add Role
        </button>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 80px;">S.No.</th>
                            <th>Name</th>
                            <th>Created At</th>
                            <th style="width: 180px; text-align: right;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($roles as $index => $role)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    <span class="text-dark fw-bold text-capitalize">
                                        {{ str_replace('_', ' ', $role->name) }}
                                    </span>
                                </td>
                                <td>{{ $role->created_at ? $role->created_at->format('d-m-Y h:i A') : '-' }}</td>
                                <td style="text-align: right;">
                                    <!-- Manage Permissions Button -->
                                    <a href="{{ route('admin.roles.permissions', $role->id) }}" class="btn btn-sm btn-success me-1" title="Manage Permissions">
                                        <i class="fa fa-lock"></i>
                                    </a>

                                    <!-- Edit Button -->
                                    <button class="btn btn-sm btn-primary me-1" data-bs-toggle="modal" data-bs-target="#editRoleModal-{{ $role->id }}" title="Edit Role Name">
                                        <i class="fa fa-edit"></i>
                                    </button>

                                    <!-- Delete Button -->
                                    @if(!in_array(strtolower($role->name), ['admin', 'super admin']))
                                        <form action="{{ route('admin.roles.destroy', $role->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this role?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" title="Delete Role">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </form>
                                    @else
                                        <button class="btn btn-sm btn-secondary" disabled title="Administrative roles cannot be deleted">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    @endif
                                </td>
                            </tr>

                            <!-- Edit Role Modal -->
                            <div class="modal fade" id="editRoleModal-{{ $role->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog">
                                    <form action="{{ route('admin.roles.update', $role->id) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-content text-start">
                                            <div class="modal-header">
                                                <h5 class="modal-title fw-bold">Edit Role Name</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <label class="form-label fw-bold">Role Name <span class="text-danger">*</span></label>
                                                    <input type="text" name="name" class="form-control" value="{{ $role->name }}" required>
                                                </div>
                                            </div>
                                            <div class="modal-footer bg-light">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                <button type="submit" class="btn btn-primary px-4">Update Role</button>
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

<!-- Add Role Modal -->
<div class="modal fade" id="addRoleModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('admin.roles.store') }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Add Role</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-start">
                    <div class="mb-3">
                        <label for="role_name" class="form-label fw-bold">Role Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="role_name" class="form-control" placeholder="e.g. Receptionist" required>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4">Create Role</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
