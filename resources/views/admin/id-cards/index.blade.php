@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <!-- Page Title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">ID Card Requests</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">ID Card Requests</li>
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

    <!-- Statistics Cards -->
    <div class="row">
        <div class="col-xl-2 col-md-4">
            <div class="card stats-card">
                <div class="card-body">
                    <div class="d-flex">
                        <div class="flex-grow-1">
                            <p class="text-truncate font-size-14 mb-2">Total Requests</p>
                            <h4 class="mb-2">{{ $requests->total() }}</h4>
                        </div>
                        <div class="avatar-sm">
                            <span class="avatar-title bg-light text-primary rounded-3">
                                <i class="mdi mdi-clipboard-list font-size-24"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-md-4">
            <div class="card stats-card">
                <div class="card-body">
                    <div class="d-flex">
                        <div class="flex-grow-1">
                            <p class="text-truncate font-size-14 mb-2">Pending</p>
                            <h4 class="mb-2">{{ $requests->where('status', 'pending')->count() }}</h4>
                        </div>
                        <div class="avatar-sm">
                            <span class="avatar-title bg-light text-warning rounded-3">
                                <i class="mdi mdi-clock-outline font-size-24"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-md-4">
            <div class="card stats-card">
                <div class="card-body">
                    <div class="d-flex">
                        <div class="flex-grow-1">
                            <p class="text-truncate font-size-14 mb-2">Approved</p>
                            <h4 class="mb-2">{{ $requests->where('status', 'approved')->count() }}</h4>
                        </div>
                        <div class="avatar-sm">
                            <span class="avatar-title bg-light text-success rounded-3">
                                <i class="mdi mdi-check-circle font-size-24"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-md-4">
            <div class="card stats-card">
                <div class="card-body">
                    <div class="d-flex">
                        <div class="flex-grow-1">
                            <p class="text-truncate font-size-14 mb-2">Printed</p>
                            <h4 class="mb-2">{{ $requests->where('status', 'printed')->count() }}</h4>
                        </div>
                        <div class="avatar-sm">
                            <span class="avatar-title bg-light text-info rounded-3">
                                <i class="mdi mdi-printer font-size-24"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-md-4">
            <div class="card stats-card">
                <div class="card-body">
                    <div class="d-flex">
                        <div class="flex-grow-1">
                            <p class="text-truncate font-size-14 mb-2">Ready</p>
                            <h4 class="mb-2">{{ $requests->where('status', 'ready')->count() }}</h4>
                        </div>
                        <div class="avatar-sm">
                            <span class="avatar-title bg-light text-primary rounded-3">
                                <i class="mdi mdi-check-all font-size-24"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-md-4">
            <div class="card stats-card">
                <div class="card-body">
                    <div class="d-flex">
                        <div class="flex-grow-1">
                            <p class="text-truncate font-size-14 mb-2">Collected</p>
                            <h4 class="mb-2">{{ $requests->where('status', 'collected')->count() }}</h4>
                        </div>
                        <div class="avatar-sm">
                            <span class="avatar-title bg-light text-secondary rounded-3">
                                <i class="mdi mdi-check-bold font-size-24"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h4 class="card-title mb-0">ID Card Requests</h4>
                        <div class="d-flex gap-2">
                            <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#bulkApproveModal" id="bulkApproveBtn" disabled>
                                <i class="mdi mdi-check-circle me-1"></i>Bulk Approve
                            </button>
                            <button class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#bulkRejectModal" id="bulkRejectBtn" disabled>
                                <i class="mdi mdi-close-circle me-1"></i>Bulk Reject
                            </button>
                            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#bulkReadyModal" id="bulkReadyBtn" disabled>
                                <i class="mdi mdi-check-all me-1"></i>Bulk Mark Ready
                            </button>
                        </div>
                    </div>

                    <!-- Filters -->
                    <form method="GET" class="mb-4">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <input type="text" class="form-control" name="search"
                                       value="{{ request('search') }}"
                                       placeholder="Search by student name, matric, or request number...">
                            </div>
                            <div class="col-md-2">
                                <select class="form-select" name="status">
                                    <option value="">All Status</option>
                                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                                    <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                                    <option value="printed" {{ request('status') === 'printed' ? 'selected' : '' }}>Printed</option>
                                    <option value="ready" {{ request('status') === 'ready' ? 'selected' : '' }}>Ready</option>
                                    <option value="collected" {{ request('status') === 'collected' ? 'selected' : '' }}>Collected</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select class="form-select" name="reason">
                                    <option value="">All Reasons</option>
                                    <option value="new" {{ request('reason') === 'new' ? 'selected' : '' }}>New Student</option>
                                    <option value="replacement" {{ request('reason') === 'replacement' ? 'selected' : '' }}>Replacement</option>
                                    <option value="lost" {{ request('reason') === 'lost' ? 'selected' : '' }}>Lost Card</option>
                                    <option value="damaged" {{ request('reason') === 'damaged' ? 'selected' : '' }}>Damaged Card</option>
                                    <option value="name_change" {{ request('reason') === 'name_change' ? 'selected' : '' }}>Name Change</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select class="form-select" name="sort">
                                    <option value="created_at" {{ request('sort', 'created_at') === 'created_at' ? 'selected' : '' }}>Date Submitted</option>
                                    <option value="reviewed_at" {{ request('sort') === 'reviewed_at' ? 'selected' : '' }}>Date Reviewed</option>
                                    <option value="status" {{ request('sort') === 'status' ? 'selected' : '' }}>Status</option>
                                </select>
                            </div>
                            <div class="col-md-1">
                                <button type="submit" class="btn btn-outline-primary w-100">
                                    <i class="mdi mdi-magnify"></i>
                                </button>
                            </div>
                            <div class="col-md-2">
                                <a href="{{ route('admin.id-cards.index') }}" class="btn btn-outline-secondary w-100">
                                    <i class="mdi mdi-refresh me-1"></i>Reset
                                </a>
                            </div>
                        </div>
                    </form>

                    <!-- Requests Table -->
                    <div class="table-responsive">
                        <table class="table table-nowrap table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th width="30">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="selectAll">
                                        </div>
                                    </th>
                                    <th>Request Details</th>
                                    <th>Student</th>
                                    <th>Reason</th>
                                    <th>Status</th>
                                    <th>Submitted</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($requests as $request)
                                    <tr>
                                        <td>
                                            <div class="form-check">
                                                <input class="form-check-input request-checkbox" type="checkbox"
                                                       value="{{ $request->id }}" data-status="{{ $request->status }}">
                                            </div>
                                        </td>
                                        <td>
                                            <div>
                                                <h6 class="mb-1">
                                                    <a href="{{ route('admin.id-cards.show', $request) }}" class="text-primary">
                                                        {{ $request->request_number }}
                                                    </a>
                                                </h6>
                                                @if($request->card_number)
                                                    <small class="text-muted">Card: {{ $request->card_number }}</small>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="flex-shrink-0 me-2">
                                                    <img src="{{ $request->user->photo_url }}"
                                                         alt="{{ $request->user->name }}"
                                                         class="avatar-xs rounded-circle">
                                                </div>
                                                <div class="flex-grow-1">
                                                    <h6 class="mb-1">{{ $request->user->name }}</h6>
                                                    <p class="text-muted mb-0">{{ $request->user->matric_no }}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-dark">{{ $request->reason_label }}</span>
                                        </td>
                                        <td>
                                            <span class="badge {{ $request->status_badge }}">

                                                {{ ucfirst($request->status) }}
                                            </span>
                                        </td>
                                        <td>
                                            <div>
                                                {{ $request->created_at->format('M d, Y') }}
                                                <br><small class="text-muted">{{ $request->created_at->diffForHumans() }}</small>
                                            </div>
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

                                                    @if($request->canBeApproved())
                                                        <li>
                                                            <form method="POST" action="{{ route('admin.id-cards.approve', $request) }}" class="d-inline">
                                                                @csrf @method('PUT')
                                                                <button type="submit" class="dropdown-item">
                                                                    <i class="mdi mdi-check-circle font-size-16 text-success me-1"></i> Approve
                                                                </button>
                                                            </form>
                                                        </li>
                                                        <li>
                                                            <a class="dropdown-item" href="#" data-bs-toggle="modal"
                                                               data-bs-target="#rejectModal{{ $request->id }}">
                                                                <i class="mdi mdi-close-circle font-size-16 text-danger me-1"></i> Reject
                                                            </a>
                                                        </li>
                                                    @endif

                                                    @if($request->canBePrinted())
                                                        <li>
                                                            <a class="dropdown-item" href="{{ route('admin.id-cards.preview', $request) }}" target="_blank">
                                                                <i class="mdi mdi-eye font-size-16 text-info me-1"></i> Preview
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <form method="POST" action="{{ route('admin.id-cards.generate', $request) }}" class="d-inline">
                                                                @csrf
                                                                <button type="submit" class="dropdown-item">
                                                                    <i class="mdi mdi-printer font-size-16 text-primary me-1"></i> Generate Card
                                                                </button>
                                                            </form>
                                                        </li>
                                                    @endif

                                                    @if($request->canBeMarkedReady())
                                                        <li>
                                                            <form method="POST" action="{{ route('admin.id-cards.ready', $request) }}" class="d-inline">
                                                                @csrf @method('PUT')
                                                                <button type="submit" class="dropdown-item">
                                                                    <i class="mdi mdi-check-all font-size-16 text-success me-1"></i> Mark Ready
                                                                </button>
                                                            </form>
                                                        </li>
                                                    @endif

                                                    @if($request->generated_card_path)
                                                        <li>
                                                            <a class="dropdown-item" href="{{ route('admin.id-cards.download', $request) }}">
                                                                <i class="mdi mdi-download font-size-16 text-info me-1"></i> Download
                                                            </a>
                                                        </li>
                                                    @endif
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>

                                    <!-- Individual Reject Modal -->
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
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-4">
                                            <div class="d-flex flex-column align-items-center">
                                                <i class="mdi mdi-clipboard-search font-size-48 text-muted mb-2"></i>
                                                <h5>No Requests Found</h5>
                                                <p class="text-muted">No ID card requests match your current filters.</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($requests->hasPages())
                        <div class="row mt-4">
                            <div class="col-sm-6">
                                <div class="dataTables_info">
                                    Showing {{ $requests->firstItem() }} to {{ $requests->lastItem() }} of {{ $requests->total() }} entries
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="dataTables_paginate paging_simple_numbers float-end">
                                    {{ $requests->links() }}
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bulk Approve Modal -->
<div class="modal fade" id="bulkApproveModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Bulk Approve Requests</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('admin.id-cards.bulk.approve') }}" id="bulkApproveForm">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="bulk_approve_feedback" class="form-label">Feedback (Optional)</label>
                        <textarea class="form-control"
                                  id="bulk_approve_feedback"
                                  name="admin_feedback"
                                  rows="3"
                                  placeholder="Add any comments or instructions..."></textarea>
                    </div>
                    <div class="alert alert-info">
                        <i class="mdi mdi-information me-2"></i>
                        <span id="selectedCount">0</span> requests will be approved. Students will be notified via email.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Approve Selected</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Bulk Reject Modal -->
