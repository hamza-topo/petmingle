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
                    <form  method="POST" action="{{ route('register') }}"  id="register-form-id">
                        @csrf
                        <div class="contact-form mb-24">
                            <label for='name' class="contact-label">{{ __('Name') }} </label>
                            <input id="name" class="form-control contact-input @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required placeholder="{{ __('Name') }}" autocomplete="name" autofocus>
                            @error('name')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>
                        <div class="contact-form mb-24">
                            <label for='email' class="contact-label">{{ __('Email Address') }} </label>
                            <input name="email" class="form-control contact-input @error('email') is-invalid @enderror" id="email" type="email" autocomplete="email" placeholder="{{ __('Email') }}">
                            @error('email')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>

                        <!-- Password -->
                        <div class="position-relative contact-form mb-24">
                            <label for="password" class="contact-label">{{ __('Password') }}</label>
                            <input id="password" type="password" class="form-control contact-input password-input @error('password') is-invalid @enderror" name="password" required placeholder="{{ __('Password') }}" autocomplete="new-password">
                            @error('password')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                            <i class="toggle-password ri-eye-line"></i>
                        </div>
                        <!-- Password -->
                        <div class="position-relative contact-form mb-24">
                            <label for="confirm-password" class="contact-label">{{ __('Confirm Password') }}</label>
                            <input type="password" name="password_confirmation" class="form-control contact-input password-input" id="confirm-password" placeholder="{{ __('Confirm Password') }}">
                            <i class="toggle-password ri-eye-line"></i>
                        </div>

                        <a onclick="document.getElementById('register-form-id').submit();" class="btn-primary-fill justify-content-center w-100">
                            <span class="d-flex justify-content-center gap-6">
                                <span>{{ __('Register') }}</span>
                            </span>
                        </a>
                    </form>

                    <div class="login-footer mb-20">
                        <div class="create-account">
                            <p>
                                Already have an account?
                                <a href="{{route('login')}}">
                                    <span class="text-primary">Login</span>
                                </a>
                            </p>
                        </div>
                    </div>

                    <div class="sign-with">
                        <p class="text-paragraph">Or Sign in with</p>
                        <ul class="icon-login-section">
                            <li class="icon-login">
                                <a href="#"><i class="ri-mail-line"></i></a>
                            </li>
                            <li class="icon-login">
                                <a href="#"><i class="ri-facebook-fill"></i></a>
                            </li>
                            <li class="icon-login">
                                <a href="#"><i class="ri-twitter-fill"></i></a>
                            </li>
                            <li class="icon-login">
                                <a href="#"> <i class="ri-linkedin-fill"></i></a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
