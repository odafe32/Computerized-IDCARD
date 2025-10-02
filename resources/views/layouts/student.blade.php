<!DOCTYPE html>
<html lang="en" data-bs-theme="light" data-menu-color="brand" data-topbar-color="light">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <title>{{ $meta_title }}</title>

    <!-- Meta Tags -->
    <meta name="description" content="{{ $meta_desc ?? 'A Laravel-based web application that automates the process of student ID card requests and issuance.' }}">
    <meta name="author" content="Lexa University" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />

    <!-- Open Graph Meta Tags -->
    <meta property="og:title" content="{{ $meta_title }}" />
    <meta property="og:description" content="{{ $meta_desc ?? 'A Laravel-based web application that automates the process of student ID card requests and issuance.' }}" />
    <meta property="og:image" content="{{ $meta_image ?? url('logo.png') }}" />
    <meta property="og:type" content="website" />

    <!-- Twitter Meta Tags -->
    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:title" content="{{ $meta_title }}" />
    <meta name="twitter:description" content="{{ $meta_desc ?? 'A Laravel-based web application that automates the process of student ID card requests and issuance.' }}" />
    <meta name="twitter:image" content="{{ $meta_image ?? url('logo.png') }}" />

    <meta name="generator" content="Lexa University" />

    <!-- Favicon -->
    <link rel="shortcut icon" href="{{ url('favicon.ico') }}" />

    <!-- CSS Files -->
    <link href="{{ url('assets/css/bootstrap.min.css?v=' . config('app.cache_version', '1.0')) }}" id="bootstrap-style" rel="stylesheet" type="text/css" />
    <link href="{{ url('assets/css/icons.min.css?v=' . config('app.cache_version', '1.0')) }}" rel="stylesheet" type="text/css" />
    <link href="{{ url('assets/css/app.min.css?v=' . config('app.cache_version', '1.0')) }}" id="app-style" rel="stylesheet" type="text/css" />
</head>

