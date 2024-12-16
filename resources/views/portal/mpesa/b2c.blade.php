@extends('layouts.app')

@section('pageTitle','B2C Transaction')

@section('content')

<div class="row">
    <div class="col-md-8 mx-auto">

        <h2 class="text-center">B2C Transaction</h2>

        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <form action="{{ route('mpesa.simulateB2C') }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label for="amount">Amount</label>
                        <input type="number" class="form-control" id="amount" name="amount" placeholder="Amount" required>
                    </div>
                    <div class="form-group">
                        <label for="msisdn">Phone Number</label>
                        <input type="text" class="form-control" id="msisdn" name="msisdn" placeholder="Recipient Phone Number" required>
                    </div>
                    <div class="form-group">
                        <label for="billRefNumber">Customer ID</label>
                        <input type="text" class="form-control" id="billRefNumber" name="billRefNumber" placeholder="Bill Ref Number" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Simulate B2C Transaction</button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection