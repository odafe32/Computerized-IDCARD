@extends('layouts.student')

@section('content')
<div class="container-fluid">
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

    @if($existingRequest)
        <div class="alert alert-warning" role="alert">
            <i class="mdi mdi-information me-2"></i>
            <strong>Notice:</strong> You already have a {{ $existingRequest->status }} ID card request
            ({{ $existingRequest->request_number }}). You cannot submit a new request until this one is completed.
            <a href="{{ route('student.id-card.status') }}" class="alert-link">Check Status</a>
        </div>
    @endif

    <div class="row justify-content-center">
        <div class="col-xl-8">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-4">
                        <i class="mdi mdi-clipboard-outline me-2"></i>ID Card Request Form
                    </h4>

                    @if(!$existingRequest)
                        <div class="alert alert-info" role="alert">
                            <i class="mdi mdi-information-outline me-2"></i>
                            <strong>Photo Requirements:</strong>
                            <ul class="mb-0 mt-2">
                                <li>Passport-size photo (minimum 300x400 pixels)</li>
                                <li>Clear, recent photograph with white background</li>
                                <li>Face should be clearly visible</li>
                                <li>File formats: JPEG, PNG, JPG only</li>
                                <li>Maximum file size: 2MB</li>
                            </ul>
                        </div>

                        <form action="{{ route('student.id-card.submit') }}" method="POST" enctype="multipart/form-data" id="requestForm">
                            @csrf

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="card border">
                                        <div class="card-header bg-light">
                                            <h5 class="card-title mb-0">Student Information</h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="mb-3">
                                                <label class="form-label">Full Name</label>
                                                <input type="text" class="form-control" value="{{ $user->name }}" readonly>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Matric Number</label>
                                                <input type="text" class="form-control" value="{{ $user->matric_no }}" readonly>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Department</label>
                                                <input type="text" class="form-control" value="{{ $user->department }}" readonly>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Email</label>
                                                <input type="email" class="form-control" value="{{ $user->email }}" readonly>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="card border">
                                        <div class="card-header bg-light">
                                            <h5 class="card-title mb-0">Request Details</h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="mb-3">
                                                <label for="reason" class="form-label">Reason for Request <span class="text-danger">*</span></label>
                                                <select class="form-select @error('reason') is-invalid @enderror" id="reason" name="reason" required>
                                                    <option value="">Select Reason</option>
                                                    <option value="new" {{ old('reason') === 'new' ? 'selected' : '' }}>New Student</option>
                                                    <option value="replacement" {{ old('reason') === 'replacement' ? 'selected' : '' }}>Replacement</option>
                                                    <option value="lost" {{ old('reason') === 'lost' ? 'selected' : '' }}>Lost ID Card</option>
                                                    <option value="damaged" {{ old('reason') === 'damaged' ? 'selected' : '' }}>Damaged ID Card</option>
                                                </select>
                                                @error('reason')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="mb-3">
                                                <label for="photo" class="form-label">Upload Photo <span class="text-danger">*</span></label>
                                                <input type="file"
                                                       class="form-control @error('photo') is-invalid @enderror"
                                                       id="photo"
                                                       name="photo"
                                                       accept="image/jpeg,image/png,image/jpg"
                                                       required>
                                                @error('photo')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label">Photo Preview</label>
                                                <div class="border rounded p-3 text-center" id="photoPreview" style="min-height: 200px;">
                                                    <i class="mdi mdi-image-outline font-size-48 text-muted"></i>
                                                    <p class="text-muted mt-2">No photo selected</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-4">
                                <div class="col-12">
                                    <div class="d-flex justify-content-between">
                                        <a href="{{ route('student.dashboard') }}" class="btn btn-secondary">
                                            <i class="mdi mdi-arrow-left me-1"></i>Cancel
                                        </a>
                                        <button type="submit" class="btn btn-primary" id="submitBtn">
                                            <span class="btn-text">
                                                <i class="mdi mdi-send me-1"></i>Submit Request
                                            </span>
                                            <span class="spinner-border spinner-border-sm d-none" role="status"></span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const photoInput = document.getElementById('photo');
    const photoPreview = document.getElementById('photoPreview');
    const form = document.getElementById('requestForm');
    const submitBtn = document.getElementById('submitBtn');

    // Photo preview
    if (photoInput) {
        photoInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    photoPreview.innerHTML = `
                        <img src="${e.target.result}"
                             alt="Photo Preview"
                             class="img-fluid rounded"
                             style="max-height: 200px; max-width: 150px; object-fit: cover;">
                        <p class="text-success mt-2 mb-0">
                            <i class="mdi mdi-check-circle"></i> Photo selected
                        </p>
                    `;
                };
                reader.readAsDataURL(file);
            }
        });
    }

    // Form submission
    if (form && submitBtn) {
        form.addEventListener('submit', function() {
            submitBtn.disabled = true;
            submitBtn.querySelector('.btn-text').innerHTML = '<i class="mdi mdi-loading mdi-spin me-1"></i>Submitting...';
            submitBtn.querySelector('.spinner-border').classList.remove('d-none');
        });
    }
});
</script>
@endsection
