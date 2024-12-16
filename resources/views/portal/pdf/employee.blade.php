@extends('layouts.pdf')

@section('title', 'employees list')

@section('content')

<!-- Information -->
<div class="pdf-information">
    <div class="table-responsive">
        <table class="table data-table items-list-table">
            <thead class="bg-light">
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Branch</th>
                    <th>Role</th>
                    <th>Remarks</th>
                </tr>
            </thead>
            <tbody>
                @if (count($employee) > 0)
                @foreach ($employee as $key => $value)
                <tr>
                    <td>{{ ucwords($value->name) }}</td>
                    <td style="color: blue;">{{ $value->email }}</td>
                    <td>{{ ucwords($value->phone) }}</td>
                    <td>{{ ucwords($value->branch->name) }}</td>
                    <td>{{ ucwords($value->role->name) }}</td>
                    <td></td>
                </tr>
                @endforeach
                @else
                <tr>
                    <td colspan="6" style="text-align: center;">No employees registered yet</td>
                </tr>
                @endif
            </tbody>
        </table>
    </div>
</div>

@endsection