@extends('layouts.admin')

@section('title', isset($category) ? 'Edit Diagnostic Category' : 'Add Diagnostic Category')
@section('page-title', isset($category) ? 'Edit Category' : 'Add Category')

@section('content')
<div class="card shadow-sm p-4">
    <h4 class="mb-3">{{ isset($category) ? '📝 Edit Category' : '➕ Add New Diagnostic Category' }}</h4>

    <form action="{{ isset($category) ? route('admin.diagnostic-categories.update', $category->id) : route('admin.diagnostic-categories.store') }}"
        method="POST"
        enctype="multipart/form-data"
        onsubmit="return formValidation()">

        @csrf
        @if(isset($category))
        @method('PUT')
        @endif

        <!-- Name -->
        <div class="mb-3">
            <label for="name" class="form-label fw-bold">Category Name <span class="text-danger">*</span></label>
            <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror"
                value="{{ isset($category) ? $category->name : old('name') }}" placeholder="e.g., Pathology, Cardiology">
            <p id="nameError" class="text-danger small"></p>
            @error('name')
            <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>

        <!-- Type -->
        <div class="mb-3">
            <label for="type" class="form-label fw-bold">Type <span class="text-danger">*</span></label>
            <select name="type" id="type" class="form-select select2">
                <option value="diag" {{ (isset($category) ? $category->type : old('type')) === 'diag' ? 'selected' : '' }}>Diagnostic</option>
                <option value="path" {{ (isset($category) ? $category->type : old('type')) === 'path' ? 'selected' : '' }}>Pathology</option>
            </select>
        </div>

        <!-- Description -->
        <div class="mb-3">
            <label for="description" class="form-label fw-bold">Description</label>
            <textarea name="description" id="description" rows="4" class="form-control @error('description') is-invalid @enderror"
                placeholder="Enter a short description about this category">{{ isset($category) ? $category->description : old('description') }}</textarea>
            <p id="descriptionError" class="text-danger small"></p>
            @error('description')
            <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>

        <!-- Image Upload -->
        <div class="mb-3">
            <label for="image" class="form-label fw-bold">Image</label>
            <input type="file" name="image" id="image" class="form-control @error('image') is-invalid @enderror">
            @if(isset($category) && $category->image_url)
                <div class="mt-2">
                    <img src="{{ $category->image_url }}" class="img-thumbnail" style="max-width: 150px; max-height: 150px; object-fit: cover;">
                </div>
            @endif
            @error('image')
            <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>

        <!-- Status -->
        <div class="mb-3">
            <label class="form-label fw-bold">Status</label>
            <select name="status" class="form-select select2">
                <option value="1" {{ (isset($category) ? $category->status : old('status', 1)) == 1 ? 'selected' : '' }}>Active</option>
                <option value="0" {{ (isset($category) ? $category->status : old('status', 1)) == 0 ? 'selected' : '' }}>Inactive</option>
            </select>
        </div>

        <!-- Buttons -->
        <div class="d-flex justify-content-between">
            <a href="{{ route('admin.diagnostic-categories.index') }}" class="btn btn-secondary">
                ⬅ Back
            </a>
            <button type="submit" class="btn btn-primary">
                {{ isset($category) ? '💾 Update Category' : '💾 Save Category' }}
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

        var isValid = true;
        document.getElementById("nameError").textContent = "";
        document.getElementById("descriptionError").textContent = "";

        if (name === "") {
            document.getElementById("nameError").textContent = "Category Name is required.";
            isValid = false;
        } else if (name.length < 3) {
            document.getElementById("nameError").textContent = "Category Name must be at least 3 characters long.";
            isValid = false;
        }

        if (description.length > 500) {
            document.getElementById("descriptionError").textContent = "Description cannot exceed 500 characters.";
            isValid = false;
        }

        return isValid;
    }
</script>
@endpush
