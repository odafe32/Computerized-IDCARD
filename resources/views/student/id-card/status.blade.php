@extends('layouts.student')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">ID Card Request Status</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('student.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Request Status</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

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
        <div class="col-12">
            @if($requests->count() > 0)
                @foreach($requests as $request)
                    <div class="card mb-4 border-0 shadow-sm">
                        <div class="card-body">
                            <!-- Header Section -->
                            <div class="d-flex justify-content-between align-items-start mb-4">
                                <div>
                                    <h5 class="card-title mb-2 d-flex align-items-center">
                                        <i class="mdi mdi-card-account-details text-primary me-2"></i>
                                        Request #{{ $request->request_number }}
                                        <span class="badge {{ $request->status_badge }} ms-2">
                                            <i class="mdi {{ $request->status_icon }} me-1"></i>
                                            {{ ucfirst($request->status) }}
                                        </span>
                                    </h5>
                                    <div class="d-flex flex-wrap gap-3 text-muted">
                                        <small>
                                            <i class="mdi mdi-calendar me-1"></i>
                                            Submitted: {{ $request->created_at->format('M d, Y \a\t h:i A') }}
                                        </small>
                                        <small>
                                            <i class="mdi mdi-tag me-1"></i>
                                            Reason: {{ $request->reason_label }}
                                        </small>
                                        @if($request->card_number)
                                            <small>
                                                <i class="mdi mdi-identifier me-1"></i>
                                                Card #: {{ $request->card_number }}
                                            </small>
                                        @endif
                                    </div>
                                </div>

                            <div class="d-flex gap-2 flex-wrap">
    @if($request->canBeDownloaded())
        <div class="dropdown">
            <button class="btn btn-success btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                <i class="mdi mdi-download me-1"></i>Download ID Card
            </button>
            <ul class="dropdown-menu">
                <li>
                    <a class="dropdown-item" href="{{ route('student.id-card.download', $request->id) }}">
                        <i class="mdi mdi-file-pdf me-2 text-danger"></i>
                        Download PDF
                        @if($request->card_file_size)
                            <small class="text-muted">({{ $request->card_file_size }})</small>
                        @endif
                    </a>
                </li>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <a class="dropdown-item" href="#" onclick="previewCard('{{ $request->id }}')">
                        <i class="mdi mdi-eye me-2 text-info"></i>Preview Card
                    </a>
                </li>
                @if($request->qr_code_url)
                    <li>
                        <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#qrModal{{ $request->id }}">
                            <i class="mdi mdi-qrcode me-2 text-primary"></i>View QR Code
                        </a>
                    </li>
                @endif
            </ul>
        </div>
    @elseif($request->status === 'printed')
        <button class="btn btn-info btn-sm" disabled>
            <i class="mdi mdi-clock me-1"></i>Processing...
        </button>
    @endif

    @if($request->canBeCancelled())
        <button type="button"
                class="btn btn-outline-danger btn-sm"
                onclick="cancelRequest('{{ $request->id }}', '{{ $request->request_number }}')">
            <i class="mdi mdi-close me-1"></i>Cancel
        </button>
    @endif

    @if($request->qr_code_url && !$request->canBeDownloaded())
        <button type="button"
                class="btn btn-outline-info btn-sm"
                data-bs-toggle="modal"
                data-bs-target="#qrModal{{ $request->id }}">
            <i class="mdi mdi-qrcode me-1"></i>QR Code
        </button>
    @endif

    <!-- Share Button (if card is ready) -->
    @if($request->canBeDownloaded())
        <button type="button"
                class="btn btn-outline-secondary btn-sm"
                onclick="shareCard('{{ $request->card_number }}')">
            <i class="mdi mdi-share me-1"></i>Share
        </button>
    @endif
