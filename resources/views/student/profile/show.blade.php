@extends('layouts.student')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">My Profile</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('student.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Profile</li>
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
        {{-- Profile Card --}}
        <div class="col-xl-4">
            <div class="card overflow-hidden">
                <div class="bg-primary-subtle">
                    <div class="row">
                        <div class="col-7">
                            <div class="text-primary p-3">
                                <h5 class="text-primary">Welcome Back!</h5>
                                <p>Student Profile</p>
                            </div>
                        </div>
                        <div class="col-5 align-self-end">
                            <img src="{{ asset('images/profile-img.png') }}" alt="" class="img-fluid">
                        </div>
                    </div>
                </div>
                <div class="card-body pt-0">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="avatar-md profile-user-wid mb-4">
                                <img src="{{ $user->photo_url }}" alt="Profile Photo" class="img-thumbnail rounded-circle">
                            </div>
                            <h5 class="font-size-15 text-truncate">{{ $user->name }}</h5>
                            <p class="text-muted mb-0 text-truncate">{{ $user->matric_no }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Quick Actions Card --}}
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-4">Quick Actions</h4>
                    <div class="d-grid gap-2">
                        <a href="{{ route('student.profile.edit') }}" class="btn btn-primary waves-effect waves-light">
                            <i class="mdi mdi-account-edit me-2"></i>Edit Profile
                        </a>
                        <a href="{{ route('student.password.change') }}" class="btn btn-outline-secondary waves-effect">
                            <i class="mdi mdi-lock-reset me-2"></i>Change Password
                        </a>
                        <a href="{{ route('student.id-card.show') }}" class="btn btn-outline-info waves-effect">
                            <i class="mdi mdi-card-account-details me-2"></i>View ID Card
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- Profile Details --}}
        <div class="col-xl-8">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-4">Profile Information</h4>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Full Name</label>
                                <p class="text-muted">{{ $user->name }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Matric Number</label>
                                <p class="text-muted">{{ $user->matric_no }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Email Address</label>
                                <p class="text-muted">{{ $user->email }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Phone Number</label>
                                <p class="text-muted">{{ $user->phone ?: 'Not provided' }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Department</label>
                                <p class="text-muted">{{ $user->department }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Account Status</label>
                                <p class="text-muted">
                                    <span class="badge bg-{{ $user->status === 'active' ? 'success' : ($user->status === 'inactive' ? 'warning' : 'danger') }}">
                                        {{ ucfirst($user->status) }}
                                    </span>
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Last Login</label>
                                <p class="text-muted">
                                    {{ $user->last_login_at ? $user->last_login_at->format('M d, Y \a\t h:i A') : 'Never' }}
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Member Since</label>
                                <p class="text-muted">{{ $user->created_at->format('M d, Y') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Update Profile Photo Card --}}
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-4">Update Profile Photo</h4>

                    <form action="{{ route('student.profile.photo') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label for="photo" class="form-label">Choose New Photo</label>
                                    <input type="file"
                                           class="form-control @error('photo') is-invalid @enderror"
                                           id="photo"
                                           name="photo"
                                           accept="image/jpeg,image/png,image/jpg">
                                    <div class="form-text">
                                        <small class="text-muted">
                                            <i class="mdi mdi-information-outline"></i>
                                            Accepted formats: JPEG, PNG, JPG. Maximum size: 2MB
                                        </small>
                                    </div>
                                    @error('photo')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Current Photo</label>
                                    <div>
                                        <img src="{{ $user->photo_url }}"
                                             alt="Current Profile Photo"
                                             class="img-thumbnail"
                                             style="width: 80px; height: 80px; object-fit: cover;">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary waves-effect waves-light">
                                    <i class="mdi mdi-cloud-upload me-2"></i>Update Photo
                                </button>
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
    // Preview selected image
    const photoInput = document.getElementById('photo');
    if (photoInput) {
        photoInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const currentPhoto = document.querySelector('img[alt="Current Profile Photo"]');
                    if (currentPhoto) {
                        currentPhoto.src = e.target.result;
                    }
                };
                reader.readAsDataURL(file);
            }
        });
    }

    // Auto-dismiss alerts
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(function(alert) {
        setTimeout(function() {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        }, 5000);
    });
});
</script>
@endsection
