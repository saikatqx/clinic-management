<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>{{ $settings->clinic_name ?? 'Clinic' }} | @yield('title')</title>

  @if(!empty($settings?->favicon))
  <link rel="icon" href="{{ asset('images/settings/'.$settings->favicon) }}">
  @endif

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.5/css/jquery.dataTables.min.css">

  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

  <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css" rel="stylesheet" />

  <!-- Font Awesome CDN -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
  <!-- Include Select2 CSS -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet" />

  <!-- Summernote CSS -->
  <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.css" rel="stylesheet">

  <style>
    body {
      font-family: system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", "Liberation Sans"
    }

    .notice-bar {
      background: #c50000;
      color: #fff;
      font-size: 14px
    }

    .contact-strip {
      border-bottom: 1px solid #eee
    }

    .contact-item {
      font-size: 14px;
      color: #0d6efd
    }

    .contact-item i {
      color: #0d6efd;
      margin-right: .4rem
    }

    .social a {
      color: #0d6efd;
      margin-left: .6rem
    }

    .navbar-brand img {
      height: 56px;
      object-fit: contain
    }

    .nav-link.active,
    .nav-link:hover {
      color: #0d6efd !important;
      font-weight: 600
    }

    /* Chatbot widget */
    .chat-widget {
      position: fixed;
      right: 1rem;
      bottom: 1rem;
      z-index: 1100;
      width: 340px;
      max-width: calc(100% - 1rem);
      font-family: Inter, system-ui, sans-serif;
    }

    .chat-widget .chat-toggle {
      border-radius: 50px;
      background: #0d6efd;
      color: #fff;
      border: none;
      padding: 0.9rem 1.1rem;
      box-shadow: 0 10px 30px rgba(13, 110, 253, .2);
      font-weight: 600;
      display: inline-flex;
      align-items: center;
      gap: 0.5rem;
    }

    .chat-widget .chat-panel {
      border-radius: 18px;
      background: #fff;
      box-shadow: 0 20px 40px rgba(0, 0, 0, .12);
      overflow: hidden;
      margin-top: 0.75rem;
      display: none;
      flex-direction: column;
      border: 1px solid #e9ecef;
      height: 440px;
    }

    .chat-widget .chat-panel.active {
      display: flex;
    }

    .chat-widget .chat-header {
      background: #0d6efd;
      color: #fff;
      padding: 0.9rem 1rem;
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 0.75rem;
    }

    .chat-widget .chat-messages {
      flex: 1;
      overflow-y: auto;
      padding: 1rem;
      background: #f8f9fa;
      min-height: 220px;
    }

    .chat-widget .chat-message {
      margin-bottom: 0.9rem;
      line-height: 1.5;
      max-width: 85%;
      padding: 0.8rem 1rem;
      border-radius: 18px;
      word-break: break-word;
    }

    .chat-widget .chat-message.user {
      margin-left: auto;
      background: #0d6efd;
      color: #fff;
      border-bottom-right-radius: 4px;
    }

    .chat-widget .chat-message.bot {
      margin-right: auto;
      background: #fff;
      border: 1px solid #dee2e6;
      color: #212529;
      border-bottom-left-radius: 4px;
    }

    .chat-widget .chat-form {
      border-top: 1px solid #e9ecef;
      display: flex;
      gap: 0.5rem;
      padding: 0.75rem;
      background: #fff;
    }

    .chat-widget .chat-form input {
      flex: 1;
      border: 1px solid #ced4da;
      border-radius: 999px;
      padding: 0.75rem 1rem;
      outline: none;
    }

    .chat-widget .chat-form button {
      border-radius: 999px;
      background: #0d6efd;
      color: #fff;
      border: none;
      padding: 0.75rem 1rem;
      min-width: 95px;
    }
  </style>
  @stack('head')
</head>

