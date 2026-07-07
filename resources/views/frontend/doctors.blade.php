@extends('frontend.layout')
@section('title', 'Doctors')

@section('content')
<section class="search-section text-center py-4">
  <div class="container">
    
    <div class="alert alert-info border-0 shadow-sm d-flex flex-wrap align-items-center justify-content-between p-3 mb-4 rounded-3 text-start">
      <div class="mb-2 mb-md-0">
        <h6 class="mb-1 text-primary"><i class="fa-solid fa-robot me-1"></i> AI Symptom Checker & Specialty Guide</h6>
        <p class="mb-0 text-muted small">Not sure which specialty you need? Tell us what symptoms you have, and our AI will select the right department!</p>
      </div>
      <button type="button" class="btn btn-primary btn-sm px-4" data-bs-toggle="modal" data-bs-target="#aiTriageModal">
        Ask AI Guide
      </button>
    </div>

    <h3 class="mb-4">Find the Right Doctor for You</h3>

    <form class="row g-3 justify-content-center" method="get" action="{{ route('doctors.index.public') }}">
      <div class="col-md-4">
        <select name="specialty" id="home_specialty" class="form-select select2" data-placeholder="Select Specialty">
          <option value="">Select Speciality</option>
          @foreach($specialties as $specialty)
          <option value="{{ $specialty->id }}" {{ (int)($specialtyId ?? 0) === (int)$specialty->id ? 'selected' : '' }}>
            {{ $specialty->name }}
          </option>
          @endforeach
        </select>
      </div>

      <div class="col-md-4">
        <select name="doctor" id="home_doctor" class="form-select select2" data-placeholder="Select Doctor">
          <option value="">Select Doctor</option>
          {{-- initially show ALL active doctors so first load works --}}
          @foreach($doctorsMaster as $doctor)
          <option value="{{ $doctor->id }}"
            data-specialty="{{ $doctor->specialty_id }}"
            {{ (int)($doctorId ?? 0) === (int)$doctor->id ? 'selected' : '' }}>
            {{ $doctor->name }}
          </option>
          @endforeach
        </select>
      </div>

      <div class="col-md-2">
        <button class="btn btn-danger w-100">Search</button>
      </div>
    </form>
  </div>
</section>

{{-- ===== Results (show all initially, filter after submit) ===== --}}
<section class="py-3">
  <div class="container">
    @if($results->count())
    <div class="row g-4">
      @foreach($results as $d)
      <div class="col-md-4">
        <div class="card h-100 shadow-sm">
          @if(!empty($d->profile_image))
          <img src="{{ asset('images/doctors/'.$d->profile_image) }}" class="card-img-top" alt="{{ $d->name }}">
          @endif
          <div class="card-body">
            <h5 class="card-title mb-1">{{ $d->name }}</h5>
            <p class="text-muted mb-2">{{ optional($d->specialty)->name }}</p>
            @if(!empty($d->experience_years))
            <p class="mb-1">Experience: {{ $d->experience_years }} yrs</p>
            @endif
            @if(!empty($d->location))
            <p class="mb-0">{{ $d->location }}</p>
            @endif
          </div>
          <div class="card-footer bg-white border-0 pb-3">
            <a href="javascript:void(0);"
              class="btn btn-outline-primary w-100 book-btn"
              data-id="{{ $d->id }}"
              data-name="{{ $d->name }}">
              Book Appointment
            </a>
          </div>
        </div>
      </div>
      @endforeach
    </div>

    <div class="mt-4">
      {{ $results->links() }}
    </div>
    @else
    <div class="text-center text-muted py-5">No doctors found.</div>
    @endif
  </div>
</section>

