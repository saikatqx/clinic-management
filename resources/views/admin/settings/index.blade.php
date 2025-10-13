@extends('layouts.admin')

@section('title', 'Clinic Settings')
@section('page-title', 'Clinic Settings')

@section('content')
<div class="card shadow-sm p-4">
    <h4 class="mb-3">⚙️ Manage Clinic Settings</h4>

    <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="row">

            <!-- Clinic Name -->
            <div class="col-md-6 mb-3">
                <label for="clinic_name" class="form-label fw-bold">Clinic Name</label>
                <input type="text" name="clinic_name" id="clinic_name" class="form-control"
                       value="{{ old('clinic_name', $setting->clinic_name ?? '') }}" required>
                @error('clinic_name') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <!-- Email -->
            <div class="col-md-6 mb-3">
                <label for="email" class="form-label fw-bold">Clinic Email</label>
                <input type="email" name="email" id="email" class="form-control"
                       value="{{ old('email', $setting->email ?? '') }}">
            </div>

            <!-- Mobile -->
            <div class="col-md-6 mb-3">
                <label for="mobile" class="form-label fw-bold">Clinic Mobile</label>
                <input type="text" name="mobile" id="mobile" class="form-control"
                       value="{{ old('mobile', $setting->mobile ?? '') }}">
            </div>

            <!-- Address -->
            <div class="col-md-6 mb-3">
                <label for="address" class="form-label fw-bold">Clinic Address</label>
                <input type="text" name="address" id="address" class="form-control"
                       value="{{ old('address', $setting->address ?? '') }}">
            </div>

            <!-- Google Map -->
            <div class="col-md-12 mb-3">
                <label for="location_link" class="form-label fw-bold">Google Map Location Link</label>
                <input type="text" name="location_link" id="location_link" class="form-control"
                       value="{{ old('location_link', $setting->location_link ?? '') }}">
            </div>

            <!-- Favicon -->
            <div class="col-md-6 mb-3">
                <label for="favicon" class="form-label fw-bold">Favicon (512KB Max)</label>
                <input type="file" name="favicon" id="favicon" class="form-control" accept="image/*">
                @if(!empty($setting->favicon))
                    <img src="{{ asset('images/settings/'.$setting->favicon) }}" class="mt-2" width="60" alt="Favicon">
                @endif
            </div>

            <!-- Clinic Logo -->
            <div class="col-md-6 mb-3">
                <label for="clinic_logo" class="form-label fw-bold">Clinic Logo (1MB Max)</label>
                <input type="file" name="clinic_logo" id="clinic_logo" class="form-control" accept="image/*">
                @if(!empty($setting->clinic_logo))
                    <img src="{{ asset('images/settings/'.$setting->clinic_logo) }}" class="mt-2 img-thumbnail" width="150">
                @endif
            </div>
        </div>

        <div class="d-flex justify-content-end mt-3">
            <button type="submit" class="btn btn-primary">💾 Save Settings</button>
        </div>
    </form>
</div>
@endsection