</div>
                            </div>

                            <!-- Progress Bar -->
                            <div class="mb-4">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <small class="text-muted">Progress</small>
                                <small class="text-muted">{{ $request->progress_percentage }}% Complete</small>
                                </div>
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar bg-{{ $request->status === 'rejected' ? 'danger' : 'primary' }}"
                                         role="progressbar"
                                         style="width: {{ $request->progress_percentage }}%"
                                         aria-valuenow="{{ $request->progress_percentage }}"
                                         aria-valuemin="0"
                                         aria-valuemax="100">
                                    </div>
                                </div>
                                <div class="mt-2">
                                    <small class="text-muted">
                                        <i class="mdi mdi-clock-outline me-1"></i>
                                        Next: {{ $request->next_status }}
                                        @if($request->estimated_completion !== 'Completed' && $request->estimated_completion !== 'Available now')
                                            (Est. {{ $request->estimated_completion }})
                                        @endif
                                    </small>
                                </div>
                            </div>

                            <!-- Status Timeline -->
                            <div class="timeline-container mb-4">
                                <div class="timeline">
                                    <!-- Submitted -->
                                    <div class="timeline-item {{ $request->created_at ? 'completed' : '' }}">
                                        <div class="timeline-marker">
                                            <i class="mdi mdi-file-document-outline"></i>
                                        </div>
                                        <div class="timeline-content">
                                            <h6 class="mb-1">Request Submitted</h6>
                                            <small class="text-muted">
                                                {{ $request->created_at ? $request->created_at->format('M d, Y h:i A') : 'Pending' }}
                                            </small>
                                        </div>
                                    </div>

                                    <!-- Under Review -->
                                    <div class="timeline-item {{ in_array($request->status, ['approved', 'printed', 'ready', 'collected']) ? 'completed' : ($request->status === 'rejected' ? 'rejected' : 'pending') }}">
                                        <div class="timeline-marker">
                                            <i class="mdi mdi-account-check-outline"></i>
                                        </div>
                                        <div class="timeline-content">
                                            <h6 class="mb-1">Under Review</h6>
                                            <small class="text-muted">
                                                @if($request->reviewed_at)
                                                    {{ $request->reviewed_at->format('M d, Y h:i A') }}
                                                    @if($request->reviewer)
                                                        <br>Reviewed by: {{ $request->reviewer->name }}
                                                    @endif
                                                @else
                                                    {{ $request->status === 'pending' ? 'In progress...' : 'Pending' }}
                                                @endif
                                            </small>
                                        </div>
                                    </div>

                                    <!-- Approved/Rejected -->
                                    @if($request->status === 'rejected')
                                        <div class="timeline-item rejected">
                                            <div class="timeline-marker">
                                                <i class="mdi mdi-close-circle-outline"></i>
                                            </div>
                                            <div class="timeline-content">
                                                <h6 class="mb-1 text-danger">Request Rejected</h6>
                                                <small class="text-muted">
                                                    {{ $request->reviewed_at ? $request->reviewed_at->format('M d, Y h:i A') : '' }}
                                                </small>
                                                @if($request->admin_feedback)
                                                    <div class="mt-2 p-2 bg-danger bg-opacity-10 rounded">
                                                        <small class="text-danger">
                                                            <strong>Reason:</strong> {{ $request->admin_feedback }}
                                                        </small>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    @else
                                        <!-- Card Generation -->
                                        <div class="timeline-item {{ in_array($request->status, ['printed', 'ready', 'collected']) ? 'completed' : 'pending' }}">
                                            <div class="timeline-marker">
                                                <i class="mdi mdi-printer"></i>
                                            </div>
                                            <div class="timeline-content">
                                                <h6 class="mb-1">Card Generation</h6>
                                                <small class="text-muted">
                                                    @if($request->printed_at)
                                                        {{ $request->printed_at->format('M d, Y h:i A') }}
                                                    @else
                                                        {{ in_array($request->status, ['approved']) ? 'Processing...' : 'Pending' }}
                                                    @endif
                                                </small>
                                            </div>
                                        </div>

                                        <!-- Ready for Collection -->
                                        <div class="timeline-item {{ in_array($request->status, ['ready', 'collected']) ? 'completed' : 'pending' }}">
                                            <div class="timeline-marker">
                                                <i class="mdi mdi-check-circle-outline"></i>
                                            </div>
                                            <div class="timeline-content">
                                                <h6 class="mb-1">Ready for Download</h6>
                                                <small class="text-muted">
                                                    @if($request->status === 'ready')
                                                        Available now
                                                    @elseif($request->status === 'collected')
                                                        Downloaded on {{ $request->collected_at ? $request->collected_at->format('M d, Y h:i A') : 'N/A' }}
                                                    @else
                                                        Pending
                                                    @endif
                                                </small>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Additional Information -->
                            @if($request->additional_info)
                                <div class="mb-3">
                                    <h6 class="text-muted mb-2">Additional Information:</h6>
                                    <div class="p-3 bg-light rounded">
                                        <small>{{ $request->additional_info }}</small>
                                    </div>
                                </div>
                            @endif

                            <!-- Admin Feedback -->
                            @if($request->admin_feedback && $request->status !== 'rejected')
                                <div class="mb-3">
                                    <h6 class="text-muted mb-2">Admin Notes:</h6>
                                    <div class="p-3 bg-info bg-opacity-10 rounded">
                                        <small class="text-info">{{ $request->admin_feedback }}</small>
                                    </div>
                                </div>
                            @endif

                            <!-- Photo Preview -->
                            @if($request->photo_url)
                                <div class="row">
                                    <div class="col-md-3">
                                        <h6 class="text-muted mb-2">Submitted Photo:</h6>
                                        <div class="border rounded p-2 text-center">
                                            <img src="{{ $request->photo_url }}"
                                                 alt="ID Photo"
                                                 class="img-fluid rounded"
                                                 style="max-height: 200px; max-width: 150px;">
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- QR Code Modal -->
                    @if($request->qr_code_url)
                        <div class="modal fade" id="qrModal{{ $request->id }}" tabindex="-1">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">
                                            <i class="mdi mdi-qrcode me-2"></i>ID Card QR Code
                                        </h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body text-center">
                                        <div class="mb-3">
                                            <img src="{{ $request->qr_code_url }}"
                                                 alt="QR Code"
                                                 class="img-fluid"
                                                 style="max-width: 200px;">
                                        </div>
                                        <p class="text-muted mb-0">
                                            <small>Scan this QR code to verify your ID card</small>
                                        </p>
                                        <p class="text-muted">
                                            <small>Card #: {{ $request->card_number }}</small>
                                        </p>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                @endforeach
            @else
                <!-- No Requests State -->
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center py-5">
                        <div class="avatar-lg mx-auto mb-4">
                            <div class="avatar-title bg-light text-muted rounded-circle">
                                <i class="mdi mdi-card-account-details-outline font-size-24"></i>
                            </div>
                        </div>
                        <h5 class="text-muted mb-3">No ID Card Requests Found</h5>
                        <p class="text-muted mb-4">
                            You haven't submitted any ID card requests yet.
                            Click the button below to request your student ID card.
                        </p>
                        <a href="{{ route('student.id-card.request') }}" class="btn btn-primary">
                            <i class="mdi mdi-plus me-2"></i>Request ID Card
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Cancel Request Modal -->
<div class="modal fade" id="cancelModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="mdi mdi-alert-circle text-warning me-2"></i>Cancel Request
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to cancel request <strong id="cancelRequestNumber"></strong>?</p>
                <div class="alert alert-warning">
                    <i class="mdi mdi-information me-2"></i>
                    <strong>Note:</strong> This action cannot be undone. You will need to submit a new request if you change your mind.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Keep Request</button>
                <form id="cancelForm" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="mdi mdi-delete me-2"></i>Yes, Cancel Request
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function cancelRequest(requestId, requestNumber) {
    document.getElementById('cancelRequestNumber').textContent = requestNumber;
    document.getElementById('cancelForm').action = `/student/id-card/cancel/${requestId}`;

    const modal = new bootstrap.Modal(document.getElementById('cancelModal'));
    modal.show();
}