<!-- Appointment Modal -->
<div class="modal fade" id="appointmentModal" tabindex="-1" aria-labelledby="appointmentModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content border-0 shadow">
      <div class="modal-header bg-danger text-white">
        <h5 class="modal-title" id="appointmentModalLabel">Book Appointment</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <form id="appointmentForm">
          @csrf
          <input type="hidden" name="doctor_id" id="doctor_id">

          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label fw-semibold">Patient Name</label>
              <input type="text" name="patient_name" class="form-control" placeholder="Enter your name" required>
            </div>

            <div class="col-md-6">
              <label class="form-label fw-semibold">Patient Email</label>
              <input type="email" name="patient_email" class="form-control" placeholder="Enter your email" required>
            </div>

            <div class="col-md-6">
              <label class="form-label fw-semibold">Phone Number</label>
              <input type="text" name="patient_phone" class="form-control" placeholder="Enter your phone" required>
            </div>

            <div class="col-md-6">
              <label class="form-label fw-semibold">Appointment Date</label>
              <input type="date" id="booking_date" class="form-control" required min="{{ date('Y-m-d') }}">
            </div>

            <input type="hidden" name="appointment_date" id="appointment_date">

            <div class="col-12 mt-2 text-start">
              <label class="form-label fw-semibold d-block">Available Time Slots</label>
              <div id="slots_container" class="d-flex flex-wrap gap-2 p-3 bg-light rounded-3 border">
                <span class="text-muted small">Please select a date first to view available time slots.</span>
              </div>
            </div>

            <div class="col-12">
              <label class="form-label fw-semibold">Notes (optional)</label>
              <textarea name="notes" class="form-control" rows="2" placeholder="Describe your issue"></textarea>
            </div>

            <div class="col-12 mt-2">
              <div class="p-3 bg-light rounded-3 border text-start">
                <h6 class="text-success mb-2"><i class="fa fa-credit-card me-1"></i> Pay Consultation Fee (₹500.00)</h6>
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
                <small class="text-muted d-block mt-1">💳 Simulated Stripe Test Checkout. Fee will be charged upon scheduling.</small>
                <div class="form-check mt-2">
                  <input class="form-check-input" type="checkbox" name="pay_now" id="pay_now" value="1" checked>
                  <label class="form-check-label small fw-semibold text-dark" for="pay_now">
                    Authorize immediate payment charge of ₹500.00
                  </label>
                </div>
              </div>
            </div>
          </div>

          <div class="text-end mt-3">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-danger">Submit</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

@endsection

