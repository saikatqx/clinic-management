@extends('layouts.admin')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col-md-12">
                <h2>Welcome to Admin Dashboard</h2>
                <p class="text-muted">Real-time clinic management statistics</p>
            </div>
        </div>

        <!-- Key Statistics Cards -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card text-bg-primary mb-3">
                    <div class="card-body">
                        <h5 class="card-title">Total Appointments</h5>
                        <h3 class="card-text">{{ $totalAppointments }}</h3>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card text-bg-success mb-3">
                    <div class="card-body">
                        <h5 class="card-title">Confirmed</h5>
                        <h3 class="card-text">{{ $confirmed }}</h3>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card text-bg-warning mb-3">
                    <div class="card-body">
                        <h5 class="card-title">Pending</h5>
                        <h3 class="card-text">{{ $pending }}</h3>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card text-bg-danger mb-3">
                    <div class="card-body">
                        <h5 class="card-title">Cancelled</h5>
                        <h3 class="card-text">{{ $cancelled }}</h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- Resources Overview -->
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card text-bg-info mb-3">
                    <div class="card-body">
                        <h5 class="card-title">👨‍⚕️ Active Doctors</h5>
                        <h3 class="card-text">{{ $doctors }}</h3>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card text-bg-secondary mb-3">
                    <div class="card-body">
                        <h5 class="card-title">📋 Services Offered</h5>
                        <h3 class="card-text">{{ $services }}</h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- Upcoming Appointments -->
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5>📅 Next 10 Appointments</h5>
                    </div>
                    <div class="card-body">
                        @if($upcoming->count())
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Patient</th>
                                            <th>Doctor</th>
                                            <th>Date & Time</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($upcoming as $appt)
                                            <tr>
                                                <td>{{ $appt->id }}</td>
                                                <td>{{ $appt->patient_name }}</td>
                                                <td>{{ $appt->doctor->name ?? '-' }}</td>
                                                <td>{{ $appt->appointment_date ? date('d M Y h:i A', strtotime($appt->appointment_date)) : '-' }}</td>
                                                <td>
                                                    @if($appt->status === 'Confirmed')
                                                        <span class="badge bg-success">✅ Confirmed</span>
                                                    @elseif($appt->status === 'Cancelled')
                                                        <span class="badge bg-danger">❌ Cancelled</span>
                                                    @else
                                                        <span class="badge bg-warning text-dark">⏳ Pending</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-muted">No upcoming appointments.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .card-body h3 {
            margin: 0;
            font-size: 2.5rem;
            font-weight: bold;
        }
    </style>
@endsection
