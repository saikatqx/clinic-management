@extends('frontend.layout')
@section('title', $service->name)

@section('content')
<style>
  /* ===== Hero ===== */
  .svc-hero{
    position:relative; overflow:hidden; padding:64px 0 48px;
    background:
      radial-gradient(900px 420px at -10% -20%, #ffe2e2 0%, transparent 60%),
      radial-gradient(800px 380px at 110% 0%, #ffe8ea 0%, transparent 60%),
      linear-gradient(135deg,#c50000,#e63946);
    color:#fff;
  }
  .svc-hero h1{ font-weight:800; letter-spacing:.3px }
  .svc-bc a{ color:#ffdede; text-decoration:none }
  .svc-bc a:hover{ color:#fff; text-decoration:underline }

  /* ===== Content card ===== */
  .svc-card{
    border:0; border-radius:18px; overflow:hidden; background:#fff;
    box-shadow:0 10px 28px rgba(15,23,42,.12);
  }
  .svc-media{ position:relative; height:340px; background:#f7f9fc }
  .svc-media img{ width:100%; height:100%; object-fit:cover; }
  .svc-grad{ position:absolute; inset:0; background:linear-gradient(to top, rgba(0,0,0,.45), rgba(0,0,0,0)); }
  .svc-chip{
    position:absolute; left:16px; bottom:16px; color:#fff;
    background:rgba(255,255,255,.18); border:1px solid rgba(255,255,255,.35);
    padding:6px 12px; border-radius:999px; backdrop-filter: blur(6px);
    font-size:.8rem; font-weight:700; letter-spacing:.3px;
  }
  .svc-body{ padding:22px 22px 6px }
  .svc-body h2{ font-weight:800; font-size:1.4rem; }
  .svc-desc{ font-size:1.02rem; line-height:1.75; color:#485169 }
  .svc-desc img{ max-width:100%; border-radius:10px; margin:12px 0 }

  /* ===== Right column ===== */
  .info-card{
    border:1px solid #eef0f3; border-radius:16px; padding:18px; background:#fff;
    box-shadow:0 6px 18px rgba(15,23,42,.06);
  }
  .info-card h6{ font-weight:800; letter-spacing:.3px }
  .check{ color:#16a34a }
  .bullet-min{ margin:0; padding-left:1.1rem }
  .bullet-min li{ margin:.35rem 0 }

  /* Sticky CTA */
  .sticky-cta{
    position:sticky; top:90px; z-index:2; border-radius:16px; padding:16px;
    background:linear-gradient(180deg,#fff, #fff7f8); border:1px solid #ffe1e6;
    box-shadow:0 8px 20px rgba(197,0,0,.08);
  }

  /* Share pills */
  .share a{ display:inline-flex; align-items:center; gap:8px; padding:8px 12px;
    border-radius:999px; border:1px solid #e7e9ef; color:#475569; text-decoration:none; font-size:.9rem }
  .share a:hover{ background:#f8fafc }

  /* Back link */
  .back-link{ color:#64748b; text-decoration:none }
  .back-link:hover{ color:#c50000; text-decoration:underline }
</style>

{{-- ===== Hero Section ===== --}}
<section class="svc-hero">
  <div class="container">
    <div class="svc-bc mb-2 small">
      <a href="{{ route('home') }}">Home</a> ·
      <a href="{{ route('services.index.public') }}">Services</a> ·
      <span class="opacity-75">{{ $service->name }}</span>
    </div>
    <h1 class="mb-1">{{ $service->name }}</h1>
    <p class="mb-0 opacity-90">Learn more about our advanced medical care and facilities.</p>
  </div>
</section>

{{-- ===== Content ===== --}}
<section class="py-4 py-md-5">
  <div class="container">
    <div class="row g-4 g-lg-5">
      {{-- Left: article --}}
      <div class="col-lg-8">
        <article class="svc-card">
          {{-- Image --}}
          @if($service->image)
            <div class="svc-media">
              <img src="{{ asset('images/services/' . $service->image) }}" alt="{{ $service->name }}">
              <span class="svc-grad"></span>
              <span class="svc-chip">Updated {{ $service->updated_at?->format('d M Y') }}</span>
            </div>
          @endif

          <div class="svc-body">
            {{-- Optional subtitle / keyline --}}
            <div class="d-flex align-items-center gap-2 text-secondary small mb-2">
              <i class="fas fa-heartbeat text-danger"></i>
              <span>Comprehensive, patient-first care</span>
            </div>

            <h2 class="mb-3">Overview</h2>
            <div class="svc-desc">
              {!! $service->description !!}
            </div>

            {{-- Share --}}
            <div class="d-flex flex-wrap align-items-center gap-2 gap-md-3 mt-4 share">
              <span class="text-secondary small">Share:</span>
              <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(request()->fullUrl()) }}" target="_blank">
                <i class="fab fa-facebook text-primary"></i> Facebook
              </a>
              <a href="https://twitter.com/intent/tweet?url={{ urlencode(request()->fullUrl()) }}&text={{ urlencode($service->name) }}" target="_blank">
                <i class="fab fa-x-twitter"></i> Post
              </a>
              <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ urlencode(request()->fullUrl()) }}" target="_blank">
                <i class="fab fa-linkedin text-primary"></i> LinkedIn
              </a>
              <a href="mailto:?subject={{ rawurlencode($service->name) }}&body={{ rawurlencode(request()->fullUrl()) }}">
                <i class="fas fa-envelope text-danger"></i> Email
              </a>
            </div>

            <div class="d-flex flex-wrap gap-2 mt-4 mb-3">
              <a href="{{ route('appointments.create.public', ['service' => $service->id]) }}" class="btn btn-danger">
                <i class="fas fa-calendar-check me-1"></i> Book Appointment
              </a>
              <a href="{{ route('services.index.public') }}" class="btn btn-outline-secondary back-link">
                ← Back to Services
              </a>
            </div>
          </div>
        </article>
      </div>

      {{-- Right: sticky CTA + highlights --}}
      <div class="col-lg-4">
        <div class="sticky-cta">
          <div class="d-flex align-items-center gap-3">
            <div class="rounded-circle bg-danger text-white d-flex align-items-center justify-content-center" style="width:44px;height:44px">
              <i class="fas fa-user-md"></i>
            </div>
            <div>
              <strong class="d-block">Need this service?</strong>
              <span class="text-secondary small">Check available doctors & time slots.</span>
            </div>
          </div>
          <a href="{{ route('appointments.create.public', ['service' => $service->id]) }}" class="btn btn-danger w-100 mt-3">
            Book Appointment
          </a>
        </div>

        <div class="info-card mt-3">
          <h6 class="mb-2"><i class="fas fa-star text-warning me-2"></i>Why choose us</h6>
          <ul class="bullet-min">
            <li><i class="fas fa-check check me-1"></i> Experienced specialists</li>
            <li><i class="fas fa-check check me-1"></i> Modern, calibrated equipment</li>
            <li><i class="fas fa-check check me-1"></i> Evidence-based protocols</li>
            <li><i class="fas fa-check check me-1"></i> Patient-centric care & safety</li>
          </ul>
        </div>

        <div class="info-card mt-3">
          <h6 class="mb-2"><i class="fas fa-info-circle text-primary me-2"></i>Need help?</h6>
          <div class="small text-secondary">
            Call us at
            @php $phone = optional(\App\Models\Setting::first())->mobile; @endphp
            <a href="tel:{{ $phone }}" class="text-decoration-none">{{ $phone ?? '+91 00000 00000' }}</a>
            or write to
            @php $mail = optional(\App\Models\Setting::first())->email; @endphp
            <a href="mailto:{{ $mail }}" class="text-decoration-none">{{ $mail ?? 'clinic@example.com' }}</a>.
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
@endsection
