@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <!-- Page Title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">Edit Student</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">Students</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.users.show', $student) }}">{{ $student->name }}</a></li>
                        <li class="breadcrumb-item active">Edit</li>
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

    <div class="row justify-content-center">
        <div class="col-xl-8">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-4">
                        <div class="flex-shrink-0 me-3">
                            <img src="{{ $student->photo_url }}"
                                 alt="{{ $student->name }}"
                                 class="avatar-md rounded-circle">
                        </div>
                        <div class="flex-grow-1">
                            <h4 class="card-title mb-1">
                                <i class="mdi mdi-account-edit me-2"></i>Edit Student Information
                            </h4>
                            <p class="text-muted mb-0">Update {{ $student->name }}'s account details</p>
                        </div>
                    </div>

                    <form action="{{ route('admin.users.update', $student) }}" method="POST" id="editStudentForm">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <!-- Personal Information -->
                            <div class="col-md-6">
                                <div class="card border">
                                    <div class="card-header bg-light">
                                        <h5 class="card-title mb-0">Personal Information</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
                                            <input type="text"
                                                   class="form-control @error('name') is-invalid @enderror"
                                                   id="name"
                                                   name="name"
                                                   value="{{ old('name', $student->name) }}"
                                                   placeholder="Enter student's full name"
                                                   required>
                                            @error('name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                                            <input type="email"
                                                   class="form-control @error('email') is-invalid @enderror"
                                                   id="email"
                                                   name="email"
                                                   value="{{ old('email', $student->email) }}"
                                                   placeholder="Enter email address"
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
                                                   value="{{ old('phone', $student->phone) }}"
                                                   placeholder="Enter phone number">
                                            @error('phone')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Academic Information -->
                            <div class="col-md-6">
                                <div class="card border">
                                    <div class="card-header bg-light">
                                        <h5 class="card-title mb-0">Academic Information</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="matric_no" class="form-label">Matric Number <span class="text-danger">*</span></label>
                                            <input type="text"
                                                   class="form-control @error('matric_no') is-invalid @enderror"
                                                   id="matric_no"
                                                   name="matric_no"
                                                   value="{{ old('matric_no', $student->matric_no) }}"
                                                   placeholder="Enter matric number"
                                                   required>
                                            @error('matric_no')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="department" class="form-label">Department <span class="text-danger">*</span></label>
                                            <select class="form-select @error('department') is-invalid @enderror"
                                                    id="department"
                                                    name="department"
                                                    required>
                                                <option value="">Select Department</option>
                                                @foreach($departments as $dept)
                                                    <option value="{{ $dept }}" {{ old('department', $student->department) === $dept ? 'selected' : '' }}>
                                                        {{ $dept }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('department')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="status" class="form-label">Account Status <span class="text-danger">*</span></label>
                                            <select class="form-select @error('status') is-invalid @enderror"
                                                    id="status"
                                                    name="status"
                                                    required>
                                                <option value="">Select Status</option>
                                                <option value="active" {{ old('status', $student->status) === 'active' ? 'selected' : '' }}>Active</option>
                                                <option value="inactive" {{ old('status', $student->status) === 'inactive' ? 'selected' : '' }}>Inactive</option>
                                                <option value="suspended" {{ old('status', $student->status) === 'suspended' ? 'selected' : '' }}>Suspended</option>
                                            </select>
                                            @error('status')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Password Section (Optional) -->
                        <div class="row mt-3">
                            <div class="col-12">
                                <div class="card border">
                                    <div class="card-header bg-light">
                                        <h5 class="card-title mb-0">Change Password (Optional)</h5>
                                        <small class="text-muted">Leave blank to keep current password</small>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="password" class="form-label">New Password</label>
                                                    <div class="input-group">
                                                        <input type="password"
                                                               class="form-control @error('password') is-invalid @enderror"
                                                               id="password"
                                                               name="password"
                                                               placeholder="Enter new password">
                                                        <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                                            <i class="mdi mdi-eye-outline" id="passwordIcon"></i>
                                                        </button>
                                                    </div>
                                                    @error('password')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                    <div class="form-text">
                                                        <small class="text-muted">Minimum 8 characters required</small>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="password_confirmation" class="form-label">Confirm New Password</label>
                                                    <div class="input-group">
                                                        <input type="password"
                                                               class="form-control @error('password_confirmation') is-invalid @enderror"
                                                               id="password_confirmation"
                                                               name="password_confirmation"
                                                               placeholder="Confirm new password">
                                                        <button class="btn btn-outline-secondary" type="button" id="togglePasswordConfirmation">
                                                            <i class="mdi mdi-eye-outline" id="passwordConfirmationIcon"></i>
                                                        </button>
                                                    </div>
                                                    @error('password_confirmation')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                    <div class="form-text">
                                                        <small class="text-muted" id="passwordMatchText"></small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Account Information Display -->
                        <div class="row mt-3">
                            <div class="col-12">
                                <div class="card border bg-light">
                                    <div class="card-body">
                                        <h6 class="card-title">Account Information</h6>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <p class="mb-1"><strong>Created:</strong></p>
                                                <p class="text-muted">{{ $student->created_at->format('M d, Y') }}</p>
                                            </div>
                                            <div class="col-md-3">
                                                <p class="mb-1"><strong>Last Login:</strong></p>
                                                <p class="text-muted">
                                                    {{ $student->last_login_at ? $student->last_login_at->format('M d, Y') : 'Never' }}
                                                </p>
                                            </div>
                                            <div class="col-md-3">
                                                <p class="mb-1"><strong>Email Verified:</strong></p>
                                                <p class="text-muted">
                                                    @if($student->email_verified_at)
                                                        <span class="badge bg-success">Verified</span>
                                                    @else
                                                        <span class="badge bg-warning">Pending</span>
                                                    @endif
                                                </p>
                                            </div>
                                            <div class="col-md-3">
                                                <p class="mb-1"><strong>ID Requests:</strong></p>
                                                <p class="text-muted">{{ $student->idCardRequests()->count() }} requests</p>
                                            </div>
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
                                        <p class="text-muted mb-0">
                                            <i class="mdi mdi-information-outline me-1"></i>
                                            All fields marked with <span class="text-danger">*</span> are required
                                        </p>
                                    </div>
                                    <div>
                                        <a href="{{ route('admin.users.show', $student) }}"
                                           class="btn btn-secondary waves-effect me-2">
                                            <i class="mdi mdi-arrow-left me-1"></i>Cancel
                                        </a>
                                        <button type="submit"
                                                class="btn btn-primary waves-effect waves-light"
                                                id="updateStudentBtn">
                                            <span class="btn-text">
                                                <i class="mdi mdi-content-save me-1"></i>Update Student
                                            </span>
                                            <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                                        </button>
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
    const form = document.getElementById('editStudentForm');
    const updateBtn = document.getElementById('updateStudentBtn');
    const btnText = updateBtn.querySelector('.btn-text');
    const spinner = updateBtn.querySelector('.spinner-border');

    // Password visibility toggles
    const toggleButtons = [
        {
            button: document.getElementById('togglePassword'),
            input: document.getElementById('password'),
            icon: document.getElementById('passwordIcon')
        },
        {
            button: document.getElementById('togglePasswordConfirmation'),
            input: document.getElementById('password_confirmation'),
            icon: document.getElementById('passwordConfirmationIcon')
        }
    ];

    toggleButtons.forEach(function(toggle) {
        if (toggle.button && toggle.input && toggle.icon) {
            toggle.button.addEventListener('click', function() {
                const type = toggle.input.getAttribute('type') === 'password' ? 'text' : 'password';
                toggle.input.setAttribute('type', type);

                if (type === 'text') {
                    toggle.icon.classList.remove('mdi-eye-outline');
                    toggle.icon.classList.add('mdi-eye-off-outline');
                } else {
                    toggle.icon.classList.remove('mdi-eye-off-outline');
                    toggle.icon.classList.add('mdi-eye-outline');
                }
            });
        }
    });

    // Password match checker
    const passwordInput = document.getElementById('password');
    const passwordConfirmInput = document.getElementById('password_confirmation');
    const matchText = document.getElementById('passwordMatchText');

    function checkPasswordMatch() {
        const password = passwordInput.value;
        const confirmPassword = passwordConfirmInput.value;

        if (password.length === 0 && confirmPassword.length === 0) {
            matchText.textContent = '';
            matchText.className = 'text-muted';
            passwordConfirmInput.classList.remove('is-invalid', 'is-valid');
            return;
        }

        if (confirmPassword.length === 0) {
            matchText.textContent = '';
            matchText.className = 'text-muted';
            return;
        }

        if (password === confirmPassword) {
            matchText.textContent = '✓ Passwords match';
            matchText.className = 'text-success';
            passwordConfirmInput.classList.remove('is-invalid');
            passwordConfirmInput.classList.add('is-valid');
        } else {
            matchText.textContent = '✗ Passwords do not match';
            matchText.className = 'text-danger';
            passwordConfirmInput.classList.remove('is-valid');
            passwordConfirmInput.classList.add('is-invalid');
        }
    }

    if (passwordInput && passwordConfirmInput && matchText) {
        passwordInput.addEventListener('input', checkPasswordMatch);
        passwordConfirmInput.addEventListener('input', checkPasswordMatch);
    }

    // Form submission with loading state
    form.addEventListener('submit', function(e) {
        // Check if passwords match before submitting (only if password is being changed)
        if (passwordInput.value && passwordInput.value !== passwordConfirmInput.value) {
            e.preventDefault();
            passwordConfirmInput.classList.add('is-invalid');
            return false;
        }

        updateBtn.disabled = true;
        btnText.innerHTML = '<i class="mdi mdi-loading mdi-spin me-1"></i>Updating Student...';
        spinner.classList.remove('d-none');
    });

    // Track changes
    const originalData = {
        name: '{{ $student->name }}',
        email: '{{ $student->email }}',
        phone: '{{ $student->phone }}',
        matric_no: '{{ $student->matric_no }}',
        department: '{{ $student->department }}',
        status: '{{ $student->status }}'
    };

    function hasChanges() {
        const currentData = {
            name: document.getElementById('name').value,
            email: document.getElementById('email').value,
            phone: document.getElementById('phone').value,
            matric_no: document.getElementById('matric_no').value,
            department: document.getElementById('department').value,
            status: document.getElementById('status').value
        };

        const passwordChanged = passwordInput.value.length > 0;

        return passwordChanged || Object.keys(originalData).some(key =>
            originalData[key] !== currentData[key]
        );
    }

    // Warn about unsaved changes
    window.addEventListener('beforeunload', function(e) {
        if (hasChanges()) {
            e.preventDefault();
            e.returnValue = '';
        }
    });

    // Remove warning when form is submitted
    form.addEventListener('submit', function() {
        window.removeEventListener('beforeunload', arguments.callee);
    });
});
</script>

<style>
.avatar-md {
    width: 60px;
    height: 60px;
    object-fit: cover;
}

.card {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    border: 1px solid rgba(0, 0, 0, 0.125);
}

.card-header {
    border-bottom: 1px solid rgba(0, 0, 0, 0.125);
}

.form-label {
    font-weight: 600;
    margin-bottom: 0.5rem;
}

.is-valid {
    border-color: #28a745;
}

.is-invalid {
    border-color: #dc3545;
}

.btn-text {
    display: inline-flex;
    align-items: center;
}

.spinner-border-sm {
    width: 1rem;
    height: 1rem;
}

.bg-light {
    background-color: #f8f9fa !important;
}
</style>
@endsection
