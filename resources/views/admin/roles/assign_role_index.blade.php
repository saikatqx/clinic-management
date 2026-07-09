@extends('layouts.admin')

@section('title', 'Assign Roles to Users')
@section('page-title', 'Assign Roles to Users')

@section('content')
<div class="container-fluid py-3">
    <!-- Success / Error Alerts -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 mb-3" role="alert">
            <i class="fa fa-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-0 fw-bold">User Role Assignment</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 mt-1 small">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item text-muted">Users</li>
                    <li class="breadcrumb-item active" aria-current="page">Assign Roles</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 80px;">S.No.</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Current Roles</th>
                            <th style="width: 120px; text-align: right;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $index => $user)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td><span class="fw-semibold text-dark">{{ $user->name }}</span></td>
                                <td>{{ $user->email }}</td>
                                <td>
                                    @if($user->roles->count())
                                        @foreach($user->roles as $role)
                                            <span class="badge bg-success px-2 py-1 text-uppercase text-white fw-semibold me-1">
                                                {{ $role->name }}
                                            </span>
                                        @endforeach
                                    @else
                                        <span class="text-muted small">No roles assigned</span>
                                    @endif
                                </td>
                                <td style="text-align: right;">
                                    @can('assign user roles')
                                    <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#assignModal-{{ $user->id }}" title="Assign Roles">
                                        <i class="fa fa-user-shield"></i>
                                    </button>
                                    @endcan
                                </td>
                            </tr>

                            <!-- Assign Modal -->
                            <div class="modal fade" id="assignModal-{{ $user->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog">
                                    <form action="{{ route('admin.assign-role.update') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="user_id" value="{{ $user->id }}">
                                        <div class="modal-content text-start">
                                            <div class="modal-header">
                                                <h5 class="modal-title fw-bold">User Role Assignment: <span class="text-primary">{{ $user->name }}</span></h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <p class="text-muted small mb-3">Select the roles to assign to this user. Unchecked roles will be revoked.</p>
                                                
                                                <div class="row">
                                                    @foreach($roles as $role)
                                                        <div class="col-md-6 mb-2">
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="checkbox" name="roles[]" value="{{ $role->name }}" id="role-{{ $user->id }}-{{ $role->id }}"
                                                                    {{ $user->hasRole($role->name) ? 'checked' : '' }}>
                                                                <label class="form-check-label text-capitalize text-dark fw-semibold" for="role-{{ $user->id }}-{{ $role->id }}">
                                                                    {{ str_replace('_', ' ', $role->name) }}
                                                                </label>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                            <div class="modal-footer bg-light">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                <button type="submit" class="btn btn-primary px-4">Update Assignment</button>
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
@endsection
