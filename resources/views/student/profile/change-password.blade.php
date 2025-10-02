@extends('layouts.student')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">Change Password</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('student.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('student.profile') }}">Profile</a></li>
                        <li class="breadcrumb-item active">Change Password</li>
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

    <div class="row justify-content-center">
        <div class="col-xl-8">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-4">
                        <i class="mdi mdi-lock-reset me-2"></i>Change Password
                    </h4>

                    <div class="alert alert-info" role="alert">
                        <i class="mdi mdi-information-outline me-2"></i>
                        <strong>Password Requirements:</strong>
                        <ul class="mb-0 mt-2">
                            <li>At least 8 characters long</li>
                            <li>Contains both uppercase and lowercase letters</li>
                            <li>Contains at least one number</li>
                            <li>Contains at least one special character (!@#$%^&*)</li>
                        </ul>
                    </div>

                    <form action="{{ route('student.password.update') }}" method="POST" id="passwordForm">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="current_password" class="form-label">Current Password <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="password"
                                               class="form-control @error('current_password') is-invalid @enderror"
                                               id="current_password"
                                               name="current_password"
                                               placeholder="Enter your current password"
                                               required>
                                        <button class="btn btn-outline-secondary" type="button" id="toggleCurrentPassword">
                                            <i class="mdi mdi-eye-outline" id="currentPasswordIcon"></i>
                                        </button>
                                    </div>
                                    @error('current_password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="password" class="form-label">New Password <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="password"
                                               class="form-control @error('password') is-invalid @enderror"
                                               id="password"
                                               name="password"
                                               placeholder="Enter new password"
                                               required>
                                        <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                            <i class="mdi mdi-eye-outline" id="passwordIcon"></i>
                                        </button>
                                    </div>
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror

                                    {{-- Password Strength Indicator --}}
                                    <div class="mt-2">
                                        <div class="progress" style="height: 5px;">
                                            <div class="progress-bar" id="passwordStrength" role="progressbar" style="width: 0%"></div>
                                        </div>
                                        <small class="text-muted" id="passwordStrengthText">Password strength</small>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="password_confirmation" class="form-label">Confirm New Password <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="password"
                                               class="form-control @error('password_confirmation') is-invalid @enderror"
                                               id="password_confirmation"
                                               name="password_confirmation"
                                               placeholder="Confirm new password"
                                               required>
                                        <button class="btn btn-outline-secondary" type="button" id="togglePasswordConfirmation">
                                            <i class="mdi mdi-eye-outline" id="passwordConfirmationIcon"></i>
                                        </button>
                                    </div>
                                    @error('password_confirmation')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror

                                    {{-- Password Match Indicator --}}
                                    <div class="mt-2">
                                        <small class="text-muted" id="passwordMatchText"></small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Security Notice --}}
                        <div class="alert alert-warning" role="alert">
                            <i class="mdi mdi-shield-alert me-2"></i>
                            <strong>Security Notice:</strong> After changing your password, you will remain logged in on this device, but you'll need to use the new password for future logins.
                        </div>

                        {{-- Form Actions --}}
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <p class="text-muted mb-0">
                                            <i class="mdi mdi-information-outline me-1"></i>
                                            All fields are required
                                        </p>
                                    </div>
                                    <div>
                                        <a href="{{ route('student.profile') }}"
                                           class="btn btn-secondary waves-effect me-2">
                                            <i class="mdi mdi-arrow-left me-1"></i>Cancel
                                        </a>
                                        <button type="submit"
                                                class="btn btn-primary waves-effect waves-light"
                                                id="changePasswordBtn">
                                            <span class="btn-text">
                                                <i class="mdi mdi-lock-reset me-1"></i>Change Password
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
    const form = document.getElementById('passwordForm');
    const changePasswordBtn = document.getElementById('changePasswordBtn');
    const btnText = changePasswordBtn.querySelector('.btn-text');
    const spinner = changePasswordBtn.querySelector('.spinner-border');

    // Password visibility toggles
    const toggleButtons = [
        {
            button: document.getElementById('toggleCurrentPassword'),
            input: document.getElementById('current_password'),
            icon: document.getElementById('currentPasswordIcon')
        },
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

    // Password strength checker
    const passwordInput = document.getElementById('password');
    const strengthBar = document.getElementById('passwordStrength');
    const strengthText = document.getElementById('passwordStrengthText');

    if (passwordInput && strengthBar && strengthText) {
        passwordInput.addEventListener('input', function() {
            const password = this.value;
            const strength = calculatePasswordStrength(password);

            strengthBar.style.width = strength.percentage + '%';
            strengthBar.className = 'progress-bar ' + strength.class;
            strengthText.textContent = strength.text;
            strengthText.className = 'text-' + strength.textClass;
        });
    }

    // Password match checker
    const passwordConfirmInput = document.getElementById('password_confirmation');
    const matchText = document.getElementById('passwordMatchText');

    if (passwordInput && passwordConfirmInput && matchText) {
        function checkPasswordMatch() {
            const password = passwordInput.value;
            const confirmPassword = passwordConfirmInput.value;

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

        passwordInput.addEventListener('input', checkPasswordMatch);
        passwordConfirmInput.addEventListener('input', checkPasswordMatch);
    }

    // Form submission with loading state
    form.addEventListener('submit', function(e) {
        // Check if passwords match before submitting
        if (passwordInput.value !== passwordConfirmInput.value) {
            e.preventDefault();
            passwordConfirmInput.classList.add('is-invalid');
            return false;
        }

        changePasswordBtn.disabled = true;
        btnText.innerHTML = '<i class="mdi mdi-loading mdi-spin me-1"></i>Changing Password...';
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

    // Password strength calculation function
    function calculatePasswordStrength(password) {
        let score = 0;
        let feedback = [];

        if (password.length === 0) {
            return {
                percentage: 0,
                class: 'bg-secondary',
                text: 'Password strength',
                textClass: 'muted'
            };
        }

        // Length check
        if (password.length >= 8) score += 25;
        else feedback.push('at least 8 characters');

        // Lowercase check
        if (/[a-z]/.test(password)) score += 25;
        else feedback.push('lowercase letters');

        // Uppercase check
        if (/[A-Z]/.test(password)) score += 25;
        else feedback.push('uppercase letters');

        // Number check
        if (/\d/.test(password)) score += 25;
        else feedback.push('numbers');

        // Special character check
        if (/[!@#$%^&*(),.?":{}|<>]/.test(password)) score += 25;
        else feedback.push('special characters');

        // Determine strength level
        if (score < 50) {
            return {
                percentage: score,
                class: 'bg-danger',
                text: 'Weak - Missing: ' + feedback.slice(0, 2).join(', '),
                textClass: 'danger'
            };
        } else if (score < 75) {
            return {
                percentage: score,
                class: 'bg-warning',
                text: 'Fair - Missing: ' + feedback.join(', '),
                textClass: 'warning'
            };
        } else if (score < 100) {
            return {
                percentage: score,
                class: 'bg-info',
                text: 'Good - Missing: ' + feedback.join(', '),
                textClass: 'info'
            };
        } else {
            return {
                percentage: 100,
                class: 'bg-success',
                text: 'Strong password',
                textClass: 'success'
            };
        }
    }

    // Focus on first input
    const currentPasswordInput = document.getElementById('current_password');
    if (currentPasswordInput) {
        currentPasswordInput.focus();
    }
});
</script>
@endsection