@push('scripts')
{{-- jQuery + Select2 includes should be in your layout --}}
<script>
  $(function() {
    $('.select2').select2({
      width: '100%',
      allowClear: true,
      placeholder: function() {
        return $(this).data('placeholder');
      }
    });

    const $spec = $('#home_specialty');
    const $doc = $('#home_doctor');

    // On specialty change → fetch doctors via AJAX (dependent dropdown)
    $spec.on('change', function() {
      const specialtyId = $(this).val();
      // reset doctor list to empty + placeholder
      $doc.empty().append(new Option('', '', false, false)).trigger('change');

      if (!specialtyId) {
        @php
          $all = $doctorsMaster->map(fn($d) => [
            'id' => $d->id,
            'name' => $d->name,
            'specialty_id' => $d->specialty_id
          ])->values();
        @endphp
        const allDoctors = @json($all);
        allDoctors.forEach(d => $doc.append(new Option(d.name, d.id)));
        $doc.trigger('change');
        return;
      }

      $.ajax({
        url: "{{ route('doctors.bySpecialty', ':id') }}".replace(':id', specialtyId),
        type: "GET",
        success: function(response) {
          if (response.length) {
            response.forEach(d => $doc.append(new Option(d.name, d.id)));
          } else {
            $doc.append(new Option('No doctors found', ''));
          }
          $doc.trigger('change');
        },
        error: function(xhr) {
          console.error(xhr.responseText);
        }
      });
    });

    // If page loaded with a specialty already selected, trigger the load
    @if(!empty($specialtyId))
    $spec.trigger('change');
    @if(!empty($doctorId))
    // after AJAX completes, preselect the doctor
    $(document).ajaxStop(function() {
      $doc.val('{{ (int)$doctorId }}').trigger('change');
      $(document).off('ajaxStop');
    });
    @endif
    @endif
  });

  $(function() {

    // Open modal and set doctor ID
    $(document).on('click', '.book-btn', function() {
      const doctorId = $(this).data('id');
      const doctorName = $(this).data('name');

      $('#doctor_id').val(doctorId);
      $('#appointmentModalLabel').text(`Book Appointment with Dr. ${doctorName}`);
      $('#booking_date').val('');
      $('#appointment_date').val('');
      $('#slots_container').html('<span class="text-muted small">Please select a date first to view available time slots.</span>');
      $('#appointmentModal').modal('show');
    });

    // Handle form submit via AJAX
    $('#appointmentForm').on('submit', function(e) {
      e.preventDefault();

      if (!$('#appointment_date').val()) {
        toastr.error('Please select an available time slot.');
        return;
      }

      $.ajax({
        url: "{{ route('appointments.store.public') }}",
        method: "POST",
        data: $(this).serialize(),
        success: function(res) {
          $('#appointmentModal').modal('hide');
          toastr.success('Appointment booked successfully!');
          $('#appointmentForm')[0].reset();
          $('#appointment_date').val('');
        },
        error: function(xhr) {
          console.error(xhr.responseText);
          const errorMsg = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Something went wrong. Please try again.';
          toastr.error(errorMsg);
        }
      });
    });

    // When Date changes → load slots via AJAX
    $(document).on('change', '#booking_date', function() {
      const date = $(this).val();
      const doctorId = $('#doctor_id').val();
      const $container = $('#slots_container');

      if (!date || !doctorId) return;

      $container.html('<span class="text-muted small"><i class="fa fa-spinner fa-spin me-1"></i> Loading slots...</span>');
      $('#appointment_date').val('');

      $.ajax({
        url: "{{ route('appointments.slots') }}",
        method: "GET",
        data: {
          doctor_id: doctorId,
          date: date
        },
        success: function(res) {
          $container.empty();
          if (res.slots && res.slots.length > 0) {
            res.slots.forEach(function(slot) {
              $container.append(`
                <button type="button" class="btn btn-outline-primary btn-sm slot-badge-btn" data-datetime="${slot.datetime}">
                  ${slot.time}
                </button>
              `);
            });
          } else {
            $container.html('<span class="text-danger small">No available slots for this day. Please select another date.</span>');
          }
        },
        error: function(xhr) {
          console.error(xhr.responseText);
          $container.html('<span class="text-danger small">Error loading slots. Please try again.</span>');
        }
      });
    });

    // When clicking a slot badge
    $(document).on('click', '.slot-badge-btn', function() {
      $('.slot-badge-btn').removeClass('btn-primary text-white').addClass('btn-outline-primary');
      $(this).removeClass('btn-outline-primary').addClass('btn-primary text-white');
      $('#appointment_date').val($(this).data('datetime'));
    });

    // Handle AI Triage Form Submit
    $('#aiTriageForm').on('submit', function (e) {
      e.preventDefault();
      const symptoms = $('#aiSymptomsInput').val().trim();
      if (!symptoms) return;

      const $btn = $('#aiTriageSubmit');
      $btn.prop('disabled', true).text('Checking...');

      $.ajax({
        url: '{{ route("chatbot.triage") }}',
        method: 'POST',
        data: {
          _token: '{{ csrf_token() }}',
          symptoms: symptoms
        },
        success: function (res) {
          $btn.prop('disabled', false).text('Check Symptoms');
          if (res.specialty_id) {
            $('#aiMatchedText').html(`💡 AI matched your symptoms to: <strong>${res.specialty}</strong>!`);
            $('#aiTriageResult').removeClass('d-none');
            
            // Select the matched specialty in the main search
            $('#home_specialty').val(res.specialty_id).trigger('change');
            
            // Wait and close modal
            setTimeout(function () {
              $('#aiTriageModal').modal('hide');
              $('#aiTriageResult').addClass('d-none');
              $('#aiSymptomsInput').val('');
            }, 2500);
          } else {
            $('#aiMatchedText').html(`❌ Unable to match symptoms to a specific department.`);
            $('#aiTriageResult').removeClass('d-none');
          }
        },
        error: function (xhr) {
          $btn.prop('disabled', false).text('Check Symptoms');
          toastr.error('Triage service unavailable.');
        }
      });
    });

  });
</script>

<!-- AI Triage Modal -->
<div class="modal fade" id="aiTriageModal" tabindex="-1" aria-labelledby="aiTriageModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title" id="aiTriageModalLabel"><i class="fa-solid fa-robot me-2"></i> AI Symptom Checker</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body text-start">
        <form id="aiTriageForm">
          <div class="mb-3">
            <label class="form-label fw-semibold">Describe what you are feeling:</label>
            <textarea id="aiSymptomsInput" class="form-control" rows="4" placeholder="e.g. I have a sore throat, mild fever, and coughing for 2 days." required></textarea>
          </div>
          <div class="text-end">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-primary" id="aiTriageSubmit">Check Symptoms</button>
          </div>
        </form>
        <div id="aiTriageResult" class="mt-3 d-none p-3 bg-light border rounded-3 text-center">
          <p class="mb-1 fw-bold text-success" id="aiMatchedText"></p>
          <small class="text-muted">The search dropdowns have been automatically configured for this specialty.</small>
        </div>
      </div>
    </div>
  </div>
</div>
@endpush