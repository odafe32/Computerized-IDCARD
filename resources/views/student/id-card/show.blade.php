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
            @if(isset($idCardRequest) && $idCardRequest && $idCardRequest->canBeDownloaded())
                <div class="card no-print">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h4 class="card-title mb-0">
                                <i class="mdi mdi-card-account-details me-2"></i>Student ID Card
                            </h4>
                            <div>
                                <a href="{{ route('student.id-card.download', $idCardRequest->id) }}"
                                   class="btn btn-success me-2">
                                    <i class="mdi mdi-download me-1"></i>Download PDF
                                </a>
                                <button onclick="printCard()" class="btn btn-primary">
                                    <i class="mdi mdi-printer me-1"></i>Print Card
                                </button>
                                <button onclick="window.print()" class="btn btn-outline-secondary">
                                    <i class="mdi mdi-printer me-1"></i>Print Page
                                </button>
                            </div>
                        </div>

                        <!-- Print Instructions -->
                        <div class="alert alert-info no-print">
                            <i class="mdi mdi-information me-2"></i>
                            <strong>Print Instructions:</strong>
                            For best results, use "Print Card" button and set your printer to:
                            <ul class="mb-0 mt-2">
                                <li>Paper size: A4 or Letter</li>
                                <li>Print quality: High/Best</li>
                                <li>Color: Enabled</li>
                                <li>Margins: Minimum</li>
                            </ul>
                        </div>

                        <!-- ID Card Preview -->
                        <div class="row justify-content-center">
                            <div class="col-md-8">
                                <div class="id-card-container">
                                    <div class="id-card" id="printableCard">
                                        <!-- Front Side -->
                                        <div class="id-card-front">
                                            <div class="card-header-section">
                                                <img src="{{ url('favicon.ico') }}" alt="University Logo" class="university-logo">
                                                <div class="university-info">
                                                    <h5>LEXA UNIVERSITY</h5>
                                                    <p>STUDENT IDENTIFICATION CARD</p>
                                                </div>
                                            </div>

                                            <div class="student-info-section">
                                                <div class="photo-section">
                                                    <img src="{{ $idCardRequest->photo_url }}" alt="Student Photo" class="student-photo">
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
                                                        <span class="value">{{ Str::limit($user->department, 20) }}</span>
                                                    </div>
                                                    <div class="info-row">
                                                        <span class="label">Card No:</span>
                                                        <span class="value">{{ $idCardRequest->card_number }}</span>
                                                    </div>
                                                    <div class="info-row">
                                                        <span class="label">Valid Until:</span>
                                                        <span class="value">{{ now()->addYears(4)->format('M Y') }}</span>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="qr-section">
                                                @if($idCardRequest->qr_code_url)
                                                    <img src="{{ $idCardRequest->qr_code_url }}" alt="QR Code" class="qr-code-img">
                                                @else
                                                    <div class="qr-placeholder">
                                                        <i class="mdi mdi-qrcode font-size-24"></i>
                                                    </div>
                                                @endif
                                            </div>

                                            <!-- Card Footer -->
                                            <div class="card-footer-section">
                                                <div class="issued-info">
                                                    <small>Issued: {{ $idCardRequest->printed_at ? $idCardRequest->printed_at->format('M d, Y') : now()->format('M d, Y') }}</small>
                                                </div>
                                                <div class="security-strip"></div>
                                            </div>
                                        </div>

                                        <!-- Back Side (Optional) -->
                                        <div class="id-card-back">
                                            <div class="back-header">
                                                <h6>TERMS & CONDITIONS</h6>
                                            </div>
                                            <div class="back-content">
                                                <ul>
                                                    <li>This card is property of Lexa University</li>
                                                    <li>Must be carried at all times on campus</li>
                                                    <li>Report lost cards immediately</li>
                                                    <li>Non-transferable</li>
                                                </ul>
                                            </div>
                                            <div class="back-footer">
                                                <div class="contact-info">
                                                    <small>
                                                        <strong>Student Services:</strong><br>
                                                        Email: student@lexauniversity.edu<br>
                                                        Phone: +1 (555) 123-4567
                                                    </small>
                                                </div>
                                                <div class="signature-strip">
                                                    <small>Authorized Signature</small>
                                                    <div class="signature-line"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Card Actions -->
                        <div class="row mt-4 no-print">
                            <div class="col-12 text-center">
                                <button onclick="flipCard()" class="btn btn-outline-info me-2">
                                    <i class="mdi mdi-rotate-3d-variant me-1"></i>Flip Card
                                </button>
                                <button onclick="downloadCardImage()" class="btn btn-outline-success">
                                    <i class="mdi mdi-image me-1"></i>Save as Image
                                </button>
                            </div>
                        </div>

                        <!-- Request Information -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="card bg-light no-print">
                                    <div class="card-body">
                                        <h6 class="card-title">Request Information</h6>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <p><strong>Request Number:</strong><br>{{ $idCardRequest->request_number }}</p>
                                            </div>
                                            <div class="col-md-3">
                                                <p><strong>Status:</strong><br>
                                                    <span class="badge {{ $idCardRequest->status_badge }}">{{ ucfirst($idCardRequest->status) }}</span>
                                                </p>
                                            </div>
                                            <div class="col-md-3">
                                                <p><strong>Issued Date:</strong><br>{{ $idCardRequest->printed_at ? $idCardRequest->printed_at->format('M d, Y') : 'N/A' }}</p>
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
                            @if(!isset($idCardRequest) || !$idCardRequest)
                                You haven't submitted any ID card requests yet.
                            @else
                                Your ID card request is still being processed.
                            @endif
                        </p>
                        <div class="mt-3">
                            @if(!isset($idCardRequest) || !$idCardRequest)
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

