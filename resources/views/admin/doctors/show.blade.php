@extends('layouts.admin')

@section('title', 'View Doctor')
@section('page-title', 'View Doctor')

@section('content')
<div class="card shadow-sm p-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">👨‍⚕️ Doctor Details</h4>
        <a href="{{ route('admin.doctors.index') }}" class="btn btn-secondary">⬅ Back</a>
    </div>

    <div class="row">
        <!-- Profile Image -->
        <div class="col-md-3 text-center">
            @if($doctor->profile_image)
                <img src="{{ asset('images/doctors/' . $doctor->profile_image) }}" 
                     alt="{{ $doctor->name }}" class="img-thumbnail rounded-circle mb-3" width="150">
            @else
                <img src="https://via.placeholder.com/150" 
                     alt="No Image" class="img-thumbnail rounded-circle mb-3">
            @endif
            <h5 class="fw-bold">{{ $doctor->name }}</h5>
            <span class="badge {{ $doctor->is_active ? 'bg-success' : 'bg-danger' }}">
                {{ $doctor->is_active ? 'Active' : 'Inactive' }}
            </span>
        </div>

        <!-- Doctor Info -->
        <div class="col-md-9">
            <table class="table table-bordered">
                <tbody>
                    <tr>
                        <th width="25%">Name</th>
                        <td>{{ $doctor->name }}</td>
                    </tr>
                    <tr>
                        <th>Email</th>
                        <td>{{ $doctor->email }}</td>
                    </tr>
                    <tr>
                        <th>Phone</th>
                        <td>{{ $doctor->phone ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Specialty</th>
                        <td>{{ $doctor->specialty->name ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Qualification</th>
                        <td>{{ $doctor->qualification ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Bio</th>
                        <td>{{ $doctor->bio ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Created At</th>
                        <td>{{ $doctor->created_at->format('d M Y') }}</td>
                    </tr>
                    <tr>
                        <th>Updated At</th>
                        <td>{{ $doctor->updated_at->format('d M Y') }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
