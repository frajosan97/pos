@extends('layouts.app')

@section('pageTitle','Account Balance Check')

@section('content')

<div class="row">
    <div class="col-md-8 mx-auto">

        <h2 class="text-center">Account Balance Check</h2>

        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <form action="{{ route('mpesa.accountBalance') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-primary w-100">Check Balance</button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection