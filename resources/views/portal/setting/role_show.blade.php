@extends('layouts.app')

@section('pageTitle', 'Manage Role')

@section('content')

<!-- Control buttons -->
<ul class="nav nav-pills rounded bg-white mb-3">
    <li class="nav-item">
        <a href="#" class="nav-link">
            <i class="fas fa-user-circle"></i>
            {{ ucwords($role->name) }}
        </a>
    </li>
</ul>
<!-- / End control buttons -->

<div class="row">
    <div class="col-12">
        <!-- Desktop Table View -->
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <form id="permissions-form" action="{{ route('setting.roles.updatePermissions', $role->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="col-md-12 d-flex justify-content-between align-items-center bg-light mb-3 py-2">
                        <label for="">
                            <input type="checkbox" id="check-all" />
                            Check All
                        </label>
                        <button type="submit" class="btn btn-outline-success"><i class="fas fa-save"></i> Save Permissions</button>
                    </div>

                    <div class="row">
                        @php
                        // Group permissions by the first part of the slug
                        $grouped_permissions = [];
                        foreach ($all_permissions as $key => $value) {
                        $first_word = explode('_', $value->slug)[0]; // Get the first part before the underscore
                        $grouped_permissions[$first_word][] = $value;
                        }
                        @endphp

                        @foreach ($grouped_permissions as $group => $permissions)
                        <div class="col-md-12 permission-group mb-3">
                            <h6 class="bg-light p-1 m-0 border-start border-end border-top">
                                {{ ucwords($group) }}
                            </h6>
                            <div class="col-md-12 border-start border-end border-bottom pt-2">
                                <div class="row">
                                    @foreach ($permissions as $permission)
                                    <div class="col-md-3">
                                        <label for="{{ $permission->id }}">
                                            <!-- Check if permission is attached to the role -->
                                            <input type="checkbox" class="permission-checkbox" name="permissions[]" value="{{ $permission->id }}"
                                                id="{{ $permission->id }}"
                                                @if($role->permissions->contains($permission->id)) checked @endif />
                                            {{ ucwords($permission->name) }}
                                        </label>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        @endforeach
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
        // Check All Checkbox Functionality
        $('#check-all').on('change', function() {
            $('.permission-checkbox').prop('checked', $(this).prop('checked'));
        });

        // Ensure Check All is updated when individual checkboxes are toggled
        $('.permission-checkbox').on('change', function() {
            if ($('.permission-checkbox:checked').length === $('.permission-checkbox').length) {
                $('#check-all').prop('checked', true);
            } else {
                $('#check-all').prop('checked', false);
            }
        });

        // Validate and handle form submission
        $("#permissions-form").validate({
            submitHandler: function(form, event) {
                event.preventDefault();

                // Confirmation Dialog Before Submitting
                Swal.fire({
                    title: 'Update Permissions',
                    text: 'Are you sure you want to update permissions?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, update',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Show Processing Indicator
                        Swal.fire({
                            title: 'Processing...',
                            text: 'Please wait while the permissions are being updated.',
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
                                    window.location.reload();
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