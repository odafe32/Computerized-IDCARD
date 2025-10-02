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
                            <span class="badge text-bg-danger rounded-pill" id="notification-count">0</span>
                        </button>
                        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end p-0"
                            aria-labelledby="page-header-notifications-dropdown" style="width: 350px;">
                            <div class="p-3">
                                <div class="row align-items-center">
                                    <div class="col">
                                        <h5 class="m-0">Notifications</h5>
                                    </div>
                                    <div class="col-auto">
                                        <button class="btn btn-sm btn-link text-decoration-none" id="mark-all-read">
                                            Mark all as read
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div data-simplebar style="max-height: 300px;" id="notifications-container">
                                <div class="text-center p-4" id="notifications-loading">
                                    <div class="spinner-border spinner-border-sm text-primary" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                    <p class="text-muted mt-2 mb-0">Loading notifications...</p>
                                </div>
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
            // Set CSRF token for AJAX requests
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

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

            // Notification System
            function loadNotifications() {
                $.ajax({
                    url: '{{ route("student.notifications.get") }}',
                    method: 'GET',
                    success: function(response) {
                        updateNotificationDropdown(response.notifications, response.unread_count);
                    },
                    error: function() {
                        $('#notifications-container').html(`
                            <div class="text-center p-4">
                                <i class="mdi mdi-alert-circle font-size-24 text-danger"></i>
                                <p class="text-muted mt-2 mb-0">Failed to load notifications</p>
                            </div>
                        `);
                    }
                });
            }

            function updateNotificationDropdown(notifications, unreadCount) {
                // Update notification count badge
                const countBadge = $('#notification-count');
                if (unreadCount > 0) {
                    countBadge.text(unreadCount).show();
                } else {
                    countBadge.hide();
                }

                // Update notifications container
                const container = $('#notifications-container');

                if (notifications.length === 0) {
                    container.html(`
                        <div class="text-center p-4">
                            <i class="mdi mdi-bell-outline font-size-24 text-muted"></i>
                            <p class="text-muted mt-2 mb-0">No notifications</p>
                        </div>
                    `);
                    return;
                }

                let notificationsHtml = '';
                notifications.forEach(function(notification) {
                    const readClass = notification.is_read ? 'notification-read' : 'notification-unread';
                    const actionUrl = notification.action_url || '#';

                    notificationsHtml += `
                        <a href="${actionUrl}" class="text-reset notification-item ${readClass}" data-notification-id="${notification.id}">
                            <div class="d-flex p-3 border-bottom">
                                <div class="flex-shrink-0 me-3">
                                    <div class="avatar-xs">
                                        <span class="avatar-title ${notification.badge_class} rounded-circle">
                                            <i class="${notification.icon}"></i>
                                        </span>
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-1 font-size-14">${notification.title}</h6>
                                    <div class="text-muted">
                                        <p class="mb-1 font-size-13">${notification.message}</p>
                                        <small class="text-muted">${notification.created_at}</small>
                                    </div>
                                </div>
                                ${!notification.is_read ? '<div class="flex-shrink-0"><div class="badge bg-primary rounded-pill">New</div></div>' : ''}
                            </div>
                        </a>
                    `;
                });

                container.html(notificationsHtml);
            }

            // Load notifications when dropdown is opened
            $('#page-header-notifications-dropdown').on('click', function() {
                loadNotifications();
            });

            // Mark notification as read when clicked
            $(document).on('click', '.notification-item', function(e) {
                const notificationId = $(this).data('notification-id');
                const isUnread = $(this).hasClass('notification-unread');

                if (isUnread && notificationId) {
                    $.ajax({
                        url: `/student/notifications/${notificationId}/read`,
                        method: 'POST',
                        success: function() {
                            // Update UI to show as read
                            $(`.notification-item[data-notification-id="${notificationId}"]`)
                                .removeClass('notification-unread')
                                .addClass('notification-read')
                                .find('.badge').remove();

                            // Update count
                            const currentCount = parseInt($('#notification-count').text()) || 0;
                            const newCount = Math.max(0, currentCount - 1);
                            if (newCount > 0) {
                                $('#notification-count').text(newCount);
                            } else {
                                $('#notification-count').hide();
                            }
                        }
                    });
                }
            });

            // Mark all notifications as read
            $('#mark-all-read').on('click', function(e) {
                e.preventDefault();

                $.ajax({
                    url: '{{ route("student.notifications.mark-all-read") }}',
                    method: 'POST',
                    success: function() {
                        // Update UI
                        $('.notification-item')
                            .removeClass('notification-unread')
                            .addClass('notification-read')
                            .find('.badge').remove();

                        $('#notification-count').hide();

                        // Show success message
                        toastr.success('All notifications marked as read');
                    },
                    error: function() {
                        toastr.error('Failed to mark notifications as read');
                    }
                });
            });

            // Load notifications on page load
            loadNotifications();

            // Refresh notifications every 30 seconds
            setInterval(loadNotifications, 30000);
        });
    </script>

    <!-- Notification Styles -->
    <style>
        .notification-item {
            transition: background-color 0.2s ease;
        }

        .notification-item:hover {
            background-color: #f8f9fa;
        }

        .notification-unread {
            background-color: #f0f8ff;
            border-left: 3px solid #007bff;
        }

        .notification-read {
            opacity: 0.8;
        }

        .avatar-title {
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
        }
    </style>

    @stack('scripts')
</body>
</html>
