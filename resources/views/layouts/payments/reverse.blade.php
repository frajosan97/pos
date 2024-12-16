<!-- resources/views/payments/reverse.blade.php -->

@extends('layouts.app')

@section('title', 'Reverse Transaction')

@section('content')
<div class="container">
    
    <h2>Reverse Transaction</h2>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('payments.reverse') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="transactionId">Transaction ID</label>
                    <input type="text" class="form-control" id="transactionId" name="transactionId" required>
                </div>
                <div class="form-group">
                    <label for="amount">Amount</label>
                    <input type="number" class="form-control" id="amount" name="amount" required>
                </div>
                <button type="submit" class="btn btn-custom">Reverse Transaction</button>
            </form>
        </div>
    </div>
</div>
@endsection