// Auto-refresh status every 30 seconds for pending requests
document.addEventListener('DOMContentLoaded', function() {
    const hasPendingRequests = {{ $requests->where('status', 'pending')->count() > 0 ? 'true' : 'false' }};

    if (hasPendingRequests) {
        setInterval(function() {
            // Check for status updates
            fetch(window.location.href, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.text())
            .then(html => {
                // Parse the response and update status if changed
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const newContent = doc.querySelector('.container-fluid');

                if (newContent) {
                    // Simple check if content has changed
                    const currentContent = document.querySelector('.container-fluid').innerHTML;
                    const newContentHtml = newContent.innerHTML;

                    if (currentContent !== newContentHtml) {
                        // Show notification that status has been updated
                        showStatusUpdateNotification();

                        // Optionally reload the page
                        setTimeout(() => {
                            window.location.reload();
                        }, 2000);
                    }
                }
            })
            .catch(error => {
                console.log('Status check failed:', error);
            });
        }, 30000); // Check every 30 seconds
    }
});

function showStatusUpdateNotification() {
    const toast = document.createElement('div');
    toast.className = 'toast align-items-center text-white bg-success border-0 position-fixed';
    toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999;';
    toast.setAttribute('role', 'alert');

    toast.innerHTML = `
        <div class="d-flex">
            <div class="toast-body">
                <i class="mdi mdi-check-circle me-2"></i>Your request status has been updated!
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    `;

    document.body.appendChild(toast);

    const bsToast = new bootstrap.Toast(toast, { autohide: true, delay: 5000 });
    bsToast.show();

    toast.addEventListener('hidden.bs.toast', function() {
        toast.remove();
    });
}
</script>

