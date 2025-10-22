@extends('frontend.layout')
@section('title', 'Home')

@section('content')

<!-- ====== Hero Carousel Section ====== -->
<section id="heroCarousel" class="carousel slide" data-bs-ride="carousel" data-bs-interval="4000">
  <div class="carousel-inner">
    @foreach($banners as $index => $banner)
    <div class="carousel-item {{ $index == 0 ? 'active' : '' }}">
      <img src="{{ asset('images/banners/' . $banner->image) }}" class="d-block w-100" alt="Banner {{ $index + 1 }}">
      <div class="carousel-caption d-none d-md-block text-start">
        @if($banner->title)
        <h1 class="fw-bold">{{ $banner->title }}</h1>
        @endif
        @if($banner->subtitle)
        <p>{{ $banner->subtitle }}</p>
        @endif
        @if($banner->button_text)
        <a href="{{ $banner->button_link ?? '#' }}" class="btn btn-danger btn-lg">{{ $banner->button_text }}</a>
        @endif
      </div>
    </div>
    @endforeach
  </div>

  <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
    <span class="visually-hidden">Previous</span>
  </button>
  <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
    <span class="carousel-control-next-icon" aria-hidden="true"></span>
    <span class="visually-hidden">Next</span>
  </button>

  <div class="carousel-indicators">
    @foreach($banners as $index => $banner)
    <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="{{ $index }}" class="{{ $index == 0 ? 'active' : '' }}"></button>
    @endforeach
  </div>
</section>

{{-- Find Doctor Section --}}
<section class="search-section text-center">
  <div class="container">
    <h3 class="mb-4">Find the Right Doctor for You</h3>
    <form class="row g-3 justify-content-center" method="get" action="{{ route('doctors.index.public') }}">
      <div class="col-md-4">
        <select name="specialty" id="home_specialty" class="form-select select2" data-placeholder="Select Specialty">
          <option value="">Select Speciality</option>
          @foreach($specialties as $specialty)
          <option value="{{ $specialty->id }}">{{ $specialty->name }}</option>
          @endforeach
        </select>
      </div>

      <div class="col-md-4">
        <select name="doctor" id="home_doctor" class="form-select select2" data-placeholder="Select Doctor">
          <option value="">Select Speciality</option>
          @foreach($doctors as $doctor)
          <option value="{{ $doctor->id }}" data-specialty="{{ $doctor->specialty_id }}">{{ $doctor->name }}</option>
          @endforeach
        </select>
      </div>

      <div class="col-md-2">
        <button class="btn btn-danger w-100">Search</button>
      </div>
    </form>
  </div>
</section>

{{-- Services Section --}}
<section class="py-5">
  <div class="container text-center">
    <h3 class="mb-4">Our Key Services</h3>
    <div class="row g-4">
      @foreach($services as $service)
      <div class="col-md-4">
        <div class="card shadow-sm h-100">
          @if($service->image)
          <img src="{{ asset('images/services/' . $service->image) }}" class="card-img-top" alt="">
          @endif
          <div class="card-body">
            <h5>{{ $service->name }}</h5>
            <p class="text-muted">{{ Str::limit(strip_tags($service->description), 80) }}</p>
            <a href="{{ route('services.show.public',$service->id) }}" class="btn btn-outline-danger btn-sm">Read More</a>
          </div>
        </div>
      </div>
      @endforeach
    </div>
  </div>
</section>

@endsection

@push('scripts')
<script>
$(function () {
  // Initialize Select2
  $('.select2').select2({
    width: '100%',
    allowClear: true,
    placeholder: function(){
      return $(this).data('placeholder');
    }
  });

  const $spec = $('#home_specialty');
  const $doc  = $('#home_doctor');

  // When specialty changes → load doctors via AJAX
  $spec.on('change', function () {
    const specialtyId = $(this).val();
    $doc.empty().append('<option value=""></option>').trigger('change'); // clear existing

    if (!specialtyId) return; // if nothing selected, stop

    $.ajax({
      url: "{{ route('doctors.bySpecialty', ':id') }}".replace(':id', specialtyId),
      type: "GET",
      success: function (response) {
        if (response.length > 0) {
          response.forEach(function(doctor) {
            const option = new Option(doctor.name, doctor.id, false, false);
            $doc.append(option);
          });
        } else {
          const option = new Option("No doctors found", "", false, false);
          $doc.append(option);
        }
        $doc.trigger('change');
      },
      error: function (xhr) {
        console.error(xhr.responseText);
      }
    });
  });
});
</script>
@endpush

