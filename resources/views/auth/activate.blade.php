@extends('layouts.app')

@section('content')

<h2><span>Account Activation</span></h2>

@include('auth.partials.alerts')

<form method="POST" action="{{ route('account.activate', $email) }}" class="custom-form" autocomplete="off">
    <!-- Csrf token -->
    @csrf

    <p>
        To continue with account activation, Kindly enter the password sent to your email and enter a new password to secure your account!
    </p>

    <!-- old password input group -->
    <div class="input-group">
        <div class="input-group-prepend">
            <i class="fas fa-lock"></i>
        </div>
        <div class="floating-label-container">
            <input type="password" name="old_password" id="old_password" placeholder="" required />
            <label for="old_password">Old password*</label>
        </div>
        <div class="input-group-prepend">
            <i class="fas fa-eye" id="show-password-icon"></i>
        </div>
    </div>

    <!-- new password input group -->
    <div class="input-group">
        <div class="input-group-prepend">
            <i class="fas fa-lock"></i>
        </div>
        <div class="floating-label-container">
            <input type="password" name="password" id="password" placeholder="" required />
            <label for="password">New password*</label>
        </div>
        <div class="input-group-prepend">
            <i class="fas fa-eye" id="show-password-icon"></i>
        </div>
    </div>

    <!-- password confirmation input group -->
    <div class="input-group">
        <div class="input-group-prepend">
            <i class="fas fa-lock"></i>
        </div>
        <div class="floating-label-container">
            <input type="password" name="password_confirmation" id="password_confirmation" placeholder=""
                required />
            <label for="password_confirmation">Confirm new password*</label>
        </div>
        <div class="input-group-prepend"></div>
    </div>

    <button type="submit" class="btn btn-filled rounded-4 w-100">
        Submit & Activate
    </button>
</form>

@endsection
