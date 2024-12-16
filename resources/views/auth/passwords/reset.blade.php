@extends('layouts.app')

@section('content')

<h2><span>Reset Password</span></h2>

<div id="passwordError"></div>

@include('auth.partials.alerts')

<form id="resetPasswordForm" method="POST" action="{{ route('password.update') }}" class="custom-form"
    autocomplete="off">
    @csrf
    <input type="hidden" name="token" value="{{ $token }}">
    <input type="hidden" name="email" value="{{ $email ?? old('email') }}">

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

    <!-- password_confirmation input group -->
    <div class="input-group">
        <div class="input-group-prepend">
            <i class="bi bi-key"></i>
        </div>
        <div class="floating-label-container">
            <input type="password_confirmation" name="password_confirmation" id="password_confirmation" placeholder=""
                required />
            <label for="password_confirmation">password confirmation*</label>
        </div>
        <div class="input-group-prepend">
            <i class="bi bi-eye" id="show-password-icon"></i>
        </div>
    </div>

    <button type="submit" class="btn btn-filled rounded-4 w-100">
        Reset Password
    </button>

</form>

@endsection