@extends('layouts.app')

@section('pageTitle', 'Catalogue Management')

@section('content')
<div class="container-fluid">
    <!-- Control Buttons -->
    <ul class="nav nav-pills rounded bg-white mb-3 shadow-sm">
        @if(auth()->user()->hasPermission('catalogue_create'))
        <li class="nav-item">
            <a href="#" class="nav-link" data-bs-toggle="modal" data-bs-target="#catalogueModal">
                <i class="fas fa-plus-circle me-1"></i>Create
            </a>
        </li>
        @endif
    </ul>

    <!-- Desktop Table -->
    <div class="card shadow-sm d-none d-md-block">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle" id="catalogues-table">
                    <thead class="table-dark text-center text-nowrap">
                        <tr>
                            <th>Catalogue Name</th>
                            <th>Products Count</th>
                            <th>Status</th>
                            <th style="width: 15%;">Actions</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Mobile View -->
    <div class="d-block d-md-none">
        <div class="input-group mb-3 shadow-sm">
            <input type="search" class="form-control" id="search-input" placeholder="Search catalogues...">
            <button class="btn btn-primary" type="button">
                <i class="fas fa-search"></i>
            </button>
        </div>
        <div class="list-group" id="catalogues-list"></div>
        <div class="d-flex justify-content-between mt-3 d-none" id="mobile-pagination">
            <button class="btn btn-outline-primary" id="prev-page" disabled>
                <i class="fas fa-chevron-left me-1"></i>Previous
            </button>
            <span class="align-self-center text-muted" id="page-indicator">Page 1</span>
            <button class="btn btn-outline-primary" id="next-page">Next<i class="fas fa-chevron-right ms-1"></i></button>
        </div>
    </div>
</div>

<!-- Catalogue Modal -->
<div class="modal fade" id="catalogueModal" tabindex="-1" aria-labelledby="catalogueModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="catalogue-form" method="POST">
                @csrf
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="catalogueModalLabel">Add New Catalogue</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="branch_id" value="{{ auth()->user()->branch_id }}">
                    <div class="mb-3">
                        <label for="catalogue" class="form-label">Catalogue Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="catalogue" name="name" 
                               placeholder="Enter catalogue name" required>
                        <div class="invalid-feedback">Please provide a valid catalogue name.</div>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" 
                                  rows="3" placeholder="Optional description"></textarea>
                    </div>
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="is_active" name="is_active" checked>
                        <label class="form-check-label" for="is_active">Active</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="save-button">
                        <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('script')
