@extends('layouts.student')

@section('content')
<div class="container-fluid">
    <!-- Page Title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">Dashboard</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item active">Dashboard</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Welcome Section -->
    <div class="row">
        <div class="col-xl-12">
            <div class="card overflow-hidden">
                <div class="bg-primary-subtle">
                    <div class="row">
                        <div class="col-7">
                            <div class="text-primary p-3">
                                <h5 class="text-primary">Welcome back, {{ $user->name }}!</h5>
                                <p class="mb-0">Here's what's happening with your student account today.</p>
                            </div>
                        </div>
                        <div class="col-5 align-self-end">
                            <img src="{{ asset('assets/images/profile-img.png') }}" alt="" class="img-fluid">
                        </div>
                    </div>
                </div>
                <div class="card-body pt-0">
                    <div class="row">
                        <div class="col-sm-4">
                            <div class="avatar-md profile-user-wid mb-4">
                                <img src="{{ $user->photo_url }}" alt="Profile Photo" class="img-thumbnail rounded-circle">
                            </div>
                            <h5 class="font-size-15 text-truncate">{{ $user->name }}</h5>
                            <p class="text-muted mb-0 text-truncate">{{ $user->matric_no }}</p>
                        </div>

                        <div class="col-sm-8">
                            <div class="pt-4">
                                <div class="row">
                                    <div class="col-6">
                                        <h5 class="font-size-15">{{ $user->department }}</h5>
                                        <p class="text-muted mb-0">Department</p>
                                    </div>
                                    <div class="col-6">
                                        <h5 class="font-size-15">
                                            <span class="badge bg-{{ $user->status === 'active' ? 'success' : 'warning' }}">
                                                {{ ucfirst($user->status) }}
                                            </span>
                                        </h5>
                                        <p class="text-muted mb-0">Account Status</p>
                                    </div>
                                </div>
                                <div class="mt-4">
                                    <a href="{{ route('student.profile') }}" class="btn btn-primary waves-effect waves-light btn-sm">
                                        View Profile <i class="mdi mdi-arrow-right ms-1"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row">
        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex">
                        <div class="flex-grow-1">
                            <p class="text-truncate font-size-14 mb-2">ID Card Requests</p>
                            <h4 class="mb-2" id="total-requests">0</h4>
                            <p class="text-muted mb-0">
                                <span class="text-success fw-bold font-size-12 me-2" id="pending-requests">
                                    <i class="ri-arrow-right-up-line me-1 align-middle"></i>0 Pending
                                </span>
                            </p>
                        </div>
                        <div class="avatar-sm">
                            <span class="avatar-title bg-light text-primary rounded-3">
                                <i class="mdi mdi-card-account-details font-size-24"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex">
                        <div class="flex-grow-1">
                            <p class="text-truncate font-size-14 mb-2">Profile Completion</p>
                            <h4 class="mb-2" id="profile-completion">0%</h4>
                            <p class="text-muted mb-0">
                                <span class="text-info fw-bold font-size-12 me-2">
                                    <i class="ri-arrow-right-up-line me-1 align-middle"></i>Complete your profile
                                </span>
                            </p>
                        </div>
                        <div class="avatar-sm">
                            <span class="avatar-title bg-light text-success rounded-3">
                                <i class="mdi mdi-account-check font-size-24"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex">
                        <div class="flex-grow-1">
                            <p class="text-truncate font-size-14 mb-2">Notifications</p>
                            <h4 class="mb-2" id="unread-notifications">0</h4>
                            <p class="text-muted mb-0">
                                <span class="text-warning fw-bold font-size-12 me-2">
                                    <i class="ri-arrow-right-up-line me-1 align-middle"></i>Unread messages
                                </span>
                            </p>
                        </div>
                        <div class="avatar-sm">
                            <span class="avatar-title bg-light text-warning rounded-3">
                                <i class="mdi mdi-bell font-size-24"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex">
                        <div class="flex-grow-1">
                            <p class="text-truncate font-size-14 mb-2">Last Login</p>
                            <h4 class="mb-2 font-size-16">{{ $user->last_login_at ? $user->last_login_at->format('M d') : 'Never' }}</h4>
                            <p class="text-muted mb-0">
                                <span class="text-muted fw-bold font-size-12 me-2">
                                    <i class="ri-time-line me-1 align-middle"></i>
                                    {{ $user->last_login_at ? $user->last_login_at->format('h:i A') : 'N/A' }}
                                </span>
                            </p>
                        </div>
                        <div class="avatar-sm">
                            <span class="avatar-title bg-light text-info rounded-3">
                                <i class="mdi mdi-clock-outline font-size-24"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Row -->
    <div class="row">
        <!-- Quick Actions -->
        <div class="col-xl-4">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-4">Quick Actions</h4>
                    <div class="d-grid gap-2">
                        <a href="{{ route('student.id-card.request') }}" class="btn btn-primary waves-effect waves-light">
                            <i class="mdi mdi-plus me-2"></i>Request New ID Card
                        </a>
                        <a href="{{ route('student.id-card.status') }}" class="btn btn-outline-info waves-effect">
                            <i class="mdi mdi-eye me-2"></i>Check Request Status
                        </a>
                        <a href="{{ route('student.profile.edit') }}" class="btn btn-outline-secondary waves-effect">
                            <i class="mdi mdi-account-edit me-2"></i>Edit Profile
                        </a>
                        <a href="{{ route('student.id-card.show') }}" class="btn btn-outline-success waves-effect">
                            <i class="mdi mdi-download me-2"></i>Download ID Card
                        </a>
                    </div>
                </div>
            </div>

            <!-- Account Information -->
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-4">Account Information</h4>
                    <div class="table-responsive">
                        <table class="table table-nowrap mb-0">
                            <tbody>
                                <tr>
                                    <th scope="row">Email :</th>
                                    <td>{{ $user->email }}</td>
                                </tr>
                                <tr>
                                    <th scope="row">Phone :</th>
                                    <td>{{ $user->phone ?: 'Not provided' }}</td>
                                </tr>
                                <tr>
                                    <th scope="row">Department :</th>
                                    <td>{{ $user->department }}</td>
                                </tr>
                                <tr>
                                    <th scope="row">Member Since :</th>
                                    <td>{{ $user->created_at->format('M d, Y') }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activity & ID Card Status -->
        <div class="col-xl-8">
            <!-- Recent ID Card Requests -->


            <!-- Recent Notifications -->
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h4 class="card-title mb-0">Recent Notifications</h4>
                        <a href="{{ route('student.notifications.all') }}" class="btn btn-sm btn-outline-primary">
                            View All <i class="mdi mdi-arrow-right ms-1"></i>
                        </a>
                    </div>

                    <div id="recent-notifications">
                        <div class="text-center py-4">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="text-muted mt-2">Loading notifications...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tips & Help Section -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-4">
                        <i class="mdi mdi-lightbulb-outline me-2"></i>Tips & Help
                    </h4>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="d-flex">
                                <div class="flex-shrink-0 me-3">
                                    <div class="avatar-sm">
                                        <div class="avatar-title bg-primary-subtle text-primary rounded-circle">
                                            <i class="mdi mdi-camera"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <h5 class="font-size-14 mb-1">Photo Requirements</h5>
                                    <p class="text-muted mb-0">Use a clear passport-size photo with white background for your ID card request.</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="d-flex">
                                <div class="flex-shrink-0 me-3">
                                    <div class="avatar-sm">
                                        <div class="avatar-title bg-success-subtle text-success rounded-circle">
                                            <i class="mdi mdi-clock-fast"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <h5 class="font-size-14 mb-1">Processing Time</h5>
                                    <p class="text-muted mb-0">ID card requests are typically processed within 3-5 business days.</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="d-flex">
                                <div class="flex-shrink-0 me-3">
                                    <div class="avatar-sm">
                                        <div class="avatar-title bg-info-subtle text-info rounded-circle">
                                            <i class="mdi mdi-bell-ring"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <h5 class="font-size-14 mb-1">Stay Updated</h5>
                                    <p class="text-muted mb-0">You'll receive notifications when your request status changes.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Load dashboard data
    loadDashboardStats();
    loadRecentRequests();
    loadRecentNotifications();

    function loadDashboardStats() {
        // Calculate profile completion
        const user = @json($user);
        let completionScore = 0;
        const fields = ['name', 'email', 'phone', 'department', 'photo'];

        fields.forEach(field => {
            if (user[field] && user[field].trim() !== '') {
                completionScore += 20;
            }
        });

        document.getElementById('profile-completion').textContent = completionScore + '%';

        // Load other stats via AJAX
        fetch('/api/dashboard-stats')
            .then(response => response.json())
            .then(data => {
                document.getElementById('total-requests').textContent = data.total_requests || 0;
                document.getElementById('pending-requests').innerHTML =
                    `<i class="ri-arrow-right-up-line me-1 align-middle"></i>${data.pending_requests || 0} Pending`;
                document.getElementById('unread-notifications').textContent = data.unread_notifications || 0;
            })
            .catch(error => {
                console.error('Error loading dashboard stats:', error);
            });
    }

    function loadRecentRequests() {
        fetch('/api/recent-requests')
            .then(response => response.json())
            .then(data => {
                const container = document.getElementById('recent-requests');

                if (data.requests && data.requests.length > 0) {
                    let html = '';
                    data.requests.forEach(request => {
                        const statusBadge = getStatusBadge(request.status);
                        html += `
                            <div class="d-flex align-items-center border-bottom pb-3 mb-3">
                                <div class="flex-shrink-0 me-3">
                                    <div class="avatar-sm">
                                        <div class="avatar-title ${statusBadge.class} rounded-circle">
                                            <i class="${statusBadge.icon}"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <h5 class="font-size-14 mb-1">Request #${request.request_number}</h5>
                                    <p class="text-muted mb-0">${request.reason} - ${request.created_at}</p>
                                </div>
                                <div class="flex-shrink-0">
                                    <span class="badge ${statusBadge.badge}">${request.status}</span>
                                </div>
                            </div>
                        `;
                    });
                    container.innerHTML = html;
                } else {
                    container.innerHTML = `
                        <div class="text-center py-4">
                            <i class="mdi mdi-clipboard-outline font-size-48 text-muted"></i>
                            <h5 class="mt-3">No Requests Yet</h5>
                            <p class="text-muted">You haven't submitted any ID card requests.</p>
                            <a href="{{ route('student.id-card.request') }}" class="btn btn-primary btn-sm">
                                <i class="mdi mdi-plus me-1"></i>Submit Request
                            </a>
                        </div>
                    `;
                }
            })
            .catch(error => {
                console.error('Error loading recent requests:', error);
                document.getElementById('recent-requests').innerHTML = `
                    <div class="text-center py-4">
                        <i class="mdi mdi-alert-circle font-size-24 text-danger"></i>
                        <p class="text-muted mt-2">Failed to load recent requests</p>
                    </div>
                `;
            });
    }

    function loadRecentNotifications() {
        fetch('{{ route("student.notifications.get") }}')
            .then(response => response.json())
            .then(data => {
                const container = document.getElementById('recent-notifications');

                if (data.notifications && data.notifications.length > 0) {
                    let html = '';
                    data.notifications.slice(0, 3).forEach(notification => {
                        html += `
                            <div class="d-flex align-items-start border-bottom pb-3 mb-3">
                                <div class="flex-shrink-0 me-3">
                                    <div class="avatar-sm">
                                        <div class="avatar-title ${notification.badge_class} rounded-circle">
                                            <i class="${notification.icon}"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <h5 class="font-size-14 mb-1">${notification.title}</h5>
                                    <p class="text-muted mb-1 font-size-13">${notification.message}</p>
                                    <small class="text-muted">${notification.created_at}</small>
                                </div>
                                ${!notification.is_read ? '<div class="flex-shrink-0"><span class="badge bg-primary rounded-pill">New</span></div>' : ''}
                            </div>
                        `;
                    });
                    container.innerHTML = html;
                } else {
                    container.innerHTML = `
                        <div class="text-center py-4">
                            <i class="mdi mdi-bell-outline font-size-48 text-muted"></i>
                            <h5 class="mt-3">No Notifications</h5>
                            <p class="text-muted">You're all caught up!</p>
                        </div>
                    `;
                }
            })
            .catch(error => {
                console.error('Error loading notifications:', error);
                document.getElementById('recent-notifications').innerHTML = `
                    <div class="text-center py-4">
                        <i class="mdi mdi-alert-circle font-size-24 text-danger"></i>
                        <p class="text-muted mt-2">Failed to load notifications</p>
                    </div>
                `;
            });
    }

    function getStatusBadge(status) {
        const badges = {
            'pending': { class: 'bg-warning-subtle text-warning', icon: 'mdi-clock-outline', badge: 'bg-warning' },
            'approved': { class: 'bg-info-subtle text-info', icon: 'mdi-check-circle', badge: 'bg-info' },
            'rejected': { class: 'bg-danger-subtle text-danger', icon: 'mdi-close-circle', badge: 'bg-danger' },
            'printed': { class: 'bg-primary-subtle text-primary', icon: 'mdi-printer', badge: 'bg-primary' },
            'ready': { class: 'bg-success-subtle text-success', icon: 'mdi-check-all', badge: 'bg-success' },
            'collected': { class: 'bg-secondary-subtle text-secondary', icon: 'mdi-check-bold', badge: 'bg-secondary' }
        };
        return badges[status] || badges['pending'];
    }

    // Refresh data every 5 minutes
    setInterval(() => {
        loadDashboardStats();
        loadRecentRequests();
        loadRecentNotifications();
    }, 300000);
});
</script>

<style>
.card {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    border: 1px solid rgba(0, 0, 0, 0.125);
    transition: all 0.3s ease;
}

.card:hover {
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    transform: translateY(-2px);
}

.avatar-title {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 100%;
    height: 100%;
}

.timeline-item {
    border-left: 2px solid #e9ecef;
    padding-left: 1rem;
    margin-bottom: 1rem;
    position: relative;
}

.timeline-item::before {
    content: '';
    position: absolute;
    left: -6px;
    top: 0.5rem;
    width: 10px;
    height: 10px;
    border-radius: 50%;
    background: #6c757d;
}

.timeline-item.active::before {
    background: #007bff;
}

.timeline-item.completed::before {
    background: #28a745;
}

@media (max-width: 768px) {
    .card:hover {
        transform: none;
    }
}
</style>
@endsection
