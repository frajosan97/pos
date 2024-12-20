@extends('layouts.app')

@section('content')

<h2><span>Password Reset</span></h2>

@include('auth.partials.alerts')

<form method="POST" action="{{ route('password.email') }}" class="custom-form" autocomplete="off">
    <!-- Csrf token -->
    @csrf

    <!-- email input group -->
    <div class="input-group">
        <div class="input-group-prepend">
            <i class="fas fa-envelope"></i>
        </div>
        <div class="floating-label-container">
            <input type="email" name="email" id="email" placeholder="" autocomplete="off" autofocus required />
            <label for="email">email address or username*</label>
        </div>
        <div class="input-group-prepend"></div>
    </div>

    <button type="submit" class="btn btn-filled rounded-4 w-100">
        Send password reset link
    </button>
</form>

@endsection