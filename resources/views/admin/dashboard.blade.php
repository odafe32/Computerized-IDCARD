@extends('layouts.admin')

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

    <!-- Welcome Message -->
    <div class="row">
        <div class="col-12">
            <div class="alert alert-info border-0 rounded-3 mb-4" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                <div class="d-flex align-items-center">
                    <div class="avatar-sm me-3">
                        <span class="avatar-title bg-white bg-opacity-25 rounded-circle">
                            <i class="mdi mdi-account-tie font-size-20 text-white"></i>
                        </span>
                    </div>
                    <div>
                        <h5 class="alert-heading mb-1 text-white">Welcome back, {{ auth()->user()->name }}!</h5>
                        <p class="mb-0 text-white-50">Here's what's happening with your student ID management system today.</p>
                    </div>
                    <div class="ms-auto">
                        <small class="text-white-50">{{ now()->format('l, F j, Y') }}</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row">
        <div class="col-xl-3 col-md-6">
            <div class="card stats-card">
                <div class="card-body">
                    <div class="d-flex">
                        <div class="flex-grow-1">
                            <p class="text-truncate font-size-14 mb-2">Total Students</p>
                            <h4 class="mb-2" id="total-students">{{ \App\Models\User::where('role', 'student')->count() }}</h4>
                            <p class="text-muted mb-0">
                                <span class="text-success me-2">
                                    <i class="mdi mdi-arrow-up"></i>
                                    {{ \App\Models\User::where('role', 'student')->where('created_at', '>=', now()->subDays(30))->count() }}
                                </span>
                                <small>this month</small>
                            </p>
                        </div>
                        <div class="avatar-sm">
                            <span class="avatar-title bg-primary text-white rounded-3">
                                <i class="mdi mdi-account-group font-size-24"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card stats-card">
                <div class="card-body">
                    <div class="d-flex">
                        <div class="flex-grow-1">
                            <p class="text-truncate font-size-14 mb-2">Pending Requests</p>
                            <h4 class="mb-2 text-warning" id="pending-requests">{{ \App\Models\IdCardRequest::where('status', 'pending')->count() }}</h4>
                            <p class="text-muted mb-0">
                                <span class="text-info me-2">
                                    <i class="mdi mdi-clock-outline"></i>
                                    {{ \App\Models\IdCardRequest::where('status', 'pending')->where('created_at', '>=', now()->subDays(7))->count() }}
                                </span>
                                <small>this week</small>
                            </p>
                        </div>
                        <div class="avatar-sm">
                            <span class="avatar-title bg-warning text-white rounded-3">
                                <i class="mdi mdi-clock-alert font-size-24"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card stats-card">
                <div class="card-body">
                    <div class="d-flex">
                        <div class="flex-grow-1">
                            <p class="text-truncate font-size-14 mb-2">Cards Generated</p>
                            <h4 class="mb-2 text-success" id="cards-generated">{{ \App\Models\IdCardRequest::whereNotNull('generated_card_path')->count() }}</h4>
                            <p class="text-muted mb-0">
                                <span class="text-success me-2">
                                    <i class="mdi mdi-trending-up"></i>
                                    {{ \App\Models\IdCardRequest::whereNotNull('generated_card_path')->where('printed_at', '>=', now()->subDays(7))->count() }}
                                </span>
                                <small>this week</small>
                            </p>
                        </div>
                        <div class="avatar-sm">
                            <span class="avatar-title bg-success text-white rounded-3">
                                <i class="mdi mdi-card-account-details font-size-24"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card stats-card">
                <div class="card-body">
                    <div class="d-flex">
                        <div class="flex-grow-1">
                            <p class="text-truncate font-size-14 mb-2">Ready for Collection</p>
                            <h4 class="mb-2 text-info" id="ready-cards">{{ \App\Models\IdCardRequest::where('status', 'ready')->count() }}</h4>
                            <p class="text-muted mb-0">
                                <span class="text-primary me-2">
                                    <i class="mdi mdi-download"></i>
                                    {{ \App\Models\IdCardRequest::where('status', 'collected')->where('collected_at', '>=', now()->subDays(7))->count() }}
                                </span>
                                <small>collected this week</small>
                            </p>
                        </div>
                        <div class="avatar-sm">
                            <span class="avatar-title bg-info text-white rounded-3">
                                <i class="mdi mdi-download-circle font-size-24"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions & Recent Activity -->
    <div class="row">
        <!-- Quick Actions -->
        <div class="col-xl-4">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">
                        <i class="mdi mdi-lightning-bolt text-warning me-2"></i>Quick Actions
                    </h4>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.id-cards.index') }}?status=pending" class="btn btn-outline-warning">
                            <i class="mdi mdi-clock-alert me-2"></i>Review Pending Requests
                            <span class="badge bg-warning ms-2" id="pending-badge">{{ \App\Models\IdCardRequest::where('status', 'pending')->count() }}</span>
                        </a>

                        <a href="{{ route('admin.users.create') }}" class="btn btn-outline-primary">
                            <i class="mdi mdi-account-plus me-2"></i>Add New Student
                        </a>

                        <a href="{{ route('admin.id-cards.index') }}?status=ready" class="btn btn-outline-success">
                            <i class="mdi mdi-download me-2"></i>Ready for Collection
                            <span class="badge bg-success ms-2">{{ \App\Models\IdCardRequest::where('status', 'ready')->count() }}</span>
                        </a>

                        <a href="{{ route('admin.users.index') }}" class="btn btn-outline-info">
                            <i class="mdi mdi-account-group me-2"></i>Manage Students
                        </a>

                        <a href="{{ route('admin.notifications.index') }}" class="btn btn-outline-secondary">
                            <i class="mdi mdi-bell me-2"></i>View Notifications
                            @php $unreadCount = \App\Models\Notification::forUser(auth()->id())->unread()->count(); @endphp
                            @if($unreadCount > 0)
                                <span class="badge bg-danger ms-2">{{ $unreadCount }}</span>
                            @endif
                        </a>
                    </div>
                </div>
            </div>

            <!-- System Status -->
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">
                        <i class="mdi mdi-server text-success me-2"></i>System Status
                    </h4>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="avatar-xs me-3">
                            <span class="avatar-title bg-success rounded-circle">
                                <i class="mdi mdi-check font-size-12"></i>
                            </span>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="mb-1">Database Connection</h6>
                            <p class="text-muted mb-0 font-size-13">Online</p>
                        </div>
                        <div class="text-success">
                            <i class="mdi mdi-circle font-size-10"></i>
                        </div>
                    </div>

                    <div class="d-flex align-items-center mb-3">
                        <div class="avatar-xs me-3">
                            <span class="avatar-title bg-success rounded-circle">
                                <i class="mdi mdi-check font-size-12"></i>
                            </span>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="mb-1">File Storage</h6>
                            <p class="text-muted mb-0 font-size-13">Available</p>
                        </div>
                        <div class="text-success">
                            <i class="mdi mdi-circle font-size-10"></i>
                        </div>
                    </div>

                    <div class="d-flex align-items-center mb-3">
                        <div class="avatar-xs me-3">
                            <span class="avatar-title bg-success rounded-circle">
                                <i class="mdi mdi-check font-size-12"></i>
                            </span>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="mb-1">QR Code Generator</h6>
                            <p class="text-muted mb-0 font-size-13">Operational</p>
                        </div>
                        <div class="text-success">
                            <i class="mdi mdi-circle font-size-10"></i>
                        </div>
                    </div>

                    <div class="d-flex align-items-center">
                        <div class="avatar-xs me-3">
                            <span class="avatar-title bg-success rounded-circle">
                                <i class="mdi mdi-check font-size-12"></i>
                            </span>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="mb-1">PDF Generation</h6>
                            <p class="text-muted mb-0 font-size-13">Working</p>
                        </div>
                        <div class="text-success">
                            <i class="mdi mdi-circle font-size-10"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="col-xl-8">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex align-items-center justify-content-between">
                        <h4 class="card-title mb-0">
                            <i class="mdi mdi-history text-primary me-2"></i>Recent Activity
                        </h4>
                        <div class="dropdown">
                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                <i class="mdi mdi-filter-variant"></i> Filter
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="#" onclick="filterActivity('all')">All Activities</a></li>
                                <li><a class="dropdown-item" href="#" onclick="filterActivity('requests')">ID Card Requests</a></li>
                                <li><a class="dropdown-item" href="#" onclick="filterActivity('approvals')">Approvals</a></li>
                                <li><a class="dropdown-item" href="#" onclick="filterActivity('generations')">Card Generations</a></li>
                                <li><a class="dropdown-item" href="#" onclick="filterActivity('downloads')">Downloads</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="timeline" id="activity-timeline">
                        @php
                            $recentRequests = \App\Models\IdCardRequest::with('user')
                                ->orderBy('updated_at', 'desc')
                                ->limit(10)
                                ->get();
                        @endphp

                        @foreach($recentRequests as $request)
                            <div class="timeline-item" data-activity="requests">
                                <div class="timeline-marker">
                                    <div class="timeline-marker-icon bg-{{ $request->status === 'pending' ? 'warning' : ($request->status === 'approved' ? 'success' : 'info') }}">
