@extends('layouts.app')

@section('pageTitle', $product->name)

@section('content')

@if(auth()->user()->hasPermission('catalogue_view'))

<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="row g-4">
                        <!-- Product Image Section -->
                        <div class="col-lg-4 col-md-5">
                            <img src="{{ asset($product->photo) }}" alt="{{ $product->name }}" class="img-fluid rounded shadow-sm w-100">
                        </div>

                        <!-- Product Details Section -->
                        <div class="col-lg-8 col-md-7">
                            <h1 class="h4 fw-bold">{{ $product->name }}</h1>
                            <p class="text-muted mb-2">
                                <i class="bi bi-tags"></i>
                                <strong>Category:</strong> {{ ucwords($product->catalogue->name) }}
                            </p>

                            <div class="table-responsive">
                                <table class="table table-sm table-borderless m-0 text-nowrap">
                                    <tr>
                                        <th class="text-end pw-5">Normal Price:</th>
                                        <th class="text-success"><i class="bi bi-currency-exchange"></i> Ksh {{ number_format($product->normal_price, 2) }}</th>
                                    </tr>
                                    <tr>
                                        <th class="text-end pw-5">Whole Sale Price:</th>
                                        <th class="text-warning"><i class="bi bi-currency-exchange"></i> Ksh {{ number_format($product->whole_sale_price, 2) }}</th>
                                    </tr>
                                    <tr>
                                        <th class="text-end pw-5">Agent Price:</th>
                                        <th class="text-primary"><i class="bi bi-currency-exchange"></i> Ksh {{ number_format($product->agent_price, 2) }}</th>
                                    </tr>
                                </table>
                            </div>

                            <!-- Stock Quantity and Available Balance -->
                            <p class="mt-3">
                                <strong>Stock Quantity:</strong> {{ $product->quantity }} units
                            </p>
                            <p class="mt-1">
                                <strong>Available Stock Balance:</strong> {{ $product->quantity - $product->sold_quantity }} units
                            </p>

                            <div class="mt-3">
                                <button class="btn btn-primary btn-sm">
                                    <i class="bi bi-cart-plus"></i> Add to Cart
                                </button>
                                <button class="btn btn-outline-secondary btn-sm">
                                    <i class="bi bi-heart"></i> Add to Wishlist
                                </button>
                            </div>
                        </div>

                        <!-- Product Description Section -->
                        <div class="col-12 mt-4">
                            <h2 class="h5 fw-bold">Description</h2>
                            <p class="text-muted">{{ $product->description }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@else
@include('layouts.partials.no_permission')
@endif

@endsection