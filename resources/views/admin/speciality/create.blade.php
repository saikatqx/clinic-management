@extends('layouts.admin')

@section('title', 'Add Specialty')
@section('page-title', 'Add Specialty')

@section('content')
<div class="card shadow-sm p-4">
    <h4 class="mb-3">➕ Add New Specialty</h4>

    <form action="{{ isset($specialty) ? route('admin.specialties.update', $specialty->id) : route('admin.specialties.store') }}"
        method="POST"
        enctype="multipart/form-data"
        onsubmit="return formValidation()">

        @csrf
        @if(isset($specialty))
        @method('PUT') {{-- Laravel requires PUT for update --}}
        @endif

        <!-- Name -->
        <div class="mb-3">
            <label for="name" class="form-label fw-bold">Specialty Name <span class="text-danger">*</span></label>
            <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror"
                value="{{ isset($specialty) ? $specialty->name : old('name') }}" placeholder="e.g., Cardiology">
            <p id="nameError" class="text-danger"></p>
            @error('name')
            <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>

        <!-- Description -->
        <div class="mb-3">
            <label for="description" class="form-label fw-bold">Description</label>
            <textarea name="description" id="description" rows="4" class="form-control @error('description') is-invalid @enderror"
                placeholder="Enter a short description about this specialty">{{ isset($specialty) ? $specialty->description : old('description') }}</textarea>
            <p id="descriptionError" class="text-danger"></p>
            @error('description')
            <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>

        <!-- Status -->
        <div class="mb-3">
            <label class="form-label fw-bold">Status</label>
            <select name="is_active" class="form-select">
                <option value="1"
                    {{ (isset($specialty) ? $specialty->is_active : old('is_active', 1)) == 1 ? 'selected' : '' }}>
                    Active
                </option>
                <option value="0"
                    {{ (isset($specialty) ? $specialty->is_active : old('is_active', 1)) == 0 ? 'selected' : '' }}>
                    Inactive
                </option>
            </select>
        </div>


        <!-- Buttons -->
        <div class="d-flex justify-content-between">
            <a href="{{ route('admin.specialties.index') }}" class="btn btn-secondary">
                ⬅ Back
            </a>
            <button type="submit" class="btn btn-primary">
                {{ isset($specialty) ? '💾 Update Specialty' : '💾 Save Specialty' }}
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    function formValidation() {
        var name = document.getElementById("name").value.trim();
        var description = document.getElementById("description").value.trim();

        // ✅ Reset error messages
        var errorElements = document.getElementsByClassName("text-sm text-red-500");
        for (var i = 0; i < errorElements.length; i++) {
            errorElements[i].textContent = "";
        }

        var isValid = true;

        // 🔎 Validate Specialty Name
        if (name === "") {
            document.getElementById("nameError").textContent = "Specialty Name is required.";
            isValid = false;
        } else if (name.length < 3) {
            document.getElementById("nameError").textContent = "Specialty Name must be at least 3 characters long.";
            isValid = false;
        }

        // 🔎 Validate Description (optional but max length)
        if (description.length > 500) {
            document.getElementById("descriptionError").textContent = "Description cannot exceed 500 characters.";
            isValid = false;
        }

        return isValid;
    }
</script>
@endpush