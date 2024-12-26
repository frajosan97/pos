@extends('layouts.app')

@section('pageTitle', 'Catalogue')

@section('content')

@if(auth()->user()->hasPermission('catalogue_view'))
@if(auth()->user()->hasPermission('catalogue_create'))
<!-- control buttons -->
<ul class="nav nav-pills rounded bg-white mb-3">
    <li class="nav-item">
        <a href="#" class="nav-link" data-bs-toggle="modal" data-bs-target="#catalogueModal">
            <i class="fas fa-plus-circle"></i> Create New Catalogue
        </a>
    </li>
</ul>
<!-- / end control buttons -->
@endif

<div class="row">
    <div class="col-12">
        <!-- Desktop Table View -->
        <div class="card border-0 shadow-sm d-none d-md-block">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle text-nowrap" id="catalogues-table">
                        <thead class="table-dark text-center">
                            <tr>
                                <th>Catalogue</th>
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
                <input type="search" class="form-control" id="search-input" placeholder="Search catalogue...">
                <span class="input-group-text bg-primary text-white">
                    <i class="fas fa-search"></i>
                </span>
            </div>
            <div class="list-group" id="catalogues-list">
                <!-- Mobile list data will be appended here -->
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="catalogueModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="catalogueModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form id="catalogue-form" action="{{ route('catalogue.store') }}" method="post">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="catalogueModalLabel">Add New Catalogue</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-capitalize">
                    <div class="row">
                        <div class="col-md-12">
                            @csrf
                        </div>
                        <div class="col-md-12">
                            <input type="hidden" name="branch_id" value="{{ auth()->user()->branch_id }}">
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="catalogue">Catalogue</label>
                                <input type="text" name="catalogue" id="catalogue" placeholder="Catalogue" class="form-control" />
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

@else
@include('layouts.partials.no_permission')
@endif

@endsection

@push('script')
<script>
    $(document).ready(function() {
        var table = $('#catalogues-table').DataTable({
            processing: true,
            serverSide: true,
            order: [
                [0, 'asc']
            ],
            ajax: "{{ route('catalogue.index') }}",
            columns: [{
                    data: 'name',
                    name: 'name'
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
                var listGroup = $('#catalogues-list');
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
                                        <h6 class="m-0 text-capitalize">${value.name}</h6>
                                    </div>
                                    <div class="text-end">
                                        <button class="btn btn-info btn-sm edit-catalogue" data-catalogue-id="${value.id}" data-catalogue-name="${value.name}">
                                            <i class="fas fa-edit"></i> Edit
                                        </button>
                                        <button class="btn btn-danger btn-sm delete-catalogue" data-catalogue-id="${value.id}">
                                            <i class="fas fa-trash-alt"></i> Delete
                                        </button>
                                    </div>
                                </div>
                            </div>
                        `);
                    });
                }
            }
        });

        // Open modal to create new catalogue
        $('#catalogue-add').on('click', function() {
            // Set the modal content for creating a new catalogue
            $('#catalogueModalLabel').text('Add New Catalogue');
            $('#catalogue-form')[0].reset(); // Reset the form fields
            $('#catalogue-form').attr('action', "{{ route('catalogue.store') }}"); // Set the action URL for creating
            $('#catalogue-form').find('input[name="_method"]').remove(); // Remove any existing method override
            $('#save-button').text('Save Changes'); // Update the button text

            // Show the modal
            $('#catalogueModal').modal('show');
        });

        // Open modal to edit catalogue
        $(document).on('click', '.edit-catalogue', function() {
            var catalogueId = $(this).data('catalogue-id');
            var catalogueName = $(this).data('catalogue-name');

            // Set the modal content for editing an existing catalogue
            $('#catalogueModalLabel').text('Edit Catalogue');
            $('#catalogue').val(catalogueName);
            $('#catalogue-form').attr('action', '/catalogue/' + catalogueId); // Set the action URL for updating
            // Add the _method hidden input for PUT
            if (!$('#catalogue-form').find('input[name="_method"]').length) {
                $('#catalogue-form').append('<input type="hidden" name="_method" value="PUT">');
            }
            $('#save-button').text('Update Catalogue'); // Update the button text

            // Show the modal
            $('#catalogueModal').modal('show');
        });

        // Handle form submission for both create and edit
        $("#catalogue-form").validate({
            submitHandler: function(form, event) {
                event.preventDefault();

                Swal.fire({
                    title: 'Catalogue Action',
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
                                    text: response,
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

        // Delete catalogue
        $(document).on('click', '.delete-catalogue', function() {
            var catalogueKey = $(this).data('catalogue-id');

            Swal.fire({
                title: 'Are you sure you want to delete the catalogue?',
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
                        text: 'Please wait while the catalogue is being deleted.',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    $.ajax({
                        url: `{{ route('catalogue.destroy', '') }}/${catalogueKey}`,
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