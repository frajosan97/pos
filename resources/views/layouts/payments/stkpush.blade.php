<!-- resources/views/payments/stkpush.blade.php -->
@extends('layouts.app')

@section('content')
<div class="container">

    @php
    $phoneNumber = request('phoneNumber', '');
    @endphp

    <h2>Initiate STK Push</h2>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('payments.stkpush') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="phoneNumber">Phone Number</label>
                    <input type="text" class="form-control" id="phoneNumber" name="phoneNumber"
                        value="{{ $phoneNumber }}" placeholder="Phone Number" required>
                </div>
                <div class="form-group">
                    <label for="amount">Amount</label>
                    <input type="number" class="form-control" id="amount" name="amount" placeholder="Amount" required>
                </div>
                <button type="submit" class="btn btn-custom">Initiate STK Push</button>
            </form>
        </div>
    </div>
</div>

@endsection