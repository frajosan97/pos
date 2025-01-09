@extends('layouts.app')

@section('pageTitle', 'Invoice')

@section('content')

<div class="row">
    <div class="col-md-4 mx-auto mb-3 bg-white p-2 shadow-sm" id="receipt">
        <style>
            .line-first,
            .line-last {
                border-top: 1px dashed #000;
                margin: 2px 0;
            }

            .text-nowrap {
                white-space: nowrap;
            }

            #receipt img {
                display: block;
                margin: 0 auto 10px;
            }
        </style>

        <!-- Company Logo and Information -->
        <div class="text-center">
            <img src="{{ asset(getImage($company_info->logo, 'logo.png')) }}" alt="Company Logo" style="width: 60px; height: auto;">
            <h4 class="m-0">{{ $company_info->name }}</h4>
            <p class="m-0">{{ $company_info->address }}</p>
            <p class="m-0">{{ $company_info->email }} | Tel: {{ $company_info->phone }}</p>
        </div>

        <!-- Divider Line -->
        <div class="line-first"></div>

        <!-- Transaction Information -->
        <div>
            <!-- <p class="m-0">Receipt No: <span>{{ $sale->id }}</span></p> -->
            <p class="m-0">Date: <span>{{ $sale->created_at->format('d/m/Y H:i') }}</span></p>
            <p class="m-0">Cashier: <span>{{ auth()->user()->name }}</span></p>
            <p class="m-0">Branch: <span>{{ auth()->user()->branch?->name }}</span></p>
        </div>

        <!-- Divider Line -->
        <div class="line-first"></div>

        <!-- Items Table -->
        <table class="table table-borderless table-sm">
            <thead class="text-nowrap">
                <tr>
                    <th>Item</th>
                    <th class="text-center">Qty</th>
                    <th class="text-end">Unit Price</th>
                    <th class="text-end">Total</th>
                </tr>
                <tr>
                    <th colspan="4">
                        <div class="line-first"></div>
                    </th>
                </tr>
            </thead>
            <tbody>
                @foreach($sale->saleItems as $saleItem)
                <tr>
                    <td>{{ Str::limit(ucwords($saleItem->product->name ?? 'Unknown Product'), 20) }}</td>
                    <td class="text-center">{{ $saleItem->quantity }}</td>
                    <td class="text-end">{{ number_format($saleItem->price, 2) }}</td>
                    <td class="text-end">{{ number_format($saleItem->total, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Divider Line -->
        <div class="line-first"></div>

        <!-- Totals -->
        <div class="mt-4">
            <!-- Sub Total -->
            <div class="d-flex justify-content-between fw-bold">
                <p class="m-0">Sub Total:</p>
                <p class="m-0">Ksh. {{ number_format($sale->total_amount, 2) }}</p>
            </div>

            <!-- Payments -->
            @foreach($sale->payments as $payment)
            <div class="d-flex justify-content-between">
                <p class="m-0">
                    {{ ucwords($payment->paymentMethod->name) }}:
                </p>
                <p class="m-0">Ksh. {{ number_format($payment->amount, 2) }}</p>
            </div>
            @endforeach

            <div class="d-flex justify-content-between fw-bold">
                <p class="m-0">Total Paid:</p>
                <p class="m-0">Ksh. {{ number_format( $sale->payments->sum('amount'), 2) }}</p>
            </div>

            <!-- Balance or Change -->
            <div class="d-flex justify-content-between fw-bold">
                @php
                $balance = $sale->total_amount - $sale->payments->sum('amount');
                @endphp
                @if ($balance < 0)
                    <p class="m-0">Change:</p>
                    <p class="m-0">Ksh. {{ number_format(abs($balance), 2) }}</p>
                    @else
                    <p class="m-0">Balance:</p>
                    <p class="m-0">Ksh. {{ number_format($balance, 2) }}</p>
                    @endif
            </div>
        </div>

        <!-- Divider Line -->
        <div class="line-last"></div>

        <!-- QR Code for Receipt -->
        <div class="text-center mt-4">
            <img src="data:image/png;base64,{{ $barcode }}" alt="Barcode">
        </div>

        <!-- Footer Message -->
        <p class="text-center m-0">THANK YOU FOR SHOPPING WITH US!</p>
        <p class="text-center m-0">Visit Again or Check Out Our Loyalty Program.</p>
    </div>
</div>

<div class="row">
    <div class="col-md-4 d-flex justify-content-center mx-auto mb-3">
        <a href="{{ route('sale.index') }}" class="btn btn-outline-success mx-2">
            <i class="fas fa-arrow-left"></i> Back to Sales
        </a>
        <button class="btn btn-outline-primary print-receipt mx-2" onclick="printDiv('receipt')">
            <i class="fas fa-print"></i> Print Receipt
        </button>
    </div>
</div>

@endsection