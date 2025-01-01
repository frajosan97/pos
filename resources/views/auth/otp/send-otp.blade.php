@extends('layouts.app')

@section('content')

<div class="text-center mb-4">
    <h2 class="m-0"><span>OTP Verification</span></h2>
    <small class="text-muted m-0">Choose where to receive a verification code</small>
</div>

@include('auth.partials.alerts')

<form action="{{ route('otp.send') }}" method="POST" class="mt-4">
    @csrf

    <ul class="list-group">
        @foreach ($verifyMethods as $option)
        @if ($option['value'])
        <li class="list-group-item border-0 rounded-4 shadow-sm p-1 mb-3 bg-light">
            <button type="submit" name="method" value="{{ $option['method'] }}"
                class="btn w-100 rounded-4 text-start d-flex align-items-center">
                <div class="icon-container me-3 d-flex align-items-center justify-content-center">
                    <i class="{{ $option['icon'] }} text-white"></i>
                </div>
                <div class="flex-grow-1">
                    <h6 class="m-0 fw-bold">{{ $option['label'] }}</h6>
                    <small class="text-muted">{{ $option['value'] }}</small>
                </div>
            </button>
        </li>
        @endif
        @endforeach
    </ul>
</form>

@endsection
