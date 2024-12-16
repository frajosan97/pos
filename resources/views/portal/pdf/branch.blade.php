@extends('layouts.pdf')

@section('title', 'company branches')

@section('content')

<!-- Information -->
<div class="pdf-information">
    <div class="table-responsive">
        <table class="table data-table items-list-table">
            <thead class="bg-light">
                <tr>
                    <th>branch name</th>
                    <th>county</th>
                    <th>constituency</th>
                    <th>ward</th>
                    <th>location</th>
                    <th>Remarks</th>
                </tr>
            </thead>
            <tbody>
                @if (count($branch) > 0)
                @foreach ($branch as $key => $value)
                <tr>
                    <td>{{ ucwords($value->name) }}</td>
                    <td>{{ ucwords($value->county->name ?? 'Not Set') }}</td>
                    <td>{{ ucwords($value->constituency->name ?? 'Not Set') }}</td>
                    <td>{{ ucwords($value->ward->name ?? 'Not Set') }}</td>
                    <td>{{ ucwords($value->location->name ?? 'Not Set') }}</td>
                    <td></td>
                </tr>
                @endforeach
                @else
                <tr>
                    <td colspan="6" style="text-align: center;">No branches registered yet</td>
                </tr>
                @endif
            </tbody>
        </table>
    </div>
</div>

@endsection