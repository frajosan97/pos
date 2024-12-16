@extends('layouts.pdf')

@section('title', 'sales Report')

@section('content')

<!-- Information -->
<div class="pdf-information">
    <div class="table-responsive">
        <table class="table data-table items-list-table">
            <thead class="bg-light">
                <tr>
                    <th>Invoice No.</th>
                    <th>Billed</th>
                    <th>Paid</th>
                    <th>Pay Mode</th>
                    <th>Done By</th>
                    <th>Status</th>
                    <th>Remarks</th>
                </tr>
            </thead>
            <tbody>
                @if (count($sales) > 0)
                @foreach ($sales as $key => $value)
                <tr>
                    <td>{{ str_pad($sale->id, 6, '0', STR_PAD_LEFT) }}</td>
                    <td>{{ number_format($sale->total_amount,2) }}</td>
                    <td>{{ number_format($sale->payments->sum('amount'),2) }}</td>
                    <td>{{ ucwords(($sale->payments->first()->paymentMethod->name) ?? 'Unknown Method') }}</td>
                    <td>{{ ucwords('Unknown Cashier') }}</td>
                    <td>{{ ucwords($sale->status) }}</td>
                    <td></td>
                </tr>
                @endforeach
                @else
                <tr>
                    <td colspan="7" style="text-align: center;">No sales made yet</td>
                </tr>
                @endif
            </tbody>
        </table>
    </div>
</div>

@endsection