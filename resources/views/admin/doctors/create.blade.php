@extends('layouts.admin')

@section('title', isset($doctor) ? 'Edit Doctor' : 'Add Doctor')
@section('page-title', isset($doctor) ? 'Edit Doctor' : 'Add Doctor')

@section('content')
<div class="card shadow-sm p-4">
    <h4 class="mb-3">
        {{ isset($doctor) ? '✏️ Edit Doctor' : '➕ Add New Doctor' }}
    </h4>

    <form action="{{ isset($doctor) ? route('admin.doctors.update', $doctor->id) : route('admin.doctors.store') }}"
        method="POST" enctype="multipart/form-data" onsubmit="return formValidation()">
        @csrf
        @if(isset($doctor))
        @method('PUT')
        @endif

        <div class="row">
            <!-- Doctor Name -->
            <div class="col-md-6 mb-3">
                <label for="name" class="form-label fw-bold">Doctor Name <span class="text-danger">*</span></label>
                <input type="text" name="name" id="name" class="form-control"
                    value="{{ old('name', $doctor->name ?? '') }}" placeholder="Enter doctor's name">
                <p id="nameError" class="text-danger"></p>
            </div>

            <!-- Specialty -->
            <div class="col-md-6 mb-3">
                <label for="specialty_id" class="form-label fw-bold">Specialty <span class="text-danger">*</span></label>
                <select name="specialty_id" id="specialty_id" class="form-select select2">
                    <option value="">-- Select Specialty --</option>
                    @foreach($specialties as $specialty)
                    <option value="{{ $specialty->id }}"
                        {{ old('specialty_id', $doctor->specialty_id ?? '') == $specialty->id ? 'selected' : '' }}>
                        {{ $specialty->name }}
                    </option>
                    @endforeach
                </select>
                <p id="specialtyError" class="text-danger"></p>
            </div>

            <!-- Email -->
            <div class="col-md-6 mb-3">
                <label for="email" class="form-label fw-bold">Email <span class="text-danger">*</span></label>
                <input type="email" name="email" id="email" class="form-control"
                    value="{{ old('email', $doctor->email ?? '') }}" placeholder="Enter email">
                <p id="emailError" class="text-danger"></p>
            </div>

            <!-- Phone -->
            <div class="col-md-6 mb-3">
                <label for="phone" class="form-label fw-bold">Phone</label>
                <input type="text" name="phone" id="phone" class="form-control"
                    value="{{ old('phone', $doctor->phone ?? '') }}" placeholder="Enter phone number">
                <p id="phoneError" class="text-danger"></p>
            </div>

            <!-- Qualification -->
            <div class="col-md-6 mb-3">
                <label for="qualification" class="form-label fw-bold">Qualification</label>
                <input type="text" name="qualification" id="qualification" class="form-control"
                    value="{{ old('qualification', $doctor->qualification ?? '') }}" placeholder="e.g., MBBS, MD">
            </div>

            <!-- Profile Image -->
            <div class="col-md-6 mb-3">
                <label for="profile_image" class="form-label fw-bold">Profile Image</label>
                <input type="file" name="profile_image" id="profile_image" class="form-control" accept="image/*">
                <p id="imageError" class="text-danger"></p>

                @if(isset($doctor) && $doctor->profile_image)
                <img src="{{ asset('images/doctors/'.$doctor->profile_image) }}" class="img-thumbnail mt-2" width="100">
                @endif
            </div>

            <!-- Bio -->
            <div class="col-md-12 mb-3">
                <label for="bio" class="form-label fw-bold">Bio</label>
                <textarea name="bio" id="bio" rows="4" class="form-control"
                    placeholder="Short description about the doctor">{{ old('bio', $doctor->bio ?? '') }}</textarea>
            </div>

            <!-- Status -->
            <div class="col-md-4 mb-3">
                <label for="is_active" class="form-label fw-bold">Status</label>
                <select name="is_active" id="is_active" class="form-select">
                    <option value="1" {{ old('is_active', $doctor->is_active ?? 1) == 1 ? 'selected' : '' }}>Active</option>
                    <option value="0" {{ old('is_active', $doctor->is_active ?? 1) == 0 ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
        </div>

        <!-- Buttons -->
        <div class="d-flex justify-content-between mt-3">
            <a href="{{ route('admin.doctors.index') }}" class="btn btn-secondary">⬅ Back</a>
            <button type="submit" class="btn btn-primary">
                {{ isset($doctor) ? '💾 Update Doctor' : '💾 Save Doctor' }}
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function () {
        $('.select2').select2();
    });

    function formValidation() {
        let isValid = true;

        // Reset error messages
        document.querySelectorAll('.text-danger').forEach(el => el.textContent = "");

        // Fields
        let name = document.getElementById("name");
        let specialty = document.getElementById("specialty_id");
        let email = document.getElementById("email");

        name.classList.remove("is-invalid");
        specialty.classList.remove("is-invalid");
        email.classList.remove("is-invalid");

        if (name.value.trim() === "") {
            document.getElementById("nameError").textContent = "Doctor name is required";
            name.classList.add("is-invalid");
            isValid = false;
        }

        if (specialty.value === "") {
            document.getElementById("specialtyError").textContent = "Specialty is required";
            specialty.classList.add("is-invalid");
            isValid = false;
        }

        if (email.value.trim() === "") {
            document.getElementById("emailError").textContent = "Email is required";
            email.classList.add("is-invalid");
            isValid = false;
        } else if (!/\S+@\S+\.\S+/.test(email.value.trim())) {
            document.getElementById("emailError").textContent = "Enter a valid email address";
            email.classList.add("is-invalid");
            isValid = false;
        }

        return isValid;
    }
</script>

<style>
    .is-invalid {
        border: 1px solid red !important;
        background-color: #ffe6e6;
    }
</style>
@endpush