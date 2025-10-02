@extends('layouts.student')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">My ID Card</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('student.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">My ID Card</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-xl-10">
            @if($request && $request->canBeDownloaded())
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h4 class="card-title mb-0">
                                <i class="mdi mdi-card-account-details me-2"></i>Student ID Card
                            </h4>
                            <div>
                                <a href="{{ route('student.id-card.download') }}"
                                   class="btn btn-success me-2">
                                    <i class="mdi mdi-download me-1"></i>Download PDF
                                </a>
                                <button onclick="window.print()" class="btn btn-primary">
                                    <i class="mdi mdi-printer me-1"></i>Print
                                </button>
                            </div>
                        </div>

                        <!-- ID Card Preview -->
                        <div class="row justify-content-center">
                            <div class="col-md-6">
                                <div class="id-card-container">
                                    <div class="id-card">
                                        <!-- Front Side -->
                                        <div class="id-card-front">
                                            <div class="card-header-section">
                                                <img src="{{ url('logo-dark.png') }}" alt="University Logo" class="university-logo">
                                                <div class="university-info">
                                                    <h5>LEXA UNIVERSITY</h5>
                                                    <p>STUDENT IDENTIFICATION CARD</p>
                                                </div>
                                            </div>

                                            <div class="student-info-section">
                                                <div class="photo-section">
                                                    <img src="{{ $request->photo_url }}" alt="Student Photo" class="student-photo">
                                                </div>

                                                <div class="details-section">
                                                    <div class="info-row">
                                                        <span class="label">Name:</span>
                                                        <span class="value">{{ $user->name }}</span>
                                                    </div>
                                                    <div class="info-row">
                                                        <span class="label">Matric No:</span>
                                                        <span class="value">{{ $user->matric_no }}</span>
                                                    </div>
                                                    <div class="info-row">
                                                        <span class="label">Department:</span>
                                                        <span class="value">{{ $user->department }}</span>
                                                    </div>
                                                    <div class="info-row">
                                                        <span class="label">Valid Until:</span>
                                                        <span class="value">{{ now()->addYears(4)->format('M Y') }}</span>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="qr-section">
                                                <div class="qr-code">
                                                    <!-- QR Code would be generated here -->
                                                    <div class="qr-placeholder">
                                                        <i class="mdi mdi-qrcode font-size-24"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Request Information -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h6 class="card-title">Request Information</h6>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <p><strong>Request Number:</strong><br>{{ $request->request_number }}</p>
                                            </div>
                                            <div class="col-md-3">
                                                <p><strong>Status:</strong><br>
                                                    <span class="badge {{ $request->status_badge }}">{{ ucfirst($request->status) }}</span>
                                                </p>
                                            </div>
                                            <div class="col-md-3">
                                                <p><strong>Issued Date:</strong><br>{{ $request->printed_at ? $request->printed_at->format('M d, Y') : 'N/A' }}</p>
                                            </div>
                                            <div class="col-md-3">
                                                <p><strong>Valid Until:</strong><br>{{ now()->addYears(4)->format('M d, Y') }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="mdi mdi-card-account-details-outline font-size-48 text-muted"></i>
                        <h5 class="mt-3">No ID Card Available</h5>
                        <p class="text-muted">
                            @if(!$request)
                                You haven't submitted any ID card requests yet.
                            @else
                                Your ID card request is still being processed.
                            @endif
                        </p>
                        <div class="mt-3">
                            @if(!$request)
                                <a href="{{ route('student.id-card.request') }}" class="btn btn-primary me-2">
                                    <i class="mdi mdi-plus me-1"></i>Request ID Card
                                </a>
                            @endif
                            <a href="{{ route('student.id-card.status') }}" class="btn btn-outline-primary">
                                <i class="mdi mdi-eye me-1"></i>Check Status
                            </a>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<style>
.id-card-container {
    perspective: 1000px;
    margin: 20px 0;
}

.id-card {
    width: 350px;
    height: 220px;
    margin: 0 auto;
    position: relative;
    transform-style: preserve-3d;
    transition: transform 0.6s;
}

.id-card-front {
    position: absolute;
    width: 100%;
    height: 100%;
    backface-visibility: hidden;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 15px;
    padding: 15px;
    color: white;
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
}

.card-header-section {
    display: flex;
    align-items: center;
    margin-bottom: 15px;
    padding-bottom: 10px;
    border-bottom: 1px solid rgba(255,255,255,0.3);
}

.university-logo {
    width: 40px;
    height: 40px;
    margin-right: 10px;
    filter: brightness(0) invert(1);
}

.university-info h5 {
    margin: 0;
    font-size: 14px;
    font-weight: bold;
}

.university-info p {
    margin: 0;
    font-size: 10px;
    opacity: 0.9;
}

.student-info-section {
    display: flex;
    gap: 15px;
    margin-bottom: 15px;
}

.student-photo {
    width: 70px;
    height: 85px;
    object-fit: cover;
    border-radius: 8px;
    border: 2px solid rgba(255,255,255,0.3);
}

.details-section {
    flex: 1;
}

.info-row {
    display: flex;
    margin-bottom: 5px;
    font-size: 11px;
}

.info-row .label {
    width: 70px;
    font-weight: bold;
    opacity: 0.9;
}

.info-row .value {
    flex: 1;
    font-weight: 500;
}

.qr-section {
    position: absolute;
    bottom: 15px;
    right: 15px;
}

.qr-placeholder {
    width: 40px;
    height: 40px;
    background: rgba(255,255,255,0.2);
    border-radius: 5px;
    display: flex;
    align-items: center;
    justify-content: center;
}

@media print {
    .btn, .breadcrumb, .page-title-box {
        display: none !important;
    }

    .id-card {
        box-shadow: none;
    }
}
</style>
@endsection