<style>
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

.timeline-item.completed .timeline-marker {
    background: #28a745;
    color: white;
}

.timeline-item.rejected .timeline-marker {
    background: #dc3545;
    color: white;
}

.timeline-item.pending .timeline-marker {
    background: #6c757d;
    color: white;
}
.timeline-marker {
    position: absolute;
    left: -22px;
    top: 0;
    width: 30px;
    height: 30px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 3px solid white;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    font-size: 14px;
}

.timeline-content {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 8px;
    border-left: 4px solid #007bff;
    margin-left: 15px;
    position: relative;
}

.timeline-item.completed .timeline-content {
    border-left-color: #28a745;
}

.timeline-item.rejected .timeline-content {
    border-left-color: #dc3545;
}

.timeline-item.pending .timeline-content {
    border-left-color: #6c757d;
}

/* Card hover effects */
.card {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15) !important;
}

/* Progress bar animations */
.progress-bar {
    transition: width 0.6s ease;
}

/* Status badge animations */
.badge {
    transition: all 0.2s ease;
}

.badge:hover {
    transform: scale(1.05);
}

/* Button hover effects */
.btn {
    transition: all 0.2s ease;
}

.btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

/* Modal improvements */
.modal-content {
    border: none;
    box-shadow: 0 10px 30px rgba(0,0,0,0.3);
    border-radius: 10px;
}

.modal-header {
    border-bottom: 1px solid #e9ecef;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-radius: 10px 10px 0 0;
}

.modal-header .btn-close {
    filter: invert(1);
}

/* Photo preview styling */
.border {
    transition: border-color 0.2s ease;
}

.border:hover {
    border-color: #007bff !important;
}

/* QR Code modal styling */
#qrModal .modal-body img {
    border: 2px solid #e9ecef;
    border-radius: 8px;
    padding: 10px;
    background: white;
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
        width: 25px;
        height: 25px;
        font-size: 12px;
    }

    .timeline-content {
        margin-left: 10px;
        padding: 10px;
    }

    .card:hover {
        transform: none;
    }

    .btn:hover {
        transform: none;
    }
}

/* Loading animation for auto-refresh */
.loading-indicator {
    position: fixed;
    top: 10px;
    right: 10px;
    background: rgba(0,123,255,0.9);
    color: white;
    padding: 8px 12px;
    border-radius: 20px;
    font-size: 12px;
    z-index: 9999;
    display: none;
}

