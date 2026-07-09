@extends('frontend.layout')

@section('title', 'Health Packages')

@section('content')
<section class="py-5 bg-light">
    <div class="container">
        <div class="text-center mb-5">
            <h1 class="fw-bold text-primary"><i class="fa fa-heartbeat me-2"></i> Preventive Health Packages</h1>
            <p class="text-muted">Explore our curated healthcare checkup packages customized for different age groups and needs. Save up to 60% compared to individual test costs.</p>
        </div>

        <div class="row g-4 justify-content-center">
            @forelse($packages as $pkg)
                <div class="col-lg-4 col-md-6">
                    <div class="card h-100 shadow-sm border-0 rounded-3 overflow-hidden">
                        <div style="position: relative;">
                            <img src="{{ $pkg->image ? asset('images/packages/' . $pkg->image) : asset('images/default.png') }}" class="card-img-top" alt="{{ $pkg->name }}" style="height: 200px; object-fit: cover;">
                            <span class="badge bg-primary position-absolute top-0 end-0 m-3 fs-6">
                                <i class="fa fa-venus-mars me-1"></i> {{ ucfirst(strtolower($pkg->gender)) }}
                            </span>
                        </div>
                        <div class="card-body d-flex flex-column p-4">
                            <h4 class="card-title fw-bold text-dark mb-2">{{ $pkg->name }}</h4>
                            <p class="text-muted small mb-3" style="min-height: 50px;">{{ Str::limit($pkg->description, 100) }}</p>
                            
                            <div class="mb-3">
                                <span class="text-muted text-decoration-line-through small me-2">Actual: ₹{{ number_format($pkg->actual_price, 2) }}</span>
                                <span class="text-success fw-bold fs-4">Offer: ₹{{ number_format($pkg->package_price, 2) }}</span>
                            </div>

                            <div class="mb-4">
                                <h6 class="fw-bold small text-secondary mb-2"><i class="fa fa-check-circle me-1 text-primary"></i> Included Tests ({{ $pkg->diagnostics->count() }}):</h6>
                                <div class="d-flex flex-wrap gap-1">
                                    @foreach($pkg->diagnostics->take(4) as $diag)
                                        <span class="badge bg-light text-dark border small">{{ $diag->name }}</span>
                                    @endforeach
                                    @if($pkg->diagnostics->count() > 4)
                                        <span class="badge bg-secondary-subtle text-dark border small">+{{ $pkg->diagnostics->count() - 4 }} more</span>
                                    @endif
                                </div>
                            </div>

                            <div class="mt-auto d-flex gap-2">
                                <button type="button" class="btn btn-outline-primary w-50" data-bs-toggle="modal" data-bs-target="#pkgModal{{ $pkg->id }}">
                                    View Details
                                </button>
                                <form action="{{ route('packages.book.public') }}" method="POST" class="w-50">
                                    @csrf
                                    <input type="hidden" name="health_package_id" value="{{ $pkg->id }}">
                                    <button type="submit" class="btn btn-primary w-100">
                                        Book Now
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Package Details Modal -->
                <div class="modal fade" id="pkgModal{{ $pkg->id }}" tabindex="-1" aria-labelledby="pkgModalLabel{{ $pkg->id }}" aria-hidden="true">
                    <div class="modal-dialog modal-md modal-dialog-centered">
                        <div class="modal-content border-0 shadow">
                            <div class="modal-header bg-primary text-white">
                                <h5 class="modal-title fw-bold" id="pkgModalLabel{{ $pkg->id }}"><i class="fa fa-heartbeat me-2"></i> {{ $pkg->name }}</h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body p-4 text-start">
                                <p class="text-muted">{{ $pkg->description }}</p>
                                <div class="d-flex justify-content-between align-items-center bg-light p-3 rounded mb-4">
                                    <span class="fw-semibold">Package Price:</span>
                                    <div>
                                        <span class="text-decoration-line-through text-muted small me-2">₹{{ number_format($pkg->actual_price, 2) }}</span>
                                        <span class="text-success fw-bold fs-4">₹{{ number_format($pkg->package_price, 2) }}</span>
                                    </div>
                                </div>

                                <h6 class="fw-bold mb-3"><i class="fa fa-vial me-1 text-primary"></i> Full Test Directory ({{ $pkg->diagnostics->count() }} tests):</h6>
                                <ul class="list-group list-group-flush border rounded-3 mb-4">
                                    @foreach($pkg->diagnostics as $d)
                                        <li class="list-group-item py-2 px-3 small d-flex justify-content-between align-items-center">
                                            <span><i class="fa fa-caret-right text-primary me-2"></i> {{ $d->name }}</span>
                                            <span class="text-muted small">{{ optional($d->category)->name }}</span>
                                        </li>
                                    @endforeach
                                </ul>

                                <form action="{{ route('packages.book.public') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="health_package_id" value="{{ $pkg->id }}">
                                    <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold">
                                        Confirm and Book Health Package
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-md-8 text-center text-muted py-5">
                    <i class="fa fa-clinic-medical fa-4x mb-3 text-secondary"></i>
                    <h4>No preventive health packages available at the moment.</h4>
                    <p class="small">Please check back later or contact lab administration.</p>
                </div>
            @endforelse
        </div>
    </div>
</section>
@endsection
