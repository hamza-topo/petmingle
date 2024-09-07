@extends('layouts.app')

@section('main')
<div class="login-area section-padding">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xl-5 col-lg-6 col-md-8 col-sm-10">
                <div class="login-card">
                    <!-- Logo -->
                    <div class="logo mb-40">
                        <a href="index.html" class="mb-30 d-block">
                            <img src="assets/images/logo/logo.png" alt="logo" class="changeLogo">
                        </a>
                    </div>
                    <!-- Form -->
                    <form method="POST" action="{{ route('login') }}" id="login-form-id">
                           @csrf
                        <div class="position-relative contact-form mb-24">
                            <label class="contact-label">{{ __('Email Address') }} </label>
                            <input   id="email" type="email"  class="form-control contact-input @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
                            @error('email')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>

                        <div class="contact-form mb-24">
                            <div class="position-relative ">
                                <div class="d-flex justify-content-between aligin-items-center">
                                    <label class="contact-label">{{ __('Password') }}</label>
                                    @if (Route::has('password.request'))
                                    <a  href="{{ route('password.request') }}"><span class="text-primary text-15">{{ __('Forgot Your Password?') }}</span></a>
                                    @endif
                                 
                                </div>
                                <input id="password" type="password" class="form-control contact-input password-input @error('password') is-invalid @enderror" name="password" required autocomplete="current-password"  placeholder="Enter Password">
                                @error('password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                                <i class="toggle-password ri-eye-line"></i>
                            </div>
                            
                        </div>

                        <a  onclick="document.getElementById('login-form-id').submit();" class="login-btn d-flex align-items-center justify-content-center gap-10 mb-1 ">
                            <span class="d-flex justify-content-center gap-6">
                                <span >{{ __('Login') }}</span>
                            </span>
                        </a>
                    </form>

                    <div class="login-footer">
                        <div class="create-account">
                            <p>
                                {{ __('Don’t have an account?') }}
                                <a href="{{route('register')}}">
                                    <span class="text-primary">>{{ __('Register') }}</span>
                                </a>
                            </p>
                        </div>
                        <a href="{{ url('/login/google') }}" class="login-btn d-flex align-items-center justify-content-center gap-10 mb-1 ">
                            <img src="assets/images/icon/google-icon.png" alt="img" class="m-0">
                            <span>  {{ __('Login with Google') }}</span>
                        </a>
                        <a href="{{ url('/login/github') }}" class="login-btn d-flex align-items-center justify-content-center gap-10 mb-1 ">
                            <img src="assets/images/icon/github-icon.png" alt="img" class="m-0">
                            <span>  {{ __('Login with GitHub') }}</span>
                        </a>
                 
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
