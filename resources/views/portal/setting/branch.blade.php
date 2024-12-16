@extends('layouts.app')

@section('pageTitle', 'branch')

@section('content')

<!-- control buttons -->
<ul class="nav nav-pills rounded bg-white mb-3">
    <li class="nav-item">
        <a href="#" class="nav-link" data-bs-toggle="modal" data-bs-target="#branchModal">
            <i class="fas fa-plus-circle"></i> Create new branch
        </a>
    </li>
    <li class="nav-item dropdown">
        <a class="nav-link" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="fas fa-print"></i> Print
        </a>
        <ul class="dropdown-menu rounded-0 border-0 shadow-sm p-0">
            <li><a href="{{ route('branch.pdf') }}" target="_blank" class="dropdown-item"><i class="fas fa-file-pdf text-danger"></i> PDF</a></li>
            <li><a href="#" class="dropdown-item"><i class="fas fa-file-excel text-success"></i> Excel</a></li>
        </ul>
    </li>
</ul>
<!-- / end control buttons -->

<div class="row">
    <div class="col-12">
        <!-- Desktop Table View -->
        <div class="card border-0 shadow-sm d-none d-md-block">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle text-nowrap" id="branches-table">
                        <thead class="table-dark text-center text-capitalize">
                            <tr>
                                <th>county</th>
                                <th>constituency</th>
                                <th>ward</th>
                                <th>location</th>
                                <th>branch</th>
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
                <input type="search" class="form-control" id="search-input" placeholder="Search branch...">
                <span class="input-group-text bg-primary text-white">
                    <i class="fas fa-search"></i>
                </span>
            </div>
            <div class="list-group" id="branches-list">
                <!-- Mobile list data will be appended here -->
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="branchModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="branchModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form id="branch-form" action="{{ route('setting.branch.store') }}" method="post">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="branchModalLabel">Add New branch</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-capitalize">
                    <div class="row">
                        <div class="col-md-12">
                            @csrf
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="county">county</label>
                                <select name="county" id="county" class="form-control">
                                    <option value="">--Select county</option>
                                    @foreach ($counties as $key => $value)
                                    <option value="{{ $value->id }}">{{ $value->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="constituency">constituency</label>
                                <select name="constituency" id="constituency" class="form-control">
                                    <option value="">--Select constituency</option>
                                    @foreach ($constituencies as $key => $value)
                                    <option value="{{ $value->id }}">{{ $value->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="ward">ward</label>
                                <select name="ward" id="ward" class="form-control">
                                    <option value="">--Select ward</option>
                                    @foreach ($wards as $key => $value)
                                    <option value="{{ $value->id }}">{{ $value->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="location">location</label>
                                <select name="location" id="location" class="form-control">
                                    <option value="">--Select location</option>
                                    @foreach ($locations as $key => $value)
                                    <option value="{{ $value->id }}">{{ $value->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="branch">branch</label>
                                <input type="text" name="branch" id="branch" placeholder="branch" class="form-control" />
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
        var table = $('#branches-table').DataTable({
            processing: true,
            serverSide: true,
            order: [
                [0, 'asc']
            ],
            ajax: "{{ route('setting.branch') }}",
            columns: [{
                    data: 'county',
                    name: 'county'
                },
                {
                    data: 'constituency',
                    name: 'constituency'
                },
                {
                    data: 'ward',
                    name: 'ward'
                },
                {
                    data: 'location',
                    name: 'location'
                },
                {
                    data: 'branch',
                    name: 'branch'
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
                var listGroup = $('#branches-list');
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
                                        <h6 class="m-0 text-capitalize">${value.branch}</h6>
                                        <p class="m-0"><small>${value.county} ${value.constituency}</small></p>
                                        <p class="m-0"><small>${value.ward} ${value.location}</small></p>
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

        // Open modal to create new branch
        $('#branch-add').on('click', function() {
            // Set the modal content for creating a new branch
            $('#branchModalLabel').text('Add New branch');
            $('#branch-form')[0].reset(); // Reset the form fields
            $('#branch-form').attr('action', "{{ route('setting.branch.store') }}"); // Set the action URL for creating
            $('#branch-form').find('input[name="_method"]').remove(); // Remove any existing method override
            $('#save-button').text('Save Changes'); // Update the button text
            // Show the modal
            $('#branchModal').modal('show');
        });

        // Open modal to edit branch
        $(document).on('click', '.edit-branch', function() {
            var countyId = $(this).data('county-id');
            var constituencyId = $(this).data('constituency-id');
            var wardId = $(this).data('ward-id');
            var locationId = $(this).data('location-id');
            var branchId = $(this).data('branch-id');
            var branchName = $(this).data('branch-name');
            // Set the modal content for editing an existing branch
            $('#branchModalLabel').text('Edit Branch');
            $('#county').val(countyId);
            $('#constituency').val(constituencyId);
            $('#ward').val(wardId);
            $('#location').val(locationId);
            $('#branch').val(branchName);
            // Dynamically set the action URL for the form
            var actionUrl = "{{ route('setting.branch.update', ':id') }}".replace(':id', branchId);
            $('#branch-form').attr('action', actionUrl);
            // Add the _method hidden input for PUT if it doesn't already exist
            if (!$('#branch-form').find('input[name="_method"]').length) {
                $('#branch-form').append('<input type="hidden" name="_method" value="PUT">');
            }
            $('#save-button').text('Update Branch'); // Update the button text
            // Show the modal
            $('#branchModal').modal('show');
        });

        // Handle form submission for both create and edit
        $("#branch-form").validate({
            submitHandler: function(form, event) {
                event.preventDefault();

                Swal.fire({
                    title: 'branch Action',
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
                                    window.branch.reload();
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

        // Delete branch
        $(document).on('click', '.delete-branch', function() {
            var branchKey = $(this).data('branch-id');

            Swal.fire({
                title: 'Are you sure you want to delete the branch?',
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
                        text: 'Please wait while the branch is being deleted.',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    $.ajax({
                        url: `{{ route('setting.branch.destroy', '') }}/${branchKey}`,
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