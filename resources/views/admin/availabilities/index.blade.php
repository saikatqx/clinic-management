@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1>Doctor Availabilities</h1>
        </div>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <!-- Add New Availability Form -->
    <div class="card mb-4">
        <div class="card-header">
            <h5>Add Availability Slot</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.availabilities.store') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-3">
                        <label for="doctor_id" class="form-label">Doctor</label>
                        <select id="doctor_id" name="doctor_id" class="form-control select2" required>
                            <option value="">-- Select Doctor --</option>
                            @foreach ($doctors as $doctor)
                                <option value="{{ $doctor->id }}">{{ $doctor->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label for="day_of_week" class="form-label">Day</label>
                        <select id="day_of_week" name="day_of_week" class="form-control select2" required>
                            <option value="">-- Select Day --</option>
                            <option value="0">Sunday</option>
                            <option value="1">Monday</option>
                            <option value="2">Tuesday</option>
                            <option value="3">Wednesday</option>
                            <option value="4">Thursday</option>
                            <option value="5">Friday</option>
                            <option value="6">Saturday</option>
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label for="start_time" class="form-label">Start Time</label>
                        <input type="time" id="start_time" name="start_time" class="form-control" required>
                    </div>

                    <div class="col-md-2">
                        <label for="end_time" class="form-label">End Time</label>
                        <input type="time" id="end_time" name="end_time" class="form-control" required>
                    </div>

                    <div class="col-md-2">
                        <label for="slot_minutes" class="form-label">Slot (min)</label>
                        <select id="slot_minutes" name="slot_minutes" class="form-control select2">
                            <option value="15">15 min</option>
                            <option value="30" selected>30 min</option>
                            <option value="45">45 min</option>
                            <option value="60">60 min</option>
                        </select>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary mt-3">Add Slot</button>
            </form>
        </div>
    </div>

    <!-- Availabilities Table -->
    <div class="card">
        <div class="card-header">
            <h5>Availability Schedule</h5>
        </div>
        <div class="card-body">
            <table id="availabilitiesTable" class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Doctor</th>
                        <th>Day</th>
                        <th>Start Time</th>
                        <th>End Time</th>
                        <th>Slot Duration</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var table = $('#availabilitiesTable').DataTable({
        processing: true,
        serverSide: false,
        ajax: {
            url: "{{ route('admin.availabilities.data') }}",
            type: 'GET'
        },
        columns: [
            { data: 0 },
            { data: 1 },
            { data: 2 },
            { data: 3 },
            { data: 4 },
            { data: 5 },
            { data: 6, orderable: false, searchable: false }
        ]
    });

    $(document).on('click', '.delete-availability', function() {
        if (confirm('Are you sure?')) {
            var id = $(this).data('id');
            $.ajax({
                url: '/admin/availabilities/' + id,
                type: 'DELETE',
                data: { _token: '{{ csrf_token() }}' },
                success: function() {
                    table.ajax.reload();
                    alert('Deleted successfully');
                }
            });
        }
    });
});
</script>
@endsection
