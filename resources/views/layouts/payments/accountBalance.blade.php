<!-- resources/views/payments/accountBalance.blade.php -->

@extends('layouts.app')

@section('title', 'Account Balance')

@section('content')
<div class="container">

    <h2>Account Balance</h2>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('payments.accountBalance') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-custom">Check Balance</button>
            </form>
        </div>
    </div>

    @if(session('response'))
    <div class="card mt-4">
        <div class="card-header">
            <h4>Account Balance Response</h4>
        </div>
        <div class="card-body">
            <pre>{{ json_encode(session('response'), JSON_PRETTY_PRINT) }}</pre>
        </div>
    </div>
    @endif
</div>
@endsection