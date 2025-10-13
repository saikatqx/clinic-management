@extends('layouts.admin')

@section('title', isset($service) ? 'Edit Service' : 'Add Service')
@section('page-title', isset($service) ? 'Edit Service' : 'Add Service')

@section('content')
<div class="card shadow-sm p-4">
    <h4 class="mb-3">{{ isset($service) ? '✏️ Edit Service' : '➕ Add New Service' }}</h4>

    <form action="{{ isset($service) ? route('admin.services.update', $service->id) : route('admin.services.store') }}"
        method="POST" enctype="multipart/form-data" onsubmit="return formValidation()">
        @csrf
        @if(isset($service))
        @method('PUT')
        @endif

        <div class="mb-3">
            <label for="name" class="form-label fw-bold">Service Name <span class="text-danger">*</span></label>
            <input type="text" name="name" id="name" class="form-control"
                value="{{ old('name', $service->name ?? '') }}" placeholder="Enter service name">
            <p id="nameError" class="text-danger"></p>
            @error('name')
            <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>

        <div class="mb-3">
            <label for="description" class="form-label fw-bold">Description</label>
            <textarea name="description" id="description" rows="4" class="form-control summernote"
                placeholder="Enter service description">{{ old('description', $service->description ?? '') }}</textarea>
            <p id="descriptionError" class="text-danger"></p>
            @error('description')
            <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>


        <div class="mb-3">
            <label for="image" class="form-label fw-bold">Service Image <span class="text-danger">*</span></label>
            <input type="file" name="image" id="image" class="form-control" accept="image/*">
            <p id="imageError" class="text-danger"></p>
            @error('image')
            <small class="text-danger">{{ $message }}</small>
            @enderror

            @if(isset($service) && $service->image)
            <div class="mt-2">
                <img src="{{ asset('images/services/'.$service->image) }}" alt="Service Image"
                    class="img-thumbnail" width="100">
            </div>
            @endif
        </div>

        <div class="mb-3">
            <label for="is_active" class="form-label fw-bold">Status</label>
            <select name="is_active" id="is_active" class="form-select">
                <option value="1" {{ old('is_active', $service->is_active ?? 1) == 1 ? 'selected' : '' }}>Active</option>
                <option value="0" {{ old('is_active', $service->is_active ?? 1) == 0 ? 'selected' : '' }}>Inactive</option>
            </select>
        </div>

        <div class="d-flex justify-content-between">
            <a href="{{ route('admin.services.index') }}" class="btn btn-secondary">⬅ Back</a>
            <button type="submit" class="btn btn-primary">
                {{ isset($service) ? '💾 Update Service' : '💾 Save Service' }}
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Initialize Summernote
        $('.summernote').summernote({
            height: 200,
            toolbar: [
                ['style', ['bold', 'italic', 'underline', 'clear']],
                ['font', ['fontsize', 'color']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['insert', ['link', 'picture']],
                ['view', ['fullscreen', 'codeview']]
            ]
        });
    });

    function formValidation() {
        let isValid = true;

        // Reset previous errors
        document.querySelectorAll('.text-danger').forEach(el => el.textContent = "");
        document.querySelectorAll('.form-control').forEach(el => el.classList.remove("is-invalid"));

        const name = document.getElementById("name");
        const image = document.getElementById("image");
        const hasExistingImage = "{{ isset($service) && $service->image ? 'true' : 'false' }}";

        // Validate Name
        if (name.value.trim() === "") {
            document.getElementById("nameError").textContent = "Service name is required.";
            name.classList.add("is-invalid");
            isValid = false;
        } else if (name.value.trim().length < 3) {
            document.getElementById("nameError").textContent = "Service name must be at least 3 characters.";
            name.classList.add("is-invalid");
            isValid = false;
        }

        // Validate Image
        if (image.files.length > 0) {
            const file = image.files[0];
            const maxSize = 300 * 1024; // 300KB
            if (file.size > maxSize) {
                document.getElementById("imageError").textContent = "Image must be less than 300KB.";
                image.classList.add("is-invalid");
                isValid = false;
            }
        } else if (hasExistingImage === "false") {
            document.getElementById("imageError").textContent = "Image is required.";
            image.classList.add("is-invalid");
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