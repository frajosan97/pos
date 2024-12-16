@extends('layouts.app')

@section('content')

<h2><span>Verify OTP</span></h2>

@include('auth.partials.alerts')

<form method="POST" action="{{ route('otp.verify') }}" class="custom-form" autocomplete="off">
    <!-- Csrf token -->
    @csrf

    <!-- otp input group -->
    <div class="input-group">
        <div class="input-group-prepend">
            <i class="bi bi-key"></i>
        </div>
        <div class="floating-label-container">
            <input type="otp" name="otp" id="otp" placeholder="" autocomplete="off" autofocus required />
            <label for="otp">OTP*</label>
        </div>
        <div class="input-group-prepend"></div>
    </div>

    <button type="submit" class="btn btn-filled rounded-4 w-100">
        Verify OTP
    </button>

</form>

@endsection