<div class="modal fade" id="bulkRejectModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Bulk Reject Requests</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('admin.id-cards.bulk.reject') }}" id="bulkRejectForm">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="bulk_reject_feedback" class="form-label">
                            Reason for Rejection <span class="text-danger">*</span>
                        </label>
                        <textarea class="form-control"
                                  id="bulk_reject_feedback"
                                  name="admin_feedback"
                                  rows="3"
                                  placeholder="Please provide a reason for rejecting these requests..."
                                  required></textarea>
                    </div>
                    <div class="alert alert-warning">
                        <i class="mdi mdi-alert me-2"></i>
                        <span id="selectedRejectCount">0</span> requests will be rejected. Students will be notified via email.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Reject Selected</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Bulk Mark Ready Modal -->
<div class="modal fade" id="bulkReadyModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Bulk Mark as Ready</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('admin.id-cards.bulk.ready') }}" id="bulkReadyForm">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="mdi mdi-information me-2"></i>
                        <span id="selectedReadyCount">0</span> ID cards will be marked as ready for collection. Students will be notified via email.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Mark Selected as Ready</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectAllCheckbox = document.getElementById('selectAll');
    const requestCheckboxes = document.querySelectorAll('.request-checkbox');
    const bulkApproveBtn = document.getElementById('bulkApproveBtn');
    const bulkRejectBtn = document.getElementById('bulkRejectBtn');
    const bulkReadyBtn = document.getElementById('bulkReadyBtn');

    // Handle select all
    selectAllCheckbox.addEventListener('change', function() {
        requestCheckboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
        updateBulkButtons();
    });

    // Handle individual checkboxes
    requestCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            updateSelectAll();
            updateBulkButtons();
        });
    });

    function updateSelectAll() {
        const checkedCount = document.querySelectorAll('.request-checkbox:checked').length;
        selectAllCheckbox.checked = checkedCount === requestCheckboxes.length;
        selectAllCheckbox.indeterminate = checkedCount > 0 && checkedCount < requestCheckboxes.length;
    }

    function updateBulkButtons() {
        const checkedBoxes = document.querySelectorAll('.request-checkbox:checked');
        const checkedCount = checkedBoxes.length;

        // Count by status
        const pendingCount = Array.from(checkedBoxes).filter(cb => cb.dataset.status === 'pending').length;
        const approvedPrintedCount = Array.from(checkedBoxes).filter(cb =>
            cb.dataset.status === 'approved' || cb.dataset.status === 'printed'
        ).length;

        // Update button states
        bulkApproveBtn.disabled = pendingCount === 0;
        bulkRejectBtn.disabled = pendingCount === 0;
        bulkReadyBtn.disabled = approvedPrintedCount === 0;

        // Update modal counts
        document.getElementById('selectedCount').textContent = pendingCount;
        document.getElementById('selectedRejectCount').textContent = pendingCount;
        document.getElementById('selectedReadyCount').textContent = approvedPrintedCount;
    }

    // Handle form submissions
    document.getElementById('bulkApproveForm').addEventListener('submit', function() {
        const checkedIds = Array.from(document.querySelectorAll('.request-checkbox:checked'))
            .filter(cb => cb.dataset.status === 'pending')
            .map(cb => cb.value);

        checkedIds.forEach(id => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'request_ids[]';
            input.value = id;
            this.appendChild(input);
        });
    });

    document.getElementById('bulkRejectForm').addEventListener('submit', function() {
        const checkedIds = Array.from(document.querySelectorAll('.request-checkbox:checked'))
            .filter(cb => cb.dataset.status === 'pending')
            .map(cb => cb.value);

        checkedIds.forEach(id => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'request_ids[]';
            input.value = id;
            this.appendChild(input);
        });
    });

    document.getElementById('bulkReadyForm').addEventListener('submit', function() {
        const checkedIds = Array.from(document.querySelectorAll('.request-checkbox:checked'))
            .filter(cb => cb.dataset.status === 'approved' || cb.dataset.status === 'printed')
            .map(cb => cb.value);

        checkedIds.forEach(id => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'request_ids[]';
            input.value = id;
            this.appendChild(input);
        });
    });
});
</script>

<style>
.avatar-xs {
    width: 32px;
    height: 32px;
    object-fit: cover;
}

.stats-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
}

.table th {
    border-top: none;
    font-weight: 600;
    text-transform: uppercase;
    font-size: 12px;
    letter-spacing: 0.5px;
}

.dropdown-toggle::after {
    display: none;
}

/* Ensure dropdowns are not clipped inside responsive tables */
.table-responsive {
    overflow: visible;
}

.card {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    border: 1px solid rgba(0, 0, 0, 0.125);
}
</style>
@endsection
