@extends('layouts.app')

@section('pageTitle','C2B Transaction Simulation')

@section('content')

<div class="row">
    <div class="col-md-8 mx-auto">

        <h2 class="text-center">C2B Transaction Simulation</h2>

        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <ul class="list-group">
                    <li class="list-group-item p-0 border-0">
                        <a href="{{ route('mpesa.registerUrl') }}">1) Register Url</a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

@endsection