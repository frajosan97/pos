@extends('layouts.app')

@section('pageTitle', 'Create Employee')

@section('content')

@if(auth()->user()->hasPermission('user_create'))

<div class="row">
    <div class="col-md-12">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <form id="create-employee-form" action="{{ route('employee.store') }}" method="post">
                    <!-- CSRF Token for form security -->
                    @csrf

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
                        <div class="col-md-6">
                            <label for="phone">Phone Number</label>
                            <input type="text" class="form-control" id="phone" name="phone" placeholder="Phone Number" required />
                        </div>

                        <!-- ID Number Field -->
                        <div class="col-md-6">
                            <label for="id_number">ID Number</label>
                            <input type="text" class="form-control" id="id_number" name="id_number" placeholder="ID Number" required />
                        </div>

                        <div class="col-md-12">
                            <hr>
                        </div>

                        <!-- User Permissions -->
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

                        <div class="col-md-12">
                            <hr>
                        </div>

                        <div class="col-md-12 text-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-plus-circle"></i> Create New Employee
                            </button>
                        </div>
                    </div>

                </form>

            </div>
        </div>
    </div>
</div>

@else
@include('layouts.partials.no_permission')
@endif

@endsection

@push('script')
<script>
    $(document).ready(function() {
        // Initialize select2 for multiple selection dropdowns
        $('.select2-multiple').select2({
            placeholder: "Select options",
            allowClear: true
        });

        // Toggle visibility of branch select when manager_branch checkbox is checked
        $('#manager_branch').on('change', function() {
            $('.branch-select').toggle(this.checked);
        });

        // Toggle visibility of catalogue select when manager_catalogue checkbox is checked
        $('#manager_catalogue').on('change', function() {
            $('.catalogue-select').toggle(this.checked);
        });

        // Toggle visibility of product select when manager_product checkbox is checked
        $('#manager_product').on('change', function() {
            $('.product-select').toggle(this.checked);
        });

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