<!DOCTYPE html>
<html lang="en" data-bs-theme="light" data-menu-color="brand" data-topbar-color="light">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <title>{{ $meta_title }}</title>

    <!-- Meta Tags -->
    <meta name="description" content="{{ $meta_desc ?? 'Admin panel for Lexa University Student ID Portal' }}">
    <meta name="author" content="Lexa University" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />

    <!-- Open Graph Meta Tags -->
    <meta property="og:title" content="{{ $meta_title }}" />
    <meta property="og:description" content="{{ $meta_desc ?? 'Admin panel for Lexa University Student ID Portal' }}" />
    <meta property="og:image" content="{{ $meta_image ?? url('logo.png') }}" />
    <meta property="og:type" content="website" />

    <!-- Twitter Meta Tags -->
    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:title" content="{{ $meta_title }}" />
    <meta name="twitter:description" content="{{ $meta_desc ?? 'Admin panel for Lexa University Student ID Portal' }}" />
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
                        <a href="{{ route('admin.dashboard') }}" class="logo logo-dark">
                            <span class="logo-sm">
                                <img src="{{ url('logo-sm.png') }}" alt="Lexa University" height="22">
                            </span>
                            <span class="logo-lg">
                                <img src="{{ url('logo-dark.png') }}" alt="Lexa University" height="17">
                            </span>
                        </a>

                        <a href="{{ route('admin.dashboard') }}" class="logo logo-light">
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
                            <span class="badge text-bg-danger rounded-pill" id="notification-count" style="display: none;">0</span>
                        </button>
                        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end p-0"
                            aria-labelledby="page-header-notifications-dropdown" style="width: 380px;">
                            <div class="p-3">
                                <div class="row align-items-center">
                                    <div class="col">
                                        <h5 class="m-0 font-size-16">Admin Notifications</h5>
                                    </div>
                                    <div class="col-auto">
                                        <button class="btn btn-sm btn-link text-decoration-none p-0" id="mark-all-read">
                                            <small>Mark all as read</small>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div data-simplebar style="max-height: 350px;" id="notifications-container">
                                <div class="text-center p-4" id="notifications-loading">
                                    <div class="spinner-border spinner-border-sm text-primary" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                    <p class="text-muted mt-2 mb-0 font-size-13">Loading notifications...</p>
                                </div>
                            </div>
                            <div class="p-2 border-top">
                                <a class="btn btn-sm btn-link font-size-13 w-100 text-center text-decoration-none" href="{{ route('admin.notifications.index') }}">
                                    <i class="mdi mdi-arrow-right-circle me-1"></i>View all notifications
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
                                 alt="{{ auth()->user()->name ?? 'Admin' }}"
                                 style="width: 32px; height: 32px; object-fit: cover;">
                            <span class="d-none d-xl-inline-block ms-1">{{ auth()->user()->name ?? 'Admin' }}</span>
                            <i class="mdi mdi-chevron-down d-none d-xl-inline-block"></i>
                        </button>
                        <div class="dropdown-menu dropdown-menu-end">
                            <!-- Profile Link -->
                            <a class="dropdown-item" href="{{ route('admin.profile.show') }}">
                                <i class="mdi mdi-account-circle font-size-17 text-muted align-middle me-1"></i>
                                My Profile
                            </a>

                            <!-- Change Password -->
                            <a class="dropdown-item" href="{{ route('admin.password.change') }}">
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
                            <a href="{{ route('admin.dashboard') }}" class="waves-effect">
                                <i class="mdi mdi-view-dashboard"></i>
                                <span>Dashboard</span>
                            </a>
                        </li>

                        <li class="menu-title">ID Card Management</li>

                        <li>
                            <a href="javascript: void(0);" class="has-arrow waves-effect">
                                <i class="mdi mdi-card-account-details"></i>
                                <span>ID Card Requests</span>
                            </a>
                            <ul class="sub-menu" aria-expanded="false">
                                <li><a href="{{ route('admin.id-cards.index') }}">All Requests</a></li>
                                <li><a href="{{ route('admin.id-cards.index') }}?status=pending">Pending Requests</a></li>
                                <li><a href="{{ route('admin.id-cards.index') }}?status=approved">Approved Requests</a></li>
                                <li><a href="{{ route('admin.id-cards.index') }}?status=ready">Ready for Collection</a></li>
                            </ul>
                        </li>

                        <li class="menu-title">User Management</li>

                        <li>
                            <a href="javascript: void(0);" class="has-arrow waves-effect">
                                <i class="mdi mdi-account-multiple"></i>
                                <span>Students</span>
                            </a>
                            <ul class="sub-menu" aria-expanded="false">
                                <li><a href="{{ route('admin.users.index') }}">All Students</a></li>
                                <li><a href="{{ route('admin.users.create') }}">Add New Student</a></li>
                                <li><a href="{{ route('admin.users.index') }}?status=active">Active Students</a></li>
                                <li><a href="{{ route('admin.users.index') }}?status=inactive">Inactive Students</a></li>
                                <li><a href="{{ route('admin.users.index') }}?status=suspended">Suspended Students</a></li>
                            </ul>
                        </li>

                        <li class="menu-title">Communication</li>

                        <li>
                            <a href="{{ route('admin.notifications.index') }}" class="waves-effect">
                                <i class="mdi mdi-bell-ring"></i>
                                <span>Notifications</span>
                                <span class="badge rounded-pill bg-danger float-end" id="sidebar-notification-count" style="display: none;">0</span>
                            </a>
                        </li>

                        <li class="menu-title">Settings</li>

                        <li>
                            <a href="{{ route('admin.profile.show') }}" class="waves-effect">
                                <i class="mdi mdi-account-circle"></i>
                                <span>My Profile</span>
                            </a>
                        </li>

                        <li>
                            <a href="{{ route('admin.password.change') }}" class="waves-effect">
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
                                Admin Portal - Student ID Management
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

                // Open parent menu if it's a submenu item