<body>

  {{-- 24 × 7 / notice bar --}}
  <div class="notice-bar py-2">
    <div class="container d-flex flex-wrap justify-content-center justify-content-md-between align-items-center">
      {{-- <div class="mb-1 mb-md-0 fw-semibold">
        {{ $settings->top_notice ?? '24 x 7 Dialysis Center at DAMA Hospital' }}
      </div> --}}
      <div class="mb-1 mb-md-0 fw-semibold">
          <span class="fs-2">{{ $settings->top_notice ?? 'MediBook' }}</span><br>
          <span class="fs-5">{{ $settings->clinic_name ?? 'Clinic' }}</span>
      </div>

      <div class="d-none d-md-block">
        <span class="me-3">
          <i class="fa-solid fa-phone"></i>
          <a class="text-white text-decoration-none" href="tel:{{ $settings->mobile }}">{{ $settings->mobile }}</a>
        </span>
        <span>
          <i class="fa-solid fa-envelope"></i>
          <a class="text-white text-decoration-none" href="mailto:{{ $settings->email }}">{{ $settings->email }}</a>
        </span>
      </div>
    </div>
  </div>

  {{-- logo + address + quick contacts row --}}
  <div class="py-3 contact-strip">
    <div class="container d-flex flex-wrap align-items-center justify-content-between gap-3">
      <a class="navbar-brand d-flex align-items-center" href="{{ route('home') }}">
        @if(!empty($settings?->clinic_logo))
        <img src="{{ asset('images/settings/'.$settings->clinic_logo) }}" alt="Logo">
        @else
        <span class="fs-4 fw-bold text-primary">{{ $settings->clinic_name ?? 'Clinic' }}</span>
        @endif
      </a>

      <div class="d-flex flex-column flex-md-row align-items-md-center gap-3">
        {{-- address --}}
        @if(!empty($settings?->address))
        <div class="contact-item">
          <i class="fa-solid fa-location-dot"></i>
          @if(!empty($settings?->location_link))
          <a class="text-decoration-none" target="_blank" href="{{ $settings->location_link }}">{{ $settings->address }}</a>
          @else
          {{ $settings->address }}
          @endif
        </div>
        @endif

        {{-- phone --}}
        @if(!empty($settings?->mobile))
        <div class="contact-item">
          <i class="fa-solid fa-phone-volume"></i>
          <a class="text-decoration-none" href="tel:{{ $settings->mobile }}">{{ $settings->mobile }}</a>
        </div>
        @endif

        {{-- email --}}
        @if(!empty($settings?->email))
        <div class="contact-item">
          <i class="fa-solid fa-envelope"></i>
          <a class="text-decoration-none" href="mailto:{{ $settings->email }}">{{ $settings->email }}</a>
        </div>
        @endif

        {{-- social --}}
        <div class="social">
          @if(!empty($settings?->linkedin)) <a href="{{ $settings->linkedin }}" target="_blank" aria-label="LinkedIn"><i class="fa-brands fa-linkedin"></i></a>@endif
          @if(!empty($settings?->youtube)) <a href="{{ $settings->youtube }}" target="_blank" aria-label="YouTube"><i class="fa-brands fa-youtube"></i></a>@endif
          @if(!empty($settings?->facebook)) <a href="{{ $settings->facebook }}" target="_blank" aria-label="Facebook"><i class="fa-brands fa-facebook"></i></a>@endif
          @if(!empty($settings?->instagram))<a href="{{ $settings->instagram }}" target="_blank" aria-label="Instagram"><i class="fa-brands fa-instagram"></i></a>@endif
        </div>
      </div>
    </div>
  </div>

  {{-- navbar --}}
  <nav class="navbar navbar-expand-lg bg-white sticky-top">
    <div class="container">
      <a class="navbar-brand d-lg-none" href="{{ route('home') }}">
        {{ $settings->clinic_name ?? 'Clinic' }}
      </a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div id="mainNav" class="collapse navbar-collapse">
        <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-2">
          <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">Home</a>
          </li>
          <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('doctors.index.public*') ? 'active' : '' }}" href="{{ route('doctors.index.public') }}">Find Doctors</a>
          </li>
          <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('services.index.public*') ? 'active' : '' }}" href="{{ route('services.index.public') }}">Services</a>
          </li>
          <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('diagnostics.index.public*') ? 'active' : '' }}" href="{{ route('diagnostics.index.public') }}">Diagnostics</a>
          </li>
          <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('pathology.index.public*') ? 'active' : '' }}" href="{{ route('pathology.index.public') }}">Pathology</a>
          </li>
          <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('packages.index.public*') ? 'active' : '' }}" href="{{ route('packages.index.public') }}">Health Packages</a>
          </li>
          <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('contact') ? 'active' : '' }}" href="{{ route('contact') }}">Contact</a>
          </li>
          <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('chatbot.index') ? 'active' : '' }}" href="{{ route('chatbot.index') }}">Health Chat</a>
          </li>
          <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('appointments.status') ? 'active' : '' }}" href="{{ route('appointments.status') }}">Track Status</a>
          </li>
          <li class="nav-item ms-lg-3">
            <a href="{{ route('doctors.index.public') }}" class="btn btn-primary">Book Appointment</a>
          </li>
        </ul>
      </div>
    </div>
  </nav>

  {{-- page content --}}
  <main>@yield('content')</main>

  {{-- footer --}}
  <footer class="py-3 border-top mt-5">
    <div class="container text-center small text-muted">
      © {{ date('Y') }} {{ $settings->clinic_name ?? 'Clinic' }}. All rights reserved.
    </div>
  </footer>

  @unless(request()->routeIs('chatbot.index'))
  <div class="chat-widget">
    <button id="chat-toggle" class="chat-toggle">
      <i class="fa-solid fa-comments"></i>
      Health Chat
    </button>

    <div id="chat-panel" class="chat-panel" aria-hidden="true">
      <div class="chat-header">
        <div>
          <strong>Health Chatbot</strong>
          <div class="small">Ask any health-related or clinic question.</div>
        </div>
        <button id="chat-close" type="button" class="btn btn-sm btn-light text-dark" aria-label="Close chat">
          <i class="fa-solid fa-xmark"></i>
        </button>
      </div>
      <div id="chat-messages" class="chat-messages"></div>
      <form id="chat-form" class="chat-form">
        <input id="chat-input" type="text" placeholder="Type your question..." autocomplete="off" />
        <button type="submit">Send</button>
      </form>
    </div>
  </div>
  @endunless

  <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
  <script>
    if (window.axios && document.querySelector('meta[name="csrf-token"]')) {
      window.axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    }
  </script>
  <script src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
  <!-- Include Select2 JS -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
  <!-- jQuery (already loaded if you’re using DataTables) -->
  <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <!-- Select2 Initialization -->
  <script>
    $(document).ready(function() {
      $('.select2').select2({
        width: '100%',
        placeholder: 'Search and select...',
        allowClear: true
      });
    });
  </script>
  @if(session('success'))
  <script>
    toastr.success('{{ session('
      success ') }}');
  </script>
  @elseif(session('warning'))
  <script>
    toastr.warning('{{ session('
      warning ') }}');
  </script>
  @elseif(session('error'))
  <script>
    toastr.error('{{ session('
      error ') }}');
  </script>
  @endif
  <script>
  // quick sanity check in console: should print "function"
  // console.log(typeof $.fn.select2);

  $(function () {
    $('.select2').each(function(){
      $(this).select2({
        theme: 'bootstrap-5',                  // pretty with Bootstrap 5
        width: '100%',
        placeholder: $(this).data('placeholder') || 'Select an option',
        allowClear: true
      });
    });
  });