<script>
let isFlipped = false;

function printCard() {
    // Create a new window for printing just the card
    const printWindow = window.open('', '_blank', 'width=800,height=600');
    const cardElement = document.getElementById('printableCard');

    printWindow.document.write(`
        <!DOCTYPE html>
        <html>
        <head>
            <title>Student ID Card - {{ $user->name }}</title>
            <style>
                * {
                    margin: 0;
                    padding: 0;
                    box-sizing: border-box;
                }

                body {
                    font-family: Arial, sans-serif;
                    background: white;
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    min-height: 100vh;
                    padding: 20px;
                }

                .id-card {
                    width: 350px;
                    height: 220px;
                    position: relative;
                    margin: 0 auto;
                }

                .id-card-front {
                    width: 100%;
                    height: 100%;
                    background: linear-gradient(135deg, #00146d 0%, #21083b 100%);
                    border-radius: 15px;
                    padding: 15px;
                    color: white;
                    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
                    position: relative;
                    -webkit-print-color-adjust: exact;
                    color-adjust: exact;
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
                    color: white;
                }

                .university-info p {
                    margin: 0;
                    font-size: 10px;
                    opacity: 0.9;
                    color: white;
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
                    color: white;
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

                .qr-code-img {
                    width: 40px;
                    height: 40px;
                    border-radius: 5px;
                    background: white;
                    padding: 2px;
                }

                .qr-placeholder {
                    width: 40px;
                    height: 40px;
                    background: rgba(255,255,255,0.2);
                    border-radius: 5px;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    color: white;
                }

                .card-footer-section {
                    position: absolute;
                    bottom: 15px;
                    left: 15px;
                    right: 80px;
                }

                .issued-info {
                    font-size: 9px;
                    opacity: 0.8;
                    color: white;
                }

                .security-strip {
                    width: 100%;
                    height: 3px;
                    background: linear-gradient(90deg, #ffd700, #ff6b6b, #4ecdc4);
                    border-radius: 2px;
                    margin-top: 5px;
                }

                /* Print specific styles */
                @media print {
                    body {
                        margin: 0;
                        padding: 0;
                    }

                    .id-card {
                        page-break-inside: avoid;
                        box-shadow: none !important;
                    }

                    .id-card-front {
                        box-shadow: none !important;
                    }
                }
            </style>
        </head>
        <body>
            ${cardElement.outerHTML}
        </body>
        </html>
    `);

    printWindow.document.close();

    // Wait for images to load before printing
    printWindow.onload = function() {
        setTimeout(() => {
            printWindow.print();
            printWindow.close();
        }, 500);
    };
}

function flipCard() {
    const card = document.querySelector('.id-card');
    isFlipped = !isFlipped;

    if (isFlipped) {
        card.style.transform = 'rotateY(180deg)';
        document.querySelector('.id-card-front').style.display = 'none';
        document.querySelector('.id-card-back').style.display = 'block';
    } else {
        card.style.transform = 'rotateY(0deg)';
        document.querySelector('.id-card-front').style.display = 'block';
        document.querySelector('.id-card-back').style.display = 'none';
    }
}

function downloadCardImage() {
    // Use html2canvas to convert the card to image
    const cardElement = document.getElementById('printableCard');

    // Create a temporary canvas
    html2canvas(cardElement, {
        backgroundColor: null,
        scale: 2,
        useCORS: true,
        allowTaint: true
    }).then(canvas => {
        // Create download link
        const link = document.createElement('a');
        link.download = 'student-id-card-{{ $user->matric_no }}.png';
        link.href = canvas.toDataURL();
        link.click();
    }).catch(error => {
        console.error('Error generating image:', error);
        alert('Failed to generate image. Please try the PDF download instead.');
    });
}
</script>

<!-- Add html2canvas library for image download -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>

<style>
/* Main card styling */
.id-card-container {
    perspective: 1000px;
    margin: 20px 0;
}

