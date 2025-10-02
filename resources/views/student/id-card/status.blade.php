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

    <div class="row">
        <div class="col-12">
            @if($requests->count() > 0)
                @foreach($requests as $request)
                    <div class="card mb-4">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div>
                                    <h5 class="card-title mb-1">
                                        Request #{{ $request->request_number }}
                                        <span class="badge {{ $request->status_badge }} ms-2">
                                            {{ ucfirst($request->status) }}
                                        </span>
                                    </h5>
                                    <p class="text-muted mb-0">
                                        <i class="mdi mdi-calendar me-1"></i>
                                        Submitted: {{ $request->created_at->format('M d, Y \a\t h:i A') }}
                                    </p>
                                </div>
                                @if($request->canBeDownloaded())
                                    <a href="{{ route('student.id-card.download') }}"
                                       class="btn btn-success btn-sm">
                                        <i class="mdi mdi-download me-1"></i>Download ID Card
                                    </a>
                                @endif
                            </div>

                            <div class="row">
                                <div class="col-md-8">
                                    <!-- Status Timeline -->
                                    <div class="timeline">
                                        <div class="timeline-item {{ $request->status === 'pending' ? 'active' : 'completed' }}">
                                            <div class="timeline-marker"></div>
                                            <div class="timeline-content">
                                                <h6>Request Submitted</h6>
                                                <p class="text-muted mb-0">{{ $request->created_at->format('M d, Y \a\t h:i A') }}</p>
                                            </div>
                                        </div>

                                        @if(in_array($request->status, ['approved', 'rejected', 'printed', 'ready', 'collected']))
                                            <div class="timeline-item {{ $request->status === 'approved' ? 'active' : 'completed' }}">
                                                <div class="timeline-marker"></div>
                                                <div class="timeline-content">
                                                    <h6>{{ $request->status === 'rejected' ? 'Request Rejected' : 'Request Approved' }}</h6>
                                                    <p class="text-muted mb-0">
                                                        {{ $request->reviewed_at ? $request->reviewed_at->format('M d, Y \a\t h:i A') : 'Processing...' }}
                                                    </p>
                                                    @if($request->reviewer)
                                                        <small class="text-muted">by {{ $request->reviewer->name }}</small>
                                                    @endif
                                                </div>
                                            </div>
                                        @endif

                                        @if(in_array($request->status, ['printed', 'ready', 'collected']))
                                            <div class="timeline-item {{ $request->status === 'printed' ? 'active' : 'completed' }}">
                                                <div class="timeline-marker"></div>
                                                <div class="timeline-content">
                                                    <h6>ID Card Printed</h6>
                                                    <p class="text-muted mb-0">
                                                        {{ $request->printed_at ? $request->printed_at->format('M d, Y \a\t h:i A') : 'In progress...' }}
                                                    </p>
                                                </div>
                                            </div>
                                        @endif

                                        @if(in_array($request->status, ['ready', 'collected']))
                                            <div class="timeline-item {{ $request->status === 'ready' ? 'active' : 'completed' }}">
                                                <div class="timeline-marker"></div>
                                                <div class="timeline-content">
                                                    <h6>Ready for Collection</h6>
                                                    <p class="text-muted mb-0">Available for download</p>
                                                </div>
                                            </div>
                                        @endif

                                        @if($request->status === 'collected')
                                            <div class="timeline-item completed">
                                                <div class="timeline-marker"></div>
                                                <div class="timeline-content">
                                                    <h6>Collected</h6>
                                                    <p class="text-muted mb-0">
                                                        {{ $request->collected_at ? $request->collected_at->format('M d, Y \a\t h:i A') : 'Recently' }}
                                                    </p>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="card bg-light">
                                        <div class="card-body">
                                            <h6 class="card-title">Request Details</h6>
                                            <p><strong>Reason:</strong> {{ ucfirst($request->reason) }}</p>

                                            @if($request->photo)
                                                <p><strong>Photo:</strong></p>
                                                <img src="{{ $request->photo_url }}"
                                                     alt="Submitted Photo"
                                                     class="img-thumbnail"
                                                     style="max-width: 100px; max-height: 120px; object-fit: cover;">
                                            @endif

                                            @if($request->admin_feedback)
                                                <div class="mt-3">
                                                    <p><strong>Admin Feedback:</strong></p>
                                                    <div class="alert alert-{{ $request->status === 'rejected' ? 'danger' : 'info' }} p-2">
                                                        {{ $request->admin_feedback }}
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="mdi mdi-clipboard-outline font-size-48 text-muted"></i>
                        <h5 class="mt-3">No ID Card Requests</h5>
                        <p class="text-muted">You haven't submitted any ID card requests yet.</p>
                        <a href="{{ route('student.id-card.request') }}" class="btn btn-primary">
                            <i class="mdi mdi-plus me-1"></i>Submit New Request
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

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
    margin-bottom: 20px;
}

.timeline-marker {
    position: absolute;
    left: -23px;
    top: 5px;
    width: 16px;
    height: 16px;
    border-radius: 50%;
    background: #e9ecef;
    border: 3px solid #fff;
    box-shadow: 0 0 0 2px #e9ecef;
}

.timeline-item.active .timeline-marker {
    background: #ffc107;
    box-shadow: 0 0 0 2px #ffc107;
}

.timeline-item.completed .timeline-marker {
    background: #28a745;
    box-shadow: 0 0 0 2px #28a745;
}

.timeline-content h6 {
    margin-bottom: 5px;
    font-weight: 600;
}
</style>
@endsection
