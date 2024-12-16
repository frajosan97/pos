@extends('layouts.app')

@section('pageTitle', 'system roles')

@section('content')

<!-- control buttons -->
<ul class="nav nav-pills rounded bg-white mb-3">
    <li class="nav-item">
        <a href="#" class="nav-link" data-bs-toggle="modal" data-bs-target="#roleModal">
            <i class="fas fa-plus-circle"></i> Create new role
        </a>
    </li>
</ul>
<!-- / end control buttons -->

<div class="row">
    <div class="col-12">
        <!-- Desktop Table View -->
        <div class="card border-0 shadow-sm d-none d-md-block">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle text-nowrap" id="counties-table">
                        <thead class="table-dark text-center text-capitalize">
                            <tr>
                                <th>role id</th>
                                <th>name</th>
                                <th>description</th>
                                <th class="pw-5">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Table data will be loaded via AJAX -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Mobile View -->
        <div class="d-block d-md-none">
            <div class="input-group mb-3 shadow-sm">
                <input type="search" class="form-control" id="search-input" placeholder="Search role...">
                <span class="input-group-text bg-primary text-white">
                    <i class="fas fa-search"></i>
                </span>
            </div>
            <div class="list-group" id="counties-list">
                <!-- Mobile list data will be appended here -->
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="roleModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="roleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form id="role-form" action="{{ route('setting.role.store') }}" method="post">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="roleModalLabel">Add New role</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-capitalize">
                    <div class="row">
                        <div class="col-md-12">
                            @csrf
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="role">role</label>
                                <input type="text" name="role" id="role" placeholder="role" class="form-control" />
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="name">name</label>
                                <input type="text" name="name" id="name" placeholder="name" class="form-control" />
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="description">description</label>
                                <textarea name="description" id="description" placeholder="description" class="form-control"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" id="save-button">Save changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('script')
<script>
    $(document).ready(function() {
        var table = $('#counties-table').DataTable({
            processing: true,
            serverSide: true,
            order: [
                [0, 'asc']
            ],
            ajax: "{{ route('setting.role') }}",
            columns: [{
                    data: 'role',
                    name: 'role'
                },
                {
                    data: 'name',
                    name: 'name'
                },
                {
                    data: 'description',
                    name: 'description'
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                }
            ],
            drawCallback: function(settings) {
                var data = this.api().rows({
                    page: 'current'
                }).data();
                var listGroup = $('#counties-list');
                listGroup.empty();

                if (data.length === 0) {
                    listGroup.append(`
                        <div class="list-group-item text-center border-0 bg-light">
                            <p class="text-muted mb-0">No data available</p>
                        </div>
                    `);
                } else {
                    $.each(data, function(index, value) {
                        listGroup.append(`
                            <div class="list-group-item border rounded shadow-sm mb-2">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="m-0 text-capitalize">${value.name} ( ${value.role} )</h6>
                                    </div>
                                   <div>
                                        ${value.action}
                                    </div>
                                </div>
                            </div>
                        `);
                    });
                }
            }
        });

        // Open modal to create new role
        $('#role-add').on('click', function() {
            // Set the modal content for creating a new role
            $('#roleModalLabel').text('Add New role');
            $('#role-form')[0].reset(); // Reset the form fields
            $('#role-form').attr('action', "{{ route('setting.role.store') }}"); // Set the action URL for creating
            $('#role-form').find('input[name="_method"]').remove(); // Remove any existing method override
            $('#save-button').text('Save Changes'); // Update the button text
            // Show the modal
            $('#roleModal').modal('show');
        });

        // Open modal to edit role
        $(document).on('click', '.edit-role', function() {
            var roleId = $(this).data('role-id');
            var role = $(this).data('role-role');
            var roleName = $(this).data('role-name');
            var roleDescription = $(this).data('role-description');
            // Set the modal content for editing an existing role
            $('#roleModalLabel').text('Edit role');
            $('#role').val(role);
            $('#name').val(roleName);
            $('#description').val(roleDescription);
            // Dynamically set the action URL for the form
            var actionUrl = "{{ route('setting.role.update', ':id') }}".replace(':id', roleId);
            $('#role-form').attr('action', actionUrl);
            // Add the _method hidden input for PUT if it doesn't already exist
            if (!$('#role-form').find('input[name="_method"]').length) {
                $('#role-form').append('<input type="hidden" name="_method" value="PUT">');
            }
            $('#save-button').text('Update role'); // Update the button text
            // Show the modal
            $('#roleModal').modal('show');
        });

        // Handle form submission for both create and edit
        $("#role-form").validate({
            submitHandler: function(form, event) {
                event.preventDefault();

                Swal.fire({
                    title: 'role Action',
                    text: 'Are you sure you want to proceed?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, Proceed',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: 'Processing...',
                            text: 'Please wait while we process your request.',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        $.ajax({
                            url: $(form).attr('action'),
                            type: $(form).attr('method'),
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
                                Swal.fire('Error!', xhr.responseJSON.error || xhr.responseJSON.message, 'error');
                            }
                        });
                    }
                });
            }
        });

        // Delete role
        $(document).on('click', '.delete-role', function() {
            var roleKey = $(this).data('role-id');

            Swal.fire({
                title: 'Are you sure you want to delete the role?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!',
                input: 'password',
                inputPlaceholder: 'Enter your password',
                inputAttributes: {
                    autocapitalize: 'off',
                    autocomplete: 'new-password', // Prevent autofill
                    required: true
                },
                preConfirm: (password) => {
                    return new Promise((resolve) => {
                        if (password) {
                            resolve(password);
                        } else {
                            Swal.showValidationMessage('Please enter your password');
                        }
                    });
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Processing...',
                        text: 'Please wait while the role is being deleted.',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    $.ajax({
                        url: `{{ route('setting.role.destroy', '') }}/${roleKey}`,
                        method: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}',
                            password: result.value
                        },
                        success: function(response) {
                            Swal.fire({
                                title: 'Success',
                                text: response.success,
                                icon: 'success'
                            }).then(() => {
                                table.ajax.reload(null, true);
                            });
                        },
                        error: function(xhr) {
                            Swal.fire('Error!', xhr.responseJSON.error || xhr.responseJSON.message, 'error');
                        }
                    });
                }
            });
        });

        // Search in mobile view
        $('#search-input').on('keyup', function() {
            table.search(this.value).draw();
        });
    });
</script>
@endpush