<script>
$(document).ready(function () {
    const MOBILE_PAGE_SIZE = 8;
    let mobileCurrentPage = 1;
    let mobileData = [];

    // Desktop DataTable
    $('#catalogues-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('catalogue.index') }}",
            type: "GET",
            data: function(d) {
                d.branch = $('.filter-branch.active').data('value');
                d.status = $('.filter-status.active').data('value');
                d.search = $('#search-input').val();
            },
            error: function(xhr) {
                showErrorAlert('Failed to load catalogues. Please try again.');
            }
        },
        columns: [
            { 
                data: 'name', 
                name: 'name', 
                render: function(data) {
                    return `<span class="text-capitalize">${data}</span>`;
                }
            },
            { 
                data: 'products_count', 
                name: 'products_count',
                className: 'text-center',
                render: function(data) {
                    return `<span class="badge bg-primary">${data}</span>`;
                }
            },
            { 
                data: 'is_active', 
                name: 'is_active',
                className: 'text-center',
                render: function(data) {
                    return data ? 
                        '<span class="badge bg-success">Active</span>' : 
                        '<span class="badge bg-danger">Inactive</span>';
                }
            },
            { 
                data: 'action', 
                name: 'action', 
                orderable: false, 
                searchable: false,
                className: 'text-center'
            }
        ],
        language: {
            emptyTable: "No catalogues found",
            info: "Showing _START_ to _END_ of _TOTAL_ catalogues",
            infoEmpty: "Showing 0 to 0 of 0 catalogues",
            loadingRecords: "Loading catalogues...",
            processing: '<div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div>'
        },
        order: [[0, 'asc']]
    });

    // Fetch all catalogues for mobile independently
    function fetchMobileCatalogues(query = '') {
        $.ajax({
            url: "{{ route('catalogue.index') }}",
            data: {
                search: query,
                branch: $('.filter-branch.active').data('value'),
                status: $('.filter-status.active').data('value'),
                mobile: true
            },
            success: function (response) {
                mobileData = response.data || [];
                mobileCurrentPage = 1;
                renderMobileList();
            },
            error: function () {
                showErrorAlert('Unable to load mobile catalogues.');
            }
        });
    }

    // Render mobile list
    function renderMobileList() {
        const list = $('#catalogues-list');
        const pagination = $('#mobile-pagination');
        list.empty();

        const totalPages = Math.ceil(mobileData.length / MOBILE_PAGE_SIZE) || 1;
        const start = (mobileCurrentPage - 1) * MOBILE_PAGE_SIZE;
        const end = start + MOBILE_PAGE_SIZE;
        const pageData = mobileData.slice(start, end);

        if (!pageData.length) {
            list.append(`<div class="list-group-item text-center py-4">
                <i class="fas fa-box-open fa-2x text-muted mb-2"></i>
                <p class="text-muted mb-0">No catalogues found</p>
            </div>`);
            pagination.addClass('d-none');
            return;
        }

        pageData.forEach(item => {
            const statusClass = item.is_active ? 'badge bg-success' : 'badge bg-danger';
            list.append(`
                <div class="list-group-item border-0 shadow-sm mb-2">
                    <div class="row align-items-center">
                        <div class="col-8">
                            <h6 class="mb-1 text-capitalize">${item.name}</h6>
                            <div class="d-flex flex-wrap">
                                <small class="me-2"><strong>Products:</strong> <span class="badge bg-primary">${item.products_count}</span></small>
                                <span class="${statusClass}">${item.is_active ? 'Active' : 'Inactive'}</span>
                            </div>
                        </div>
                        <div class="col-4 text-end">${item.action}</div>
                    </div>
                </div>
            `);
        });

        $('#page-indicator').text(`Page ${mobileCurrentPage} of ${totalPages}`);
        $('#prev-page').prop('disabled', mobileCurrentPage === 1);
        $('#next-page').prop('disabled', mobileCurrentPage === totalPages);
        pagination.toggleClass('d-none', totalPages <= 1);
    }

    // Pagination handlers
    $('#prev-page').click(() => {
        if (mobileCurrentPage > 1) {
            mobileCurrentPage--;
            renderMobileList();
        }
    });

    $('#next-page').click(() => {
        const totalPages = Math.ceil(mobileData.length / MOBILE_PAGE_SIZE);
        if (mobileCurrentPage < totalPages) {
            mobileCurrentPage++;
            renderMobileList();
        }
    });

    // Search functionality
    $('#search-input').on('keyup', debounce(function () {
        fetchMobileCatalogues(this.value);
        $('#catalogues-table').DataTable().search(this.value).draw();
    }, 300));

    // Filters
    $(document).on('click', '.filter-branch, .filter-status', function (e) {
        e.preventDefault();
        const filterClass = '.' + $(this).attr('class').split(' ')[0];
        $(filterClass).removeClass('active');
        $(this).addClass('active');
        fetchMobileCatalogues($('#search-input').val());
        $('#catalogues-table').DataTable().ajax.reload();
    });

    // Initialize mobile view
    fetchMobileCatalogues();

    // Form handling
    $('#catalogue-form').validate({
        rules: {
            name: {
                required: true,
                minlength: 2,
                maxlength: 255
            }
        },
        messages: {
            name: {
                required: "Please enter a catalogue name",
                minlength: "Catalogue name must be at least 2 characters",
                maxlength: "Catalogue name cannot exceed 255 characters"
            }
        },
        errorElement: 'span',
        errorPlacement: function(error, element) {
            error.addClass('invalid-feedback');
            element.closest('.mb-3').append(error);
        },
        highlight: function(element) {
            $(element).addClass('is-invalid');
        },
        unhighlight: function(element) {
            $(element).removeClass('is-invalid');
        },
        submitHandler: function(form) {
            const submitButton = $('#save-button');
            const spinner = submitButton.find('.spinner-border');
            
            submitButton.prop('disabled', true);
            spinner.removeClass('d-none');
            
            const formData = new FormData(form);
            const method = $(form).find('input[name="_method"]').val() || 'POST';
            const url = method === 'POST' ? $(form).attr('action') : $(form).attr('action').replace('/edit', '');
            
            $.ajax({
                url: url,
                type: method,
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    $('#catalogueModal').modal('hide');
                    showSuccessAlert(response.message || 'Operation completed successfully');
                    $('#catalogues-table').DataTable().ajax.reload(null, false);
                    fetchMobileCatalogues($('#search-input').val());
                },
                error: function(xhr) {
                    let errorMessage = 'An error occurred. Please try again.';
                    if (xhr.responseJSON && xhr.responseJSON.errors) {
                        // Handle validation errors
                        const errors = xhr.responseJSON.errors;
                        $.each(errors, function(key, value) {
                            const element = $(`#${key}`);
                            element.addClass('is-invalid');
                            element.next('.invalid-feedback').text(value[0]);
                        });
                        errorMessage = 'Please correct the errors in the form.';
                    } else if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    showErrorAlert(errorMessage);
                },
                complete: function() {
                    submitButton.prop('disabled', false);
                    spinner.addClass('d-none');
                }
            });
        }
    });

    // Edit Catalogue Handler
    $(document).on('click', '.edit-catalogue', function() {
        const id = $(this).data('id');
        const url = `{{ route('catalogue.edit', ['catalogue' => ':id']) }}`.replace(':id', id);

        // Show loading state in SweetAlert
        Swal.fire({
            icon: info,
            title: 'Loading...',
            text: 'Please wait while we fetch the catalogue details.',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        $.get(url, function(data) {
            // Close the loading SweetAlert
            Swal.close();

            // Display catalogue details in modal
            $('#catalogueModalLabel').text('Edit Catalogue');
            $('#game-form').attr('action', `{{ url('portal/game') }}/${id}`);
            $('#game-form input[name="_method"]').val('PUT');

            $('#name').val(data.name);
            $('#slug').val(data.slug);
            $('#game_category_id').val(data.game_category_id);
            $('#icon').val(data.icon);
            $('#provider').val(data.provider);
            $('#portal_name').val(data.portal_name);
            $('#game_launch_category').val(data.game_launch_category);
            $('#game_launch_name').val(data.game_launch_name);
            $('#is_active').val(data.is_active ? 1 : 0);

            // save button
            $('#save-button').html('<span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span> Update Catalogue');

            $('#gameModal').modal('show');
        }).fail(function() {
            Swal.fire('Error', 'Failed to fetch game details.', 'error');
        });





        // const name = $(this).data('name');
        // const description = $(this).data('description');
        // const isActive = $(this).data('active');
        
        // const form = $('#catalogue-form');
        // form[0].reset();
        // form.find('.is-invalid').removeClass('is-invalid');
        
        // $('#catalogueModalLabel').text('Edit Catalogue');
        // form.attr('action', `/catalogue/${id}/edit`);
        // form.append('<input type="hidden" name="_method" value="PUT">');
        // $('#catalogue').val(name);
        // $('#description').val(description);
        // $('#is_active').prop('checked', isActive);
        // $('#save-button').html('<span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span> Update Catalogue');
        
        // $('#catalogueModal').modal('show');
    });

    // Delete Catalogue Handler
    $(document).on('click', '.delete-catalogue', function() {
        const id = $(this).data('id');
        const name = $(this).data('name');
        
        Swal.fire({
            title: 'Delete Catalogue',
            html: `Are you sure you want to delete <strong>${name}</strong>?<br>This action cannot be undone.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/catalogue/${id}`,
                    type: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    beforeSend: function() {
                        Swal.fire({
                            title: 'Deleting...',
                            html: 'Please wait while we delete the catalogue.',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });
                    },
                    success: function(response) {
                        Swal.fire({
                            title: 'Deleted!',
                            text: response.message || 'Catalogue has been deleted.',
                            icon: 'success'
                        }).then(() => {
                            $('#catalogues-table').DataTable().ajax.reload(null, false);
                            fetchMobileCatalogues($('#search-input').val());
                        });
                    },
                    error: function(xhr) {
                        let errorMessage = 'Failed to delete catalogue.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }
                        Swal.fire('Error!', errorMessage, 'error');
                    }
                });
            }
        });
    });

    // Reset form when modal is closed
    $('#catalogueModal').on('hidden.bs.modal', function () {
        const form = $('#catalogue-form');
        form[0].reset();
        form.find('.is-invalid').removeClass('is-invalid');
        form.find('input[name="_method"]').remove();
        form.attr('action', "{{ route('catalogue.store') }}");
        $('#catalogueModalLabel').text('Add New Catalogue');
        $('#save-button').html('<span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span> Save Changes');
    });

    // Utility functions
    function debounce(func, wait) {
        let timeout;
        return function () {
            clearTimeout(timeout);
            timeout = setTimeout(() => func.apply(this, arguments), wait);
        };
    }

    function showErrorAlert(message) {
        Swal.fire({ title: 'Error!', text: message, icon: 'error', confirmButtonText: 'OK' });
    }
    
    function showSuccessAlert(message) {
        Swal.fire({ title: 'Success!', text: message, icon: 'success', confirmButtonText: 'OK' });
    }
});
</script>
@endpush