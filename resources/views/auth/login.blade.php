@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="">
            <div class="login-box">
                <div class="card shadow-sm border-0 rounded-lg">
                    <div class="card-header text-center bg-primary text-white py-3">
                        <h4 class="mb-0">{{ __('Login') }}</h4>
                    </div>
                    <div class="card-body pb-0 p-4">
                        <form method="POST" action="{{ route('login') }}">
                            @csrf
                            <div class="mb-3">
                                <label for="email" class="form-label">{{ __('Email Address') }}</label>
                                <input id="email" type="email"
                                    class="form-control py-2 shadow-none @error('email') is-invalid @enderror"
                                    name="email" value="{{ old('email','support@electronicspartsbd.com') }}" required autofocus>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label">{{ __('Password') }}</label>
                                <input id="password" type="password"
                                    class="form-control py-2 shadow-none @error('password') is-invalid @enderror"
                                    name="password" required value="123456">
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- <div class="mb-3 form-check">
                            <input class="form-check-input" type="checkbox" name="remember" id="remember"
                                {{ old('remember') ? 'checked' : '' }}>
                            <label class="form-check-label" for="remember">{{ __('Remember Me') }}</label>
                        </div> --}}

                            <div class="d-grid">
                                <button type="submit" class="btn btn-success py-2">{{ __('Login') }}</button>
                            </div>
                        </form>
                    </div>
                    {{-- <div class="card-footer text-center bg-white pb-4 border-0">
                    @if (Route::has('password.request'))
                        <a class="text-decoration-none"
                            href="{{ route('password.request') }}">{{ __('Forgot Your Password?') }}</a>
                    @endif
                </div> --}}
                </div>
            </div>
        </div>
    </div>
@endsection
