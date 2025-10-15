@extends('frontend.layout')
@section('title', 'Services')

@section('content')
<style>
  /* ===== Hero ===== */
  .srv-hero{
    position:relative; overflow:hidden; padding:64px 0 36px;
    background:
      radial-gradient(900px 380px at -10% -10%, #ffe2e2 0%, transparent 60%),
      radial-gradient(800px 340px at 110% 0%, #ffe8ea 0%, transparent 60%),
      linear-gradient(135deg,#fff,#f7f9fc 40%, #f6f7ff 100%);
    border-bottom:1px solid #eef0f3;
  }
  .srv-hero h1{ font-weight:800; letter-spacing:.3px }
  .srv-hero .lead{ color:#6b7280 }

  .pill-search{
    display:flex; gap:10px; background:#fff; border:1px solid #e6e8ee;
    border-radius:999px; padding:6px; box-shadow:0 4px 16px rgba(15,23,42,.05)
  }
  .pill-search input{ border:none; padding:10px 14px; width:100%; outline:none; border-radius:999px }
  .pill-search .btn{ border-radius:999px; padding:.65rem 1rem }

  /* ===== Cards ===== */
  .srv-card{
    border:0; border-radius:18px; overflow:hidden; height:100%;
    box-shadow:0 10px 24px rgba(15,23,42,.06);
    transition: transform .2s ease, box-shadow .2s ease;
    background:#fff;
  }
  .srv-card:hover{ transform: translateY(-4px); box-shadow:0 16px 32px rgba(15,23,42,.10) }
  .srv-img-wrap{ position:relative; height:190px; overflow:hidden }
  .srv-img{ width:100%; height:100%; object-fit:cover; transform:scale(1.01); transition: transform .5s ease }
  .srv-card:hover .srv-img{ transform:scale(1.07) }
  .srv-grad{
    position:absolute; inset:0;
    background:linear-gradient(to top, rgba(0,0,0,.45), rgba(0,0,0,0));
  }
  .srv-kicker{
    position:absolute; left:12px; bottom:12px; color:#fff;
    background:rgba(255,255,255,.18); border:1px solid rgba(255,255,255,.35);
    padding:6px 10px; border-radius:999px; backdrop-filter: blur(6px);
    font-size:.8rem; font-weight:700; letter-spacing:.3px;
  }

  .srv-title{ font-weight:700; margin-bottom:.25rem }
  .srv-desc{ color:#6b7280; display:-webkit-box; -webkit-line-clamp:3; -webkit-box-orient:vertical; overflow:hidden }

  .srv-footer{ background:#fff; border:0; padding:0 1rem 1rem 1rem }
  .tag-badge{
    display:inline-block; background:#fff5f5; color:#c1121f; border:1px solid #ffd4d4;
    border-radius:999px; padding:4px 10px; font-size:.75rem; font-weight:700
  }

  /* Pagination centering fix (bootstrap component) */
  .pagination{ justify-content:center }
</style>

{{-- ===== Header + search ===== --}}
<section class="srv-hero">
  <div class="container">
    <div class="row g-3 align-items-end">
      <div class="col-lg-7">
        <h1 class="mb-2">Our Services</h1>
        <p class="lead mb-0">Comprehensive care across specialties—explore what we offer.</p>
      </div>
      <div class="col-lg-5">
        <form action="{{ route('services.index.public') }}" method="GET" class="pill-search">
          <input type="text" name="q" value="{{ request('q') }}" placeholder="Search services (e.g., Cardiology, Dental)…">
          <button class="btn btn-danger" type="submit"><i class="fas fa-search me-1"></i> Search</button>
        </form>
      </div>
    </div>
  </div>
</section>

{{-- ===== Grid ===== --}}
<section class="py-4 py-md-5">
  <div class="container">
    @if($services->count())
      <div class="row g-4">
        @foreach ($services as $service)
          @php
            $img = $service->image ? asset('images/services/'.$service->image)
                                   : asset('frontend/images/placeholder-service.jpg');
          @endphp
          <div class="col-md-6 col-lg-4">
            <div class="srv-card">
              <div class="srv-img-wrap">
                <img class="srv-img" src="{{ $img }}" alt="{{ $service->name }}">
                <span class="srv-grad"></span>
                <span class="srv-kicker">{{ $service->updated_at?->format('d M Y') }}</span>
              </div>

              <div class="card-body d-flex flex-column">
                <h5 class="srv-title">{{ $service->name }}</h5>
                <p class="srv-desc mb-3">{{ \Illuminate\Support\Str::limit(strip_tags($service->description), 210) }}</p>

                <div class="mt-auto d-flex flex-wrap gap-2">
                  @if (Route::has('services.show.public'))
                    <a href="{{ route('services.show.public', $service->id) }}" class="btn btn-outline-danger btn-sm">
                      Read More
                    </a>
                  @endif
                  @if (Route::has('appointments.create.public'))
                    <a href="{{ route('appointments.create.public', ['service' => $service->id]) }}" class="btn btn-danger btn-sm">
                      Book Now
                    </a>
                  @endif>
                </div>
              </div>

              <div class="srv-footer">
                <span class="tag-badge"><i class="fas fa-heartbeat me-1"></i> Care</span>
              </div>
            </div>
          </div>
        @endforeach
      </div>

      {{-- Pagination --}}
      <div class="mt-4">
        {{ $services->withQueryString()->links('pagination::bootstrap-5') }}
      </div>
    @else
      <div class="text-center py-5">
        <h5 class="mb-1">No services found</h5>
        <p class="text-muted">Try a different search term.</p>
      </div>
    @endif
  </div>
</section>
@endsection
