@extends('layouts.admin')

@section('title', 'Add New User')
@section('page-title', 'Add New User')

@section('content')
<div class="container-fluid py-3">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-0 fw-bold">➕ Add New User</h2>
            <p class="text-muted mb-0 small">Create a new system user and assign roles</p>
        </div>
        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary shadow-sm">
            <i class="fa fa-arrow-left me-1"></i> Back to Users
        </a>
    </div>

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0 mb-4">
            <i class="fa fa-exclamation-circle me-2"></i>
            <strong>Please fix the following errors:</strong>
            <ul class="mb-0 mt-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <form action="{{ route('admin.users.store') }}" method="POST">
        @csrf

        <div class="row g-4">
            {{-- Left Column: User Info --}}
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white py-3 border-bottom">
                        <h5 class="mb-0 fw-semibold"><i class="fas fa-user me-2 text-primary"></i>User Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Full Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                    value="{{ old('name') }}" placeholder="Enter full name" required>
                                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Email Address <span class="text-danger">*</span></label>
                                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                                    value="{{ old('email') }}" placeholder="Enter email address" required>
                                @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Password <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="password" name="password" id="password"
                                        class="form-control @error('password') is-invalid @enderror"
                                        placeholder="Minimum 8 characters" required>
                                    <button type="button" class="btn btn-outline-secondary" onclick="togglePassword('password')">
                                        <i class="fas fa-eye" id="password-icon"></i>
                                    </button>
                                </div>
                                @error('password') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Confirm Password <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="password" name="password_confirmation" id="password_confirmation"
                                        class="form-control" placeholder="Re-enter password" required>
                                    <button type="button" class="btn btn-outline-secondary" onclick="togglePassword('password_confirmation')">
                                        <i class="fas fa-eye" id="password_confirmation-icon"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="email_verified" id="email_verified" value="1"
                                        {{ old('email_verified') ? 'checked' : '' }}>
                                    <label class="form-check-label fw-semibold" for="email_verified">
                                        Mark email as verified
                                    </label>
                                    <small class="text-muted d-block">Check this to bypass email verification for this user.</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Right Column: Role Assignment --}}
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white py-3 border-bottom">
                        <h5 class="mb-0 fw-semibold"><i class="fas fa-shield-alt me-2 text-primary"></i>Assign Roles</h5>
                    </div>
                    <div class="card-body">
                        @if($roles->isEmpty())
                            <p class="text-muted small">No roles available. <a href="{{ route('admin.roles.index') }}">Create a role first.</a></p>
                        @else
                            <p class="text-muted small mb-3">Select one or more roles for this user.</p>
                            @foreach($roles as $role)
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" name="roles[]"
                                        id="role_{{ $role->id }}" value="{{ $role->name }}"
                                        {{ in_array($role->name, old('roles', [])) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="role_{{ $role->id }}">
                                        <span class="badge bg-primary me-1">{{ ucfirst($role->name) }}</span>
                                    </label>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>

                {{-- Submit Card --}}
                <div class="card border-0 shadow-sm mt-3">
                    <div class="card-body">
                        <button type="submit" class="btn btn-primary w-100 fw-semibold py-2">
                            <i class="fa fa-save me-1"></i> Create User
                        </button>
                        <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary w-100 mt-2">
                            Cancel
                        </a>
                    </div>
                </div>
            </div>
        </div>

    </form>
</div>

@push('scripts')
<script>
function togglePassword(id) {
    const input = document.getElementById(id);
    const icon  = document.getElementById(id + '-icon');
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.replace('fa-eye', 'fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.replace('fa-eye-slash', 'fa-eye');
    }
}
</script>
@endpush
@endsection
