@extends('layouts.admin')

@section('title', isset($package) ? 'Edit Health Package' : 'Add Health Package')
@section('page-title', isset($package) ? 'Edit Package' : 'Add Package')

@section('content')
<div class="card shadow-sm p-4">
    <h4 class="mb-3">{{ isset($package) ? '📝 Edit Health Package' : '➕ Add New Health Package' }}</h4>

    <form action="{{ isset($package) ? route('admin.health-packages.update', $package->id) : route('admin.health-packages.store') }}"
        method="POST"
        enctype="multipart/form-data"
        onsubmit="return formValidation()">

        @csrf
        @if(isset($package))
        @method('PUT')
        @endif

        <div class="row g-3">
            <!-- Name -->
            <div class="col-md-6">
                <label for="name" class="form-label fw-bold">Package Name <span class="text-danger">*</span></label>
                <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror"
                    value="{{ isset($package) ? $package->name : old('name') }}" placeholder="e.g., Executive Health Checkup, Cardiac Health Package" required>
                <p id="nameError" class="text-danger small"></p>
                @error('name')
                <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            <!-- Gender -->
            <div class="col-md-6">
                <label for="gender" class="form-label fw-bold">Applicable Gender <span class="text-danger">*</span></label>
                <select name="gender" id="gender" class="form-select select2" required>
                    <option value="BOTH" {{ (isset($package) ? $package->gender : old('gender')) === 'BOTH' ? 'selected' : '' }}>Both (Male & Female)</option>
                    <option value="MALE" {{ (isset($package) ? $package->gender : old('gender')) === 'MALE' ? 'selected' : '' }}>Male Only</option>
                    <option value="FEMALE" {{ (isset($package) ? $package->gender : old('gender')) === 'FEMALE' ? 'selected' : '' }}>Female Only</option>
                </select>
            </div>

            <!-- Diagnostics Selection -->
            <div class="col-12">
                <label for="diagnostic_ids" class="form-label fw-bold">Select Included Tests <span class="text-danger">*</span></label>
                <select name="diagnostic_ids[]" id="diagnostic_ids" class="form-select select2" multiple required data-placeholder="Select tests to include in this package">
                    @php
                        $selectedTestIds = isset($package) ? $package->diagnostics->pluck('id')->toArray() : [];
                    @endphp
                    @foreach($diagnostics as $test)
                        <option value="{{ $test->id }}" data-price="{{ $test->price }}" {{ in_array($test->id, $selectedTestIds) ? 'selected' : '' }}>
                            {{ $test->name }} (₹{{ number_format($test->price, 2) }})
                        </option>
                    @endforeach
                </select>
                <p id="testsError" class="text-danger small"></p>
                <small class="text-muted">You can select multiple diagnostic/pathology tests. The total price will be computed automatically.</small>
            </div>

            <!-- Actual Price -->
            <div class="col-md-6">
                <label for="actual_price" class="form-label fw-bold">Actual Total Price (₹) <span class="text-danger">*</span></label>
                <input type="number" step="0.01" name="actual_price" id="actual_price" class="form-control @error('actual_price') is-invalid @enderror"
                    value="{{ isset($package) ? $package->actual_price : old('actual_price', '0.00') }}" placeholder="e.g., 1500.00" required>
                <p id="actualPriceError" class="text-danger small"></p>
                @error('actual_price')
                <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            <!-- Package Price (Discounted) -->
            <div class="col-md-6">
                <label for="package_price" class="form-label fw-bold">Offer Package Price (₹) <span class="text-danger">*</span></label>
                <input type="number" step="0.01" name="package_price" id="package_price" class="form-control @error('package_price') is-invalid @enderror"
                    value="{{ isset($package) ? $package->package_price : old('package_price', '0.00') }}" placeholder="e.g., 999.00" required>
                <p id="packagePriceError" class="text-danger small"></p>
                @error('package_price')
                <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            <!-- Description -->
            <div class="col-12">
                <label for="description" class="form-label fw-bold">Description</label>
                <textarea name="description" id="description" rows="3" class="form-control @error('description') is-invalid @enderror"
                    placeholder="Enter key details or benefits of this package">{{ isset($package) ? $package->description : old('description') }}</textarea>
                @error('description')
                <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            <!-- Image Upload -->
            <div class="col-md-6">
                <label for="image" class="form-label fw-bold">Package Image</label>
                <input type="file" name="image" id="image" class="form-control @error('image') is-invalid @enderror">
                @if(isset($package) && $package->image)
                    <div class="mt-2">
                        <img src="{{ asset('images/packages/' . $package->image) }}" class="img-thumbnail" style="max-width: 150px; max-height: 150px; object-fit: cover;">
                    </div>
                @endif
                @error('image')
                <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            <!-- Status -->
            <div class="col-md-6">
                <label class="form-label fw-bold">Status</label>
                <select name="status" class="form-select select2">
                    <option value="1" {{ (isset($package) ? $package->status : old('status', 1)) == 1 ? 'selected' : '' }}>Active</option>
                    <option value="0" {{ (isset($package) ? $package->status : old('status', 1)) == 0 ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
        </div>

        <!-- Buttons -->
        <div class="d-flex justify-content-between mt-4">
            <a href="{{ route('admin.health-packages.index') }}" class="btn btn-secondary">
                ⬅ Back
            </a>
            <button type="submit" class="btn btn-primary">
                {{ isset($package) ? '💾 Update Package' : '💾 Save Package' }}
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Auto-calculate actual price based on selected tests
        $('#diagnostic_ids').on('change', function() {
            let total = 0;
            $('#diagnostic_ids option:selected').each(function() {
                let price = parseFloat($(this).data('price'));
                if (!isNaN(price)) {
                    total += price;
                }
            });
            $('#actual_price').val(total.toFixed(2));
        });
    });

    function formValidation() {
        var name = document.getElementById("name").value.trim();
        var tests = $('#diagnostic_ids').val();
        var actualPrice = document.getElementById("actual_price").value;
        var packagePrice = document.getElementById("package_price").value;

        var isValid = true;
        document.getElementById("nameError").textContent = "";
        document.getElementById("testsError").textContent = "";
        document.getElementById("actualPriceError").textContent = "";
        document.getElementById("packagePriceError").textContent = "";

        if (name === "") {
            document.getElementById("nameError").textContent = "Package Name is required.";
            isValid = false;
        }

        if (!tests || tests.length === 0) {
            document.getElementById("testsError").textContent = "Please select at least one test to include.";
            isValid = false;
        }

        if (actualPrice === "" || parseFloat(actualPrice) <= 0) {
            document.getElementById("actualPriceError").textContent = "Actual Price is required.";
            isValid = false;
        }

        if (packagePrice === "" || parseFloat(packagePrice) <= 0) {
            document.getElementById("packagePriceError").textContent = "Package Offer Price is required.";
            isValid = false;
        }

        return isValid;
    }
</script>
@endpush
