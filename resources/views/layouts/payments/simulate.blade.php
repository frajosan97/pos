<!-- resources/views/payments/simulate.blade.php -->
@extends('layouts.app')

@section('title', 'Simulate C2B Transaction')

@section('content')
<div class="container">

    <h2>Simulate C2B Transaction</h2>

    <div class="card">
        <div class="card-body">
            <ul class="list-group">
                <li class="list-group-item p-0 border-0"><a href="{{ route('payments.registerUrl') }}">1) Register Url</a></li>
                <li class="list-group-item p-0 border-0"><a href="{{ route('payments.confirmation') }}">2) Confirmation Url</a></li>
                <li class="list-group-item p-0 border-0"><a href="{{ route('payments.validation') }}">3) Validation Url</a></li>
            </ul>
            <form action="{{ route('payments.simulate') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="amount">Amount</label>
                    <input type="number" class="form-control" id="amount" name="amount" required>
                </div>
                <div class="form-group">
                    <label for="msisdn">Phone Number</label>
                    <input type="text" class="form-control" id="msisdn" name="msisdn" required>
                </div>
                <div class="form-group">
                    <label for="billRefNumber">Bill Reference Number</label>
                    <input type="text" class="form-control" id="billRefNumber" name="billRefNumber" required>
                </div>
                <button type="submit" class="btn btn-custom">Simulate C2B Transaction</button>
            </form>
        </div>
    </div>
</div>
@endsection