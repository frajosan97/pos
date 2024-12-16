@extends('layouts.pdf')

@section('title', 'Inventory Report')

@section('content')

<!-- Information -->
<div class="pdf-information">
    <div class="table-responsive">
        <table class="table data-table items-list-table">
            <thead class="bg-light">
                <tr>
                    <th>Product Name</th>
                    <th>Category</th>
                    <th>Stock</th>
                    <th>Unit Price (Ksh)</th>
                    <th>Total Value (Ksh)</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @if (count($inventory) > 0)
                @foreach ($inventory as $key => $value)
                <tr>
                    <td>{{ ucwords($value->name) }}</td>
                    <td>{{ ucwords($value->catalogue->name) }}</td>
                    <td style="text-align: right">{{ $value->quantity }}</td>
                    <td style="text-align: right">{{ number_format($value->normal_price,2) }}</td>
                    <td style="text-align: right">{{ number_format($value->normal_price,2) }}</td>
                    <td>In Stock</td>
                </tr>
                @endforeach
                @else
                <tr>
                    <td colspan="6" style="text-align: center;">No inventory added yet</td>
                </tr>
                @endif
            </tbody>
        </table>
    </div>
</div>

@endsection