@extends('layouts.admin')

@section('title', isset($banner) ? 'Edit Banner' : 'Add Banner')
@section('page-title', isset($banner) ? 'Edit Banner' : 'Add Banner')

@section('content')
<div class="card shadow-sm p-4">
    <h4 class="mb-3">{{ isset($banner) ? '✏️ Edit Banner' : '➕ Add New Banner' }}</h4>

    <form action="{{ isset($banner) ? route('admin.banners.update', $banner->id) : route('admin.banners.store') }}"
          method="POST" enctype="multipart/form-data" onsubmit="return validateBannerForm()">
        @csrf
        @if(isset($banner))
            @method('PUT')
        @endif

        <div class="row">
            <!-- Title -->
            <div class="col-md-6 mb-3">
                <label for="title" class="form-label fw-bold">Title</label>
                <input type="text" name="title" id="title" class="form-control"
                       value="{{ old('title', $banner->title ?? '') }}" placeholder="Enter banner title">
                <p id="titleError" class="text-danger"></p>
                @error('title')<small class="text-danger">{{ $message }}</small>@enderror
            </div>

            <!-- Subtitle -->
            <div class="col-md-6 mb-3">
                <label for="subtitle" class="form-label fw-bold">Subtitle</label>
                <input type="text" name="subtitle" id="subtitle" class="form-control"
                       value="{{ old('subtitle', $banner->subtitle ?? '') }}" placeholder="Enter banner subtitle">
                <p id="subtitleError" class="text-danger"></p>
                @error('subtitle')<small class="text-danger">{{ $message }}</small>@enderror
            </div>

            <!-- Button Text -->
            <div class="col-md-6 mb-3">
                <label for="button_text" class="form-label fw-bold">Button Text</label>
                <input type="text" name="button_text" id="button_text" class="form-control"
                       value="{{ old('button_text', $banner->button_text ?? '') }}" placeholder="e.g., Book Now">
                @error('button_text')<small class="text-danger">{{ $message }}</small>@enderror
            </div>

            <!-- Button Link -->
            <div class="col-md-6 mb-3">
                <label for="button_link" class="form-label fw-bold">Button Link</label>
                <input type="text" name="button_link" id="button_link" class="form-control"
                       value="{{ old('button_link', $banner->button_link ?? '') }}" placeholder="https://example.com">
                @error('button_link')<small class="text-danger">{{ $message }}</small>@enderror
            </div>

            <!-- Image -->
            <div class="col-md-6 mb-3">
                <label for="image" class="form-label fw-bold">Banner Image <span class="text-danger">*</span></label>
                <input type="file" name="image" id="image" class="form-control" accept="image/*" onchange="previewImage(event)">
                <p id="imageError" class="text-danger"></p>
                @error('image')<small class="text-danger">{{ $message }}</small>@enderror

                <div class="mt-3">
                    <img id="imagePreview"
                         src="{{ isset($banner->image) ? asset('images/banners/'.$banner->image) : '' }}"
                         alt="Preview" class="img-thumbnail" style="max-width: 250px; {{ isset($banner->image) ? '' : 'display:none;' }}">
                </div>
            </div>

            <!-- Order -->
            <div class="col-md-3 mb-3">
                <label for="order" class="form-label fw-bold">Display Order</label>
                <input type="number" name="order" id="order" class="form-control"
                       value="{{ old('order', $banner->order ?? 0) }}">
                @error('order')<small class="text-danger">{{ $message }}</small>@enderror
            </div>

            <!-- Status -->
            <div class="col-md-3 mb-3">
                <label for="is_active" class="form-label fw-bold">Status</label>
                <select name="is_active" id="is_active" class="form-select">
                    <option value="1" {{ old('is_active', $banner->is_active ?? 1) == 1 ? 'selected' : '' }}>Active</option>
                    <option value="0" {{ old('is_active', $banner->is_active ?? 1) == 0 ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
        </div>

        <!-- Buttons -->
        <div class="d-flex justify-content-between mt-4">
            <a href="{{ route('admin.banners.index') }}" class="btn btn-secondary">⬅ Back</a>
            <button type="submit" class="btn btn-primary">
                {{ isset($banner) ? '💾 Update Banner' : '💾 Save Banner' }}
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
function validateBannerForm() {
    let isValid = true;
    document.querySelectorAll('.text-danger').forEach(el => el.textContent = '');

    let imageInput = document.getElementById('image');
    let title = document.getElementById('title');

    if (!title.value.trim()) {
        document.getElementById('titleError').textContent = 'Title is required';
        isValid = false;
    }

    // Validate image size only if selected
    if (imageInput.files.length > 0) {
        let file = imageInput.files[0];
        let sizeKB = file.size / 1024;
        if (sizeKB > 512) {
            document.getElementById('imageError').textContent = 'Image size must be below 512KB';
            isValid = false;
        }
    } else if (!'{{ isset($banner) }}') {
        document.getElementById('imageError').textContent = 'Please upload a banner image';
        isValid = false;
    }

    return isValid;
}

function previewImage(event) {
    const reader = new FileReader();
    reader.onload = function() {
        const preview = document.getElementById('imagePreview');
        preview.src = reader.result;
        preview.style.display = 'block';
    }
    reader.readAsDataURL(event.target.files[0]);
}
</script>

<style>
.is-invalid { border-color: red; background-color: #ffe6e6; }
</style>
@endpush
