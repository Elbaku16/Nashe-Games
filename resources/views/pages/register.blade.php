@extends('layouts.app')

@section('title', 'Sign Up')

@section('content')
<div class="login-container">
    <h1 class="login-title">Sign up</h1>

    <div class="logo-area">
        <img src="{{ asset('images/LogoNashe.png') }}" alt="Nashe Games Logo" class="nashe-logo-img">
    </div>

    @if (isset($errors) && $errors->any())
        <div class="auth-error">
            <i class="bi bi-exclamation-triangle-fill"></i>
            <ul class="auth-error-list">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('register.attempt') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="name">Username</label>
            <input type="text" id="name" name="name" class="form-input" value="{{ old('name') }}" required autofocus>
        </div>

        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" class="form-input" value="{{ old('email') }}" required>
        </div>

        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" class="form-input" required>
        </div>

        <div class="form-group">
            <label for="password_confirmation">Confirm password</label>
            <input type="password" id="password_confirmation" name="password_confirmation" class="form-input" required>
        </div>

        <button type="submit" class="auth-submit-btn">
            Sign up <i class="bi bi-arrow-right"></i>
        </button>

        <div class="signup-link">
            Already have an account? <a href="{{ route('login') }}">Log in</a>
        </div>
    </form>
</div>
@endsection
