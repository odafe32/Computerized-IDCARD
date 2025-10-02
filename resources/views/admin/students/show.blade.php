@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <!-- Page Title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">Student Details</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">Students</a></li>
                        <li class="breadcrumb-item active">{{ $student->name }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="mdi mdi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="mdi mdi-alert-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Student Profile -->
    <div class="row">
        <div class="col-xl-4">
            <!-- Student Info Card -->
            <div class="card">
                <div class="card-body">
                    <div class="text-center">
                        <div class="mb-4">
                            <img src="{{ $student->photo_url }}"
                                 alt="{{ $student->name }}"
                                 class="avatar-lg rounded-circle img-thumbnail">
                        </div>
                        <h5 class="font-size-16 mb-1">{{ $student->name }}</h5>
                        <p class="text-muted mb-2">{{ $student->matric_no }}</p>
                        <div class="mb-3">
                            @php
                                $statusClasses = [
                                    'active' => 'bg-success',
                                    'inactive' => 'bg-warning',
                                    'suspended' => 'bg-danger'
                                ];
                            @endphp
                            <span class="badge {{ $statusClasses[$student->status] ?? 'bg-secondary' }} font-size-12">
                                {{ ucfirst($student->status) }}
                            </span>
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-flex gap-2 justify-content-center flex-wrap">
                            <a href="{{ route('admin.users.edit', $student) }}" class="btn btn-primary btn-sm">
                                <i class="mdi mdi-pencil me-1"></i>Edit
                            </a>

                            @if($student->status === 'active')
                                <form method="POST" action="{{ route('admin.users.deactivate', $student) }}" class="d-inline">
                                    @csrf @method('PUT')
                                    <button type="submit" class="btn btn-warning btn-sm">
                                        <i class="mdi mdi-account-off me-1"></i>Deactivate
                                    </button>
                                </form>
                            @else
                                <form method="POST" action="{{ route('admin.users.activate', $student) }}" class="d-inline">
                                    @csrf @method('PUT')
                                    <button type="submit" class="btn btn-success btn-sm">
                                        <i class="mdi mdi-check-circle me-1"></i>Activate
                                    </button>
                                </form>
                            @endif

                            <form method="POST" action="{{ route('admin.users.reset-password', $student) }}" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-info btn-sm"
                                        data-confirm="Are you sure you want to reset this student's password?">
                                    <i class="mdi mdi-lock-reset me-1"></i>Reset Password
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contact Information -->
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-4">Contact Information</h4>
                    <div class="table-responsive">
                        <table class="table table-nowrap mb-0">
                            <tbody>
                                <tr>
                                    <th scope="row">Email :</th>
                                    <td>
                                        <a href="mailto:{{ $student->email }}" class="text-primary">
                                            {{ $student->email }}
                                        </a>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">Phone :</th>
                                    <td>
                                        @if($student->phone)
                                            <a href="tel:{{ $student->phone }}" class="text-primary">
                                                {{ $student->phone }}
                                            </a>
                                        @else
                                            <span class="text-muted">Not provided</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">Department :</th>
                                    <td>{{ $student->department }}</td>
                                </tr>
                                <tr>
                                    <th scope="row">Status :</th>
                                    <td>
                                        <span class="badge {{ $statusClasses[$student->status] ?? 'bg-secondary' }}">
                                            {{ ucfirst($student->status) }}
                                        </span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Account Statistics -->
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-4">Account Statistics</h4>
                    <div class="table-responsive">
                        <table class="table table-nowrap mb-0">
                            <tbody>
                                <tr>
                                    <th scope="row">Member Since :</th>
                                    <td>{{ $student->created_at->format('M d, Y') }}</td>
                                </tr>
                                <tr>
                                    <th scope="row">Last Login :</th>
                                    <td>
                                        @if($student->last_login_at)
                                            {{ $student->last_login_at->format('M d, Y h:i A') }}
                                            <br><small class="text-muted">{{ $student->last_login_at->diffForHumans() }}</small>
                                        @else
                                            <span class="text-muted">Never logged in</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">Last IP :</th>
                                    <td>
                                        @if($student->last_login_ip)
                                            <code>{{ $student->last_login_ip }}</code>
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">Email Verified :</th>
                                    <td>
                                        @if($student->email_verified_at)
                                            <span class="badge bg-success">
                                                <i class="mdi mdi-check me-1"></i>Verified
                                            </span>
                                            <br><small class="text-muted">{{ $student->email_verified_at->format('M d, Y') }}</small>
                                        @else
                                            <span class="badge bg-warning">
                                                <i class="mdi mdi-clock me-1"></i>Pending
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-8">
            <!-- ID Card Requests -->
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h4 class="card-title mb-0">ID Card Requests</h4>
                        <span class="badge bg-primary">{{ $idCardRequests->count() }} Total</span>
                    </div>

                    @if($idCardRequests->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-nowrap table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Request #</th>
                                        <th>Reason</th>
                                        <th>Status</th>
                                        <th>Submitted</th>
                                        <th>Reviewed By</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($idCardRequests as $request)
                                        <tr>
                                            <td>
                                                <a href="{{ route('admin.id-cards.show', $request) }}" class="text-primary fw-bold">
                                                    {{ $request->request_number }}
                                                </a>
                                            </td>
                                            <td>
                                                <span class="badge bg-light text-dark">
                                                    {{ ucfirst($request->reason) }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge {{ $request->status_badge }}">
                                                    {{ ucfirst($request->status) }}
                                                </span>
                                            </td>
                                            <td>
                                                {{ $request->created_at->format('M d, Y') }}
                                                <br><small class="text-muted">{{ $request->created_at->diffForHumans() }}</small>
                                            </td>
                                            <td>
                                                @if($request->reviewer)
                                                    {{ $request->reviewer->name }}
                                                    <br><small class="text-muted">{{ $request->reviewed_at->format('M d, Y') }}</small>
                                                @else
                                                    <span class="text-muted">Not reviewed</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="dropdown">
                                                    <button class="btn btn-link font-size-16 shadow-none py-0 text-muted dropdown-toggle"
                                                            type="button" data-bs-toggle="dropdown" data-bs-display="static" aria-expanded="false" aria-label="Actions">
                                                        <i class="mdi mdi-dots-horizontal"></i>
                                                    </button>
                                                    <ul class="dropdown-menu dropdown-menu-end">
                                                        <li>
                                                            <a class="dropdown-item" href="{{ route('admin.id-cards.show', $request) }}">
                                                                <i class="mdi mdi-eye font-size-16 text-success me-1"></i> View Details
                                                            </a>
                                                        </li>
                                                        @if($request->status === 'pending')
                                                            <li>
                                                                <form method="POST" action="{{ route('admin.id-cards.approve', $request) }}" class="d-inline">
                                                                    @csrf @method('PUT')
                                                                    <button type="submit" class="dropdown-item">
                                                                        <i class="mdi mdi-check-circle font-size-16 text-success me-1"></i> Approve
                                                                    </button>
                                                                </form>
                                                            </li>
                                                            <li>
                                                                <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#rejectModal{{ $request->id }}">
                                                                    <i class="mdi mdi-close-circle font-size-16 text-danger me-1"></i> Reject
                                                                </a>
                                                            </li>
                                                        @endif
                                                        @if(in_array($request->status, ['approved', 'printed']))
                                                            <li>
                                                                <form method="POST" action="{{ route('admin.id-cards.ready', $request) }}" class="d-inline">
                                                                    @csrf @method('PUT')
                                                                    <button type="submit" class="dropdown-item">
                                                                        <i class="mdi mdi-check-all font-size-16 text-info me-1"></i> Mark Ready
                                                                    </button>
                                                                </form>
                                                            </li>
                                                        @endif
                                                    </ul>
                                                </div>
                                            </td>
                                        </tr>

                                        <!-- Reject Modal -->
                                        <div class="modal fade" id="rejectModal{{ $request->id }}" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Reject Request</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <form method="POST" action="{{ route('admin.id-cards.reject', $request) }}">
                                                        @csrf @method('PUT')
                                                        <div class="modal-body">
                                                            <div class="mb-3">
                                                                <label for="admin_feedback{{ $request->id }}" class="form-label">
                                                                    Reason for Rejection <span class="text-danger">*</span>
                                                                </label>
                                                                <textarea class="form-control"
                                                                          id="admin_feedback{{ $request->id }}"
                                                                          name="admin_feedback"
                                                                          rows="3"
                                                                          placeholder="Please provide a reason for rejecting this request..."
                                                                          required></textarea>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                            <button type="submit" class="btn btn-danger">Reject Request</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="mdi mdi-clipboard-outline font-size-48 text-muted"></i>
                            <h5 class="mt-3">No ID Card Requests</h5>
                            <p class="text-muted">This student hasn't submitted any ID card requests yet.</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Activity Timeline -->
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-4">Recent Activity</h4>

                    <div class="timeline">
                        <div class="timeline-item">
                            <div class="timeline-marker bg-primary"></div>
                            <div class="timeline-content">
                                <h6>Account Created</h6>
                                <p class="text-muted mb-1">Student account was created</p>
                                <small class="text-muted">{{ $student->created_at->format('M d, Y \a\t h:i A') }}</small>
                            </div>
                        </div>

                        @if($student->email_verified_at)
                            <div class="timeline-item">
                                <div class="timeline-marker bg-success"></div>
                                <div class="timeline-content">
                                    <h6>Email Verified</h6>
                                    <p class="text-muted mb-1">Email address was verified</p>
                                    <small class="text-muted">{{ $student->email_verified_at->format('M d, Y \a\t h:i A') }}</small>
                                </div>
                            </div>
                        @endif

                        @if($student->last_login_at)
                            <div class="timeline-item">
                                <div class="timeline-marker bg-info"></div>
                                <div class="timeline-content">
                                    <h6>Last Login</h6>
                                    <p class="text-muted mb-1">Logged in from {{ $student->last_login_ip }}</p>
                                    <small class="text-muted">{{ $student->last_login_at->format('M d, Y \a\t h:i A') }}</small>
                                </div>
                            </div>
                        @endif

                        @foreach($idCardRequests->take(3) as $request)
                            <div class="timeline-item">
                                <div class="timeline-marker {{ $request->status === 'approved' ? 'bg-success' : ($request->status === 'rejected' ? 'bg-danger' : 'bg-warning') }}"></div>
                                <div class="timeline-content">
                                    <h6>ID Card Request {{ ucfirst($request->status) }}</h6>
                                    <p class="text-muted mb-1">Request #{{ $request->request_number }} - {{ ucfirst($request->reason) }}</p>
                                    <small class="text-muted">{{ $request->created_at->format('M d, Y \a\t h:i A') }}</small>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.avatar-lg {
    width: 80px;
    height: 80px;
    object-fit: cover;
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
    margin-bottom: 25px;
}

.timeline-marker {
    position: absolute;
    left: -23px;
    top: 5px;
    width: 16px;
    height: 16px;
    border-radius: 50%;
    border: 3px solid #fff;
    box-shadow: 0 0 0 2px #e9ecef;
}

.timeline-content h6 {
    margin-bottom: 5px;
    font-weight: 600;
}

.timeline-content p {
    margin-bottom: 5px;
    font-size: 14px;
}

.dropdown-toggle::after {
    display: none;
}

/* Ensure dropdowns are not clipped inside responsive tables */
.table-responsive {
    overflow: visible;
}

.table th {
    border-top: none;
    font-weight: 600;
    text-transform: uppercase;
    font-size: 12px;
    letter-spacing: 0.5px;
}

.card {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    border: 1px solid rgba(0, 0, 0, 0.125);
    margin-bottom: 1.5rem;
}
</style>
@endsection
