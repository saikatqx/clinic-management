@extends('frontend.layout')
@section('title', 'Appointment Status')

@section('content')
<style>
  /* Hero */
  .status-hero {
    background: linear-gradient(135deg, #c50000, #e63946);
    color: #fff;
    padding: 60px 0 40px;
    text-align: center;
  }
  .status-hero h1 { font-weight: 700; }
  .status-hero p { opacity: .95; }

  /* Card */
  .status-wrapper { background:#f7f9fc; }
  .status-card {
    background:#fff; border-radius:16px;
    box-shadow: 0 10px 25px rgba(0,0,0,.08);
    padding: 28px; margin-top: -40px;
  }

  /* Search */
  .floating-input { position:relative; }
  .floating-input input {
    height:56px; padding: 26px 16px 8px;
    border-radius:12px;
  }
  .floating-input label {
    position:absolute; left:14px; top:16px; color:#6c757d;
    transition: all .15s ease;
    pointer-events:none; background:#fff; padding:0 6px;
  }
  .floating-input input:focus + label,
  .floating-input input:not(:placeholder-shown) + label {
    top:-9px; font-size:12px; color:#c50000;
  }

  /* Stepper */
  .stepper { display:flex; gap:24px; justify-content:space-between; margin-top:22px; }
  .step {
    flex:1; text-align:center;
  }
  .step .dot {
    width:44px; height:44px; border-radius:50%;
    display:inline-flex; align-items:center; justify-content:center;
    background:#e9ecef; color:#6c757d; font-weight:700;
    transition:.2s ease;
  }
  .step.active .dot { background:#c50000; color:#fff; box-shadow:0 8px 20px rgba(197,0,0,.35); }
  .step.done .dot   { background:#28a745; color:#fff; }
  .step label { display:block; margin-top:8px; font-weight:600; }
  .step small { display:block; color:#6c757d; margin-top:2px; }

  .connector {
    position:relative; top:22px; flex:0 0 24px; height:4px; background:#e9ecef; border-radius:4px;
  }
  .connector.fill-red { background:#c50000; }
  .connector.fill-green { background:#28a745; }

  /* Details */
  .kv { display:flex; gap:10px; margin-bottom:8px; }
  .kv .k { min-width:160px; color:#6c757d; }
  .badge-soft {
    background:rgba(197,0,0,.08); color:#c50000; border:1px solid rgba(197,0,0,.25);
  }
</style>

<section class="status-hero">
  <div class="container">
    <h1 class="mb-2">Appointment Status</h1>
    <p class="mb-0">Track your appointment from request to prescription — in one place.</p>
  </div>
</section>

<section class="status-wrapper py-5">
  <div class="container" style="max-width:860px;">
    <div class="status-card">
      {{-- Search --}}
      <h4 class="mb-3">Find your appointment</h4>

      @if(session('error'))
        <div class="alert alert-danger mb-3">{{ session('error') }}</div>
      @endif

      <form action="{{ route('appointments.status.check') }}" method="POST" class="row g-3">
        @csrf
        <div class="col-md-9">
          <div class="floating-input">
            <input type="text" name="appointment_no" class="form-control" placeholder=" " required>
            <label>Enter your Appointment Number</label>
          </div>
        </div>
        <div class="col-md-3 d-grid">
          <button type="submit" class="btn btn-danger btn-lg">Search</button>
        </div>
      </form>

      {{-- Result --}}
      @if(!empty($appointment))
        @php
          // derive step index & nice dates
          $stepIndex = 1; // 1=requested, 2=confirmed, 3=prescription generated
          if ($appointment->status === 'Confirmed') { $stepIndex = 2; }
          if ($appointment->status === 'Cancelled') { $stepIndex = 2; } // stops at 2 but cancelled
          if (!empty($appointment->prescription_generated_at)) { $stepIndex = 3; }

          $createdAt      = optional($appointment->created_at)->format('d M Y, h:i A');
          $confirmedAt    = $appointment->confirmed_at ? \Illuminate\Support\Carbon::parse($appointment->confirmed_at)->format('d M Y, h:i A') : null;
          $prescGenAt     = optional($appointment->prescription_generated_at)->format('d M Y, h:i A');
          $apptDatePretty = \Illuminate\Support\Carbon::parse($appointment->appointment_date)->format('d M Y, h:i A');
        @endphp

        <hr class="my-4">

        {{-- Stepper --}}
        <div class="stepper">
          <div class="step {{ $stepIndex >= 1 ? 'active done' : '' }}">
            <span class="dot">1</span>
            <label>Requested</label>
            <small>{{ $createdAt ?? '-' }}</small>
          </div>

          <div class="connector {{ $stepIndex >= 2 ? ($appointment->status === 'Cancelled' ? '' : 'fill-green') : 'fill-red' }}"></div>

          <div class="step {{ $stepIndex >= 2 ? ($appointment->status === 'Cancelled' ? 'active' : 'active done') : '' }}">
            <span class="dot">{{ $appointment->status === 'Cancelled' ? '✕' : '2' }}</span>
            <label>{{ $appointment->status === 'Cancelled' ? 'Cancelled' : 'Confirmed' }}</label>
            <small>{{ $confirmedAt ?? '-' }}</small>
          </div>

          <div class="connector {{ $stepIndex >= 3 ? 'fill-green' : '' }}"></div>

          <div class="step {{ $stepIndex >= 3 ? 'active done' : '' }}">
            <span class="dot">3</span>
            <label>Prescription</label>
            <small>{{ $prescGenAt ?? '-' }}</small>
          </div>
        </div>

        {{-- Details --}}
        <div class="row g-3 mt-4">
          <div class="col-md-6">
            <div class="kv"><div class="k">Appointment No</div><div class="v">#{{ $appointment->id }}</div></div>
            <div class="kv"><div class="k">Patient Name</div><div class="v">{{ $appointment->patient_name }}</div></div>
            <div class="kv"><div class="k">Patient Email</div><div class="v">{{ $appointment->patient_email }}</div></div>
            <div class="kv"><div class="k">Patient Phone</div><div class="v">{{ $appointment->patient_phone }}</div></div>
          </div>
          <div class="col-md-6">
            <div class="kv"><div class="k">Doctor</div><div class="v">{{ $appointment->doctor->name ?? 'N/A' }}</div></div>
            <div class="kv"><div class="k">Appointment Time</div><div class="v">{{ $apptDatePretty ?? '-' }}</div></div>
            <div class="kv">
              <div class="k">Current Status</div>
              <div class="v">
                @php
                  $badgeClass = 'badge-soft';
                  if ($appointment->status === 'Confirmed')  $badgeClass = 'bg-success';
                  if ($appointment->status === 'Cancelled')  $badgeClass = 'bg-danger';
                @endphp
                <span class="badge {{ $badgeClass }}">{{ $appointment->status }}</span>
              </div>
            </div>
            @if($appointment->status === 'Cancelled')
              <div class="kv"><div class="k">Reason</div><div class="v">{{ $appointment->cancel_reason ?? '-' }}</div></div>
            @endif
          </div>
        </div>

        {{-- Prescription Action --}}
        <div class="d-flex gap-2 mt-4">
          @if(!empty($appointment->prescription_file))
            <a href="{{ route('appointments.prescription.download', $appointment->id) }}"
               class="btn btn-outline-secondary">
              <i class="fa-solid fa-file-arrow-down me-1"></i> Download Prescription
            </a>
          @else
            <button class="btn btn-outline-secondary" disabled>
              <i class="fa-solid fa-file-lines me-1"></i> Prescription not generated yet
            </button>
          @endif
        </div>

      @endif
    </div>
  </div>
</section>
@endsection
