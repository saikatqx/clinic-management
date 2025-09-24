@extends('layouts.admin')

@section('title', 'Specialty Details')
@section('page-title', 'Specialty Details')

@section('content')
<div class="card shadow-sm p-4">
    <h4 class="mb-3">📄 Specialty Details</h4>

    <div class="mb-3">
        <strong>Name:</strong>
        <p>{{ $specialty->name }}</p>
    </div>

    <div class="mb-3">
        <strong>Description:</strong>
        <p>{{ $specialty->description ?? '—' }}</p>
    </div>

    <div class="mb-3">
        <strong>Status:</strong>
        <span class="badge {{ $specialty->is_active ? 'bg-success' : 'bg-secondary' }}">
            {{ $specialty->is_active ? 'Active' : 'Inactive' }}
        </span>
    </div>

    <div class="mb-3">
        <strong>Created At:</strong>
        <p>{{ $specialty->created_at->format('d M Y, h:i A') }}</p>
    </div>

    <a href="{{ route('admin.specialties.index') }}" class="btn btn-secondary">⬅ Back to List</a>
</div>
@endsection
