@extends('layouts.app')

@section('pageTitle', 'Employee Management')

@section('content')

<div class="row">
    <div class="col-md-12">

        <!-- Profile Card -->
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <div class="row">
                    <!-- Profile Image -->
                    <div class="col-md-3 text-center text-capitalize">
                        <img src="{{ asset(getImage($user->passport,'passport.png')) }}"
                            alt="Profile Picture" class="rounded-circle img-fluid profile-picture mb-3 p-1 shadow-sm">
                        <h3 class="fw-bold">{{ $user->name }}</h3>
                        <p class="text-muted">{{ $user->role->name ?? 'Role not assigned' }}</p>
                        <a href="{{ route('employee.edit', $user->id) }}" class="btn btn-primary">
                            <i class="fas fa-pencil"></i> Edit Profile
                        </a>
                    </div>

                    <!-- Profile Details -->
                    <div class="col-md-9">
                        <h4 class="mb-3">Employee Biodata</h4>
                        <div class="table-responive">
                            <table class="table table-borderless table-striped">
                                <tbody>
                                    <tr>
                                        <th class="text-muted">Email</th>
                                        <td>{{ $user->email }}</td>
                                    </tr>
                                    <tr>
                                        <th class="text-muted">Phone</th>
                                        <td>{{ $user->phone }}</td>
                                    </tr>
                                    <tr>
                                        <th class="text-muted">Gender</th>
                                        <td>{{ ucfirst($user->gender) }}</td>
                                    </tr>
                                    <tr>
                                        <th class="text-muted">Branch</th>
                                        <td>{{ $user->branch->name ?? 'Branch not assigned' }}</td>
                                    </tr>
                                    <tr>
                                        <th class="text-muted">Email Verified</th>
                                        <td>{{ $user->email_verified_at ? 'Verified' : 'Not Verified' }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- Employee Sales Data Section -->
<div class="row">
    <div class="col-md-12">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <h4 class="mb-3">Employee Sales Data</h4>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="salesDataTable">
                        <thead>
                            <tr>
                                <th class="text-muted">Sale Date</th>
                                <th class="text-muted">Customer Name</th>
                                <th class="text-muted">Amount</th>
                                <th class="text-muted">Status</th>
                                <th class="text-muted">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Sales data will be dynamically populated by AJAX -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>

@endsection

@push('script')
<script>
    $(document).ready(function() {
        var table = $('#salesDataTable').DataTable({
            processing: true,
            serverSide: true,
            order: [
                [0, 'asc']
            ], // Default sorting by sale date
            ajax: "{{ route('employee.show', $user->id) }}", // Route to fetch sales data
            columns: [{
                    data: 'sale_date',
                    name: 'sale_date'
                },
                {
                    data: 'customer_name',
                    name: 'customer_name'
                },
                {
                    data: 'amount',
                    name: 'amount'
                },
                {
                    data: 'status',
                    name: 'status'
                },
                {
                    data: 'action',
                    name: 'action'
                }
            ]
        });

        // Optional: Search functionality for mobile view (if search input exists)
        $('#search-input').on('keyup', function() {
            table.search(this.value).draw();
        });
    });
</script>
@endpush