.loading-indicator.show {
    display: block;
    animation: fadeInOut 2s ease-in-out;
}

@keyframes fadeInOut {
    0%, 100% { opacity: 0; }
    50% { opacity: 1; }
}

/* Status-specific styling */
.status-pending {
    border-left: 4px solid #ffc107;
}

.status-approved {
    border-left: 4px solid #17a2b8;
}

.status-printed {
    border-left: 4px solid #6f42c1;
}

.status-ready {
    border-left: 4px solid #28a745;
}

.status-collected {
    border-left: 4px solid #007bff;
}

.status-rejected {
    border-left: 4px solid #dc3545;
}

/* Pulse animation for pending status */
.status-pending .timeline-marker {
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0% {
        box-shadow: 0 0 0 0 rgba(255, 193, 7, 0.7);
    }
    70% {
        box-shadow: 0 0 0 10px rgba(255, 193, 7, 0);
    }
    100% {
        box-shadow: 0 0 0 0 rgba(255, 193, 7, 0);
    }
}

/* Success animation for completed status */
.status-ready .timeline-marker,
.status-collected .timeline-marker {
    animation: bounce 1s ease-in-out;
}

@keyframes bounce {
    0%, 20%, 50%, 80%, 100% {
        transform: translateY(0);
    }
    40% {
        transform: translateY(-10px);
    }
    60% {
        transform: translateY(-5px);
    }
}

/* Alert styling improvements */
.alert {
    border: none;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.alert-warning {
    background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%);
    border-left: 4px solid #ffc107;
}

