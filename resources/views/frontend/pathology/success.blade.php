@extends('frontend.layout')

@section('title', 'Pathology Lab Booking Confirmed')

@section('content')
<section class="py-5">
    <div class="container text-center">
        <div class="row justify-content-center">
            <div class="col-md-7">
                <div class="card shadow border-0 p-5 rounded-4">
                    <div class="mb-4">
                        <i class="fa fa-check-circle text-success" style="font-size: 80px;"></i>
                    </div>
                    
                    <h2 class="fw-bold mb-3 text-success">Pathology Booking Confirmed!</h2>
                    <p class="text-muted fs-5">Thank you, {{ $booking->patient_name }}. Your pathology laboratory booking request has been submitted successfully.</p>
                    
                    <div class="p-4 bg-light rounded-3 border text-start my-4">
                        <div class="row g-2">
                            <div class="col-sm-6">
                                <span class="text-muted small d-block">BOOKING NUMBER</span>
                                <strong class="fs-5 text-primary">{{ $booking->booking_no }}</strong>
                            </div>
                            <div class="col-sm-6">
                                <span class="text-muted small d-block">APPOINTMENT DATE</span>
                                <strong class="fs-5">{{ date('d M Y', strtotime($booking->booking_date)) }}</strong>
                            </div>
                            <div class="col-sm-6">
                                <span class="text-muted small d-block">PREFERRED TIME</span>
                                <strong class="fs-6">{{ $booking->booking_time }}</strong>
                            </div>
                            <div class="col-sm-6">
                                <span class="text-muted small d-block">COLLECTION TYPE</span>
                                <span class="badge bg-secondary">{{ ($booking->collection_type === 'home') ? '🏠 Home Collection' : '🏢 Clinic Visit' }}</span>
                            </div>
                            <div class="col-12 mt-2">
                                <span class="text-muted small d-block">TOTAL AMOUNT PAID/PAYABLE</span>
                                <strong class="fs-4 text-success">₹{{ number_format($booking->total_amount, 2) }}</strong>
                                <span class="badge bg-{{ $booking->payment_status === 'paid' ? 'success' : 'warning text-dark' }} ms-1">
                                    {{ ucfirst($booking->payment_status) }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="alert alert-info py-3 text-start small mb-4">
                        <i class="fa fa-info-circle me-1"></i> Our medical staff will contact you shortly on <strong>{{ $booking->mobile }}</strong> to confirm the sample collection timeline. You can check report updates using your booking number in the status panel.
                    </div>

                    <div class="d-flex flex-wrap gap-2 justify-content-center">
                        <a href="{{ route('home') }}" class="btn btn-outline-primary px-4 py-2">
                            🏠 Return Home
                        </a>
                        <a href="{{ route('pathology.index.public') }}" class="btn btn-primary px-4 py-2">
                            🔬 Book More Tests
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
