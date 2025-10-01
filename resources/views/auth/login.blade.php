@extends('layouts.auth')
@section('content')
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-md-8 col-lg-6 col-xl-5">
        <div class="card overflow-hidden">
          <div class="card-body pt-0">

            <h3 class="text-center mt-5 mb-4">
              <a href="{{ route('login') }}" class="d-block auth-logo">
                <img src="{{ url('logo-dark.png') }}" alt="Lexa University Logo" height="30" class="auth-logo-dark">
                <img src="{{ url('logo-light.png') }}" alt="Lexa University Logo" height="30" class="auth-logo-light">
              </a>
            </h3>

            <div class="p-3">
              <h4 class="text-muted font-size-18 mb-1 text-center">Welcome Back!</h4>
              <p class="text-muted text-center">Sign in to continue to <strong>Lexa University Student ID Portal</strong>.</p>

              {{-- Display Success Messages --}}
              @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                  <i class="mdi mdi-check-circle me-2"></i>
                  {{ session('success') }}
                  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
              @endif

              {{-- Display Status Messages --}}
              @if(session('status'))
                <div class="alert alert-info alert-dismissible fade show" role="alert">
                  <i class="mdi mdi-information me-2"></i>
                  {{ session('status') }}
                  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
              @endif

              {{-- Display Error Messages --}}
              @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                  <i class="mdi mdi-alert-circle me-2"></i>
                  @foreach($errors->all() as $error)
                    {{ $error }}<br>
                  @endforeach
                  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
              @endif

              <form class="form-horizontal mt-4" action="{{ route('login') }}" method="POST">
                @csrf

                <div class="mb-3">
                  <label for="login_id" class="form-label">Matric Number / Email</label>
                  <input type="text"
                         name="login_id"
                         class="form-control @error('login_id') is-invalid @enderror"
                         id="login_id"
                         placeholder="Enter your Matric Number or Email"
                         value="{{ old('login_id') }}"
                         required
                         autocomplete="username">
                  <div class="form-text">
                    <small class="text-muted">
                      <i class="mdi mdi-information-outline"></i>
                      Students: Use your Matric Number (e.g., 02200470001) |
                      Administrators: Use your Email Address
                    </small>
                  </div>
                  @error('login_id')
                    <div class="invalid-feedback">
                      {{ $message }}
                    </div>
                  @enderror
                </div>

                <div class="mb-3">
                  <label for="userpassword" class="form-label">Password</label>
                  <div class="input-group">
                    <input type="password"
                           name="password"
                           class="form-control @error('password') is-invalid @enderror"
                           id="userpassword"
                           placeholder="Enter password"
                           required
                           autocomplete="current-password">
                    <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                      <i class="mdi mdi-eye-outline" id="toggleIcon"></i>
                    </button>
                  </div>
                  @error('password')
                    <div class="invalid-feedback">
                      {{ $message }}
                    </div>
                  @enderror
                </div>

                <div class="mb-3 row mt-4">
                  <div class="col-6">
                    <div class="form-check">
                      <input type="checkbox"
                             class="form-check-input"
                             id="remember"
                             name="remember"
                             {{ old('remember') ? 'checked' : '' }}>
                      <label class="form-check-label" for="remember">
                        Remember me
                      </label>
                    </div>
                  </div>
                  <div class="col-6 text-end">
                    <button class="btn btn-primary w-md waves-effect waves-light" type="submit" id="loginBtn">
                      <span class="btn-text">Log In</span>
                      <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                    </button>
                  </div>
                </div>

                <div class="form-group mb-0 row">
                  <div class="col-12 mt-4">
                    <a href="{{ route('password.request') }}" class="text-muted">
                      <i class="mdi mdi-lock"></i> Forgot your password?
                    </a>
                  </div>
                </div>


              </form>
            </div>
          </div>
        </div>
        <div class="mt-5 text-center">
          <p>© <script>document.write(new Date().getFullYear())</script> Lexa University</p>
        </div>
      </div>
    </div>
  </div>

  {{-- JavaScript for enhanced functionality --}}
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      // Password toggle functionality
      const togglePassword = document.getElementById('togglePassword');
      const passwordInput = document.getElementById('userpassword');
      const toggleIcon = document.getElementById('toggleIcon');

      if (togglePassword && passwordInput && toggleIcon) {
        togglePassword.addEventListener('click', function() {
          const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
          passwordInput.setAttribute('type', type);

          // Toggle icon
          if (type === 'text') {
            toggleIcon.classList.remove('mdi-eye-outline');
            toggleIcon.classList.add('mdi-eye-off-outline');
          } else {
            toggleIcon.classList.remove('mdi-eye-off-outline');
            toggleIcon.classList.add('mdi-eye-outline');
          }
        });
      }

      // Form submission with loading state
      const loginForm = document.querySelector('form');
      const loginBtn = document.getElementById('loginBtn');
      const btnText = loginBtn.querySelector('.btn-text');
      const spinner = loginBtn.querySelector('.spinner-border');

      if (loginForm && loginBtn) {
        loginForm.addEventListener('submit', function() {
          // Disable button and show loading state
          loginBtn.disabled = true;
          btnText.textContent = 'Signing In...';
          spinner.classList.remove('d-none');
        });
      }

      // Auto-dismiss alerts after 5 seconds
      const alerts = document.querySelectorAll('.alert');
      alerts.forEach(function(alert) {
        setTimeout(function() {
          const bsAlert = new bootstrap.Alert(alert);
          bsAlert.close();
        }, 5000);
      });

      // Input validation hints
      const loginIdInput = document.getElementById('login_id');
      if (loginIdInput) {
        loginIdInput.addEventListener('input', function() {
          const value = this.value.trim();
          const isEmail = value.includes('@');
          const helpText = this.parentNode.querySelector('.form-text small');

          if (isEmail) {
            helpText.innerHTML = '<i class="mdi mdi-information-outline text-info"></i> Email detected - Admin login mode';
          } else if (value.length > 0) {
            helpText.innerHTML = '<i class="mdi mdi-information-outline text-success"></i> Matric Number detected - Student login mode';
          } else {
            helpText.innerHTML = '<i class="mdi mdi-information-outline"></i> Students: Use your Matric Number (e.g., 02200470001) | Administrators: Use your Email Address';
          }
        });
      }

      // Focus on first input
      if (loginIdInput) {
        loginIdInput.focus();
      }
    });
  </script>
@endsection