<body data-sidebar="dark">
    <div id="layout-wrapper">
        <!-- Header -->
        <header id="page-topbar">
            <div class="navbar-header">
                <div class="d-flex">
                    <!-- LOGO -->
                    <div class="navbar-brand-box">
                        <a href="{{ route('student.dashboard') }}" class="logo logo-dark">
                            <span class="logo-sm">
                                <img src="{{ url('logo-sm.png') }}" alt="Lexa University" height="22">
                            </span>
                            <span class="logo-lg">
                                <img src="{{ url('logo-dark.png') }}" alt="Lexa University" height="17">
                            </span>
                        </a>

                        <a href="{{ route('student.dashboard') }}" class="logo logo-light">
                            <span class="logo-sm">
                                <img src="{{ url('logo-sm.png') }}" alt="Lexa University" height="22">
                            </span>
                            <span class="logo-lg">
                                <img src="{{ url('logo-light.png') }}" alt="Lexa University" height="18">
                            </span>
                        </a>
                    </div>

                    <button type="button" class="btn btn-sm px-3 font-size-24 header-item waves-effect vertical-menu-btn">
                        <i class="mdi mdi-menu"></i>
                    </button>
                </div>

                <div class="d-flex">
                    <!-- Fullscreen Button -->
                    <div class="dropdown d-none d-lg-inline-block">
                        <button type="button" class="btn header-item noti-icon waves-effect" data-toggle="fullscreen">
                            <i class="mdi mdi-fullscreen font-size-24"></i>
                        </button>
                    </div>

                    <!-- Notifications -->
                    <div class="dropdown d-inline-block ms-1">
                        <button type="button" class="btn header-item noti-icon waves-effect" id="page-header-notifications-dropdown"
                            data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="ti-bell"></i>
                            <span class="badge text-bg-danger rounded-pill">0</span>
                        </button>
                        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end p-0"
                            aria-labelledby="page-header-notifications-dropdown">
                            <div class="p-3">
                                <div class="row align-items-center">
                                    <div class="col">
                                        <h5 class="m-0">Notifications</h5>
                                    </div>
                                </div>
                            </div>
                            <div data-simplebar style="max-height: 230px;">
                                <div class="text-center p-4">
                                    <i class="mdi mdi-bell-outline font-size-24 text-muted"></i>
                                    <p class="text-muted mt-2">No new notifications</p>
                                </div>
                            </div>
                            <div class="p-2 border-top">
                                <a class="btn btn-sm btn-link font-size-14 w-100 text-center" href="javascript:void(0)">
                                    View all
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- User Profile Dropdown -->
                    <div class="dropdown d-inline-block">
                        <button type="button" class="btn header-item waves-effect" id="page-header-user-dropdown"
                            data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <img class="rounded-circle header-profile-user"
                                 src="{{ auth()->user()->photo_url ?? url('assets/images/empty.png') }}"
                                 alt="{{ auth()->user()->name ?? 'User' }}"
                                 style="width: 32px; height: 32px; object-fit: cover;">
                            <span class="d-none d-xl-inline-block ms-1">{{ auth()->user()->name ?? 'Student' }}</span>
                            <i class="mdi mdi-chevron-down d-none d-xl-inline-block"></i>
                        </button>
                        <div class="dropdown-menu dropdown-menu-end">
                            <!-- Profile Link -->
                            <a class="dropdown-item" href="{{ route('student.profile') }}">
                                <i class="mdi mdi-account-circle font-size-17 text-muted align-middle me-1"></i>
                                My Profile
                            </a>

                            <!-- Change Password -->
                            <a class="dropdown-item" href="{{ route('student.password.change') }}">
                                <i class="mdi mdi-lock-reset font-size-17 text-muted align-middle me-1"></i>
                                Change Password
                            </a>

                            <div class="dropdown-divider"></div>

                            <!-- Logout -->
                            <form method="POST" action="{{ route('logout') }}" class="d-inline">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger">
                                    <i class="mdi mdi-power font-size-17 align-middle me-1 text-danger"></i>
                                    Logout
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Left Sidebar -->
        <div class="vertical-menu">
            <div data-simplebar class="h-100">
                <!-- Sidemenu -->
                <div id="sidebar-menu">
                    <!-- Left Menu Start -->
                    <ul class="metismenu list-unstyled" id="side-menu">
                        <li class="menu-title">Main</li>

                        <li>
                            <a href="{{ route('student.dashboard') }}" class="waves-effect">
                                <i class="mdi mdi-view-dashboard"></i>
                                <span>Dashboard</span>
                            </a>
                        </li>

                        <li>
                            <a href="{{ route('student.profile') }}" class="waves-effect">
                                <i class="mdi mdi-account-box"></i>
                                <span>My Profile</span>
                            </a>
                        </li>

                        <li class="menu-title">ID Card</li>

                        <li>
                            <a href="{{ route('student.id-card.show') }}" class="waves-effect">
                                <i class="mdi mdi-card-account-details"></i>
                                <span>My ID Card</span>
                            </a>
                        </li>

                        <li>
                            <a href="{{ route('student.id-card.request') }}" class="waves-effect">
                                <i class="mdi mdi-clipboard-outline"></i>
                                <span>Request ID Card</span>
                            </a>
                        </li>

                        <li>
                            <a href="{{ route('student.id-card.status') }}" class="waves-effect">
                                <i class="mdi mdi-buffer"></i>
                                <span>Request Status</span>
                            </a>
                        </li>

                        <li class="menu-title">Settings</li>

                        <li>
                            <a href="{{ route('student.password.change') }}" class="waves-effect">
                                <i class="mdi mdi-lock-reset"></i>
                                <span>Change Password</span>
                            </a>
                        </li>
                    </ul>
                </div>
                <!-- Sidebar -->
            </div>
        </div>
        <!-- Left Sidebar End -->

        <!-- Main Content -->
        <div class="main-content">
            <div class="page-content">
                @yield('content')
            </div>
            <!-- End Page-content -->

            <!-- Footer -->
            <footer class="footer">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-sm-6">
                            <script>document.write(new Date().getFullYear())</script> © Lexa University.
                        </div>
                        <div class="col-sm-6">
                            <div class="text-sm-end d-none d-sm-block">
                                Student ID Portal
                            </div>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
        <!-- end main content-->
    </div>
    <!-- END layout-wrapper -->

    <!-- JAVASCRIPT -->
    <script src="{{ url('assets/libs/jquery/jquery.min.js?v=' . config('app.cache_version', '1.0')) }}"></script>
    <script src="{{ url('assets/libs/bootstrap/js/bootstrap.bundle.min.js?v=' . config('app.cache_version', '1.0')) }}"></script>
    <script src="{{ url('assets/libs/metismenu/metisMenu.min.js?v=' . config('app.cache_version', '1.0')) }}"></script>
    <script src="{{ url('assets/libs/simplebar/simplebar.min.js?v=' . config('app.cache_version', '1.0')) }}"></script>
    <script src="{{ url('assets/libs/node-waves/waves.min.js?v=' . config('app.cache_version', '1.0')) }}"></script>
    <script src="{{ url('assets/libs/jquery-sparkline/jquery.sparkline.min.js?v=' . config('app.cache_version', '1.0')) }}"></script>

    <!-- App js -->
    <script src="{{ url('assets/js/app.js?v=' . config('app.cache_version', '1.0')) }}"></script>

    <!-- Custom JavaScript -->
    <script>
        $(document).ready(function() {
            // Set active menu item based on current route
            var currentUrl = window.location.href;
            $('#side-menu a').each(function() {
                if (this.href === currentUrl) {
                    $(this).addClass('active');
                    $(this).closest('li').addClass('mm-active');
                }
            });

            // Auto-dismiss alerts after 5 seconds
            $('.alert').each(function() {
                var alert = this;
                setTimeout(function() {
                    $(alert).fadeOut('slow');
                }, 5000);
            });

            // Initialize tooltips
            $('[data-bs-toggle="tooltip"]').tooltip();

            // Fullscreen toggle
            $('[data-toggle="fullscreen"]').on('click', function() {
                if (!document.fullscreenElement) {
                    document.documentElement.requestFullscreen();
                } else {
                    if (document.exitFullscreen) {
                        document.exitFullscreen();
                    }
                }
            });
        });
    </script>

    @stack('scripts')
</body>
</html>
