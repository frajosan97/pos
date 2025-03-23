@extends('layouts.pdf')

@section('title', 'sales Report')

@section('content')

<!-- Information -->
<div class="pdf-information">
    <div class="table-responsive">
        <table class="table data-table items-list-table">
            <thead class="bg-light">
                <tr>
                    <th>Receipt No.</th>
                    <th>Billed (KES)</th>
                    <th>Paid (KES)</th>
                    <th>Pay Mode</th>
                    <th>Done By</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @if (count($sales) > 0)
                @foreach ($sales as $sale)
                <tr>
                    <td>{{ str_pad($sale->id, 6, '0', STR_PAD_LEFT) }}</td>
                    <td>{{ number_format($sale->total_amount,2) }}</td>
                    <td>{{ number_format($sale->payments->sum('amount'),2) }}</td>
                    <td>
                        @foreach($sale->payments as $payment)
                        <p class="p-0 m-0">{{ ucwords($payment->paymentMethod->name.' - KES '.number_format($payment->amount,2)) }}</p>
                        @endforeach
                    </td>
                    <td>{{ ucwords($sale->user->name ?? 'No User') }}</td>
                    <td>{{ ucwords($sale->status) }}</td>
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