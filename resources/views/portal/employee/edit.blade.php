@extends('layouts.app')

@section('pageTitle', ucwords('editing ' . $user->name))

@section('content')

<div class="row">
    <div class="col-md-12">
        <div class="card shadow-sm border-0">
            <div class="card-body">

                <form id="update-employee-form" action="{{ route('employee.update', $user->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <!-- Section: Basic Details -->
                    <h5 class="bg-light p-1 rounded mb-3">Basic Details</h5>
                    <div class="row">
                        <div class="col-md-3 mb-3 text-center">
                            <!-- Display current passport image -->
                            <img id="passportPreview"
                                src="{{ asset(getImage($user->passport, 'passport.png')) }}"
                                alt="Profile Picture"
                                class="img-fluid profile-picture mb-3 p-1 shadow-sm"
                                style="max-height: 150px; border: 1px solid #ddd;">

                            <div class="">
                                <!-- Hidden file input -->
                                <input type="file" name="passport" id="passport" class="d-none" accept="image/*">
                                <!-- Button to trigger file input -->
                                <button type="button" id="changePassportBtn" class="btn btn-sm btn-info rounded-pill">
                                    <i class="fas fa-image"></i> Change Passport
                                </button>
                            </div>
                        </div>
                        <div class="col-md-9">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="user_name" class="form-label">Username</label>
                                        <input type="text" id="user_name" name="user_name" class="form-control" value="{{ $user->user_name }}" readonly required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="name" class="form-label">Full Name</label>
                                        <input type="text" id="name" name="name" class="form-control" value="{{ $user->name }}" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="gender" class="form-label">Gender</label>
                                        <select id="gender" name="gender" class="form-select">
                                            <option value="Male" {{ $user->gender == 'Male' ? 'selected' : '' }}>Male</option>
                                            <option value="Female" {{ $user->gender == 'Female' ? 'selected' : '' }}>Female</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if(auth()->user()->hasPermission('user_edit'))
                    <!-- Section: Contact Details -->
                    <h5 class="bg-light p-1 rounded mb-3">Contact Details</h5>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="phone" class="form-label">Phone</label>
                                <input type="text" id="phone" name="phone" class="form-control" value="{{ $user->phone }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" id="email" name="email" class="form-control" value="{{ $user->email }}" readonly required>
                            </div>
                        </div>
                    </div>

                    <!-- Section: Work Details -->
                    <h5 class="bg-light p-1 rounded mb-3">Work Details</h5>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="branch_id" class="form-label">Branch</label>
                                <select id="branch_id" name="branch_id" class="form-select">
                                    @foreach ($branches as $branch)
                                    <option value="{{ $branch->id }}" {{ $user->branch_id == $branch->id ? 'selected' : '' }}>
                                        {{ ucwords($branch->name) }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="role" class="form-label">Role</label>
                                <select id="role" name="role" class="form-select">
                                    @foreach ($roles as $role)
                                    <option value="{{ $role->id }}">
                                        {{ ucwords($role->name) }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Section: Status -->
                    <h5 class="bg-light p-1 rounded mb-3">Status</h5>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="status" class="form-label">Status</label>
                                <select id="status" name="status" class="form-select">
                                    <option value="1" {{ $user->status == 1 ? 'selected' : '' }}>Active</option>
                                    <option value="0" {{ $user->status == 0 ? 'selected' : '' }}>Inactive</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="id_number" class="form-label">ID Number</label>
                                <input type="text" id="id_number" name="id_number" class="form-control" value="{{ $user->id_number }}">
                            </div>
                        </div>
                    </div>
                    @endif

                    <hr>

                    <div class="row">
                        <div class="col-md-12 text-end">
                            <button type="submit" class="btn btn-primary">Update User</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@push('script')
<script>
    $(document).ready(function() {
        // Trigger file input when the button is clicked
        $('#changePassportBtn').on('click', function() {
            $('#passport').click();
        });

        // Update the image preview when a new file is selected
        $('#passport').on('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    $('#passportPreview').attr('src', e.target.result);
                };
                reader.readAsDataURL(file);
            }
        });

        // Validate and handle form submission
        $("#update-employee-form").validate({
            submitHandler: function(form, event) {
                event.preventDefault();

                Swal.fire({
                    title: 'update new employee',
                    text: 'Are you sure you want to update new employee?',
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
                            text: 'Please wait while the employee is being updated.',
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
                                    window.location.reload();
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