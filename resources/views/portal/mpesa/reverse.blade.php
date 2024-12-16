@extends('layouts.app')

@section('pageTitle','Mpesa Reversal')

@section('content')

<div class="row">
    <div class="col-md-8 mx-auto">

        <h2 class="text-center">Mpesa Reversal</h2>

        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <form action="{{ route('mpesa.reverse') }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label for="transactionId">Transaction ID</label>
                        <input type="text" class="form-control" id="transactionId" name="transactionId" placeholder="Transaction ID" required>
                    </div>
                    <div class="form-group">
                        <label for="amount">Amount</label>
                        <input type="number" class="form-control" id="amount" name="amount" placeholder="Amount" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Reverse Transaction</button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection