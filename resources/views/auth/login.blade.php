@extends('layouts.app')

@section('content')

<h2><span>Login</span></h2>

@include('auth.partials.alerts')

<form method="POST" action="{{ route('login') }}" class="custom-form" autocomplete="off">
    <!-- Csrf token -->
    @csrf

    <!-- email input group -->
    <div class="input-group">
        <div class="input-group-prepend">
            <i class="bi bi-person-circle"></i>
        </div>
        <div class="floating-label-container">
            <input type="email" name="email" id="email" placeholder="" autocomplete="off" autofocus required />
            <label for="email">email address or username*</label>
        </div>
        <div class="input-group-prepend"></div>
    </div>

    <!-- password input group -->
    <div class="input-group">
        <div class="input-group-prepend">
            <i class="bi bi-key"></i>
        </div>
        <div class="floating-label-container">
            <input type="password" name="password" id="password" placeholder="" required />
            <label for="password">password*</label>
        </div>
        <div class="input-group-prepend">
            <i class="bi bi-eye" id="show-password-icon"></i>
        </div>
    </div>

    <div class="form-footer">
        <!-- Password reset link -->
        <a class="password-reset" href="{{ route('password.request') }}">
            Forgot Your Password?
        </a>
    </div>

    <button type="submit" class="btn btn-filled rounded-4 w-100">
        <i class="bi bi-sign-in"></i> Login
    </button>

</form>

@endsection