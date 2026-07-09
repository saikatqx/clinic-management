@extends('layouts.admin')

@section('title', 'Roles & Permissions')
@section('page-title', 'Roles & Permissions')

@section('content')
<div class="container-fluid py-4">
    <!-- Success / Error Notifications -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 mb-4" role="alert">
            <i class="fa fa-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0 mb-4" role="alert">
            <i class="fa fa-exclamation-circle me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Tab Header Navigation -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white pb-0 pt-3 border-0">
            <ul class="nav nav-tabs card-header-tabs" id="roleTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active fw-bold text-primary" id="roles-tab" data-bs-toggle="tab" data-bs-target="#roles-pane" type="button" role="tab">
                        🔑 Roles Manager
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link fw-bold text-primary" id="permissions-tab" data-bs-toggle="tab" data-bs-target="#permissions-pane" type="button" role="tab">
                        🛡️ Permissions Registry
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link fw-bold text-primary" id="users-tab" data-bs-toggle="tab" data-bs-target="#users-pane" type="button" role="tab">
                        👨‍⚕️ User Role Assignment
                    </button>
                </li>
            </ul>
        </div>
        <div class="card-body p-4 tab-content" id="roleTabsContent">
            
            <!-- TAB 1: ROLES MANAGER -->
            <div class="tab-pane fade show active" id="roles-pane" role="tabpanel">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0 fw-bold">Active Roles</h5>
                    <button class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#addRoleModal">
                        <i class="fa fa-plus-circle me-1"></i> Add Custom Role
                    </button>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Role Name</th>
                                <th>Assigned Permissions</th>
                                <th style="width: 200px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($roles as $role)
                                <tr>
                                    <td>
                                        <span class="badge bg-primary px-3 py-2 text-uppercase fw-semibold" style="letter-spacing: 0.5px;">
                                            {{ $role->name }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($role->permissions->count())
                                            @foreach($role->permissions as $perm)
                                                <span class="badge bg-light text-dark border me-1 mb-1">
                                                    {{ $perm->name }}
                                                </span>
                                            @endforeach
                                        @else
                                            <span class="text-muted small">No permissions assigned.</span>
                                        @endif
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-primary me-1" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#editPermissionsModal-{{ $role->id }}">
                                            <i class="fa fa-shield me-1"></i> Permissions
                                        </button>
                                        
                                        @if(!in_array($role->name, ['admin', 'doctor', 'receptionist', 'patient']))
                                            <form action="{{ route('admin.roles-permissions.roles.destroy', $role->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this custom role?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>

                                <!-- Permissions Editor Modal -->
                                <div class="modal fade" id="editPermissionsModal-{{ $role->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-lg">
                                        <form action="{{ route('admin.roles-permissions.roles.update', $role->id) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title fw-bold">Configure Permissions for Role: <span class="text-primary text-uppercase">{{ $role->name }}</span></h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <p class="text-muted small mb-4">Toggle the privileges that this role should possess. Core resources might require multiple actions.</p>
                                                    <div class="row">
                                                        @foreach($permissions as $perm)
                                                            <div class="col-md-4 mb-3">
                                                                <div class="form-check form-switch">
                                                                    <input class="form-check-input" type="checkbox" name="permissions[]" value="{{ $perm->name }}" id="perm-{{ $role->id }}-{{ $perm->id }}"
                                                                        {{ $role->hasPermissionTo($perm->name) ? 'checked' : '' }}>
                                                                    <label class="form-check-label text-capitalize fw-semibold text-dark" for="perm-{{ $role->id }}-{{ $perm->id }}">
                                                                        {{ str_replace('manage ', '', $perm->name) }}
                                                                    </label>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                                <div class="modal-footer bg-light">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                    <button type="submit" class="btn btn-primary px-4">Save Permissions</button>
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

            <!-- TAB 2: PERMISSIONS REGISTRY -->
            <div class="tab-pane fade" id="permissions-pane" role="tabpanel">
                <h5 class="mb-3 fw-bold text-dark">Available Permissions</h5>
                <p class="text-muted small mb-4">The following is the system's hardcoded listing of permissions used across middlewares and gates to restrict access.</p>
                <div class="row">
                    @foreach($permissions as $perm)
                        <div class="col-md-3 mb-3">
                            <div class="card shadow-sm border border-light h-100">
                                <div class="card-body d-flex align-items-center py-3">
                                    <i class="fa fa-lock text-warning me-3 fs-5"></i>
                                    <div>
                                        <div class="fw-bold text-dark text-capitalize">{{ str_replace('manage ', '', $perm->name) }}</div>
                                        <span class="text-muted" style="font-size: 11px;">Guard: {{ $perm->guard_name }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- TAB 3: USER ROLE ASSIGNMENT -->
            <div class="tab-pane fade" id="users-pane" role="tabpanel">
                <h5 class="mb-4 fw-bold text-dark">User Roles Listing</h5>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Assigned Roles</th>
                                <th style="width: 250px;">Assign Role</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $user)
                                <tr>
                                    <td>
                                        <div class="fw-bold text-dark">{{ $user->name }}</div>
                                    </td>
                                    <td>{{ $user->email }}</td>
                                    <td>
                                        @if($user->roles->count())
                                            @foreach($user->roles as $userRole)
                                                <span class="badge bg-info px-2 py-1 text-uppercase text-dark fw-semibold">
                                                    {{ $userRole->name }}
                                                </span>
                                            @endforeach
                                        @else
                                            <span class="badge bg-secondary px-2 py-1 text-uppercase">
                                                {{ $user->role ?? 'patient' }}
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <form action="{{ route('admin.roles-permissions.assign') }}" method="POST" class="d-flex align-items-center">
                                            @csrf
                                            <input type="hidden" name="user_id" value="{{ $user->id }}">
                                            <select name="roles[]" class="form-select form-select-sm me-2 fw-semibold" style="max-width: 160px;">
                                                @foreach($roles as $r)
                                                    <option value="{{ $r->name }}" {{ $user->hasRole($r->name) ? 'selected' : '' }}>
                                                        {{ strtoupper($r->name) }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <button type="submit" class="btn btn-sm btn-primary">Update</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- Add Role Modal -->
<div class="modal fade" id="addRoleModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('admin.roles-permissions.roles.store') }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Add Custom Role</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="name" class="form-label fw-bold">Role Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="name" class="form-control" placeholder="e.g. Receptionist" required>
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
