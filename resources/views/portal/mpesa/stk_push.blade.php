@extends('layouts.app')

@section('pageTitle','STK Push')

@section('content')

<div class="row">
    <div class="col-md-8 mx-auto">

        <h2 class="text-center">Initiate STK Push</h2>

        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <form action="{{ route('mpesa.stkpush') }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label for="phoneNumber">Phone Number</label>
                        <input type="text" class="form-control" id="phoneNumber" name="phoneNumber" placeholder="Phone Number" required>
                    </div>
                    <div class="form-group">
                        <label for="amount">Amount</label>
                        <input type="number" class="form-control" id="amount" name="amount" placeholder="Amount" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Initiate STK Push</button>
                </form>
            </div>
        </div>

    </div>
</div>

@endsection