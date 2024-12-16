@extends('layouts.app')

@section('pageTitle','Transaction Status')

@section('content')

<div class="row">
    <div class="col-md-8 mx-auto">

        <h2 class="text-center">Transaction Status</h2>

        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <form action="{{ route('mpesa.getTransactionStatus') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="transactionId" class="form-label">Transaction ID</label>
                        <input type="text" class="form-control" id="transactionId" name="transactionId" placeholder="Transaction Id" required>
                    </div>
                    <div class="mb-3">
                        <label for="originatorConversationID" class="form-label">Originator Conversation ID</label>
                        <input type="text" class="form-control" id="originatorConversationID" name="originatorConversationID" placeholder="Originator Conversation ID" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Check Status</button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection