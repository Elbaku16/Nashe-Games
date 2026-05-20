@extends('layouts.app')

@section('title', 'Log In')

@section('content')
<div class="login-container">
    <h1 class="login-title">Log in</h1>

    <div class="logo-area">
        <img src="{{ asset('images/LogoNashe.png') }}" alt="Nashe Games Logo" class="nashe-logo-img">
    </div>

    @if (isset($errors) && $errors->any())
        <div class="auth-error">
            <i class="bi bi-exclamation-triangle-fill"></i>
            {{ $errors->first() }}
        </div>
    @endif

    <form action="{{ route('login.attempt') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" class="form-input" value="{{ old('email') }}" required autofocus>
        </div>

        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" class="form-input" required>
        </div>

        <button type="submit" class="auth-submit-btn">
            Log in <i class="bi bi-arrow-right"></i>
        </button>

        <div class="signup-link">
            Don't have an account? <a href="{{ route('register') }}">Sign up!</a>
        </div>
    </form>
</div>
@endsection
