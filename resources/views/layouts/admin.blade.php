<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clinic Admin - @yield('title')</title>

    <!-- Bootstrap 5 -->
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
            display: flex;
            min-height: 100vh;
            margin: 0;
            font-family: 'Inter', sans-serif;
            background: #f8f9fb;
        }

        /* Sidebar */
        .sidebar {
            width: 250px;
            background: #ffffff;
            border-right: 1px solid #e3e3e3;
            display: flex;
            flex-direction: column;
            padding-top: 20px;
        }

        .sidebar h4 {
            text-align: center;
            margin-bottom: 20px;
            font-weight: 700;
            color: #007bff;
        }

        .sidebar a {
            display: block;
            padding: 12px 20px;
            margin: 5px 10px;
            border-radius: 8px;
            color: #333;
            font-weight: 500;
            text-decoration: none;
            transition: background 0.2s, color 0.2s;
        }

        .sidebar a:hover {
            background: #f0f4ff;
            color: #007bff;
        }

        .sidebar a.active {
            background: #007bff;
            color: white;
            font-weight: bold;
            border-radius: 8px;
        }

        .submenu-wrapper {
            display: flex;
            flex-direction: column;
            margin: 0 10px;
        }

        .submenu-toggle {
            background: none;
            border: none;
            text-align: left;
            padding: 12px 20px;
            font-weight: 500;
            color: #333;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-radius: 8px;
            transition: background 0.2s, color 0.2s;
        }

        .submenu-toggle:hover {
            background: #f0f4ff;
            color: #007bff;
        }

        .submenu-toggle.active {
            background: #007bff;
            color: white;
        }

        .submenu {
            display: none;
            flex-direction: column;
            padding-left: 15px;
        }

        .submenu.show {
            display: flex;
        }

        .submenu a {
            padding: 8px 20px;
            font-size: 14px;
            color: #555;
            text-decoration: none;
            transition: background 0.2s, color 0.2s;
            border-radius: 6px;
        }

        .submenu a:hover {
            background: #f0f4ff;
            color: #007bff;
        }

        .submenu a.active {
            background: #007bff;
            color: white;
        }


        /* Main Content Area */
        .main-content {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
        }

        /* Header */
        header {
            background: #ffffff;
            border-bottom: 1px solid #ddd;
            padding: 15px 25px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        header h5 {
            margin: 0;
            font-weight: 600;
        }

        .logout-btn {
            background: #dc3545;
            border: none;
            padding: 6px 14px;
            border-radius: 6px;
            color: white;
            font-weight: 500;
            transition: background 0.2s;
        }

        .logout-btn:hover {
            background: #c82333;
        }

        /* Page Content */
        .content {
            flex-grow: 1;
            padding: 25px;
        }

        /* Footer */
        footer {
            background: #ffffff;
            border-top: 1px solid #ddd;
            text-align: center;
            padding: 10px;
            font-size: 14px;
            color: #666;
        }

        /* Dashboard Cards */
        .dashboard-card {
            display: block;
            text-decoration: none;
            padding: 20px;
            border-radius: 12px;
            color: white;
            font-weight: 600;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .dashboard-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
        }

        .card-blue {
            background: linear-gradient(135deg, #007bff, #0056b3);
        }

        .card-green {
            background: linear-gradient(135deg, #28a745, #1e7e34);
        }

        .card-orange {
            background: linear-gradient(135deg, #fd7e14, #d46b0e);
        }

        .card-purple {
            background: linear-gradient(135deg, #6f42c1, #512da8);
        }

        .table-loader {
            position: absolute;
            top: 50%;
            /* middle of the table container */
            left: 50%;
            transform: translate(-50%, -50%);
            background: rgba(255, 255, 255, 0.6);
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 10;
            border-radius: 8px;
        }

        .spinner {
            border: 8px solid #f3f3f3;
            border-top: 8px solid #007bff;
            border-radius: 50%;
            width: 60px;
            height: 60px;
            animation: spin 0.8s linear infinite;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        .switch {
            position: relative;
            display: inline-block;
            width: 60px;
            height: 34px;
        }

        .switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            -webkit-transition: .4s;
            transition: .4s;
        }

        .slider:before {
            position: absolute;
            content: "";
            height: 26px;
            width: 26px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            -webkit-transition: .4s;
            transition: .4s;
        }

        input:checked+.slider {
            background-color: #2196F3;
        }

        input:focus+.slider {
            box-shadow: 0 0 1px #2196F3;
        }

        input:checked+.slider:before {
            -webkit-transform: translateX(26px);
            -ms-transform: translateX(26px);
            transform: translateX(26px);
        }

        /* Rounded sliders */
        .slider.round {
            border-radius: 34px;
        }

        .slider.round:before {
            border-radius: 50%;
        }
    </style>
</head>

<body>

    @include('layouts.partials.admin-sidebar')

    <!-- Main Content -->
    <div class="main-content">
        <!-- Header -->
        @include('layouts.partials.admin-header')

        <!-- Dynamic Content -->
        <div class="content">
            @yield('content')
        </div>

        @include('layouts.partials.admin-footer')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.querySelectorAll('.submenu-toggle').forEach(button => {
            button.addEventListener('click', () => {
                button.classList.toggle('active');
                const submenu = button.nextElementSibling;
                submenu.classList.toggle('show');
            });
        });
    </script>
    <!-- jQuery + DataTables -->
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <!-- Include Select2 JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
    <!-- jQuery (already loaded if you’re using DataTables) -->
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.js"></script>

    @if(session('success'))
    <script>
        toastr.success('{{ session('success') }}');
    </script>
    @elseif(session('warning'))
    <script>
        toastr.warning('{{ session('warning') }}');
    </script>
    @elseif(session('error'))
    <script>
        toastr.error('{{ session('error') }}');
    </script>
    @endif
    @stack('scripts')
</body>

</html>