// Open parent menu if it's a submenu item
                var parentMenu = $(this).closest('.sub-menu');
                if (parentMenu.length) {
                    parentMenu.addClass('mm-show');
                    parentMenu.prev('a').addClass('mm-active');
                    parentMenu.prev('a').attr('aria-expanded', 'true');
                }
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

        // Admin Notification System
        function loadAdminNotifications() {
            const container = $('#notifications-container');
            const loadingDiv = $('#notifications-loading');

            loadingDiv.show();

            fetch('/admin/api/notifications?limit=10', {
                method: 'GET',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    'Content-Type': 'application/json',
                }
            })
            .then(response => response.json())
            .then(data => {
                loadingDiv.hide();

                if (data.success && data.notifications.length > 0) {
                    let notificationsHtml = '';

                    data.notifications.forEach(notification => {
                        const isUnread = !notification.is_read;
                        const actionUrl = notification.action_url || '#';

                        notificationsHtml += `
                            <div class="notification-dropdown-item ${isUnread ? 'notification-unread' : ''}"
                                 data-notification-id="${notification.id}">
                                <div class="d-flex align-items-start p-3 border-bottom">
                                    <div class="avatar-sm me-3 flex-shrink-0">
                                        <span class="avatar-title ${notification.badge_class} rounded-circle">
                                            <i class="${notification.icon} font-size-14"></i>
                                        </span>
                                    </div>
                                    <div class="flex-grow-1 min-width-0">
                                        <h6 class="mb-1 font-size-14 ${isUnread ? 'fw-bold' : ''}">
                                            ${notification.title}
                                            ${isUnread ? '<span class="badge bg-primary ms-1 font-size-10">New</span>' : ''}
                                        </h6>
                                        <p class="mb-1 font-size-13 text-muted">
                                            ${notification.message.length > 80 ? notification.message.substring(0, 80) + '...' : notification.message}
                                        </p>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <small class="text-muted font-size-12">${notification.time_ago}</small>
                                            <div class="btn-group btn-group-sm">
                                                ${actionUrl !== '#' ? `<a href="${actionUrl}" class="btn btn-outline-primary btn-sm font-size-11">View</a>` : ''}
                                                ${isUnread ? `<button class="btn btn-outline-success btn-sm font-size-11 mark-notification-read" data-id="${notification.id}">Mark Read</button>` : ''}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `;
                    });

                    container.html(notificationsHtml);

                    // Update notification count
                    const unreadCount = data.unread_count;
                    updateNotificationCounts(unreadCount);

                } else {
                    container.html(`
                        <div class="text-center p-4">
                            <i class="mdi mdi-bell-outline font-size-24 text-muted"></i>
                            <p class="text-muted mt-2 mb-0 font-size-13">No new notifications</p>
                        </div>
                    `);
                    updateNotificationCounts(0);
                }
            })
            .catch(error => {
                console.error('Error loading notifications:', error);
                loadingDiv.hide();
                container.html(`
                    <div class="text-center p-4">
                        <i class="mdi mdi-alert-circle font-size-24 text-danger"></i>
                        <p class="text-danger mt-2 mb-0 font-size-13">Failed to load notifications</p>
                    </div>
                `);
            });
        }

        function updateNotificationCounts(count) {
            const headerCount = $('#notification-count');
            const sidebarCount = $('#sidebar-notification-count');

            if (count > 0) {
                headerCount.text(count).show();
                sidebarCount.text(count).show();
            } else {
                headerCount.hide();
                sidebarCount.hide();
            }
        }

        // Mark notification as read from dropdown
        $(document).on('click', '.mark-notification-read', function(e) {
            e.preventDefault();
            e.stopPropagation();

            const notificationId = $(this).data('id');
            const button = $(this);
            const notificationItem = button.closest('.notification-dropdown-item');

            button.prop('disabled', true).html('<i class="mdi mdi-loading mdi-spin"></i>');

            fetch(`/admin/notifications/${notificationId}/read`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    'Content-Type': 'application/json',
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    notificationItem.removeClass('notification-unread');
                    const newBadge = notificationItem.find('.badge.bg-primary');
                    if (newBadge.length) newBadge.remove();
                    button.remove();

                    // Update count
                    const currentCount = parseInt($('#notification-count').text()) || 0;
                    const newCount = Math.max(0, currentCount - 1);
                    updateNotificationCounts(newCount);

                    showNotificationToast('Notification marked as read', 'success');
                } else {
                    button.prop('disabled', false).html('Mark Read');
                    showNotificationToast('Failed to mark notification as read', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                button.prop('disabled', false).html('Mark Read');
                showNotificationToast('An error occurred', 'error');
            });
        });

        // Mark all notifications as read
        $('#mark-all-read').on('click', function(e) {
            e.preventDefault();

            const button = $(this);
            const originalText = button.html();

            button.prop('disabled', true).html('<i class="mdi mdi-loading mdi-spin"></i> Processing...');

            fetch('/admin/notifications/mark-all-read', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    'Content-Type': 'application/json',
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    loadAdminNotifications(); // Reload notifications
                    showNotificationToast(`Marked ${data.count || 'all'} notifications as read`, 'success');
                } else {
                    showNotificationToast('Failed to mark all notifications as read', 'error');
                }
                button.prop('disabled', false).html(originalText);
            })
            .catch(error => {
                console.error('Error:', error);
                button.prop('disabled', false).html(originalText);
                showNotificationToast('An error occurred', 'error');
            });
        });

        // Show notification toast
        function showNotificationToast(message, type = 'info') {
            const toastId = 'toast-' + Date.now();
            const toastClass = type === 'success' ? 'bg-success' : type === 'error' ? 'bg-danger' : 'bg-info';
            const iconClass = type === 'success' ? 'mdi-check-circle' : type === 'error' ? 'mdi-alert-circle' : 'mdi-information';

            const toast = $(`
                <div id="${toastId}" class="toast align-items-center text-white ${toastClass} border-0 position-fixed"
                     style="top: 20px; right: 20px; z-index: 9999; min-width: 300px;" role="alert" aria-live="assertive" aria-atomic="true">
                    <div class="d-flex">
                        <div class="toast-body">
                            <i class="mdi ${iconClass} me-2"></i>${message}
                        </div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                    </div>
                </div>
            `);

            $('body').append(toast);

            const bsToast = new bootstrap.Toast(toast[0], {
                autohide: true,
                delay: 5000
            });

            bsToast.show();

            // Remove from DOM after hiding
            toast[0].addEventListener('hidden.bs.toast', function() {
                toast.remove();
            });
        }

        // Load notifications when dropdown is opened
        $('#page-header-notifications-dropdown').on('click', function() {
            loadAdminNotifications();
        });

        // Load notifications on page load
        loadAdminNotifications();

        // Refresh notifications every 30 seconds
        setInterval(loadAdminNotifications, 30000);

        // Confirmation dialogs for destructive actions
        $(document).on('click', '[data-confirm]', function(e) {
            e.preventDefault();
            var message = $(this).data('confirm');
            var href = $(this).attr('href');

            if (confirm(message)) {
                if ($(this).is('a')) {
                    window.location.href = href;
                } else if ($(this).is('form') || $(this).closest('form').length) {
                    $(this).closest('form').submit();
                }
            }
        });

        // Auto-refresh dashboard stats every 5 minutes
        if (window.location.pathname.includes('/admin/dashboard')) {
            setInterval(function() {
                // Refresh dashboard data
                if (typeof refreshDashboardStats === 'function') {
                    refreshDashboardStats();
                }
            }, 300000); // 5 minutes
        }
    });
    </script>

    <!-- Notification Styles -->
    <style>
        .notification-dropdown-item {
            transition: background-color 0.2s ease;
            cursor: pointer;
        }

        .notification-dropdown-item:hover {
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

        /* Admin specific styles */
        .admin-badge {
            background: linear-gradient(45deg, #667eea, #764ba2);
            color: white;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .stats-card {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .stats-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }

        /* Sidebar active states */
        .mm-active > a {
            color: #5664d2 !important;
            background-color: rgba(86, 100, 210, 0.1) !important;
        }

        .sub-menu .mm-active > a {
            color: #5664d2 !important;
            background-color: rgba(86, 100, 210, 0.05) !important;
            border-left: 3px solid #5664d2;
            padding-left: 20px;
        }

        /* Notification dropdown improvements */
        .dropdown-menu-lg {
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            border: 1px solid #e9ecef;
        }

        .min-width-0 {
            min-width: 0;
        }

        .font-size-10 { font-size: 10px !important; }
        .font-size-11 { font-size: 11px !important; }
        .font-size-12 { font-size: 12px !important; }
        .font-size-13 { font-size: 13px !important; }
        .font-size-14 { font-size: 14px !important; }

        /* Loading animation */
        .mdi-loading {
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Toast positioning */
        .toast {
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        }

        /* Responsive notifications */
        @media (max-width: 768px) {
            .dropdown-menu-lg {
                width: 320px !important;
                left: -280px !important;
            }

            .notification-dropdown-item .btn-group {
                flex-direction: column;
                gap: 2px;
            }

            .notification-dropdown-item .btn-group .btn {
                font-size: 10px;
                padding: 2px 6px;
            }
        }
    </style>

    @stack('scripts')
</body>
</html>
