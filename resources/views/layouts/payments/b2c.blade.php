@extends('layouts.app')

@section('title', 'Simulate B2C Transaction')

@section('content')
<div class="container">

    <h2>Simulate B2C Transaction</h2>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('payments.simulateB2C') }}" method="POST"> <!-- Changed the route to simulateB2C -->
                @csrf
                <div class="form-group">
                    <label for="amount">Amount</label>
                    <input type="number" class="form-control" id="amount" name="amount" required>
                </div>
                <div class="form-group">
                    <label for="msisdn">Phone Number</label> <!-- Changed the name from msisdn to msisdn -->
                    <input type="text" class="form-control" id="msisdn" name="msisdn" required>
                </div>
                <div class="form-group">
                    <label for="billRefNumber">Customer ID</label> <!-- Added Customer ID for OriginatorConversationID -->
                    <input type="text" class="form-control" id="billRefNumber" name="billRefNumber" required>
                </div>
                <button type="submit" class="btn btn-custom">Simulate B2C Transaction</button> <!-- Changed button text -->
            </form>
        </div>
    </div>
</div>
@endsection