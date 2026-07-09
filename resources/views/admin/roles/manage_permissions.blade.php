@extends('layouts.admin')

@section('title', 'Manage Permissions')
@section('page-title', 'Manage Permissions')

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
            <h2 class="mb-0 fw-bold">Manage Permissions: <span class="text-primary text-uppercase">{{ $role->name }}</span></h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 mt-1 small">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.roles.index') }}">Roles</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Manage Permissions</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('admin.roles.index') }}" class="btn btn-outline-secondary px-4 fw-semibold">
            <i class="fa fa-arrow-left me-1"></i> Back to Roles
        </a>
    </div>

    <form action="{{ route('admin.roles.permissions.update', $role->id) }}" method="POST">
        @csrf
        @method('PUT')

        <!-- Select All Header -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body p-3 d-flex align-items-center bg-light-primary rounded">
                <div class="form-check fs-5">
                    <input class="form-check-input border-primary" type="checkbox" id="selectAllPermissions">
                    <label class="form-check-label fw-bold text-primary ms-2" for="selectAllPermissions">
                        Select All Permissions
                    </label>
                </div>
            </div>
        </div>

        <!-- Grouped Modules Checkbox Blocks -->
        @foreach($groupedPermissions as $groupName => $permissions)
            <div class="card border-0 shadow-sm mb-4 group-container">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-bottom-0">
                    <h5 class="mb-0 fw-bold text-uppercase text-dark" style="letter-spacing: 0.5px;">{{ $groupName ?: 'General' }}</h5>
                    <div>
                        <button type="button" class="btn btn-sm btn-outline-secondary select-all-group me-1">Select All</button>
                        <button type="button" class="btn btn-sm btn-outline-danger deselect-all-group">Deselect All</button>
                    </div>
                </div>
                <div class="card-body px-4 pb-4 pt-1">
                    <div class="row">
                        @foreach($permissions as $perm)
                            <div class="col-md-4 col-lg-3 mb-3">
                                <div class="form-check form-switch card-check-wrapper p-3 rounded border">
                                    <input class="form-check-input permission-checkbox ms-0 float-end" type="checkbox" name="permissions[]" value="{{ $perm->name }}" id="perm-{{ $perm->id }}"
                                        {{ $role->hasPermissionTo($perm->name) ? 'checked' : '' }}>
                                    <label class="form-check-label text-capitalize text-dark fw-semibold ps-0" for="perm-{{ $perm->id }}">
                                        {{ str_replace('manage ', '', $perm->name) }}
                                    </label>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endforeach

        <!-- Submit Panel -->
        <div class="card border-0 shadow-sm mb-5">
            <div class="card-body p-3 text-end bg-light rounded">
                <a href="{{ route('admin.roles.index') }}" class="btn btn-secondary px-4 me-2">Cancel</a>
                <button type="submit" class="btn btn-primary px-5 fw-bold shadow-sm">Save & Update Permissions</button>
            </div>
        </div>
    </form>
</div>

<style>
    .bg-light-primary {
        background-color: #f0f4ff;
    }
    .card-check-wrapper {
        background-color: #fcfcfc;
        transition: border-color 0.2s, background-color 0.2s;
    }
    .card-check-wrapper:hover {
        background-color: #f8fafc;
        border-color: #cbd5e1 !important;
    }
</style>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Toggle Global "Select All"
        $('#selectAllPermissions').on('change', function() {
            var checked = this.checked;
            $('.permission-checkbox').each(function() {
                this.checked = checked;
            });
        });

        // Update "Select All" state dynamically
        function updateGlobalState() {
            var allChecked = true;
            $('.permission-checkbox').each(function() {
                if (!this.checked) {
                    allChecked = false;
                }
            });
            $('#selectAllPermissions').prop('checked', allChecked);
        }
        
        $('.permission-checkbox').on('change', updateGlobalState);
        updateGlobalState();

        // Select All inside Group
        $('.select-all-group').on('click', function() {
            $(this).closest('.group-container').find('.permission-checkbox').prop('checked', true);
            updateGlobalState();
        });

        // Deselect All inside Group
        $('.deselect-all-group').on('click', function() {
            $(this).closest('.group-container').find('.permission-checkbox').prop('checked', false);
            updateGlobalState();
        });
    });
</script>
@endpush
