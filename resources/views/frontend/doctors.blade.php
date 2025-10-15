@extends('frontend.layout')
@section('title', 'Doctors')

@section('content')
<section class="search-section text-center py-4">
  <div class="container">
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
                <a href="" class="btn btn-outline-primary w-100">
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
@endsection

@push('scripts')
{{-- jQuery + Select2 includes should be in your layout --}}
<script>
$(function () {
  $('.select2').select2({
    width: '100%',
    allowClear: true,
    placeholder: function(){ return $(this).data('placeholder'); }
  });

  const $spec = $('#home_specialty');
  const $doc  = $('#home_doctor');

  // On specialty change → fetch doctors via AJAX (dependent dropdown)
  $spec.on('change', function () {
    const specialtyId = $(this).val();
    // reset doctor list to empty + placeholder
    $doc.empty().append(new Option('','', false, false)).trigger('change');

    if (!specialtyId) {
      // if cleared, repopulate ALL from the server-rendered master (page already has them)
      @php
        $all = $doctorsMaster->map(fn($d)=>['id'=>$d->id,'name'=>$d->name,'specialty_id'=>$d->specialty_id])->values();
      @endphp
      const allDoctors = @json($all);
      allDoctors.forEach(d => $doc.append(new Option(d.name, d.id)));
      $doc.trigger('change');
      return;
    }

    $.ajax({
      url: "{{ route('doctors.bySpecialty', ':id') }}".replace(':id', specialtyId),
      type: "GET",
      success: function (response) {
        if (response.length) {
          response.forEach(d => $doc.append(new Option(d.name, d.id)));
        } else {
          $doc.append(new Option('No doctors found',''));
        }
        $doc.trigger('change');
      },
      error: function (xhr) { console.error(xhr.responseText); }
    });
  });

  // If page loaded with a specialty already selected, trigger the load
  @if(!empty($specialtyId))
    $spec.trigger('change');
    @if(!empty($doctorId))
      // after AJAX completes, preselect the doctor
      $(document).ajaxStop(function(){
        $doc.val('{{ (int)$doctorId }}').trigger('change');
        $(document).off('ajaxStop');
      });
    @endif
  @endif
});
</script>
@endpush
