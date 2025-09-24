@extends('layouts.admin')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
    <h2>Welcome to Admin Dashboard</h2>
    <p>Here you can manage doctors, specialties, services, and appointments.</p>

    <div class="row">
        <div class="col-md-3">
            <div class="card text-bg-primary mb-3">
                <div class="card-body">
                    <h5 class="card-title">Specialties</h5>
                    <p class="card-text">Manage doctor specialties.</p>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card text-bg-success mb-3">
                <div class="card-body">
                    <h5 class="card-title">Doctors</h5>
                    <p class="card-text">Manage doctor profiles.</p>
                </div>
            </div>
        </div>
    </div>
@endsection
