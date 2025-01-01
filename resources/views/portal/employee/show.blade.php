@extends('layouts.app')

@section('pageTitle', ucwords($user->name))

@section('content')

<div class="row">
    <div class="col-md-12">

        <!-- Profile Card -->
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <div class="row">
                    <!-- Profile Image -->
                    <div class="col-md-3 text-center text-capitalize">
                        <img src="{{ asset(getImage($user->passport, 'passport.png')) }}"
                            alt="Profile Picture" class="rounded-circle img-fluid profile-picture mb-3 p-1 shadow-sm">
                        <h3 class="fw-bold">{{ $user->name }}</h3>
                        <a href="{{ route('employee.edit', $user->id) }}" class="btn btn-outline-primary">
                            <i class="fas fa-pencil"></i> Edit Profile
                        </a>
                    </div>

                    <!-- Profile Details -->
                    <div class="col-md-9">
                        <h4 class="mb-3">Employee Biodata</h4>
                        <div class="table-responsive">
                            <table class="table table-borderless table-sm border table-striped">
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
                                    <tr>
                                        <th class="text-muted">All Time Commision</th>
                                        <td>Ksh {{ $allTimeCommission }}</td>
                                    </tr>
                                    <tr>
                                        <th class="text-muted">Month to Date Commision</th>
                                        <td>Ksh {{ $monthToDateCommission }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- User Permissions Section -->
                    <div class="col-md-12">
                        <h5 class="bg-light p-2">User Permissions</h5>
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered table-sm">
                                <thead class="table-primary">
                                    <tr>
                                        <th>#</th>
                                        <th>Permission</th>
                                        <th>More Description</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if($user->permissions->isEmpty())
                                    <tr>
                                        <td colspan="3" class="text-center text-muted">No permissions assigned.</td>
                                    </tr>
                                    @else
                                    @foreach ($user->permissions as $key => $permission)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td>{{ $permission->name }}</td>
                                        <td>
                                            <!-- Display additional information based on permission slug -->
                                            @switch($permission->slug)
                                            @case('manager_branch')
                                            <ul>
                                                @foreach($user->selectedBranches() as $branch)
                                                <li>{{ $branch->name }}</li>
                                                @endforeach
                                            </ul>
                                            @break

                                            @case('manager_product')
                                            <ul>
                                                @foreach($user->selectedProducts() as $product)
                                                <li>{{ $product->name }}</li>
                                                @endforeach
                                            </ul>
                                            @break

                                            @case('manager_catalogue')
                                            <ul>
                                                @foreach($user->selectedCatalogues() as $catalogue)
                                                <li>{{ $catalogue->name }}</li>
                                                @endforeach
                                            </ul>
                                            @break

                                            @default
                                            <!-- You can add a default case here if needed -->
                                            @endswitch
                                        </td>
                                    </tr>
                                    @endforeach
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
            </div>
        </div>

    </div>
</div>

@endsection