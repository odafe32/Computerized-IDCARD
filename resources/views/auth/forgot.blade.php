
  @extends('layouts.auth')
@section('content')
<div class="container">
                <div class="row justify-content-center">
                    <div class="col-md-8 col-lg-6 col-xl-5">
                        <div class="card overflow-hidden">
                            <div class="card-body pt-0">
                                <h3 class="text-center mt-5 mb-4">
                                    <a href="{{ route('password.request') }}" class="d-block auth-logo">
                                        <img src="{{ url('logo-dark.png') }}" alt="" height="30" class="auth-logo-dark">
                                        <img src="{{ url('logo-light.png') }}" alt="" height="30" class="auth-logo-light">
                                    </a>
                                </h3>
                                <div class="p-3">
                                    <h4 class="text-muted font-size-18 mb-3 text-center">Reset Password</h4>
                                    <div class="alert alert-info" role="alert">
                                        Enter your Email and instructions will be sent to you!
                                    </div>
                                    <form class="form-horizontal mt-4" action="">

                                        <div class="mb-3">
                                            <label for="useremail">Email</label>
                                            <input type="email" class="form-control" id="useremail" placeholder="Enter email">
                                        </div>

                                        <div class="mb-3 row">
                                            <div class="col-12 text-end">
                                                <button class="btn btn-primary w-md waves-effect waves-light" type="submit">Reset</button>
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

            @endsection
