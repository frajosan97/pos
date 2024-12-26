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

                        <!-- Role Selection -->
                        <div class="col-md-6">
                            <label for="role">Role</label>
                            <select class="form-control" id="role" name="role" required>
                                <option value="">Select Role</option>
                                @foreach ($roles as $role)
                                <option value="{{ $role->id }}" {{ old('role') == $role->id ? 'selected' : '' }}>
                                    {{ ucwords($role->name) }}
                                </option>
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

                        <div class="col-md-6">
                            <label for="id_number">Id Number</label>
                            <input type="text" class="form-control" id="id_number" name="id_number" placeholder="ID Number" required />
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