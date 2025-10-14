@extends('layouts.admin')

@section('title', 'Clinic Settings')
@section('page-title', 'Clinic Settings')

@section('content')
<div class="card shadow-sm p-4">
    <h4 class="mb-3">⚙️ Manage Clinic Settings</h4>

    <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data">
        @csrf

        {{-- BASIC INFO --}}
        <h5 class="mb-3">🏥 Clinic Info</h5>
        <div class="row">
            <!-- Clinic Name -->
            <div class="col-md-6 mb-3">
                <label for="clinic_name" class="form-label fw-bold">Clinic Name <span class="text-danger">*</span></label>
                <input type="text" name="clinic_name" id="clinic_name" class="form-control @error('clinic_name') is-invalid @enderror"
                       value="{{ old('clinic_name', $setting->clinic_name ?? '') }}" required>
                @error('clinic_name') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <!-- Email -->
            <div class="col-md-6 mb-3">
                <label for="email" class="form-label fw-bold">Clinic Email</label>
                <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror"
                       value="{{ old('email', $setting->email ?? '') }}">
                @error('email') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <!-- Mobile -->
            <div class="col-md-6 mb-3">
                <label for="mobile" class="form-label fw-bold">Clinic Mobile</label>
                <input type="text" name="mobile" id="mobile" class="form-control @error('mobile') is-invalid @enderror"
                       value="{{ old('mobile', $setting->mobile ?? '') }}">
                @error('mobile') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <!-- Address -->
            <div class="col-md-6 mb-3">
                <label for="address" class="form-label fw-bold">Clinic Address</label>
                <input type="text" name="address" id="address" class="form-control @error('address') is-invalid @enderror"
                       value="{{ old('address', $setting->address ?? '') }}">
                @error('address') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <!-- Google Map -->
            <div class="col-md-12 mb-3">
                <label for="location_link" class="form-label fw-bold">Google Map Location Link</label>
                <input type="url" name="location_link" id="location_link" class="form-control @error('location_link') is-invalid @enderror"
                       placeholder="https://maps.google.com/..."
                       value="{{ old('location_link', $setting->location_link ?? '') }}">
                @error('location_link') <small class="text-danger">{{ $message }}</small> @enderror
            </div>
        </div>

        {{-- SOCIAL LINKS --}}
        <hr class="my-4">
        <h5 class="mb-3">🌐 Social Links</h5>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="facebook_url" class="form-label fw-bold">
                    <i class="fab fa-facebook text-primary me-1"></i>Facebook URL
                </label>
                <input type="url" name="facebook_url" id="facebook_url" class="form-control @error('facebook_url') is-invalid @enderror"
                       placeholder="https://facebook.com/yourpage"
                       value="{{ old('facebook_url', $setting->facebook ?? '') }}">
                @error('facebook_url') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <div class="col-md-6 mb-3">
                <label for="instagram_url" class="form-label fw-bold">
                    <i class="fab fa-instagram text-danger me-1"></i>Instagram URL
                </label>
                <input type="url" name="instagram_url" id="instagram_url" class="form-control @error('instagram_url') is-invalid @enderror"
                       placeholder="https://instagram.com/yourhandle"
                       value="{{ old('instagram_url', $setting->instagram ?? '') }}">
                @error('instagram_url') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <div class="col-md-6 mb-3">
                <label for="youtube_url" class="form-label fw-bold">
                    <i class="fab fa-youtube text-danger me-1"></i>YouTube URL
                </label>
                <input type="url" name="youtube_url" id="youtube_url" class="form-control @error('youtube_url') is-invalid @enderror"
                       placeholder="https://youtube.com/@yourchannel"
                       value="{{ old('youtube_url', $setting->youtube ?? '') }}">
                @error('youtube_url') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <div class="col-md-6 mb-3">
                <label for="linkedin_url" class="form-label fw-bold">
                    <i class="fab fa-linkedin text-primary me-1"></i>LinkedIn URL
                </label>
                <input type="url" name="linkedin_url" id="linkedin_url" class="form-control @error('linkedin_url') is-invalid @enderror"
                       placeholder="https://linkedin.com/company/yourcompany"
                       value="{{ old('linkedin_url', $setting->linkedin ?? '') }}">
                @error('linkedin_url') <small class="text-danger">{{ $message }}</small> @enderror
            </div>
        </div>

        {{-- BRANDING / FILES --}}
        <hr class="my-4">
        <h5 class="mb-3">🎨 Branding</h5>
        <div class="row">
            <!-- Favicon -->
            <div class="col-md-6 mb-3">
                <label for="favicon" class="form-label fw-bold">Favicon (PNG/JPG/ICO • Max 512KB)</label>
                <input type="file" name="favicon" id="favicon" class="form-control @error('favicon') is-invalid @enderror" accept="image/*">
                @error('favicon') <small class="text-danger d-block mt-1">{{ $message }}</small> @enderror

                @if(!empty($setting->favicon))
                    <div class="mt-2">
                        <img src="{{ asset('images/settings/'.$setting->favicon) }}" class="border rounded" width="60" height="60" alt="Favicon">
                    </div>
                @endif
            </div>

            <!-- Clinic Logo -->
            <div class="col-md-6 mb-3">
                <label for="clinic_logo" class="form-label fw-bold">Clinic Logo (PNG/JPG/SVG • Max 1MB)</label>
                <input type="file" name="clinic_logo" id="clinic_logo" class="form-control @error('clinic_logo') is-invalid @enderror" accept="image/*">
                @error('clinic_logo') <small class="text-danger d-block mt-1">{{ $message }}</small> @enderror

                @if(!empty($setting->clinic_logo))
                    <div class="mt-2">
                        <img src="{{ asset('images/settings/'.$setting->clinic_logo) }}" class="img-thumbnail" width="160" alt="Clinic Logo">
                    </div>
                @endif
            </div>
        </div>

        {{-- SUBMIT --}}
        <div class="d-flex justify-content-end mt-3">
            <button type="submit" class="btn btn-primary">💾 Save Settings</button>
        </div>
    </form>
</div>
@endsection
