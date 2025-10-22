@extends('layouts.admin')

@section('content')
<section class="section">
  <div class="section-header">
    <h1>Prescription for {{ $appointment->patient_name }}</h1>
  </div>

  <div class="card card-primary">
    <div class="card-body">
      <form action="{{ route('admin.appointments.prescription.store', $appointment->id) }}" method="POST">
        @csrf

        <div class="mb-3">
          <label class="form-label">Medicine Name</label>
          <input type="text" name="medicine_name" class="form-control" required>
        </div>

        <div class="mb-3">
          <label class="form-label">Checkup Name</label>
          <input type="text" name="checkup_name" class="form-control">
        </div>

        <div class="mb-3">
          <label class="form-label">Medicine Eating Time</label>
          <input type="text" name="eating_time" class="form-control" placeholder="e.g., Morning & Night after food" required>
        </div>

        <div class="mb-3">
          <label class="form-label">Short Note</label>
          <textarea name="short_note" class="form-control" rows="3" placeholder="Add remarks or next appointment note"></textarea>
        </div>

        <div class="text-end">
          <button type="submit" class="btn btn-danger">Generate PDF Prescription</button>
        </div>
      </form>
    </div>
  </div>
</section>
@endsection
