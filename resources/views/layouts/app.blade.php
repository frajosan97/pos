<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ ucwords($company_info->name) }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="{{ asset(getImage($company_info->logo,'logo.png')) }}" type="image/x-icon">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/libs/fa/css/all.min.css') }}">
    <link href="{{ asset('assets/libs/dataTables/css/dataTables.bootstrap5.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <link rel="stylesheet" href="{{ asset('assets/libs/adminlte/css/adminlte.css') }}">
    <style>
        :root {
            --primary-color: <?php echo $company_info->color ?? 'rgb(201, 40, 0)'; ?>;
        }
    </style>
    <link rel="stylesheet" href="{{ asset('assets/css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/auth.css') }}">
    @stack('style')
</head>

<body class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed">

    <div id="app">

        @php
        $isAuthRoute = Request::is(['login', 'register', 'password','password/*', 'otp','otp/*', 'verify','verify/*']);
        $isPortal = Request::is(['portal', 'portal/*']);
        @endphp

        @if($isAuthRoute)

        <!-- ================ Authentication routes ================ -->
        @include('layouts.partials.auth')
        <!-- ================ Authentication routes end ================ -->

        @else

        <!-- ================ Main Body ================ -->
        <div cs="wrapper">

            <!-- Preloader -->
            <div class="preloader flex-column justify-content-center align-items-center">
                <img class="animation__shake" src="{{ asset(getImage($company_info->logo,'logo.png')) }}" alt="" height="60" width="60">
            </div>

            <!-- Navbar -->
            <nav class="main-header navbar navbar-expand navbar-white navbar-light">
                <!-- Left navbar links -->
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
                    </li>
                    <li class="nav-item d-none d-sm-inline-block">
                        <a href="/" class="nav-link">
                            Branch: {{ auth()->user()->branch?->name }}
                        </a>
                    </li>
                </ul>
            </nav>
            <!-- /.navbar -->

            <!-- Main Sidebar Container -->
            <aside class="main-sidebar sidebar-dark-primary elevation-4 main-bg">
                <!-- Brand Logo -->
                <a href="/" class="brand-link border-0 shadow-sm">
                    <img src="{{ asset(getImage($company_info->logo,'logo.png')) }}" alt="" class="brand-image rounded-4" style="opacity: .8">
                    <span class="brand-text font-weight-light"> POS</span>
                </a>

                <!-- Sidebar -->
                <div class="sidebar">
                    <!-- Sidebar user panel (optional) -->
                    <div class="user-panel my-3 p-2 rounded-4 d-flex align-items-center border-0 bg-light">
                        <div class="image">
                            <img src="{{ asset('assets/images/defaults/passport.png') }}" class="img-circle elevation-2" alt="User Image">
                        </div>
                        <div class="info">
                            <a href="{{ route('employee.show',auth()->user()->id) }}" class="d-block text-color fw-bolder text-capitalize">
                                <h6 class="m-0 fw-bold">{{ auth()->user()->name }}</h6>
                                @foreach (auth()->user()->roles as $role)
                                <small>{{ $role->name }}</small>
                                @endforeach
                            </a>
                        </div>
                    </div>

                    <!-- Sidebar Menu -->
                    <nav class="mt-2">
                        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu"
                            data-accordion="false">
                            @include('layouts.partials.menu')
                        </ul>
                    </nav>
                    <!-- /.sidebar-menu -->
                </div>
                <!-- /.sidebar -->
            </aside>

            <!-- Content Wrapper. Contains page content -->
            <div class="content-wrapper">
                <!-- Content Header (Page header) -->
                <div class="content-header">
                    <div class="container-fluid">
                        <div class="row mb-2 text-capitalize">
                            <div class="col-sm-6">
                                <h1 class="m-0">@yield('pageTitle')</h1>
                            </div><!-- /.col -->
                            <div class="col-sm-6">
                                <ol class="breadcrumb float-sm-right">
                                    <li class="breadcrumb-item"><a href="#">POS</a></li>
                                    <li class="breadcrumb-item active">@yield('pageTitle')</li>
                                </ol>
                            </div><!-- /.col -->
                        </div><!-- /.row -->
                    </div><!-- /.container-fluid -->
                </div>
                <!-- /.content-header -->

                <!-- Main content -->
                <section class="content">
                    <div class="container-fluid">

                        @yield('content')

                    </div>
                </section>
                <!-- /.content -->
            </div>

            <!-- Main Footer -->
            <footer class="main-footer border-0 text-center text-primary shadow-sm">
                Copyright &copy; {{ ucwords($company_info->name) }} | Software by
                <a href="https://www.frajosantech.co.ke"><u>Frajosan IT Consultancies Ltd</u></a>
            </footer>
        </div>
        <!-- ================ Main Body End ================ -->

        @endif

    </div>

    <!-- Scripts -->
    <script src="{{ asset('assets/js/jquery.js') }}"></script>
    <script src="{{ asset('assets/libs/adminlte/js/adminlte.js') }}"></script>
    <script src="{{ asset('assets/js/bootstrap.js') }}"></script>
    <script src="{{ asset('assets/js/jquery.validate.min.js') }}"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
    <script src="{{ asset('assets/libs/dataTables/js/dataTables.js') }}"></script>
    <script src="{{ asset('assets/libs/dataTables/js/dataTables.bootstrap5.js') }}"></script>
    <script src="{{ asset('assets/js/sweetalert.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="{{ asset('assets/js/app.js') }}"></script>
    @stack('script')

</body>

</html>