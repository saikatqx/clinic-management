@extends('frontend.layout')

@section('title', 'Complete Health Package Booking')

@section('content')
<section class="py-5 bg-light">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-primary text-white py-3">
                        <h4 class="mb-0 fw-bold"><i class="fa fa-clipboard-check me-2"></i> Patient & Collection Details</h4>
                    </div>
                    <div class="card-body p-4">
                        <form action="{{ route('packages.store.public') }}" method="POST" id="bookingForm" onsubmit="return validateBookingForm()">
                            @csrf
                            <input type="hidden" name="health_package_id" value="{{ $package->id }}">
                            
                            <div class="row g-3">
                                <!-- Package Info Summary -->
                                <div class="col-12 mb-3">
                                    <h5 class="fw-bold mb-3 text-secondary">Summary of Selected Package</h5>
                                    <div class="list-group">
                                        <div class="list-group-item d-flex justify-content-between align-items-start bg-light">
                                            <div>
                                                <strong class="text-dark fs-5">{{ $package->name }}</strong>
                                                <div class="mt-2 text-muted small">
                                                    @foreach($package->diagnostics as $test)
                                                        <span class="badge bg-white text-dark border me-1 mb-1"><i class="fa fa-caret-right text-primary me-1"></i> {{ $test->name }}</span>
                                                    @endforeach
                                                </div>
                                            </div>
                                            <div class="text-end">
                                                <span class="text-muted text-decoration-line-through small d-block">Actual: ₹{{ number_format($package->actual_price, 2) }}</span>
                                                <span class="text-success fw-bold fs-4">Offer: ₹{{ number_format($package->package_price, 2) }}</span>
                                            </div>
                                        </div>
                                        <div class="list-group-item d-flex justify-content-between align-items-center bg-primary-subtle border-primary">
                                            <span class="fs-5 fw-bold text-primary">Grand Total amount:</span>
                                            <span class="fs-4 fw-bold text-success">₹{{ number_format($package->package_price, 2) }}</span>
                                        </div>
                                    </div>
                                </div>

                                <hr>

                                <!-- Patient Name -->
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Patient Full Name <span class="text-danger">*</span></label>
                                    <input type="text" name="patient_name" id="patient_name" class="form-control" placeholder="Enter patient's name" required>
                                    <p id="patientNameError" class="text-danger small"></p>
                                </div>

                                <!-- Mobile -->
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Mobile Number <span class="text-danger">*</span></label>
                                    <input type="text" name="mobile" id="mobile" class="form-control" placeholder="e.g. 9876543210" required>
                                    <p id="mobileError" class="text-danger small"></p>
                                </div>

                                <!-- Email -->
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Email Address (Optional)</label>
                                    <input type="email" name="email" class="form-control" placeholder="patient@example.com">
                                    <small class="text-muted d-block">We will mail the invoice and lab report PDF to this email.</small>
                                </div>

                                <input type="hidden" name="collection_type" value="clinic">

                                <!-- Date & Time -->
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Preferred Date <span class="text-danger">*</span></label>
                                    <input type="date" name="booking_date" id="booking_date" class="form-control" required min="{{ date('Y-m-d') }}">
                                    <p id="dateError" class="text-danger small"></p>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Preferred Time Slot <span class="text-danger">*</span></label>
                                    <select name="booking_time" class="form-select select2" required>
                                        <option value="08:00 AM - 10:00 AM">Morning (08:00 AM - 10:00 AM)</option>
                                        <option value="10:00 AM - 12:00 PM">Morning (10:00 AM - 12:00 PM)</option>
                                        <option value="12:00 PM - 02:00 PM">Midday (12:00 PM - 02:00 PM)</option>
                                        <option value="02:00 PM - 04:00 PM">Afternoon (02:00 PM - 04:00 PM)</option>
                                        <option value="04:00 PM - 06:00 PM">Evening (04:00 PM - 06:00 PM)</option>
                                    </select>
                                </div>

                                <!-- Payment Method -->
                                <div class="col-12">
                                    <div class="p-3 bg-light rounded border mt-3">
                                        <h6 class="fw-bold text-primary mb-3"><i class="fa fa-credit-card me-1"></i> Payment Options</h6>
                                        
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="radio" name="payment_method" id="pay_cash" value="Cash" checked>
                                            <label class="form-check-label fw-medium" for="pay_cash">
                                                💵 Pay Cash / UPI on Sample Collection
                                            </label>
                                        </div>

                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="payment_method" id="pay_online" value="Card">
                                            <label class="form-check-label fw-medium" for="pay_online">
                                                💳 Pay Online (Simulated Sandbox Card Checkout)
                                            </label>
                                        </div>

                                        <!-- Card fields (collapsible) -->
                                        <div class="mt-3 p-3 bg-white rounded border d-none" id="card_checkout_details">
                                            <h7 class="fw-semibold text-success d-block mb-2">Simulated Credit/Debit Card Checkout</h7>
                                            <div class="row g-2">
                                                <div class="col-md-6">
                                                    <input type="text" class="form-control form-control-sm" placeholder="Card Number" value="4242 4242 4242 4242" disabled>
                                                </div>
                                                <div class="col-md-3">
                                                    <input type="text" class="form-control form-control-sm" placeholder="MM/YY" value="12/28" disabled>
                                                </div>
                                                <div class="col-md-3">
                                                    <input type="text" class="form-control form-control-sm" placeholder="CVC" value="123" disabled>
                                                </div>
                                            </div>
                                            <small class="text-muted d-block mt-1">💳 Sandbox transaction. Card authorization will happen automatically on booking.</small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Buttons -->
                            <div class="d-flex justify-content-between mt-4">
                                <a href="{{ route('packages.index.public') }}" class="btn btn-secondary py-2 px-4">
                                    ⬅ Back to Packages
                                </a>
                                <button type="submit" class="btn btn-primary py-2 px-5 fw-bold" id="confirm_booking_btn">
                                    Confirm Package Booking
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('input[name="payment_method"]').on('change', function() {
        if ($(this).val() === 'Card') {
            $('#card_checkout_details').removeClass('d-none');
        } else {
            $('#card_checkout_details').addClass('d-none');
        }
    });
});

function validateBookingForm() {
    var name = document.getElementById("patient_name").value.trim();
    var mobile = document.getElementById("mobile").value.trim();
    var date = document.getElementById("booking_date").value;
    var collection = $('#collection_type').val();

    var isValid = true;
    document.getElementById("patientNameError").textContent = "";
    document.getElementById("mobileError").textContent = "";
    document.getElementById("dateError").textContent = "";
    if (document.getElementById("addressError")) {
        document.getElementById("addressError").textContent = "";
    }

    if (name === "") {
        document.getElementById("patientNameError").textContent = "Patient Name is required.";
        isValid = false;
    }

    if (mobile === "") {
        document.getElementById("mobileError").textContent = "Mobile Number is required.";
        isValid = false;
    }

    if (date === "") {
        document.getElementById("dateError").textContent = "Appointment Date is required.";
        isValid = false;
    }

    return isValid;
}
</script>
@endpush
