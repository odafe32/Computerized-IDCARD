@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <!-- Page Title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">ID Card Request Details</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.id-cards.index') }}">ID Card Requests</a></li>
                        <li class="breadcrumb-item active">{{ $request->request_number }}</li>
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

    <div class="row">
        <!-- Request Details -->
        <div class="col-xl-8">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-4">
                        <div>
                            <h4 class="card-title mb-2">Request #{{ $request->request_number }}</h4>
                            <div class="d-flex align-items-center gap-3">
                                <span class="badge {{ $request->status_badge }} font-size-12">
                                    <i class="{{ $request->status_icon }} me-1"></i>
                                    {{ ucfirst($request->status) }}
                                </span>
                                <span class="badge bg-light text-dark">{{ $request->reason_label }}</span>
                                @if($request->card_number)
                                    <span class="badge bg-info">Card: {{ $request->card_number }}</span>
                                @endif
                            </div>
                        </div>
                        <div class="d-flex gap-2">
                            @if($request->canBeApproved())
                                <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#approveModal">
                                    <i class="mdi mdi-check-circle me-1"></i>Approve
                                </button>
                                <button class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#rejectModal">
                                    <i class="mdi mdi-close-circle me-1"></i>Reject
                                </button>
                            @endif

                            @if($request->canBePrinted())
                                <a href="{{ route('admin.id-cards.preview', $request) }}" target="_blank" class="btn btn-info btn-sm">
                                    <i class="mdi mdi-eye me-1"></i>Preview
                                </a>
                                <form method="POST" action="{{ route('admin.id-cards.generate', $request) }}" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-primary btn-sm">
                                        <i class="mdi mdi-printer me-1"></i>Generate Card
                                    </button>
                                </form>
                            @endif

                            @if($request->canBeMarkedReady())
                                <form method="POST" action="{{ route('admin.id-cards.ready', $request) }}" class="d-inline">
                                    @csrf @method('PUT')
                                    <button type="submit" class="btn btn-success btn-sm">
                                        <i class="mdi mdi-check-all me-1"></i>Mark Ready
                                    </button>
                                </form>
                            @endif

                            @if($request->generated_card_path)
                                <a href="{{ route('admin.id-cards.download', $request) }}" class="btn btn-outline-primary btn-sm">
                                    <i class="mdi mdi-download me-1"></i>Download
                                </a>
                            @endif
                        </div>
                    </div>

                    <!-- Student Information -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card border">
                                <div class="card-header bg-light">
                                    <h5 class="card-title mb-0">Student Information</h5>
                                </div>
                                <div class="card-body">
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="flex-shrink-0 me-3">
                                            <img src="{{ $request->user->photo_url }}"
                                                 alt="{{ $request->user->name }}"
                                                 class="avatar-lg rounded-circle">
                                        </div>
                                        <div class="flex-grow-1">
                                            <h5 class="mb-1">{{ $request->user->name }}</h5>
                                            <p class="text-muted mb-0">{{ $request->user->matric_no }}</p>
                                        </div>
                                    </div>

                                    <div class="table-responsive">
                                        <table class="table table-sm table-nowrap mb-0">
                                            <tbody>
                                                <tr>
                                                    <th>Email:</th>
                                                    <td>{{ $request->user->email }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Phone:</th>
                                                    <td>{{ $request->user->phone ?: 'Not provided' }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Department:</th>
                                                    <td>{{ $request->user->department }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Status:</th>
                                                    <td>
                                                        <span class="badge bg-{{ $request->user->status === 'active' ? 'success' : 'warning' }}">
                                                            {{ ucfirst($request->user->status) }}
                                                        </span>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="card border">
                                <div class="card-header bg-light">
                                    <h5 class="card-title mb-0">Request Information</h5>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-sm table-nowrap mb-0">
                                            <tbody>
                                                <tr>
                                                    <th>Request Number:</th>
                                                    <td>{{ $request->request_number }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Reason:</th>
                                                    <td>{{ $request->reason_label }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Submitted:</th>
                                                    <td>
                                                        {{ $request->created_at->format('M d, Y \a\t h:i A') }}
                                                        <br><small class="text-muted">{{ $request->created_at->diffForHumans() }}</small>
                                                    </td>
                                                </tr>
                                                @if($request->reviewed_at)
                                                <tr>
                                                    <th>Reviewed:</th>
                                                    <td>
                                                        {{ $request->reviewed_at->format('M d, Y \a\t h:i A') }}
                                                        <br><small class="text-muted">by {{ $request->reviewer->name }}</small>
                                                    </td>
                                                </tr>
                                                @endif
                                                @if($request->printed_at)
                                                <tr>
                                                    <th>Printed:</th>
                                                    <td>{{ $request->printed_at->format('M d, Y \a\t h:i A') }}</td>
                                                </tr>
                                                @endif
                                                @if($request->collected_at)
                                                <tr>
                                                    <th>Collected:</th>
                                                    <td>
                                                        {{ $request->collected_at->format('M d, Y \a\t h:i A') }}
                                                        <br><small class="text-muted">by {{ $request->collected_by }}</small>
                                                    </td>
                                                </tr>
                                                @endif
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Additional Information -->
                    @if($request->additional_info)
                    <div class="row mt-3">
                        <div class="col-12">
                            <div class="card border">
                                <div class="card-header bg-light">
                                    <h5 class="card-title mb-0">Additional Information</h5>
                                </div>
                                <div class="card-body">
                                    <p class="mb-0">{{ $request->additional_info }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Admin Feedback -->
                    @if($request->admin_feedback)
                    <div class="row mt-3">
                        <div class="col-12">
                            <div class="card border">
                                <div class="card-header bg-light">
                                    <h5 class="card-title mb-0">Admin Feedback</h5>
                                </div>
                                <div class="card-body">
                                    <p class="mb-0">{{ $request->admin_feedback }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Collection Information -->
                    @if($request->collection_notes)
                    <div class="row mt-3">
                        <div class="col-12">
                            <div class="card border">
                                <div class="card-header bg-light">
                                    <h5 class="card-title mb-0">Collection Notes</h5>
                                </div>
                                <div class="card-body">
                                    <p class="mb-0">{{ $request->collection_notes }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-xl-4">
            <!-- Photo Preview -->
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-3">Student Photo</h5>
                    <div class="text-center">
                        <img src="{{ $request->photo_url }}"
                             alt="Student Photo"
                             class="img-fluid rounded"
                             style="max-height: 300px;">
                    </div>
                </div>
            </div>

            <!-- QR Code (if generated) -->
            @if($request->qr_code_path)
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-3">QR Code</h5>
                    <div class="text-center">
                        <img src="{{ $request->qr_code_url }}"
                             alt="QR Code"
                             class="img-fluid"
                             style="max-width: 200px;">
                        <p class="text-muted mt-2 mb-0">Scan to verify ID card</p>
                    </div>
                </div>
            </div>
            @endif

            <!-- Status Timeline -->
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-3">Status Timeline</h5>
                    <div class="timeline">
                        <div class="timeline-item">
                            <div class="timeline-marker bg-primary"></div>
                            <div class="timeline-content">
                                <h6>Request Submitted</h6>
                                <p class="text-muted mb-1">Student submitted ID card request</p>
                                <small class="text-muted">{{ $request->created_at->format('M d, Y \a\t h:i A') }}</small>
                            </div>
                        </div>

                        @if($request->reviewed_at)
                        <div class="timeline-item">
                            <div class="timeline-marker {{ $request->status === 'approved' ? 'bg-success' : 'bg-danger' }}"></div>
                            <div class="timeline-content">
                                <h6>Request {{ ucfirst($request->status) }}</h6>
                                <p class="text-muted mb-1">Reviewed by {{ $request->reviewer->name }}</p>
                                <small class="text-muted">{{ $request->reviewed_at->format('M d, Y \a\t h:i A') }}</small>
                            </div>
                        </div>
                        @endif

                        @if($request->printed_at)
                        <div class="timeline-item">
                            <div class="timeline-marker bg-info"></div>
                            <div class="timeline-content">
                                <h6>ID Card Generated</h6>
                                <p class="text-muted mb-1">ID card printed and ready</p>
                                <small class="text-muted">{{ $request->printed_at->format('M d, Y \a\t h:i A') }}</small>
                            </div>
                        </div>
                        @endif

                        @if($request->collected_at)
                        <div class="timeline-item">
                            <div class="timeline-marker bg-secondary"></div>
                            <div class="timeline-content">
                                <h6>ID Card Collected</h6>
                                <p class="text-muted mb-1">Collected by {{ $request->collected_by }}</p>
                                <small class="text-muted">{{ $request->collected_at->format('M d, Y \a\t h:i A') }}</small>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Approve Modal -->
<div class="modal fade" id="approveModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Approve Request</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('admin.id-cards.approve', $request) }}">
                @csrf @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="admin_feedback_approve" class="form-label">Feedback (Optional)</label>
                        <textarea class="form-control"
                                  id="admin_feedback_approve"
                                  name="admin_feedback"
                                  rows="3"
                                  placeholder="Add any comments or instructions..."></textarea>
                    </div>
                    <div class="alert alert-info">
                        <i class="mdi mdi-information me-2"></i>
                        The student will be notified via email about the approval.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Approve Request</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1" aria-hidden="true">
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
                        <label for="admin_feedback_reject" class="form-label">
                            Reason for Rejection <span class="text-danger">*</span>
                        </label>
                        <textarea class="form-control"
                                  id="admin_feedback_reject"
                                  name="admin_feedback"
                                  rows="3"
                                  placeholder="Please provide a detailed reason for rejecting this request..."
                                  required></textarea>
                    </div>
                    <div class="alert alert-warning">
                        <i class="mdi mdi-alert me-2"></i>
                        The student will be notified via email about the rejection and the reason provided.
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

.card {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    border: 1px solid rgba(0, 0, 0, 0.125);
    margin-bottom: 1.5rem;
}
</style>
@endsection