</script>

  <script>
    $(function () {
      const $toggle = $('#chat-toggle');
      const $panel = $('#chat-panel');
      const $close = $('#chat-close');
      const $messages = $('#chat-messages');
      const $input = $('#chat-input');
      const $form = $('#chat-form');

      const chatRoute = '{{ route('chatbot.send') }}';

      function addChatMessage(type, text) {
        const message = $('<div>').addClass('chat-message ' + type).text(text);
        $messages.append(message);
        $messages.scrollTop($messages[0].scrollHeight);
      }

      function setPanelVisible(visible) {
        if (visible) {
          $panel.addClass('active');
          $panel.attr('aria-hidden', 'false');
          $input.focus();
        } else {
          $panel.removeClass('active');
          $panel.attr('aria-hidden', 'true');
        }
      }

      $toggle.on('click', function () {
        setPanelVisible(!$panel.hasClass('active'));
      });

      $close.on('click', function () {
        setPanelVisible(false);
      });

      $form.on('submit', function (event) {
        event.preventDefault();
        const text = $input.val().trim();
        if (!text) {
          return;
        }

        addChatMessage('user', text);
        $input.val('');
        addChatMessage('bot', 'Thinking...');

        axios.post(chatRoute, { message: text })
          .then(function (response) {
            const botText = response.data.message || 'Sorry, I could not retrieve an answer.';
            $messages.find('.chat-message.bot').last().text(botText);
          })
          .catch(function (error) {
            const errorMessage = error.response?.data?.message || 'Unable to reach the chat service.';
            $messages.find('.chat-message.bot').last().text(errorMessage);
          });
      });
    });
  </script>

  @stack('scripts')
</body>

</html>