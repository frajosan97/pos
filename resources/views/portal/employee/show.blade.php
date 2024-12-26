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
                        <img src="{{ asset(getImage($user->passport,'passport.png')) }}"
                            alt="Profile Picture" class="rounded-circle img-fluid profile-picture mb-3 p-1 shadow-sm">
                        <h3 class="fw-bold">{{ $user->name }}</h3>
                        @foreach ($user->roles as $role)
                        <p class="text-muted">{{ $role->name }}</p>
                        @endforeach
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
@endsection