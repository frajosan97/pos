@extends('layouts.app')

@section('pageTitle', 'create employee')

@section('content')

<form id="create-employee-form" action="{{ route('employee.store') }}" method="post">
    <!-- csrf token -->
    @csrf

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="row g-3">
                <!-- Branch -->
                <div class="col-md-6">
                    <label for="branch">Branch</label>
                    <select class="form-control" id="branch" name="branch" required>
                        <option value="">Select Branch</option>
                        @foreach ($branches as $key => $value)
                        <option value="{{ $value->id }}">{{ ucwords($value->name) }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Role -->
                <div class="col-md-6">
                    <label for="role">Role</label>
                    <select class="form-control" id="role" name="role" required>
                        <option value="">Select Role</option>
                        @foreach ($roles as $key => $value)
                        <option value="{{ $value->id }}" {{ old('role') == $value->id ? 'selected' : '' }}>
                            {{ ucwords($value->name) }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <!-- Full Name -->
                <div class="col-md-4">
                    <label for="name">Full Name</label>
                    <input type="text" class="form-control" id="name" name="name" placeholder="Full Name" required />
                </div>

                <!-- Email -->
                <div class="col-md-4">
                    <label for="email">Email</label>
                    <input type="email" class="form-control" id="email" name="email" placeholder="Email Address" required />
                </div>

                <!-- Phone Number -->
                <div class="col-md-4">
                    <label for="phone">Phone Number</label>
                    <input type="text" class="form-control" id="phone" name="phone" placeholder="Phone Number" required />
                </div>

            </div>
        </div>

        <div class="card-footer bg-light text-end">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-plus-circle"></i> Create New Employee
            </button>
        </div>
    </div>

</form>

@endsection

@push('script')
<script>
    $(document).ready(function() {
        // Validate and handle form submission
        $("#create-employee-form").validate({
            submitHandler: function(form, event) {
                event.preventDefault();

                Swal.fire({
                    title: 'Create new employee',
                    text: 'Are you sure you want to create new employee?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, Update',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: 'Processing...',
                            text: 'Please wait while the employee is being created.',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        $.ajax({
                            url: $(form).attr('action'),
                            type: 'POST',
                            data: new FormData(form),
                            contentType: false,
                            processData: false,
                            success: function(response) {
                                Swal.fire({
                                    title: 'Success',
                                    text: response.success,
                                    icon: 'success',
                                }).then((result) => {
                                    window.location.href =
                                        "{{ route('employee.index') }}";
                                });
                            },
                            error: function(xhr) {
                                Swal.fire('Error!', xhr.responseJSON.error || xhr
                                    .responseJSON.message, 'error');
                            }
                        });
                    }
                });
            }
        });
    });
</script>
@endpush