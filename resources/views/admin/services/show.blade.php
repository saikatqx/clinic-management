@extends('layouts.admin')

@section('title', 'View Service')
@section('page-title', 'Service Details')

@section('content')
<div class="card shadow-sm p-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">🩺 Service Details</h4>
        <a href="{{ route('admin.services.index') }}" class="btn btn-secondary">⬅ Back to List</a>
    </div>

    <div class="row">
        <!-- Service Name -->
        <div class="col-md-6 mb-3">
            <h6 class="fw-bold">Service Name:</h6>
            <p>{{ $service->name }}</p>
        </div>

        <!-- Status -->
        <div class="col-md-6 mb-3">
            <h6 class="fw-bold">Status:</h6>
            @if($service->is_active)
                <span class="badge bg-success">Active</span>
            @else
                <span class="badge bg-danger">Inactive</span>
            @endif
        </div>

        <!-- Description -->
        <div class="col-md-12 mb-4">
            <h6 class="fw-bold">Description:</h6>
            <div class="border rounded p-3" style="background-color: #f9f9f9;">
                {!! $service->description ?? '<em>No description available</em>' !!}
            </div>
        </div>

        <!-- Image -->
        <div class="col-md-12 mb-4">
            <h6 class="fw-bold">Image:</h6>
            @if($service->image)
                <img src="{{ asset('images/services/' . $service->image) }}" 
                     alt="{{ $service->name }}" 
                     class="img-thumbnail" 
                     style="max-width: 300px;">
            @else
                <p><em>No image uploaded</em></p>
            @endif
        </div>

        <!-- Created At -->
        <div class="col-md-6 mb-2">
            <h6 class="fw-bold">Created On:</h6>
            <p>{{ $service->created_at->format('d M Y, h:i A') }}</p>
        </div>

        <!-- Updated At -->
        <div class="col-md-6 mb-2">
            <h6 class="fw-bold">Last Updated:</h6>
            <p>{{ $service->updated_at->format('d M Y, h:i A') }}</p>
        </div>
    </div>
</div>
@endsection
