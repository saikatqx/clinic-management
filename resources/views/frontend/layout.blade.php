<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Clinic | @yield('title')</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet">
  <style>
    .top-bar {background: #c50000; color: white; font-size: 14px;}
    .top-bar a {color: white; text-decoration: none;}
    .navbar-brand img {height: 50px;}
    .hero {background: url('{{ asset("frontend/images/hero.jpg") }}') center/cover no-repeat; color: white; padding: 100px 0;}
    .hero h1 {font-size: 2.5rem; font-weight: 700;}
    .hero p {font-size: 1.1rem;}
    .search-section {background: #f7f9fc; padding: 40px 0;}
    .footer {background: #f5f5f5; padding: 20px 0; font-size: 14px; color: #555;}
  </style>
</head>
<body>

  {{-- Top bar --}}
  <div class="top-bar py-2 text-center">
    <div class="container d-flex justify-content-between">
      <div>
        <strong>24 x 7 Dialysis Center at DAMA Hospital</strong>
      </div>
      <div>
        📞 <a href="tel:+918017000093">+91 8017000093</a> |
        ✉️ <a href="mailto:contact.technodama@gmail.com">contact.technodama@gmail.com</a>
      </div>
    </div>
  </div>

  {{-- Navbar --}}
  <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom">
    <div class="container">
      <a class="navbar-brand fw-bold" href="{{ route('home') }}">
        <img src="{{ asset('frontend/images/logo.png') }}" alt="Clinic Logo">
      </a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#nav">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="nav">
        <ul class="navbar-nav ms-auto">
          <li class="nav-item"><a class="nav-link {{ request()->routeIs('home') ? 'text-danger fw-bold' : '' }}" href="{{ route('home') }}">Home</a></li>
          <li class="nav-item"><a class="nav-link" href="{{ route('doctors.index.public') }}">Find Doctors</a></li>
          <li class="nav-item"><a class="nav-link" href="{{ route('services.index.public') }}">Services</a></li>
          <li class="nav-item"><a class="nav-link" href="{{ route('contact') }}">Contact</a></li>
          <li class="nav-item ms-3">
            <a href="{{ route('appointments.create.public') }}" class="btn btn-danger text-white">Book Appointment</a>
          </li>
        </ul>
      </div>
    </div>
  </nav>

  {{-- Main content --}}
  <main>@yield('content')</main>

  {{-- Footer --}}
  <footer class="footer text-center">
    <div class="container">
      <p class="mb-0">© {{ date('Y') }} Techno DAMA Clinic. All Rights Reserved.</p>
    </div>
  </footer>

  <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
  <script>
    @if (session('success'))
        toastr.success("{{ session('success') }}");
    @elseif (session('error'))
        toastr.error("{{ session('error') }}");
    @elseif (session('warning'))
        toastr.warning("{{ session('warning') }}");
    @elseif (session('info'))
        toastr.info("{{ session('info') }}");
    @endif
  </script>
  @stack('scripts')
</body>
</html>