<i class="mdi mdi-{{ $request->status_icon }} font-size-12"></i>
                                    </div>
                                </div>
                                <div class="timeline-content">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <h6 class="mb-1">{{ $request->user->name }} - {{ $request->reason_label }}</h6>
                                            <p class="text-muted mb-1 font-size-13">
                                                Request #{{ $request->request_number }}
                                                <span class="badge bg-{{ $request->status_badge }} ms-2">{{ ucfirst($request->status) }}</span>
                                            </p>
                                            <small class="text-muted">{{ $request->updated_at->diffForHumans() }}</small>
                                        </div>
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                <i class="mdi mdi-dots-horizontal"></i>
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li><a class="dropdown-item" href="{{ route('admin.id-cards.show', $request->id) }}">
                                                    <i class="mdi mdi-eye me-2"></i>View Details
                                                </a></li>
                                                @if($request->canBeApproved())
                                                    <li><a class="dropdown-item text-success" href="#" onclick="quickApprove('{{ $request->id }}')">
                                                        <i class="mdi mdi-check me-2"></i>Quick Approve
                                                    </a></li>
                                                @endif
                                                @if($request->generated_card_path)
                                                    <li><a class="dropdown-item" href="{{ route('admin.id-cards.download', $request->id) }}">
                                                        <i class="mdi mdi-download me-2"></i>Download Card
                                                    </a></li>
                                                @endif
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach

                        @if($recentRequests->isEmpty())
                            <div class="text-center py-4">
                                <div class="avatar-lg mx-auto mb-3">
                                    <div class="avatar-title bg-light text-muted rounded-circle">
                                        <i class="mdi mdi-history font-size-24"></i>
                                    </div>
                                </div>
                                <h5 class="text-muted">No recent activity</h5>
                                <p class="text-muted mb-0">Activity will appear here as students submit requests.</p>
                            </div>
                        @endif
                    </div>

                    @if($recentRequests->count() >= 10)
                        <div class="text-center mt-3">
                            <a href="{{ route('admin.id-cards.index') }}" class="btn btn-outline-primary">
                                <i class="mdi mdi-arrow-right me-2"></i>View All Activities
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row">
        <!-- Request Status Chart -->
        <div class="col-xl-6">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">
                        <i class="mdi mdi-chart-donut text-info me-2"></i>Request Status Distribution
                    </h4>
                </div>
                <div class="card-body">
                    <div id="status-chart" style="height: 300px;"></div>
                </div>
            </div>
        </div>

        <!-- Monthly Trends Chart -->
        <div class="col-xl-6">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">
                        <i class="mdi mdi-chart-line text-success me-2"></i>Monthly Request Trends
                    </h4>
                </div>
                <div class="card-body">
                    <div id="trends-chart" style="height: 300px;"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Department Statistics -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">
                        <i class="mdi mdi-school text-primary me-2"></i>Department Statistics
                    </h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Department</th>
                                    <th>Total Students</th>
                                    <th>Pending Requests</th>
                                    <th>Completed Cards</th>
                                    <th>Completion Rate</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $departments = \App\Models\User::where('role', 'student')
                                        ->select('department')
                                        ->distinct()
                                        ->pluck('department')
                                        ->filter();
                                @endphp

                                @foreach($departments as $department)
                                    @php
                                        $totalStudents = \App\Models\User::where('role', 'student')
                                            ->where('department', $department)
                                            ->count();

                                        $pendingRequests = \App\Models\IdCardRequest::whereHas('user', function($q) use ($department) {
                                            $q->where('department', $department);
                                        })->where('status', 'pending')->count();

                                        $completedCards = \App\Models\IdCardRequest::whereHas('user', function($q) use ($department) {
                                            $q->where('department', $department);
                                        })->whereIn('status', ['ready', 'collected'])->count();

                                        $totalRequests = \App\Models\IdCardRequest::whereHas('user', function($q) use ($department) {
                                            $q->where('department', $department);
                                        })->count();

                                        $completionRate = $totalRequests > 0 ? round(($completedCards / $totalRequests) * 100, 1) : 0;
                                    @endphp
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-xs me-3">
                                                    <span class="avatar-title bg-primary rounded-circle">
                                                        {{ substr($department, 0, 2) }}
                                                    </span>
                                                </div>
                                                <div>
                                                    <h6 class="mb-0">{{ $department }}</h6>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-info">{{ $totalStudents }}</span>
                                        </td>
                                        <td>
                                            @if($pendingRequests > 0)
                                                <span class="badge bg-warning">{{ $pendingRequests }}</span>
                                            @else
                                                <span class="text-muted">0</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-success">{{ $completedCards }}</span>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="progress flex-grow-1 me-2" style="height: 6px;">
                                                    <div class="progress-bar bg-{{ $completionRate >= 80 ? 'success' : ($completionRate >= 50 ? 'warning' : 'danger') }}"
                                                         style="width: {{ $completionRate }}%"></div>
                                                </div>
                                                <small class="text-muted">{{ $completionRate }}%</small>
                                            </div>
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.users.index') }}?department={{ urlencode($department) }}"
                                               class="btn btn-sm btn-outline-primary">
                                                <i class="mdi mdi-eye"></i> View
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript for Dashboard -->
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize charts
    initializeStatusChart();
    initializeTrendsChart();

    // Auto-refresh dashboard every 5 minutes
    setInterval(refreshDashboardStats, 300000);
});

