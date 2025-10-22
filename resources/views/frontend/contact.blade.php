@extends('frontend.layout')
@section('title', 'Contact Us')

@section('content')
<style>
  /* ===== Hero ===== */
  .contact-hero{
    position:relative; overflow:hidden; padding:86px 0; color:#fff;
    background: radial-gradient(1200px 500px at 20% -10%, #ff6b6b55, transparent),
                radial-gradient(1000px 400px at 120% 10%, #c1121f66, transparent),
                linear-gradient(135deg,#c1121f,#e5383b);
  }
  .contact-hero .shine{
    position:absolute; inset:-40%; background:
      radial-gradient(closest-side,#ffffff1a,#ffffff00 70%),
      radial-gradient(closest-side,#ffffff1a,#ffffff00 70%);
    transform:rotate(8deg);
  }
  .contact-hero h1{ font-weight:800; letter-spacing:.3px }
  .contact-hero p{ opacity:.95 }

  /* ===== Cards ===== */
  .glass{
    backdrop-filter:saturate(160%) blur(8px);
    background:rgba(255,255,255,.82);
    border:1px solid #ffffffcc; border-radius:16px;
    box-shadow:0 8px 30px rgba(16,24,40,.08);
  }
  .card-edge{ border-radius:16px }
  .icon-pill{
    display:inline-flex; align-items:center; justify-content:center;
    width:44px; height:44px; border-radius:12px; color:#c1121f;
    background:#fff5f5; box-shadow:inset 0 0 0 1px #ffd5d5;
  }
  .info-line{ display:flex; gap:12px; align-items:flex-start }
  .info-line + .info-line{ margin-top:14px }

  /* ===== Socials ===== */
  .socials a{
    --bg:#f8f9fa; --fg:#0f172a;
    display:inline-flex; align-items:center; gap:8px;
    padding:10px 14px; border-radius:999px; background:var(--bg); color:var(--fg);
    font-weight:600; font-size:.9rem; text-decoration:none; transition:.18s ease;
    border:1px solid #e9ecef;
  }
  .socials a i{ font-size:1.05rem }
  .socials a:hover{ transform: translateY(-2px); box-shadow:0 6px 16px rgba(16,24,40,.08) }
  .s-fb{ --bg:#eef2ff; --fg:#1d4ed8 } .s-ig{ --bg:#fff1f2; --fg:#db2777 }
  .s-yt{ --bg:#fef2f2; --fg:#ef4444 } .s-li{ --bg:#eff6ff; --fg:#2563eb }

  /* ===== Map / CTA ===== */
  .ratio iframe{ border-radius:12px }
  .cta{
    border-radius:16px; background:linear-gradient(135deg,#0ea5e9,#2563eb);
    color:#fff; padding:28px; display:flex; align-items:center; justify-content:space-between; gap:18px
  }
  .cta h5{ margin:0; font-weight:800 }
  .cta .btn-white{
    background:#fff; color:#0f172a; border:none; font-weight:700;
    padding:10px 16px; border-radius:10px;
  }

  /* Minor utils */
  .muted{ color:#6b7280 } .fw-700{ font-weight:700 }
</style>

{{-- ===== Hero ===== --}}
<section class="contact-hero">
  <span class="shine"></span>
  <div class="container position-relative">
    <div class="row align-items-center">
      <div class="col-lg-8">
        <h1 class="display-5 mb-2">Let’s get you the care you need</h1>
        <p class="lead mb-0">We’re here for appointments, questions, and quick directions.</p>
      </div>
    </div>
  </div>
</section>

{{-- ===== Body ===== --}}
<section class="py-5">
  <div class="container">
    <div class="row g-4">

      {{-- Contact details --}}
      <div class="col-lg-7">
        <div class="glass p-4 p-md-5 card-edge">
          <div class="d-flex align-items-center gap-2 mb-3">
            <span class="icon-pill"><i class="fas fa-hospital"></i></span>
            <div>
              <h4 class="mb-0">{{ $setting->clinic_name ?? 'Clinic Name' }}</h4>
              <small class="muted">Care • Compassion • Convenience</small>
            </div>
          </div>

          <div class="info-line">
            <span class="icon-pill"><i class="fas fa-map-marker-alt"></i></span>
            <div>
              <div class="fw-700">Address</div>
              <div class="muted">{{ $setting->address ?? 'Clinic Address' }}</div>
            </div>
          </div>

          <div class="info-line">
            <span class="icon-pill"><i class="fas fa-phone"></i></span>
            <div>
              <div class="fw-700">Phone</div>
              <a href="tel:{{ $setting->mobile }}" class="text-decoration-none">{{ $setting->mobile ?? '—' }}</a>
            </div>
          </div>

          <div class="info-line">
            <span class="icon-pill"><i class="fas fa-envelope"></i></span>
            <div>
              <div class="fw-700">Email</div>
              <a href="mailto:{{ $setting->email }}" class="text-decoration-none">{{ $setting->email ?? '—' }}</a>
            </div>
          </div>

          {{-- Hours (optional — tweak as needed) --}}
          <div class="info-line">
            <span class="icon-pill"><i class="fas fa-clock"></i></span>
            <div>
              <div class="fw-700">Hours</div>
              <div class="muted">Mon–Sat: 9:00 AM – 7:00 PM &nbsp;|&nbsp; Sun: Emergency Only</div>
            </div>
          </div>

          {{-- Socials --}}
          <hr class="my-4">
          <div class="fw-700 mb-2">Follow us</div>
          <div class="socials d-flex flex-wrap gap-2">
            @if(!empty($setting->facebook))  <a class="s-fb" href="{{ $setting->facebook }}" target="_blank"><i class="fab fa-facebook"></i>Facebook</a>@endif
            @if(!empty($setting->instagram)) <a class="s-ig" href="{{ $setting->instagram }}" target="_blank"><i class="fab fa-instagram"></i>Instagram</a>@endif
            @if(!empty($setting->youtube))   <a class="s-yt" href="{{ $setting->youtube }}" target="_blank"><i class="fab fa-youtube"></i>YouTube</a>@endif
            @if(!empty($setting->linkedin))  <a class="s-li" href="{{ $setting->linkedin }}" target="_blank"><i class="fab fa-linkedin"></i>LinkedIn</a>@endif
          </div>
        </div>
      </div>

      {{-- Map + CTA --}}
      <div class="col-lg-5">
        <div class="d-flex flex-column gap-4">

          <div class="glass p-3 card-edge">
            <div class="ratio ratio-4x3">
              @if(!empty($setting->location_link))
                <iframe src="{{ $setting->location_link }}" allowfullscreen loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
              @else
                <div class="d-flex align-items-center justify-content-center text-center p-4">
                  <div>
                    <div class="mb-2"><i class="fas fa-map-marked-alt fa-2x text-secondary"></i></div>
                    <div class="muted">Map is not configured yet.</div>
                  </div>
                </div>
              @endif
            </div>
          </div>

          <div class="cta">
            <div>
              <h5>Need an appointment?</h5>
              <div class="opacity-75">Pick a doctor and time that works for you in seconds.</div>
            </div>
            <a href="{{ route('doctors.index.public') }}" class="btn btn-white">Book Now</a>
          </div>

        </div>
      </div>

    </div>
  </div>
</section>
@endsection
