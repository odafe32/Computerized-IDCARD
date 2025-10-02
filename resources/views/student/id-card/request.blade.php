@extends('layouts.student')

@section('content')
<div class="container-fluid">
    <!-- Page Title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">Request ID Card</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('student.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Request ID Card</li>
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

    <!-- Pending Request Alert -->
    @if($pendingRequest)
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <i class="mdi mdi-clock-alert me-2"></i>
            <strong>Pending Request:</strong> You have a pending ID card request (#{{ $pendingRequest->request_number }})
            submitted on {{ $pendingRequest->created_at->format('M d, Y') }}.
            <a href="{{ route('student.id-card.status') }}" class="alert-link">Check status here</a>.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row justify-content-center">
        <div class="col-xl-10">
            <div class="card">
                <div class="card-body">
                    <div class="text-center mb-4">
                        <div class="avatar-lg mx-auto mb-3">
                            <div class="avatar-title bg-light text-primary rounded-circle">
                                <i class="mdi mdi-card-account-details font-size-24"></i>
                            </div>
                        </div>
                        <h4 class="card-title">ID Card Request Form</h4>
                        <p class="text-muted">Please fill out the form below to request your student ID card</p>
                    </div>

                    @if(!$pendingRequest)
                        <form action="{{ route('student.id-card.submit') }}" method="POST" enctype="multipart/form-data" id="idCardRequestForm">
                            @csrf

                            <div class="row">
                                <!-- Student Information (Read-only) -->
                                <div class="col-lg-6">
                                    <div class="card border">
                                        <div class="card-header bg-light">
                                            <h5 class="card-title mb-0">
                                                <i class="mdi mdi-account me-2"></i>Student Information
                                            </h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="text-center mb-3">
                                                <img src="{{ $user->photo_url }}"
                                                     alt="{{ $user->name }}"
                                                     class="avatar-lg rounded-circle img-thumbnail"
                                                     id="currentPhoto">
                                            </div>

                                            <div class="table-responsive">
                                                <table class="table table-sm table-nowrap mb-0">
                                                    <tbody>
                                                        <tr>
                                                            <th width="40%">Full Name:</th>
                                                            <td>{{ $user->name }}</td>
                                                        </tr>
                                                        <tr>
                                                            <th>Matric Number:</th>
                                                            <td><span class="badge bg-primary">{{ $user->matric_no }}</span></td>
                                                        </tr>
                                                        <tr>
                                                            <th>Email:</th>
                                                            <td>{{ $user->email }}</td>
                                                        </tr>
                                                        <tr>
                                                            <th>Department:</th>
                                                            <td>{{ $user->department }}</td>
                                                        </tr>
                                                        <tr>
                                                            <th>Phone:</th>
                                                            <td>{{ $user->phone ?: 'Not provided' }}</td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>

                                            @if(!$user->phone)
                                                <div class="alert alert-info mt-3">
                                                    <i class="mdi mdi-information me-2"></i>
                                                    <small>Consider updating your phone number in your
                                                    <a href="{{ route('student.profile.edit') }}" class="alert-link">profile</a>
                                                    for better communication.</small>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <!-- Request Details -->
                                <div class="col-lg-6">
                                    <div class="card border">
                                        <div class="card-header bg-light">
                                            <h5 class="card-title mb-0">
                                                <i class="mdi mdi-clipboard-text me-2"></i>Request Details
                                            </h5>
                                        </div>
                                        <div class="card-body">
                                            <!-- Reason for Request -->
                                            <div class="mb-3">
                                                <label for="reason" class="form-label">
                                                    Reason for Request <span class="text-danger">*</span>
                                                </label>
                                                <select class="form-select @error('reason') is-invalid @enderror"
                                                        id="reason"
                                                        name="reason"
                                                        required>
                                                    <option value="">Select a reason</option>
                                                    <option value="new" {{ old('reason') === 'new' ? 'selected' : '' }}>
                                                        New Student - First ID Card
                                                    </option>
                                                    <option value="replacement" {{ old('reason') === 'replacement' ? 'selected' : '' }}>
                                                        Replacement - Card Worn Out
                                                    </option>
                                                    <option value="lost" {{ old('reason') === 'lost' ? 'selected' : '' }}>
                                                        Lost Card
                                                    </option>
                                                    <option value="damaged" {{ old('reason') === 'damaged' ? 'selected' : '' }}>
                                                        Damaged Card
                                                    </option>
                                                    <option value="name_change" {{ old('reason') === 'name_change' ? 'selected' : '' }}>
                                                        Name Change
                                                    </option>
                                                </select>
                                                @error('reason')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <!-- Additional Information -->
                                            <div class="mb-3">
                                                <label for="additional_info" class="form-label">
                                                    Additional Information
                                                    <small class="text-muted">(Optional)</small>
                                                </label>
                                                <textarea class="form-control @error('additional_info') is-invalid @enderror"
                                                          id="additional_info"
                                                          name="additional_info"
                                                          rows="4"
                                                          placeholder="Please provide any additional details about your request..."
                                                          maxlength="1000">{{ old('additional_info') }}</textarea>
                                                @error('additional_info')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                                <div class="form-text">
                                                    <small class="text-muted">
                                                        <span id="charCount">0</span>/1000 characters
                                                    </small>
                                                </div>
                                            </div>

                                            <!-- Photo Upload -->
                                            <div class="mb-3">
                                                <label for="photo" class="form-label">
                                                    Upload New Photo
                                                    <small class="text-muted">(Optional - Current photo will be used if not provided)</small>
                                                </label>
                                                <input type="file"
                                                       class="form-control @error('photo') is-invalid @enderror"
                                                       id="photo"
                                                       name="photo"
                                                       accept="image/jpeg,image/png,image/jpg">
                                                @error('photo')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                                <div class="form-text">
                                                    <small class="text-muted">
                                                        <i class="mdi mdi-information me-1"></i>
                                                        Accepted formats: JPEG, PNG, JPG. Maximum size: 2MB.
                                                        <br>For best results, use a passport-style photo with plain background.
                                                    </small>
                                                </div>
                                            </div>

                                            <!-- Photo Preview -->
                                            <div class="mb-3" id="photoPreviewContainer" style="display: none;">
                                                <label class="form-label">Photo Preview</label>
                                                <div class="text-center">
                                                    <img id="photoPreview"
                                                         class="img-thumbnail"
                                                         style="max-width: 150px; max-height: 200px;">
                                                    <div class="mt-2">
                                                        <button type="button"
                                                                class="btn btn-sm btn-outline-danger"
                                                                id="removePhoto">
                                                            <i class="mdi mdi-close me-1"></i>Remove Photo
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Important Information -->
                            <div class="row mt-3">
                                <div class="col-12">
                                    <div class="card border-warning">
                                        <div class="card-header bg-warning bg-opacity-10">
                                            <h5 class="card-title mb-0 text-warning">
                                                <i class="mdi mdi-alert me-2"></i>Important Information
                                            </h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <h6 class="text-primary">Processing Time:</h6>
                                                    <ul class="list-unstyled mb-3">
                                                        <li><i class="mdi mdi-check text-success me-2"></i>Review: 1-2 business days</li>
                                                        <li><i class="mdi mdi-check text-success me-2"></i>Printing: 2-3 business days</li>
                                                        <li><i class="mdi mdi-check text-success me-2"></i>Total: 3-5 business days</li>
                                                    </ul>
                                                </div>
                                                <div class="col-md-6">
                                                    <h6 class="text-primary">Collection:</h6>
                                                    <ul class="list-unstyled mb-3">
                                                        <li><i class="mdi mdi-map-marker text-info me-2"></i>Student Affairs Office</li>
                                                        <li><i class="mdi mdi-clock text-info me-2"></i>Mon-Fri: 9:00 AM - 4:00 PM</li>
                                                        <li><i class="mdi mdi-card-account-details text-info me-2"></i>Bring valid identification</li>
                                                    </ul>
                                                </div>
                                            </div>
                                            <div class="alert alert-info mb-0">
                                                <i class="mdi mdi-information me-2"></i>
                                                <strong>Note:</strong> You will receive email notifications about your request status.
                                                Make sure your email address is up to date in your profile.
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Terms and Conditions -->
                            <div class="row mt-3">
                                <div class="col-12">
                                    <div class="card border">
                                        <div class="card-body">
                                            <div class="form-check">
                                                <input class="form-check-input"
                                                       type="checkbox"
                                                       id="terms"
                                                       name="terms"
                                                       required>
                                                <label class="form-check-label" for="terms">
                                                    I agree to the <a href="#" data-bs-toggle="modal" data-bs-target="#termsModal">terms and conditions</a>
                                                    and confirm that all information provided is accurate and complete.
                                                    I understand that providing false information may result in rejection of my request.
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Form Actions -->
                            <div class="row mt-4">
                                <div class="col-12">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <a href="{{ route('student.dashboard') }}"
                                               class="btn btn-secondary waves-effect">
                                                <i class="mdi mdi-arrow-left me-1"></i>Back to Dashboard
                                            </a>
                                        </div>
                                        <div>
                                            <button type="reset"
                                                    class="btn btn-outline-secondary waves-effect me-2">
                                                <i class="mdi mdi-refresh me-1"></i>Reset Form
                                            </button>
                                            <button type="submit"
                                                    class="btn btn-primary waves-effect waves-light"
                                                    id="submitBtn">
                                                <span class="btn-text">
                                                    <i class="mdi mdi-send me-1"></i>Submit Request
                                                </span>
                                                <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    @else
                        <!-- Show pending request info -->
                        <div class="text-center py-5">
                            <div class="avatar-lg mx-auto mb-3">
                                <div class="avatar-title bg-warning text-white rounded-circle">
                                    <i class="mdi mdi-clock-alert font-size-24"></i>
                                </div>
                            </div>
                            <h4>Request Already Submitted</h4>
                            <p class="text-muted mb-4">
                                You have a pending ID card request that is currently being processed.
                                You cannot submit a new request until the current one is completed.
                            </p>
                            <div class="d-flex justify-content-center gap-2">
                                <a href="{{ route('student.id-card.status') }}" class="btn btn-primary">
                                    <i class="mdi mdi-eye me-1"></i>Check Status
                                </a>
                                <a href="{{ route('student.dashboard') }}" class="btn btn-outline-secondary">
                                    <i class="mdi mdi-home me-1"></i>Go to Dashboard
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Terms and Conditions Modal -->
<div class="modal fade" id="termsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Terms and Conditions</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="terms-content">
                    <h6>ID Card Request Terms and Conditions</h6>
                    <ol>
                        <li><strong>Eligibility:</strong> Only registered students of Lexa University are eligible to request an ID card.</li>
                        <li><strong>Information Accuracy:</strong> All information provided must be accurate and complete. False information may result in request rejection.</li>
                        <li><strong>Photo Requirements:</strong> Photos must be recent, passport-style, with plain background. Inappropriate photos will be rejected.</li>
                        <li><strong>Processing Time:</strong> Standard processing time is 3-5 business days. Rush requests are not available.</li>
                        <li><strong>Collection:</strong> ID cards must be collected within 30 days of notification. Uncollected cards will be destroyed.</li>
                        <li><strong>Replacement Policy:</strong> Lost or damaged cards require a new request. Previous cards will be deactivated.</li>
                        <li><strong>Fees:</strong> First ID card is free. Replacement cards may incur fees as per university policy.</li>
                        <li><strong>Usage:</strong> ID cards are for university purposes only and remain property of Lexa University.</li>
                        <li><strong>Privacy:</strong> Personal information will be handled according to university privacy policy.</li>
                        <li><strong>Compliance:</strong> Students must comply with all university policies regarding ID card usage.</li>
                    </ol>
                    <p class="text-muted mt-3">
                        <small>By submitting this request, you acknowledge that you have read, understood, and agree to these terms and conditions.</small>
                    </p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal" onclick="document.getElementById('terms').checked = true;">
                    I Agree
                </button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('idCardRequestForm');
    const submitBtn = document.getElementById('submitBtn');
    const btnText = submitBtn?.querySelector('.btn-text');
    const spinner = submitBtn?.querySelector('.spinner-border');
    const photoInput = document.getElementById('photo');
    const photoPreview = document.getElementById('photoPreview');
    const photoPreviewContainer = document.getElementById('photoPreviewContainer');
    const removePhotoBtn = document.getElementById('removePhoto');
    const additionalInfoTextarea = document.getElementById('additional_info');
    const charCount = document.getElementById('charCount');
    const reasonSelect = document.getElementById('reason');

    // Character counter for additional info
    if (additionalInfoTextarea && charCount) {
        function updateCharCount() {
            const count = additionalInfoTextarea.value.length;
            charCount.textContent = count;

            if (count > 900) {
                charCount.classList.add('text-warning');
            } else if (count > 950) {
                charCount.classList.remove('text-warning');
                charCount.classList.add('text-danger');
            } else {
                charCount.classList.remove('text-warning', 'text-danger');
            }
        }

        additionalInfoTextarea.addEventListener('input', updateCharCount);
        updateCharCount(); // Initial count
    }

    // Photo preview functionality
    if (photoInput && photoPreview && photoPreviewContainer) {
        photoInput.addEventListener('change', function(e) {
            const file = e.target.files[0];

            if (file) {
                // Validate file size (2MB)
                if (file.size > 2 * 1024 * 1024) {
                    alert('File size must be less than 2MB');
                    photoInput.value = '';
                    return;
                }

                // Validate file type
                if (!['image/jpeg', 'image/png', 'image/jpg'].includes(file.type)) {
                    alert('Please select a valid image file (JPEG, PNG, JPG)');
                    photoInput.value = '';
                    return;
                }

                const reader = new FileReader();
                reader.onload = function(e) {
                    photoPreview.src = e.target.result;
                    photoPreviewContainer.style.display = 'block';
                };
                reader.readAsDataURL(file);
            } else {
                photoPreviewContainer.style.display = 'none';
            }
        });

        // Remove photo functionality
        if (removePhotoBtn) {
            removePhotoBtn.addEventListener('click', function() {
                photoInput.value = '';
                photoPreviewContainer.style.display = 'none';
            });
        }
    }

    // Dynamic additional info placeholder based on reason
    if (reasonSelect && additionalInfoTextarea) {
        const placeholders = {
            'new': 'Please provide any additional information about your enrollment or special circumstances...',
            'replacement': 'Please describe the condition of your current card and why it needs replacement...',
            'lost': 'Please provide details about when and where you lost your card. Consider filing a report if necessary...',
            'damaged': 'Please describe how your card was damaged and attach photos if possible...',
            'name_change': 'Please provide details about your name change and attach supporting documents...'
        };

        reasonSelect.addEventListener('change', function() {
            const selectedReason = this.value;
            if (placeholders[selectedReason]) {
                additionalInfoTextarea.placeholder = placeholders[selectedReason];
            } else {
                additionalInfoTextarea.placeholder = 'Please provide any additional details about your request...';
            }
        });
    }

    // Form submission with loading state
    if (form && submitBtn && btnText && spinner) {
        form.addEventListener('submit', function(e) {
            // Validate terms checkbox
            const termsCheckbox = document.getElementById('terms');
            if (!termsCheckbox.checked) {
                e.preventDefault();
                alert('Please accept the terms and conditions to proceed.');
                return false;
            }

            // Show loading state
            submitBtn.disabled = true;
            btnText.innerHTML = '<i class="mdi mdi-loading mdi-spin me-1"></i>Submitting Request...';
            spinner.classList.remove('d-none');
        });
    }

    // Form reset functionality
    const resetBtn = document.querySelector('button[type="reset"]');
    if (resetBtn) {
        resetBtn.addEventListener('click', function() {
            if (confirm('Are you sure you want to reset the form? All entered data will be lost.')) {
                if (photoPreviewContainer) {
                    photoPreviewContainer.style.display = 'none';
                }
                if (charCount) {
                    charCount.textContent = '0';
                    charCount.classList.remove('text-warning', 'text-danger');
                }
                if (additionalInfoTextarea) {
                    additionalInfoTextarea.placeholder = 'Please provide any additional details about your request...';
                }
            } else {
                return false;
            }
        });
    }

    // Auto-save form data to localStorage (optional)
    const formInputs = form?.querySelectorAll('input, select, textarea');
    if (formInputs) {
        formInputs.forEach(input => {
            // Load saved data
            const savedValue = localStorage.getItem(`idCardForm_${input.name}`);
            if (savedValue && input.type !== 'file' && input.type !== 'checkbox') {
                input.value = savedValue;
            }

            // Save data on change
            input.addEventListener('change', function() {
                if (this.type !== 'file') {
                    localStorage.setItem(`idCardForm_${this.name}`, this.value);
                }
            });
        });

        // Clear saved data on successful submission
        form.addEventListener('submit', function() {
            formInputs.forEach(input => {
                localStorage.removeItem(`idCardForm_${input.name}`);
            });
        });
    }
});
</script>

<style>
.avatar-lg {
    width: 80px;
    height: 80px;
    object-fit: cover;
}

.card {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    border: 1px solid rgba(0, 0, 0, 0.125);
    margin-bottom: 1.5rem;
}

.card-header {
    border-bottom: 1px solid rgba(0, 0, 0, 0.125);
}

.form-label {
    font-weight: 600;
    margin-bottom: 0.5rem;
}

.btn-text {
    display: inline-flex;
    align-items: center;
}

.spinner-border-sm {
    width: 1rem;
    height: 1rem;
}

.terms-content {
    max-height: 400px;
    overflow-y: auto;
}

.terms-content ol {
    padding-left: 1.5rem;
}

.terms-content li {
    margin-bottom: 0.5rem;
}

#photoPreview {
    border: 2px dashed #dee2e6;
    padding: 10px;
}

.bg-opacity-10 {
    background-color: rgba(var(--bs-warning-rgb), 0.1) !important;
}

@media (max-width: 768px) {
    .d-flex.justify-content-between {
        flex-direction: column;
        gap: 1rem;
    }

    .d-flex.justify-content-between > div {
        text-align: center;
    }
}
</style>
@endsection
