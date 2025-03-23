@extends('layouts.app')

@section('pageTitle', 'Receipt')

@section('content')

<iframe src="{{ route('receipt.pdf',$sale->id) }}" frameborder="0" width="100%" height="450px"></iframe>

@endsection