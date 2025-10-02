@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">Edit Profile</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.profile.show') }}">Profile</a></li>
                        <li class="breadcrumb-item active">Edit</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    {{-- Success/Error Messages --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="mdi mdi-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="mdi mdi-alert-circle me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-4">
                        <i class="mdi mdi-account-edit me-2"></i>Edit Profile Information
                    </h4>

                    <form action="{{ route('admin.profile.update') }}" method="POST" id="profileForm">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            {{-- Personal Information Section --}}
                            <div class="col-lg-6">
                                <div class="card border">
                                    <div class="card-header bg-light">
                                        <h5 class="card-title mb-0">
                                            <i class="mdi mdi-account me-2"></i>Personal Information
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
                                            <input type="text"
                                                   class="form-control @error('name') is-invalid @enderror"
                                                   id="name"
                                                   name="name"
                                                   value="{{ old('name', $user->name) }}"
                                                   placeholder="Enter your full name"
                                                   required>
                                            @error('name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>



                                    </div>
                                </div>
                            </div>

                            {{-- Contact Information Section --}}
                            <div class="col-lg-6">
                                <div class="card border">
                                    <div class="card-header bg-light">
                                        <h5 class="card-title mb-0">
                                            <i class="mdi mdi-contacts me-2"></i>Contact Information
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                                            <input type="email"
                                                   class="form-control @error('email') is-invalid @enderror"
                                                   id="email"
                                                   name="email"
                                                   value="{{ old('email', $user->email) }}"
                                                   placeholder="Enter your email address"
                                                   required>
                                            @error('email')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="phone" class="form-label">Phone Number</label>
                                            <input type="tel"
                                                   class="form-control @error('phone') is-invalid @enderror"
                                                   id="phone"
                                                   name="phone"
                                                   value="{{ old('phone', $user->phone) }}"
                                                   placeholder="Enter your phone number">
                                            <div class="form-text">
                                                <small class="text-muted">
                                                    <i class="mdi mdi-information-outline"></i>
                                                    Include country code (e.g., +1234567890)
                                                </small>
                                            </div>
                                            @error('phone')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        {{-- Account Status Display --}}
                                        <div class="mb-3">
                                            <label class="form-label">Account Status</label>
                                            <div>
                                                <span class="badge bg-{{ $user->status === 'active' ? 'success' : ($user->status === 'inactive' ? 'warning' : 'danger') }} fs-6">
                                                    {{ ucfirst($user->status) }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Form Actions --}}
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <p class="text-muted mb-0">
                                                    <i class="mdi mdi-information-outline me-1"></i>
                                                    Fields marked with <span class="text-danger">*</span> are required
                                                </p>
                                            </div>
                                            <div>
                                                <a href="{{ route('admin.profile.show') }}"
                                                   class="btn btn-secondary waves-effect me-2">
                                                    <i class="mdi mdi-arrow-left me-1"></i>Cancel
                                                </a>
                                                <button type="submit"
                                                        class="btn btn-primary waves-effect waves-light"
                                                        id="updateBtn">
                                                    <span class="btn-text">
                                                        <i class="mdi mdi-content-save me-1"></i>Update Profile
                                                    </span>
                                                    <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('profileForm');
    const updateBtn = document.getElementById('updateBtn');
    const btnText = updateBtn.querySelector('.btn-text');
    const spinner = updateBtn.querySelector('.spinner-border');

    // Form submission with loading state
    form.addEventListener('submit', function() {
        updateBtn.disabled = true;
        btnText.innerHTML = '<i class="mdi mdi-loading mdi-spin me-1"></i>Updating...';
        spinner.classList.remove('d-none');
    });

    // Auto-dismiss alerts
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(function(alert) {
        setTimeout(function() {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        }, 5000);
    });

    // Form validation feedback
    const inputs = form.querySelectorAll('input, select');
    inputs.forEach(function(input) {
        input.addEventListener('blur', function() {
            if (this.hasAttribute('required') && !this.value.trim()) {
                this.classList.add('is-invalid');
            } else {
                this.classList.remove('is-invalid');
                this.classList.add('is-valid');
            }
        });

        input.addEventListener('input', function() {
            if (this.classList.contains('is-invalid') && this.value.trim()) {
                this.classList.remove('is-invalid');
                this.classList.add('is-valid');
            }
        });
    });

    // Email validation
    const emailInput = document.getElementById('email');
    if (emailInput) {
        emailInput.addEventListener('blur', function() {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (this.value && !emailRegex.test(this.value)) {
                this.classList.add('is-invalid');
            }
        });
    }

    // Phone number formatting
    const phoneInput = document.getElementById('phone');
    if (phoneInput) {
        phoneInput.addEventListener('input', function() {
            // Remove non-numeric characters except + and spaces
            let value = this.value.replace(/[^\d+\s-()]/g, '');
            this.value = value;
        });
    }

    // Confirm navigation away from unsaved changes
    let formChanged = false;
    inputs.forEach(function(input) {
        input.addEventListener('change', function() {
            formChanged = true;
        });
    });

    window.addEventListener('beforeunload', function(e) {
        if (formChanged && !form.submitted) {
            e.preventDefault();
            e.returnValue = '';
        }
    });

    form.addEventListener('submit', function() {
        form.submitted = true;
    });
});
</script>
@endsection