// Status Distribution Chart
function initializeStatusChart() {
 const statusData = @json($statusData);
    console.log(statusData);


    const options = {
        series: Object.values(statusData),
        chart: {
            type: 'donut',
            height: 300
        },
        labels: ['Pending', 'Approved', 'Printed', 'Ready', 'Collected', 'Rejected'],
        colors: ['#ffc107', '#17a2b8', '#6f42c1', '#28a745', '#007bff', '#dc3545'],
        legend: {
            position: 'bottom'
        },
        responsive: [{
            breakpoint: 480,
            options: {
                chart: {
                    width: 200
                },
                legend: {
                    position: 'bottom'
                }
            }
        }]
    };

    const chart = new ApexCharts(document.querySelector("#status-chart"), options);
    chart.render();
}

// Monthly Trends Chart
function initializeTrendsChart() {
    @php
        $monthlyData = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $monthlyData[] = [
                'month' => $month->format('M Y'),
                'requests' => \App\Models\IdCardRequest::whereYear('created_at', $month->year)
                    ->whereMonth('created_at', $month->month)
                    ->count(),
                'completed' => \App\Models\IdCardRequest::whereYear('created_at', $month->year)
                    ->whereMonth('created_at', $month->month)
                    ->whereIn('status', ['ready', 'collected'])
                    ->count()
            ];
        }
    @endphp

    const trendsData = @json($monthlyData);

    const options = {
        series: [{
            name: 'Total Requests',
            data: trendsData.map(item => item.requests)
        }, {
            name: 'Completed',
            data: trendsData.map(item => item.completed)
        }],
        chart: {
            type: 'line',
            height: 300,
            toolbar: {
                show: false
            }
        },
        colors: ['#007bff', '#28a745'],
        xaxis: {
            categories: trendsData.map(item => item.month)
        },
        stroke: {
            curve: 'smooth',
            width: 3
        },
        markers: {
            size: 6
        },
        legend: {
            position: 'top'
        }
    };

    const chart = new ApexCharts(document.querySelector("#trends-chart"), options);
    chart.render();
}

