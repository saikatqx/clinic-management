@extends('layouts.admin')

@section('title', 'View Banner')
@section('page-title', 'View Banner')

@section('content')
<div class="card shadow-sm p-4">
    <h4 class="mb-3">🖼️ Banner Details</h4>

    <div class="row">
        <div class="col-md-6 mb-3">
            <label class="fw-bold">Title:</label>
            <p>{{ $banner->title ?? '-' }}</p>
        </div>

        <div class="col-md-6 mb-3">
            <label class="fw-bold">Subtitle:</label>
            <p>{{ $banner->subtitle ?? '-' }}</p>
        </div>

        <div class="col-md-6 mb-3">
            <label class="fw-bold">Button Text:</label>
            <p>{{ $banner->button_text ?? '-' }}</p>
        </div>

        <div class="col-md-6 mb-3">
            <label class="fw-bold">Button Link:</label>
            @if($banner->button_link)
                <a href="{{ $banner->button_link }}" target="_blank">{{ $banner->button_link }}</a>
            @else
                <p>-</p>
            @endif
        </div>

        <div class="col-md-6 mb-3">
            <label class="fw-bold">Status:</label>
            <span class="badge {{ $banner->is_active ? 'bg-success' : 'bg-danger' }}">
                {{ $banner->is_active ? 'Active' : 'Inactive' }}
            </span>
        </div>

        <div class="col-md-6 mb-3">
            <label class="fw-bold">Order:</label>
            <p>{{ $banner->order ?? '0' }}</p>
        </div>

        <div class="col-md-12 mb-3 text-center">
            <label class="fw-bold d-block mb-2">Banner Image:</label>
            @if($banner->image)
                <img src="{{ asset('images/banners/' . $banner->image) }}" 
                     alt="Banner Image" 
                     class="img-fluid rounded shadow-sm" 
                     style="max-height: 350px;">
            @else
                <p>No image uploaded.</p>
            @endif
        </div>
    </div>

    <div class="mt-3">
        <a href="{{ route('admin.banners.index') }}" class="btn btn-secondary">⬅ Back to List</a>
        <a href="{{ route('admin.banners.edit', $banner->id) }}" class="btn btn-warning">✏️ Edit</a>
    </div>
</div>
@endsection
