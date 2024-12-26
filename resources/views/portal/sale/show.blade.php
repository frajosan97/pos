@extends('layouts.app')

@section('pageTitle', 'Invoice')

@section('content')

<div class="row">
    <div class="col-md-4 mx-auto mb-3 bg-white p-2 shadow-sm" id="receipt">
        <style>
            .line-first {
                border-top: 1px dashed #000;
                margin: 2px 0;
            }

            .line-last {
                border-top: 1px dashed #000;
                margin: 2px 0;
            }
        </style>

        <!-- Company Information -->
        <h4 class="text-center m-0">{{ $company_info->name }}</h4>
        <p class="text-center m-0">{{ $company_info->address }}</p>
        <p class="text-center m-0">{{ $company_info->email }}</p>
        <p class="text-center m-0">Tel.: {{ $company_info->phone }}</p>

        <!-- Divider Line -->
        <div class="line-first"></div>
        <div class="line-last"></div>

        <!-- Branch Information -->
        <div class="branch-info">
            <p class="m-0">Cashier: <span>{{ auth()->user()->name }}</span></p>
            <p class="m-0">Branch: <span>{{ auth()->user()->branch?->name }}</span></p>
        </div>

        <!-- Divider Line -->
        <div class="line-first"></div>
        <div class="line-last"></div>

        <table class="table table-borderless table-sm m-0">
            <thead class="text-nowrap">
                <tr>
                    <th class="p-0">Item</th>
                    <th class="p-0 px-2">Qty</th>
                    <th class="p-0 px-2">Unit Price</th>
                    <th class="p-0 text-end">Total</th>
                </tr>
                <tr>
                    <th class="p-0" colspan="4">
                        <div class="line-first"></div>
                    </th>
                </tr>
            </thead>
            <tbody>
                @foreach($sale->saleItems as $saleItem)
                <tr>
                    <td class="p-0">{{ ucwords($saleItem->product->name ?? 'Unknown Product') }}</td>
                    <td class="p-0 px-2">{{ $saleItem->quantity }}</td>
                    <td class="p-0 px-2 text-end">{{ number_format($saleItem->price, 2) }}</td>
                    <td class="p-0 text-end">{{ number_format($saleItem->total, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Divider Line -->
        <div class="line-first"></div>
        <div class="line-last"></div>

        <!-- Summations -->
        <div class="d-flex justify-content-between">
            <p class="m-0 fw-bold">Sub Total</p>
            <p class="m-0 fw-bold">Ksh. {{ number_format($sale->total_amount, 2) }}</p>
        </div>
        <div class="d-flex justify-content-between">
            <p class="m-0 fw-bold">CASH</p>
            <p class="m-0 fw-bold">Ksh. {{ number_format($sale->payments->sum('amount'), 2) }}</p>
        </div>
        <div class="d-flex justify-content-between">
            @php
            $balance = $sale->total_amount - $sale->payments->sum('amount');
            @endphp

            @if ($balance < 0)
                <p class="m-0 fw-bold">CHANGE</p>
                <p class="m-0 fw-bold">Ksh. {{ number_format(abs($balance), 2) }}</p>
                @else
                <p class="m-0 fw-bold">UNPAID BALANCE</p>
                <p class="m-0 fw-bold">Ksh. {{ number_format($balance, 2) }}</p>
                @endif
        </div>

        <!-- Divider Line -->
        <div class="line-last"></div>

        <p class="text-center m-0">THANK YOU!</p>
        <p class="text-center m-0">Glad to see you again!</p>
    </div>
</div>

<div class="row">
    <div class="col-md-4 d-flex justify-content-center mx-auto mb-3">
        <a href="/" class="btn btn-outline-success mx-2">
            <i class="fas fa-arrow-left"></i> Back to Sale
        </a>
        <button class="btn btn-outline-primary print-receipt mx-2" onclick="printDiv('receipt')">
            <i class="fas fa-print"></i> Print Receipt
        </button>
    </div>
</div>

@endsection