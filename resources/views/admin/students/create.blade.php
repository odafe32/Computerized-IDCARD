@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <!-- Page Title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">Add New Student</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">Students</a></li>
                        <li class="breadcrumb-item active">Add New</li>
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
                    <h4 class="card-title mb-4">
                        <i class="mdi mdi-account-plus me-2"></i>Student Information
                    </h4>

                    <form action="{{ route('admin.users.store') }}" method="POST" id="createStudentForm" novalidate>
                        @csrf

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
                                                   value="{{ old('name') }}"
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
                                                   value="{{ old('email') }}"
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
                                                   value="{{ old('phone') }}"
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
                                            <div class="input-group">
                                                <input type="text"
                                                       class="form-control @error('matric_no') is-invalid @enderror"
                                                       id="matric_no"
                                                       name="matric_no"
                                                       value="{{ old('matric_no') }}"
                                                       placeholder="Enter matric number"
                                                       required>
                                                <button class="btn btn-outline-secondary" type="button" id="generateMatricBtn">
                                                    <i class="mdi mdi-auto-fix"></i>
                                                </button>
                                            </div>
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
                                                    <option value="{{ $dept }}" {{ old('department') === $dept ? 'selected' : '' }}>
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
                                                <option value="active" {{ old('status', 'active') === 'active' ? 'selected' : '' }}>Active</option>
                                                <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                                                <option value="suspended" {{ old('status') === 'suspended' ? 'selected' : '' }}>Suspended</option>
                                            </select>
                                            @error('status')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Password Section -->
                        <div class="row mt-3">
                            <div class="col-12">
                                <div class="card border">
                                    <div class="card-header bg-light">
                                        <h5 class="card-title mb-0">Account Security</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                                                    <div class="input-group">
                                                        <input type="password"
                                                               class="form-control @error('password') is-invalid @enderror"
                                                               id="password"
                                                               name="password"
                                                               placeholder="Enter password"
                                                               minlength="8"
                                                               required>
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
                                                    <label for="password_confirmation" class="form-label">Confirm Password <span class="text-danger">*</span></label>
                                                    <div class="input-group">
                                                        <input type="password"
                                                               class="form-control"
                                                               id="password_confirmation"
                                                               name="password_confirmation"
                                                               placeholder="Confirm password"
                                                               minlength="8"
                                                               required>
                                                        <button class="btn btn-outline-secondary" type="button" id="togglePasswordConfirmation">
                                                            <i class="mdi mdi-eye-outline" id="passwordConfirmationIcon"></i>
                                                        </button>
                                                    </div>
                                                    <div class="form-text">
                                                        <small id="passwordMatchText" class="text-muted"></small>
                                                    </div>
                                                </div>
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
                                        <a href="{{ route('admin.users.index') }}"
                                           class="btn btn-secondary waves-effect me-2">
                                            <i class="mdi mdi-arrow-left me-1"></i>Cancel
                                        </a>
                                        <button type="submit"
                                                class="btn btn-primary waves-effect waves-light"
                                                id="createStudentBtn">
                                            <span class="btn-text">
                                                <i class="mdi mdi-account-plus me-1"></i>Create Student
                                            </span>
                                            <span class="spinner-border spinner-border-sm d-none ms-2" role="status" aria-hidden="true"></span>
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
    const form = document.getElementById('createStudentForm');
    const createBtn = document.getElementById('createStudentBtn');
    const btnText = createBtn.querySelector('.btn-text');
    const spinner = createBtn.querySelector('.spinner-border');

    // Password visibility toggles
    function setupPasswordToggle(toggleId, inputId, iconId) {
        const toggleBtn = document.getElementById(toggleId);
        const input = document.getElementById(inputId);
        const icon = document.getElementById(iconId);

        if (toggleBtn && input && icon) {
            toggleBtn.addEventListener('click', function() {
                const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
                input.setAttribute('type', type);

                if (type === 'text') {
                    icon.classList.remove('mdi-eye-outline');
                    icon.classList.add('mdi-eye-off-outline');
                } else {
                    icon.classList.remove('mdi-eye-off-outline');
                    icon.classList.add('mdi-eye-outline');
                }
            });
        }
    }

    setupPasswordToggle('togglePassword', 'password', 'passwordIcon');
    setupPasswordToggle('togglePasswordConfirmation', 'password_confirmation', 'passwordConfirmationIcon');

    // Password match checker
    const passwordInput = document.getElementById('password');
    const passwordConfirmInput = document.getElementById('password_confirmation');
    const matchText = document.getElementById('passwordMatchText');

    function checkPasswordMatch() {
        const password = passwordInput.value;
        const confirmPassword = passwordConfirmInput.value;

        if (confirmPassword.length === 0) {
            matchText.textContent = '';
            matchText.className = 'text-muted';
            passwordConfirmInput.classList.remove('is-invalid', 'is-valid');
            return true;
        }

        if (password === confirmPassword && password.length >= 8) {
            matchText.textContent = '✓ Passwords match';
            matchText.className = 'text-success';
            passwordConfirmInput.classList.remove('is-invalid');
            passwordConfirmInput.classList.add('is-valid');
            return true;
        } else {
            matchText.textContent = '✗ Passwords do not match';
            matchText.className = 'text-danger';
            passwordConfirmInput.classList.remove('is-valid');
            passwordConfirmInput.classList.add('is-invalid');
            return false;
        }
    }

    if (passwordInput && passwordConfirmInput && matchText) {
        passwordInput.addEventListener('input', checkPasswordMatch);
        passwordConfirmInput.addEventListener('input', checkPasswordMatch);
    }
