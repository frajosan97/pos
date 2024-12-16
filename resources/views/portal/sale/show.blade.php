@extends('layouts.app')

@section('pageTitle', 'Invoice')

@section('content')

<div class="row justify-content-center">
    <div class="col-12 col-lg-9 col-xl-8 col-xxl-7">
        <div class="card border-0 shadow-sm rounded-0" id="invoice">
            <div class="card-body">
                <div class="row gy-3 mb-3">
                    <div class="col-12">
                        <a class="d-block text-end" href="#!">
                            <img src="{{ asset('assets/images/logo.png') }}" class="img-fluid" alt="Company Logo" width="135" height="44">
                        </a>
                    </div>
                    <div class="col-12">
                        <h4>From</h4>
                        <address>
                            <strong>Atricare Ltd</strong><br>
                            875 N Coast Hwy<br>
                            Nairobi, Kenya<br>
                            Phone: (949) 494-7695<br>
                            Email: info@atricare.co.ke
                        </address>
                    </div>
                </div>

                <div class="row mb-3">
                    <!-- Billing Info -->
                    <div class="col-12 col-sm-6">
                        <h4>Bill To</h4>
                        <address>
                            <strong>{{ ucwords($sale->customer->name ?? 'Unknown Customer') }}</strong><br>
                            {{ $sale->customer->address ?? 'Address not provided' }}<br>
                            Phone: {{ $sale->customer->phone ?? 'Not available' }}<br>
                            Email: {{ $sale->customer->email ?? 'Not available' }}
                        </address>
                    </div>
                    <!-- Invoice Info -->
                    <div class="col-12 col-sm-6">
                        <h4 class="row">
                            <span class="col-6">Invoice #</span>
                            <span class="col-6 text-sm-end">{{ 'INV-' . str_pad($sale->id, 5, '0', STR_PAD_LEFT) }}</span>
                        </h4>
                        <div class="row">
                            <span class="col-6">Payment Method</span>
                            <span class="col-6 text-sm-end">{{ $sale->payments->first()->paymentMethod->name }}</span>

                            @if($sale->payments->first()->paymentMethod->name == 'mpesa')
                            <span class="col-6">Paybill Number</span>
                            <span class="col-6 text-sm-end">7856965</span>
                            @endif

                            <span class="col-6">Order ID</span>
                            <span class="col-6 text-sm-end">{{ $sale->order_id }}</span>
                            <span class="col-6">Invoice Date</span>
                            <span class="col-6 text-sm-end">{{ $sale->created_at->format('Y-m-d') }}</span>
                            <span class="col-6">Due Date</span>
                            <span class="col-6 text-sm-end">{{ \Carbon\Carbon::parse($sale->created_at)->addDays(7)->format('Y-m-d') }}</span>
                        </div>
                    </div>
                </div>

                <!-- Items Table -->
                <div class="row mb-3">
                    <div class="col-12">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th scope="col" class="text-uppercase">Qty</th>
                                        <th scope="col" class="text-uppercase">Product</th>
                                        <th scope="col" class="text-uppercase text-end">Unit Price</th>
                                        <th scope="col" class="text-uppercase text-end">Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($sale->saleItems as $saleItem)
                                    <tr>
                                        <th scope="row">{{ $saleItem->quantity }}</th>
                                        <td>{{ ucwords($saleItem->product->name ?? 'Unknown Product') }}</td>
                                        <td class="text-end">{{ number_format($saleItem->product->buying_price, 2) }}</td>
                                        <td class="text-end">{{ number_format($saleItem->quantity * $saleItem->product->buying_price, 2) }}</td>
                                    </tr>
                                    @endforeach
                                    <tr>
                                        <th scope="row" colspan="3" class="text-uppercase text-end"><strong>Total</strong></th>
                                        <td class="text-end"><strong>{{ number_format($sale->total_amount, 2) }}</strong></td>
                                    </tr>
                                    <tr>
                                        <th scope="row" colspan="3" class="text-uppercase text-end"><strong>Paid</strong></th>
                                        <td class="text-end"><strong>{{ number_format($sale->payments->sum('amount'), 2) }}</strong></td>
                                    </tr>
                                    <tr>
                                        <th scope="row" colspan="3" class="text-uppercase text-end"><strong>Balance</strong></th>
                                        <td class="text-end">
                                            <strong>
                                                @php
                                                $balance = $sale->total_amount - $sale->payments->sum('amount');
                                                @endphp

                                                @if ($balance < 0)
                                                    <span class="text-success">Change: {{ number_format(abs($balance), 2) }}</span>
                                                    @else
                                                    <span class="text-danger">Unpaid Balance: {{ number_format($balance, 2) }}</span>
                                                    @endif
                                            </strong>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="row">
            <div class="col-12 text-end">
                <button type="button" class="btn btn-outline-secondary mb-3" onclick="printDiv('invoice')">
                    <i class="fas fa-print"></i> Print Invoice
                </button>
            </div>
        </div>
    </div>
</div>

@endsection