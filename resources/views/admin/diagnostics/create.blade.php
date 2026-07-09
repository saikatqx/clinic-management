@extends('layouts.admin')

@section('title', isset($diagnostic) ? 'Edit Diagnostic Test' : 'Add Diagnostic Test')
@section('page-title', isset($diagnostic) ? 'Edit Test' : 'Add Test')

@section('content')
<div class="card shadow-sm p-4">
    <h4 class="mb-3">{{ isset($diagnostic) ? '📝 Edit Test' : '➕ Add New Diagnostic Test' }}</h4>

    <form action="{{ isset($diagnostic) ? route('admin.diagnostics.update', $diagnostic->id) : route('admin.diagnostics.store') }}"
        method="POST"
        enctype="multipart/form-data"
        onsubmit="return formValidation()">

        @csrf
        @if(isset($diagnostic))
        @method('PUT')
        @endif

        <!-- Category -->
        <div class="mb-3">
            <label for="diagnostic_category_id" class="form-label fw-bold">Category <span class="text-danger">*</span></label>
            <select name="diagnostic_category_id" id="diagnostic_category_id" class="form-select select2" required>
                <option value="">Select Category</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ (isset($diagnostic) ? $diagnostic->diagnostic_category_id : old('diagnostic_category_id')) == $category->id ? 'selected' : '' }}>
                        {{ $category->name }} ({{ ($category->type === 'diag') ? 'Diagnostic' : 'Pathology' }})
                    </option>
                @endforeach
            </select>
            <p id="categoryError" class="text-danger small"></p>
        </div>

        <!-- Name -->
        <div class="mb-3">
            <label for="name" class="form-label fw-bold">Test Name <span class="text-danger">*</span></label>
            <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror"
                value="{{ isset($diagnostic) ? $diagnostic->name : old('name') }}" placeholder="e.g., Complete Blood Count (CBC), Lipid Profile">
            <p id="nameError" class="text-danger small"></p>
            @error('name')
            <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>

        <!-- Price -->
        <div class="mb-3">
            <label for="price" class="form-label fw-bold">Price (₹) <span class="text-danger">*</span></label>
            <input type="number" step="0.01" name="price" id="price" class="form-control @error('price') is-invalid @enderror"
                value="{{ isset($diagnostic) ? $diagnostic->price : old('price') }}" placeholder="e.g., 350.00">
            <p id="priceError" class="text-danger small"></p>
            @error('price')
            <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>

        <!-- Image Upload -->
        <div class="mb-3">
            <label for="image" class="form-label fw-bold">Image</label>
            <input type="file" name="image" id="image" class="form-control @error('image') is-invalid @enderror">
            @if(isset($diagnostic) && $diagnostic->image_url)
                <div class="mt-2">
                    <img src="{{ $diagnostic->image_url }}" class="img-thumbnail" style="max-width: 150px; max-height: 150px; object-fit: cover;">
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
                <option value="1" {{ (isset($diagnostic) ? $diagnostic->status : old('status', 1)) == 1 ? 'selected' : '' }}>Active</option>
                <option value="0" {{ (isset($diagnostic) ? $diagnostic->status : old('status', 1)) == 0 ? 'selected' : '' }}>Inactive</option>
            </select>
        </div>

        <!-- Buttons -->
        <div class="d-flex justify-content-between">
            <a href="{{ $type === 'path' ? route('admin.diagnostics.indexPath') : route('admin.diagnostics.indexDiag') }}" class="btn btn-secondary">
                ⬅ Back
            </a>
            <button type="submit" class="btn btn-primary">
                {{ isset($diagnostic) ? '💾 Update Test' : '💾 Save Test' }}
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    function formValidation() {
        var category = document.getElementById("diagnostic_category_id").value;
        var name = document.getElementById("name").value.trim();
        var price = document.getElementById("price").value;

        var isValid = true;
        document.getElementById("categoryError").textContent = "";
        document.getElementById("nameError").textContent = "";
        document.getElementById("priceError").textContent = "";

        if (category === "") {
            document.getElementById("categoryError").textContent = "Please select a Category.";
            isValid = false;
        }

        if (name === "") {
            document.getElementById("nameError").textContent = "Test Name is required.";
            isValid = false;
        } else if (name.length < 3) {
            document.getElementById("nameError").textContent = "Test Name must be at least 3 characters long.";
            isValid = false;
        }

        if (price === "" || parseFloat(price) < 0) {
            document.getElementById("priceError").textContent = "Price is required and must be 0 or more.";
            isValid = false;
        }

        return isValid;
    }
</script>
@endpush
