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

        <!-- Lab & Packages Statistics -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card text-bg-info mb-3">
                    <div class="card-body">
                        <h5 class="card-title">🩺 Diagnostic Bookings</h5>
                        <h3 class="card-text">{{ $totalDiagBookings }}</h3>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card text-bg-dark mb-3">
                    <div class="card-body">
                        <h5 class="card-title">🧪 Pathology Bookings</h5>
                        <h3 class="card-text">{{ $totalPathBookings }}</h3>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card text-bg-warning text-dark mb-3">
                    <div class="card-body">
                        <h5 class="card-title">📦 Package Bookings</h5>
                        <h3 class="card-text">{{ $totalPkgBookings }}</h3>
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

        <!-- Recent Lab and Package Bookings -->
        <div class="row mt-4">
            <div class="col-md-6 mb-4">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0 text-primary fw-bold"><i class="fa fa-microscope me-2"></i> Recent Lab Bookings</h5>
                        <a href="{{ route('admin.diagnostic-bookings.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
                    </div>
                    <div class="card-body">
                        @if($recentLabBookings->count())
                            <div class="table-responsive">
                                <table class="table table-striped table-hover align-middle small">
                                    <thead>
                                        <tr>
                                            <th>Booking No</th>
                                            <th>Patient</th>
                                            <th>Type</th>
                                            <th>Amount</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($recentLabBookings as $booking)
                                            <tr>
                                                <td>
                                                    <a href="{{ $booking->type === 'path' ? route('admin.pathology-bookings.show', $booking->id) : route('admin.diagnostic-bookings.show', $booking->id) }}" class="fw-semibold">
                                                        {{ $booking->booking_no }}
                                                    </a>
                                                </td>
                                                <td>
                                                    <div class="fw-semibold">{{ $booking->patient_name }}</div>
                                                    <div class="text-muted" style="font-size: 11px;">{{ $booking->mobile }}</div>
                                                </td>
                                                <td>
                                                    <span class="badge bg-secondary">
                                                        {{ $booking->type === 'path' ? '🧪 Pathology' : '🩺 Diagnostic' }}
                                                    </span>
                                                </td>
                                                <td class="fw-semibold">₹{{ number_format($booking->total_amount, 2) }}</td>
                                                <td>
                                                    <span class="badge bg-{{ $booking->booking_status_badge }}">
                                                        {{ $booking->booking_status_label }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-muted my-3 text-center">No recent lab bookings.</p>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-md-6 mb-4">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0 text-primary fw-bold"><i class="fa fa-box me-2"></i> Recent Package Bookings</h5>
                        <a href="{{ route('admin.health-package-bookings.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
                    </div>
                    <div class="card-body">
                        @if($recentPkgBookings->count())
                            <div class="table-responsive">
                                <table class="table table-striped table-hover align-middle small">
                                    <thead>
                                        <tr>
                                            <th>Booking No</th>
                                            <th>Patient</th>
                                            <th>Package</th>
                                            <th>Amount</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($recentPkgBookings as $booking)
                                            <tr>
                                                <td>
                                                    <a href="{{ route('admin.health-package-bookings.show', $booking->id) }}" class="fw-semibold">
                                                        {{ $booking->booking_no }}
                                                    </a>
                                                </td>
                                                <td>
                                                    <div class="fw-semibold">{{ $booking->patient_name }}</div>
                                                    <div class="text-muted" style="font-size: 11px;">{{ $booking->mobile }}</div>
                                                </td>
                                                <td class="text-truncate" style="max-width: 150px;">
                                                    {{ $booking->items->first()->package_name ?? 'N/A' }}
                                                </td>
                                                <td class="fw-semibold">₹{{ number_format($booking->total_amount, 2) }}</td>
                                                <td>
                                                    <span class="badge bg-{{ $booking->booking_status_badge }}">
                                                        {{ $booking->booking_status_label }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-muted my-3 text-center">No recent package bookings.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <!-- Analytics & Reports Section -->
        <div class="row mt-4 mb-4">
            <div class="col-lg-7 mb-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="card-title mb-0">📈 Appointment Booking Trends (Last 15 Days)</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="trendChart" height="250"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-lg-5 mb-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="card-title mb-0">📊 Doctor Workloads & Specialties</h5>
                    </div>
                    <div class="card-body">
                        <ul class="nav nav-tabs mb-3" id="chartTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="doctors-tab" data-bs-toggle="tab" data-bs-target="#doctorsChartPane" type="button" role="tab">Doctors</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="specialties-tab" data-bs-toggle="tab" data-bs-target="#specialtiesChartPane" type="button" role="tab">Specialties</button>
                            </li>
                        </ul>
                        <div class="tab-content" id="chartTabsContent">
                            <div class="tab-pane fade show active" id="doctorsChartPane" role="tabpanel">
                                <canvas id="doctorsChart" height="200"></canvas>
                            </div>
                            <div class="tab-pane fade" id="specialtiesChartPane" role="tabpanel">
                                <div style="max-height: 200px; display: flex; justify-content: center;">
                                    <canvas id="specialtiesChart" height="200"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Interactive Scheduler Calendar -->
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="card-title mb-0">📅 Interactive Appointment Scheduler & Rescheduler</h5>
                    </div>
                    <div class="card-body">
                        <p class="text-muted small mb-3">💡 Tip: You can drag and drop appointments to reschedule them. Click on an appointment to view details.</p>
                        <div id="calendar" style="min-height: 500px;"></div>
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
        .fc .fc-button-primary {
            background-color: #007bff;
            border-color: #007bff;
        }
        .fc .fc-button-primary:hover {
            background-color: #0056b3;
            border-color: #0056b3;
        }
        .fc .fc-button-primary:disabled {
            background-color: #007bff;
            border-color: #007bff;
        }
        .fc-event {
            cursor: pointer;
        }
    </style>
@endsection

@push('scripts')
    <!-- CDNs for Chart.js and FullCalendar -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js"></script>

    <script>
        $(function () {
            // --- 1. CHARTS ---
            // Trend Chart
            const ctxTrend = document.getElementById('trendChart').getContext('2d');
            new Chart(ctxTrend, {
                type: 'line',
                data: {
                    labels: @json($daysRange),
                    datasets: [{
                        label: 'Appointments',
                        data: @json($appointmentTrends),
                        borderColor: '#007bff',
                        backgroundColor: 'rgba(0, 123, 255, 0.1)',
                        fill: true,
                        tension: 0.3,
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: { stepSize: 1 }
                        }
                    }
                }
            });

            // Doctor Chart
            const ctxDoc = document.getElementById('doctorsChart').getContext('2d');
            new Chart(ctxDoc, {
                type: 'bar',
                data: {
                    labels: @json(array_keys($doctorStats)),
                    datasets: [{
                        label: 'Bookings',
                        data: @json(array_values($doctorStats)),
                        backgroundColor: '#28a745',
                        borderRadius: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: { stepSize: 1 }
                        }
                    }
                }
            });

            // Specialty Chart
            const ctxSpec = document.getElementById('specialtiesChart').getContext('2d');
            new Chart(ctxSpec, {
                type: 'doughnut',
                data: {
                    labels: @json(array_keys($specialtyStats)),
                    datasets: [{
                        data: @json(array_values($specialtyStats)),
                        backgroundColor: [
                            '#fd7e14', '#6f42c1', '#17a2b8', '#e83e8c', '#20c997', '#ffc107'
                        ]
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false
                }
            });

            // --- 2. INTERACTIVE CALENDAR ---
            const calendarEl = document.getElementById('calendar');
            const calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay'
                },
                editable: true,
                events: '{{ route("admin.appointments.calendarEvents") }}',
                eventDrop: function(info) {
                    if (!confirm(`Are you sure you want to reschedule "${info.event.title}"?`)) {
                        info.revert();
                        return;
                    }
                    
                    // Format ISO date string into SQL friendly format (Y-m-d H:i:s)
                    const dateObj = new Date(info.event.startStr);
                    const formattedDate = dateObj.getFullYear() + '-' +
                        String(dateObj.getMonth() + 1).padStart(2, '0') + '-' +
                        String(dateObj.getDate()).padStart(2, '0') + ' ' +
                        String(dateObj.getHours()).padStart(2, '0') + ':' +
                        String(dateObj.getMinutes()).padStart(2, '0') + ':' +
                        String(dateObj.getSeconds()).padStart(2, '0');
                    
                    $.ajax({
                        url: '{{ route("admin.appointments.reschedule") }}',
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            id: info.event.id,
                            appointment_date: formattedDate
                        },
                        success: function(response) {
                            toastr.success(response.message || 'Rescheduled successfully!');
                        },
                        error: function(xhr) {
                            toastr.error(xhr.responseJSON?.message || 'Failed to reschedule.');
                            info.revert();
                        }
                    });
                },
                eventClick: function(info) {
                    const props = info.event.extendedProps;
                    toastr.info(
                        `<strong>Patient:</strong> ${props.patient_name}<br>` +
                        `<strong>Doctor:</strong> ${props.doctor_name}<br>` +
                        `<strong>Phone:</strong> ${props.phone}<br>` +
                        `<strong>Status:</strong> ${props.status}`,
                        'Appointment Details',
                        { timeOut: 10000, closeButton: true }
                    );
                }
            });
            calendar.render();
        });
    </script>
@endpush