.id-card {
    width: 350px;
    height: 220px;
    position: relative;
    margin: 0 auto;
    transition: transform 0.6s;
    transform-style: preserve-3d;
}

.id-card-front, .id-card-back {
    width: 100%;
    height: 100%;
    position: absolute;
    backface-visibility: hidden;
    border-radius: 15px;
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
}

.id-card-front {
    background: linear-gradient(135deg, #00146d 0%, #21083b 100%);
    color: white;
    padding: 15px;
    position: relative;
}

.id-card-back {
    background: linear-gradient(135deg, #21083b 0%, #00146d 100%);
    color: white;
    padding: 15px;
    transform: rotateY(180deg);
    display: none;
}

/* Header section */
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

/* Student info section */
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

/* QR section */
.qr-section {
    position: absolute;
    bottom: 15px;
    right: 15px;
}

.qr-code-img {
    width: 40px;
    height: 40px;
    border-radius: 5px;
    background: white;
    padding: 2px;
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

/* Footer section */
.card-footer-section {
    position: absolute;
    bottom: 15px;
    left: 15px;
    right: 80px;
}

.issued-info {
    font-size: 9px;
    opacity: 0.8;
}

.security-strip {
    width: 100%;
    height: 3px;
    background: linear-gradient(90deg, #ffd700, #ff6b6b, #4ecdc4);
    border-radius: 2px;
    margin-top: 5px;
}

/* Back side styling */
.back-header {
    text-align: center;
    margin-bottom: 15px;
    padding-bottom: 10px;
    border-bottom: 1px solid rgba(255,255,255,0.3);
}

.back-header h6 {
    margin: 0;
    font-size: 12px;
    font-weight: bold;
}

.back-content {
    margin-bottom: 20px;
}

.back-content ul {
    list-style: none;
    padding: 0;
    margin: 0;
}

.back-content li {
    font-size: 9px;
    margin-bottom: 5px;
    padding-left: 10px;
    position: relative;
}

.back-content li:before {
    content: "•";
    position: absolute;
    left: 0;
    color: #ffd700;
}

.back-footer {
    position: absolute;
    bottom: 15px;
    left: 15px;
    right: 15px;
}

.contact-info {
    font-size: 8px;
    margin-bottom: 10px;
}

.signature-strip {
    text-align: center;
}

.signature-strip small {
    font-size: 8px;
    opacity: 0.8;
}

.signature-line {
    width: 100px;
    height: 1px;
    background: rgba(255,255,255,0.5);
    margin: 5px auto 0;
}

/* Print styles */
@media print {
    .no-print {
        display: none !important;
    }

    body {
        background: white !important;
    }

    .id-card {
        page-break-inside: avoid;
        box-shadow: none !important;
    }

    .id-card-front {
        -webkit-print-color-adjust: exact;
        color-adjust: exact;
        box-shadow: none !important;
    }

    .security-strip {
        -webkit-print-color-adjust: exact;
        color-adjust: exact;
    }
}

/* Responsive design */
@media (max-width: 768px) {
    .id-card {
        width: 300px;
        height: 190px;
    }

    .student-photo {
        width: 60px;
        height: 75px;
    }

    .university-logo {
        width: 35px;
        height: 35px;
    }

    .university-info h5 {
        font-size: 12px;
    }

    .info-row {
        font-size: 10px;
    }

    .qr-code-img {
        width: 35px;
        height: 35px;
    }
}

/* Hover effects */
.id-card:hover {
    transform: translateY(-5px);
    transition: transform 0.3s ease;
}

/* Animation for card flip */
.id-card.flipped {
    transform: rotateY(180deg);
}

/* Button styling */
.btn {
    transition: all 0.2s ease;
}

.btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

/* Alert styling */
.alert-info {
    border-left: 4px solid #17a2b8;
    background: linear-gradient(135deg, #d1ecf1 0%, #bee5eb 100%);
}

/* Card container hover effect */
.id-card-container:hover .id-card {
    box-shadow: 0 12px 35px rgba(0,0,0,0.2);
}

/* Loading animation for image generation */
.generating {
    opacity: 0.7;
    pointer-events: none;
}

.generating::after {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 20px;
    height: 20px;
    margin: -10px 0 0 -10px;
    border: 2px solid #fff;
    border-top-color: transparent;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

/* Success animation */
.print-success {
    animation: pulse 0.5s ease-in-out;
}

@keyframes pulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.05); }
    100% { transform: scale(1); }
}

/* Error state */
.print-error {
    border: 2px solid #dc3545;
    animation: shake 0.5s ease-in-out;
}

@keyframes shake {
    0%, 100% { transform: translateX(0); }
    25% { transform: translateX(-5px); }
    75% { transform: translateX(5px); }
}
</style>
@endsection