.alert-success {
    background: linear-gradient(135deg, #d4edda 0%, #a8e6cf 100%);
    border-left: 4px solid #28a745;
}

.alert-danger {
    background: linear-gradient(135deg, #f8d7da 0%, #ff7675 100%);
    border-left: 4px solid #dc3545;
}

/* Toast notification styling */
.toast {
    border-radius: 8px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.2);
    backdrop-filter: blur(10px);
}

/* Print styles */
@media print {
    .btn, .modal, .alert {
        display: none !important;
    }

    .card {
        box-shadow: none !important;
        border: 1px solid #dee2e6 !important;
    }

    .timeline-marker {
        box-shadow: none !important;
    }
}

/* Dark mode support (if needed) */
@media (prefers-color-scheme: dark) {
    .timeline-content {
        background: #2d3748;
        color: #e2e8f0;
    }

    .card {
        background: #2d3748;
        color: #e2e8f0;
    }

    .text-muted {
        color: #a0aec0 !important;
    }
}

/* Accessibility improvements */
.timeline-marker:focus {
    outline: 2px solid #007bff;
    outline-offset: 2px;
}

.btn:focus {
    box-shadow: 0 0 0 3px rgba(0,123,255,0.25);
}

/* Custom scrollbar for long content */
.card-body {
    scrollbar-width: thin;
    scrollbar-color: #cbd5e0 #f7fafc;
}

.card-body::-webkit-scrollbar {
    width: 6px;
}

.card-body::-webkit-scrollbar-track {
    background: #f7fafc;
    border-radius: 3px;
}

.card-body::-webkit-scrollbar-thumb {
    background: #cbd5e0;
    border-radius: 3px;
}

.card-body::-webkit-scrollbar-thumb:hover {
    background: #a0aec0;
}
</style>

<!-- Add loading indicator -->
<div class="loading-indicator" id="loadingIndicator">
    <i class="mdi mdi-loading mdi-spin me-1"></i>Checking for updates...
</div>

<script>
// Enhanced auto-refresh with loading indicator
document.addEventListener('DOMContentLoaded', function() {
    const hasPendingRequests = {{ $requests->where('status', 'pending')->count() > 0 ? 'true' : 'false' }};
    const loadingIndicator = document.getElementById('loadingIndicator');

    if (hasPendingRequests) {
        setInterval(function() {
            // Show loading indicator
            loadingIndicator.classList.add('show');

            // Check for status updates
            fetch(window.location.href, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.text())
            .then(html => {
                // Hide loading indicator
                loadingIndicator.classList.remove('show');

                // Parse the response and update status if changed
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const newContent = doc.querySelector('.container-fluid');

                if (newContent) {
                    // Simple check if content has changed
                    const currentContent = document.querySelector('.container-fluid').innerHTML;
                    const newContentHtml = newContent.innerHTML;

                    if (currentContent !== newContentHtml) {
                        // Show notification that status has been updated
                        showStatusUpdateNotification();

                        // Optionally reload the page
                        setTimeout(() => {
                            window.location.reload();
                        }, 2000);
                    }
                }
            })
            .catch(error => {
                console.log('Status check failed:', error);
                loadingIndicator.classList.remove('show');
            });
        }, 30000); // Check every 30 seconds
    }

    // Add smooth scrolling to timeline items
    const timelineItems = document.querySelectorAll('.timeline-item');
    timelineItems.forEach((item, index) => {
        item.style.animationDelay = `${index * 0.1}s`;
        item.classList.add('fade-in');
    });
});

// Enhanced cancel request function with confirmation
function cancelRequest(requestId, requestNumber) {
    document.getElementById('cancelRequestNumber').textContent = requestNumber;
    document.getElementById('cancelForm').action = `/student/id-card/cancel/${requestId}`;

    const modal = new bootstrap.Modal(document.getElementById('cancelModal'));
    modal.show();
}

// Enhanced status update notification
function showStatusUpdateNotification() {
    const toast = document.createElement('div');
    toast.className = 'toast align-items-center text-white bg-success border-0 position-fixed';
    toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    toast.setAttribute('role', 'alert');
    toast.setAttribute('aria-live', 'assertive');
    toast.setAttribute('aria-atomic', 'true');

    toast.innerHTML = `
        <div class="d-flex">
            <div class="toast-body">
                <i class="mdi mdi-check-circle me-2"></i>Your request status has been updated!
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    `;

    document.body.appendChild(toast);

    const bsToast = new bootstrap.Toast(toast, {
        autohide: true,
        delay: 5000
    });

    bsToast.show();

    toast.addEventListener('hidden.bs.toast', function() {
        toast.remove();
    });
}

// Add fade-in animation class
const style = document.createElement('style');
style.textContent = `
    .fade-in {
        opacity: 0;
        transform: translateY(20px);
        animation: fadeInUp 0.6s ease forwards;
    }

    @keyframes fadeInUp {
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
`;
document.head.appendChild(style);
</script>

<script>
// Preview card function
function previewCard(requestId) {
    // You can implement a preview modal or redirect to a preview page
    window.open(`/student/id-card/preview/${requestId}`, '_blank', 'width=800,height=600');
}

// Share card function
function shareCard(cardNumber) {
    const shareData = {
        title: 'My Student ID Card',
        text: `My Student ID Card #${cardNumber} from Lexa University`,
        url: window.location.href
    };

    if (navigator.share) {
        navigator.share(shareData);
    } else {
        // Fallback: copy to clipboard
        const textToCopy = `My Student ID Card #${cardNumber} from Lexa University - ${window.location.href}`;
        navigator.clipboard.writeText(textToCopy).then(() => {
            showToast('Card information copied to clipboard!', 'success');
        });
    }
}

// Enhanced download with progress tracking
function downloadWithProgress(url, filename) {
    const link = document.createElement('a');
    link.href = url;
    link.download = filename;

    // Show download started notification
    showToast('Download started...', 'info');

    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);

    // Show download completed notification after a delay
    setTimeout(() => {
        showToast('Download completed!', 'success');
    }, 2000);
}

// Toast notification function
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
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    `;

    document.body.appendChild(toast);

    const bsToast = new bootstrap.Toast(toast, {
        autohide: true,
        delay: 5000
    });

    bsToast.show();

    toast.addEventListener('hidden.bs.toast', function() {
        toast.remove();
    });
}
</script>
@endsection