// Activity Filter
function filterActivity(type) {
    const items = document.querySelectorAll('.timeline-item');

    items.forEach(item => {
        if (type === 'all' || item.dataset.activity === type) {
            item.style.display = 'block';
        } else {
            item.style.display = 'none';
        }
    });
}

// Quick Approve Function
function quickApprove(requestId) {
    if (confirm('Are you sure you want to approve this request?')) {
        fetch(`/admin/id-cards/${requestId}/approve`, {
            method: 'PUT',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                admin_feedback: 'Quick approved from dashboard'
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast('Request approved successfully!', 'success');
                setTimeout(() => location.reload(), 1500);
            } else {
                showToast('Failed to approve request', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('An error occurred', 'error');
        });
    }
}

// Refresh Dashboard Stats
function refreshDashboardStats() {
    fetch('/admin/api/dashboard-stats')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update stat cards
                document.getElementById('total-students').textContent = data.stats.total_students;
                document.getElementById('pending-requests').textContent = data.stats.pending_requests;
                document.getElementById('cards-generated').textContent = data.stats.cards_generated;
                document.getElementById('ready-cards').textContent = data.stats.ready_cards;

                // Update badges
                document.getElementById('pending-badge').textContent = data.stats.pending_requests;
            }
        })
        .catch(error => console.error('Error refreshing stats:', error));
}

