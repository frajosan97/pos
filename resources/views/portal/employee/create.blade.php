@extends('layouts.app')

@section('pageTitle', 'create Employee')

@section('content')

<div class="container-fluid">

    <form id="create-employee-form" action="{{ route('employee.store') }}" method="post" enctype="multipart/form-data">
        @csrf

        <!-- DATA CARD -->
        <div class="card shadow-sm border-0">
            <div class="card-body">

                <ul class="nav nav-tabs" id="myTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link text-capitalize active" id="bio-data-tab" data-bs-toggle="tab" data-bs-target="#bio-data-tab-pane" type="button" role="tab" aria-controls="bio-data-tab-pane" aria-selected="true">
                            <i class="fas fa-user"></i> bio data
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link text-capitalize" id="permission-tab" data-bs-toggle="tab" data-bs-target="#permission-tab-pane" type="button" role="tab" aria-controls="permission-tab-pane" aria-selected="false">
                            <i class="fas fa-user-cog"></i> permissions
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link text-capitalize" id="kyc-tab" data-bs-toggle="tab" data-bs-target="#kyc-tab-pane" type="button" role="tab" aria-controls="kyc-tab-pane" aria-selected="false">
                            <i class="fas fa-file-alt"></i> KYC Information
                        </button>
                    </li>
                </ul>
                <div class="tab-content" id="myTabContent">
                    <div class="tab-pane fade py-3 show active" id="bio-data-tab-pane" role="tabpanel" aria-labelledby="bio-data-tab" tabindex="0">
                        <!-- Tab title -->
                        <h5 class="bg-light text-capitalize p-2">bio data</h5>
                        <!-- Tab content -->
                        <div class="row g-3">
                            <!-- Branch Selection -->
                            <div class="col-md-6">
                                <label for="branch">Branch</label>
                                <select class="form-control" id="branch" name="branch" required>
                                    <option value="">Select Branch</option>
                                    @foreach ($branches as $branch)
                                    <option value="{{ $branch->id }}">{{ ucwords($branch->name) }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Username Field -->
                            <div class="col-md-6">
                                <label for="user_name">Username</label>
                                <input type="text" class="form-control" id="user_name" name="user_name" placeholder="Username" required />
                            </div>

                            <!-- Full Name Field -->
                            <div class="col-md-6">
                                <label for="name">Full Name</label>
                                <input type="text" class="form-control" id="name" name="name" placeholder="Full Name" required />
                            </div>

                            <!-- Email Address Field -->
                            <div class="col-md-6">
                                <label for="email">Email</label>
                                <input type="email" class="form-control" id="email" name="email" placeholder="Email Address" required />
                            </div>

                            <!-- Phone Number Field -->
                            <div class="col-md-4">
                                <label for="phone">Phone Number</label>
                                <input type="text" class="form-control" id="phone" name="phone" placeholder="Phone Number" required />
                            </div>

                            <!-- ID Number Field -->
                            <div class="col-md-4">
                                <label for="id_number">ID Number</label>
                                <input type="text" class="form-control" id="id_number" name="id_number" placeholder="ID Number" required />
                            </div>

                            <!-- Commission Rate Field -->
                            <div class="col-md-4">
                                <label for="commission_rate">Commission Rate</label>
                                <input type="text" class="form-control" id="commission_rate" name="commission_rate" placeholder="Commision Rate in percentage (%)" required />
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade py-3" id="permission-tab-pane" role="tabpanel" aria-labelledby="permission-tab" tabindex="0">
                        <!-- Tab title -->
                        <h5 class="bg-light text-capitalize p-2">permissions</h5>
                        <!-- Tab content -->
                        <div class="row g-3">
                            @php
                            // Group permissions by the first part of the slug
                            $grouped_permissions = [];
                            foreach ($permissions as $key => $value) {
                            $first_word = explode('_', $value->slug)[0];
                            $grouped_permissions[$first_word][] = $value;
                            }
                            @endphp

                            @foreach ($grouped_permissions as $group => $permissions)
                            <div class="col-md-12 permission-group">
                                <h6 class="bg-light p-1 m-0 border-start border-end border-top">
                                    {{ ucwords($group) }}
                                </h6>
                                <div class="col-md-12 border-start border-end border-bottom pt-2">
                                    <div class="row">
                                        @foreach ($permissions as $permission)
                                        <div class="col-md-3">
                                            <label for="{{ $permission->id }}">
                                                <input type="checkbox" class="permission-checkbox" name="permissions[]" value="{{ $permission->id }}" id="{{ $permission->slug }}" />
                                                {{ ucwords($permission->name) }}
                                            </label>

                                            @switch($permission->slug)
                                            @case('manager_branch')
                                            <div class="branch-select" style="display: none;">
                                                <select name="viewable_branches[]" id="branches_{{ $permission->id }}" class="select2-multiple" multiple>
                                                    @foreach ($branches as $branch)
                                                    <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            @break

                                            @case('manager_catalogue')
                                            <div class="catalogue-select" style="display: none;">
                                                <select name="viewable_catalogues[]" id="catalogues_{{ $permission->id }}" class="select2-multiple" multiple>
                                                    @foreach ($catalogue as $catalogue_value)
                                                    <option value="{{ $catalogue_value->id }}">{{ $catalogue_value->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            @break

                                            @case('manager_product')
                                            <div class="product-select" style="display: none;">
                                                <select name="viewable_products[]" id="products_{{ $permission->id }}" class="select2-multiple" multiple>
                                                    @foreach ($products as $product)
                                                    <option value="{{ $product->id }}">{{ $product->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            @break

                                            @default
                                            <!-- Optionally, you can add a default case if needed -->
                                            @endswitch
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="tab-pane fade py-3" id="kyc-tab-pane" role="tabpanel" aria-labelledby="kyc-tab" tabindex="0">
                        <!-- Tab title -->
                        <h5 class="bg-light text-capitalize p-2">KYC Information</h5>
                        <!-- Tab content -->
                        <div class="row g-3">

                            @foreach (kyc_docs() as $key => $value)
                            <div class="col-md-6">
                                <label for="{{ $key }}">{{ $value }}</label>
                                <input type="file" class="form-control" id="{{ $key }}" name="{{ $key }}">
                            </div>
                            @endforeach

                        </div>
                    </div>
                </div>

                <div class="col-md-12 text-end mb-3">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Save Employee
                    </button>
                </div>

            </div>
        </div>
        <!-- / DATA CARD -->
    </form>

</div>

@endsection

@push('script')
<script>
    $(document).ready(function() {
        // Validate and handle form submission
        $("#create-employee-form").validate({
            submitHandler: function(form, event) {
                event.preventDefault();

                // Confirmation Dialog Before Submitting
                Swal.fire({
                    title: 'Create new employee',
                    text: 'Are you sure you want to create new employee?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, Create',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Show Processing Indicator
                        Swal.fire({
                            title: 'Processing...',
                            text: 'Please wait while the employee is being created.',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        // AJAX Form Submission
                        $.ajax({
                            url: $(form).attr('action'),
                            type: 'POST',
                            data: new FormData(form),
                            contentType: false,
                            processData: false,
                            success: function(response) {
                                // Success Notification and Redirect
                                Swal.fire({
                                    title: 'Success',
                                    text: response.success,
                                    icon: 'success',
                                }).then((result) => {
                                    window.location.href = "{{ route('employee.index') }}";
                                });
                            },
                            error: function(xhr) {
                                // Error Notification
                                Swal.fire('Error!', xhr.responseJSON.error || xhr.responseJSON.message, 'error');
                            }
                        });
                    }
                });
            }
        });
    });
</script>
@endpush