// Generate matric number
const generateMatricBtn = document.getElementById('generateMatricBtn');
const matricInput = document.getElementById('matric_no');

if (generateMatricBtn && matricInput) {
    generateMatricBtn.addEventListener('click', function() {
        const year = new Date().getFullYear().toString().substr(-2);
        const random = Math.floor(Math.random() * 100000).toString().padStart(5, '0');
        const matricNumber = `${year}200${random}`;

        matricInput.value = matricNumber;
        matricInput.focus();

        // Add visual feedback
        matricInput.classList.add('is-valid');
        setTimeout(() => {
            matricInput.classList.remove('is-valid');
        }, 2000);
    });
}

// Email validation
const emailInput = document.getElementById('email');
if (emailInput) {
    emailInput.addEventListener('blur', function() {
        const email = this.value;
        if (email && !email.includes('@student.lexauniversity.edu')) {
            // Auto-suggest student email format
            const username = email.split('@')[0];
            this.value = `${username}@student.lexauniversity.edu`;
        }
    });
}

// Form submission handling
if (form && createBtn) {
    form.addEventListener('submit', function(e) {
        e.preventDefault();

        // Validate passwords match
        if (!checkPasswordMatch()) {
            alert('Please ensure passwords match before submitting.');
            return false;
        }

        // Show loading state
        createBtn.disabled = true;
        btnText.classList.add('d-none');
        spinner.classList.remove('d-none');

        // Submit form
        setTimeout(() => {
            this.submit();
        }, 500);
    });
}

// Real-time validation
const requiredFields = form.querySelectorAll('input[required], select[required]');
requiredFields.forEach(field => {
    field.addEventListener('blur', function() {
        if (this.value.trim() === '') {
            this.classList.add('is-invalid');
        } else {
            this.classList.remove('is-invalid');
            this.classList.add('is-valid');
        }
    });

    field.addEventListener('input', function() {
        if (this.classList.contains('is-invalid') && this.value.trim() !== '') {
            this.classList.remove('is-invalid');
            this.classList.add('is-valid');
        }
    });
});

// Department-based matric number prefix
const departmentSelect = document.getElementById('department');
if (departmentSelect && generateMatricBtn) {
    generateMatricBtn.addEventListener('click', function() {
        const selectedDept = departmentSelect.value;
        const year = new Date().getFullYear().toString().substr(-2);
        let deptCode = '200'; // Default

        // Department codes
        const deptCodes = {
            'Computer Science': '470',
            'Engineering': '480',
            'Business Administration': '490',
            'Medicine': '500',
            'Law': '510',
            'Arts and Humanities': '520',
            'Social Sciences': '530',
            'Natural Sciences': '540',
            'Education': '550',
            'Agriculture': '560'
        };

        if (selectedDept && deptCodes[selectedDept]) {
            deptCode = deptCodes[selectedDept];
        }

        const random = Math.floor(Math.random() * 1000).toString().padStart(3, '0');
        const matricNumber = `${year}${deptCode}${random}`;

        matricInput.value = matricNumber;
        matricInput.focus();

        // Visual feedback
        matricInput.classList.add('is-valid');
        setTimeout(() => {
            matricInput.classList.remove('is-valid');
        }, 2000);
    });
}

// Auto-format phone number
const phoneInput = document.getElementById('phone');
if (phoneInput) {
    phoneInput.addEventListener('input', function() {
        let value = this.value.replace(/\D/g, ''); // Remove non-digits

        if (value.length > 0) {
            if (value.length <= 3) {
                value = `+${value}`;
            } else if (value.length <= 6) {
                value = `+${value.slice(0, 3)} ${value.slice(3)}`;
            } else if (value.length <= 10) {
                value = `+${value.slice(0, 3)} ${value.slice(3, 6)} ${value.slice(6)}`;
            } else {
                value = `+${value.slice(0, 3)} ${value.slice(3, 6)} ${value.slice(6, 10)}`;
            }
        }

        this.value = value;
    });
}

// Form reset functionality
const resetBtn = document.createElement('button');
resetBtn.type = 'button';
resetBtn.className = 'btn btn-outline-secondary me-2';
resetBtn.innerHTML = '<i class="mdi mdi-refresh me-1"></i>Reset Form';
resetBtn.addEventListener('click', function() {
    if (confirm('Are you sure you want to reset the form? All entered data will be lost.')) {
        form.reset();

        // Remove validation classes
        form.querySelectorAll('.is-valid, .is-invalid').forEach(el => {
            el.classList.remove('is-valid', 'is-invalid');
        });

        // Clear password match text
        if (matchText) {
            matchText.textContent = '';
            matchText.className = 'text-muted';
        }

        // Focus first input
        const firstInput = form.querySelector('input');
        if (firstInput) firstInput.focus();
    }
});

// Insert reset button before cancel button
const cancelBtn = document.querySelector('a[href*="admin.users.index"]');
if (cancelBtn) {
    cancelBtn.parentNode.insertBefore(resetBtn, cancelBtn);
}
});
</script>
@endsection