// Toast Notification
function showToast(message, type = 'info') {
    const toastId = 'toast-' + Date.now();
    const toastClass = type === 'success' ? 'bg-success' : type === 'error' ? 'bg-danger' : 'bg-info';
    const iconClass = type === 'success' ? 'mdi-check-circle' : type === 'error' ? 'mdi-alert-circle' : 'mdi-information';

    const toast = document.createElement('div');
    toast.id = toastId;
    toast.className = `toast align-items-center text-white ${toastClass} border-0 position-fixed`;
    toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    toast.setAttribute('role', 'alert');
    toast.setAttribute('aria-live', 'assertive');
    toast.setAttribute('aria-atomic', 'true');

    toast.innerHTML = `
        <div class="d-flex">
            <div class="toast-body">
                <i class="mdi ${iconClass} me-2"></i>${message}
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    `;

    document.body.appendChild(toast);

    const bsToast = new bootstrap.Toast(toast, {
        autohide: true,
        delay: 5000
    });

    bsToast.show();

    // Remove from DOM after hiding
    toast.addEventListener('hidden.bs.toast', function() {
        toast.remove();
    });
}
</script>

<!-- Custom Styles for Dashboard -->
<style>
.stats-card {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
    border: none;
    box-shadow: 0 2px 10px rgba(0,0,0,0.08);
}

.stats-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
}

.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 15px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #e9ecef;
}

.timeline-item {
    position: relative;
    margin-bottom: 30px;
}

.timeline-marker {
    position: absolute;
    left: -22px;
    top: 0;
}

.timeline-marker-icon {
    width: 30px;
    height: 30px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    border: 3px solid white;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
}

.timeline-content {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 8px;
    border-left: 4px solid #007bff;
    margin-left: 15px;
}

.avatar-title {
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 12px;
}

.progress {
    background-color: #e9ecef;
}

.card {
    border: none;
    box-shadow: 0 2px 10px rgba(0,0,0,0.08);
    border-radius: 10px;
}

.card-header {
    background: transparent;
    border-bottom: 1px solid #e9ecef;
    padding: 1.25rem 1.5rem;
}

.badge {
    font-size: 0.75em;
    padding: 0.35em 0.65em;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .timeline {
        padding-left: 20px;
    }

    .timeline::before {
        left: 10px;
    }

    .timeline-marker {
        left: -17px;
    }

    .timeline-marker-icon {
        width: 25px;
        height: 25px;
    }

    .timeline-content {
        margin-left: 10px;
    }

    .stats-card:hover {
        transform: none;
    }
}

/* Animation for loading states */
.loading-shimmer {
    background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
    background-size: 200% 100%;
    animation: shimmer 1.5s infinite;
}

@keyframes shimmer {
    0% {
        background-position: -200% 0;
    }
    100% {
        background-position: 200% 0;
    }
}

/* Chart container styling */
#status-chart, #trends-chart {
    min-height: 300px;
}

/* Toast improvements */
.toast {
    box-shadow: 0 4px 15px rgba(0,0,0,0.2);
    border-radius: 8px;
}

/* Department table enhancements */
.table-hover tbody tr:hover {
    background-color: rgba(0,123,255,0.05);
}

/* Quick action buttons */
.btn-outline-warning:hover,
.btn-outline-primary:hover,
.btn-outline-success:hover,
.btn-outline-info:hover,
.btn-outline-secondary:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

/* System status indicators */
.avatar-xs {
    width: 24px;
    height: 24px;
}

.avatar-xs .avatar-title {
    font-size: 10px;
}

/* Welcome message gradient */
.alert-info {
    border: none;
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
}

/* Dropdown menu improvements */
.dropdown-menu {
    border: none;
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    border-radius: 8px;
}

.dropdown-item:hover {
    background-color: #f8f9fa;
    color: #007bff;
}

/* Badge animations */
.badge {
    transition: all 0.2s ease;
}

.badge:hover {
    transform: scale(1.1);
}
</style>
@endsection
