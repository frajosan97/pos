<!-- resources/views/payments/transactionStatus.blade.php -->

@extends('layouts.app')

@section('title', 'Transaction Status')

@section('content')
<div class="container">

    <h2>Transaction Status</h2>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('payments.getTransactionStatus') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="transactionId" class="form-label">Transaction ID</label>
                    <input type="text" class="form-control" id="transactionId" name="transactionId" required>
                </div>
                <div class="mb-3">
                    <label for="originatorConversationID" class="form-label">Originator Conversation ID</label>
                    <input type="text" class="form-control" id="originatorConversationID" name="originatorConversationID" required>
                </div>
                <button type="submit" class="btn btn-custom">Check Status</button>
            </form>
        </div>
    </div>

    @if(session('response'))
    <div class="card mt-4">
        <div class="card-header">
            <h4>Transaction Status Response</h4>
        </div>
        <div class="card-body">
            <pre>{{ json_encode(session('response'), JSON_PRETTY_PRINT) }}</pre>
        </div>
    </div>
    @endif
</div>
@endsection