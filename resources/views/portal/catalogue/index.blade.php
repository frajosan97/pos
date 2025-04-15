@extends('layouts.app')

@section('pageTitle', 'Catalogue Management')

@section('content')

    <div class="container-fluid">
        <!-- Create button -->
        @if(auth()->user()->hasPermission('catalogue_create'))
        <!-- Control Buttons -->
        <div class="d-flex justify-content-end mb-3">
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#catalogueModal">
                <i class="fas fa-plus-circle me-2"></i>Create New Catalogue
            </button>
        </div>
        @endif

        <!-- Desktop Table View -->
        <div class="card shadow-sm d-none d-md-block">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover align-middle" id="catalogues-table">
                        <thead class="table-secondary">
                            <tr>
                                <th>Catalogue Name</th>
                                <th style="width: 15%;" class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Data populated via DataTables -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Mobile View -->
        <div class="d-block d-md-none">
            <div class="input-group mb-3 shadow-sm">
                <input type="search" class="form-control" id="search-input" placeholder="Search catalogue...">
                <button class="btn btn-primary" type="button">
                    <i class="fas fa-search"></i>
                </button>
            </div>
            <div class="list-group" id="catalogues-list">
                <!-- Mobile list populated dynamically -->
            </div>
            <div class="d-flex justify-content-between mt-3 d-none" id="mobile-pagination">
                <button class="btn btn-outline-primary" id="prev-page" disabled>
                    <i class="fas fa-chevron-left me-1"></i>Previous
                </button>
                <span class="align-self-center text-muted" id="page-indicator">Page 1</span>
                <button class="btn btn-outline-primary" id="next-page">
                    Next<i class="fas fa-chevron-right ms-1"></i>
                </button>
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
                            <label for="catalogue" class="form-label">Catalogue Name</label>
                            <input type="text" class="form-control" id="catalogue" name="catalogue" 
                                   placeholder="Enter catalogue name" required>
                            <div class="invalid-feedback">Please provide a valid catalogue name.</div>
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
    // Constants and configuration
    const MOBILE_PAGE_SIZE = 8;
    let mobileCurrentPage = 1;
    let totalMobilePages = 1;
    
    // Initialize DataTable
    const table = $('#catalogues-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('catalogue.index') }}",
            type: "GET",
            error: function(xhr) {
                showErrorAlert('Failed to load catalogues. Please try again.');
            }
        },
        columns: [
            { 
                data: 'name', 
                name: 'name',
                render: function(data, type, row) {
                    return `<span class="text-capitalize">${data}</span>`;
                }
            },
            { 
                data: 'action', 
                name: 'action', 
                orderable: false, 
                searchable: false,
                className: 'text-end',
            }
        ],
        language: {
            emptyTable: "No catalogues found",
            info: "Showing _START_ to _END_ of _TOTAL_ catalogues",
            infoEmpty: "Showing 0 to 0 of 0 catalogues",
            loadingRecords: "Loading catalogues...",
            processing: '<div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div>'
        },
        drawCallback: function(settings) {
            updateMobileView();
        }
    });

    // Update mobile view based on current DataTable data
    function updateMobileView() {
        const data = table.rows({ search: 'applied' }).data().toArray();
        const listGroup = $('#catalogues-list');
        const pagination = $('#mobile-pagination');
        
        listGroup.empty();
        totalMobilePages = Math.ceil(data.length / MOBILE_PAGE_SIZE) || 1;
        
        if (data.length === 0) {
            listGroup.append(`
                <div class="list-group-item text-center py-4">
                    <i class="fas fa-box-open fa-2x text-muted mb-2"></i>
                    <p class="text-muted mb-0">No catalogues found</p>
                </div>
            `);
            pagination.addClass('d-none');
            return;
        }
        
        // Adjust current page if it exceeds total pages
        if (mobileCurrentPage > totalMobilePages) {
            mobileCurrentPage = totalMobilePages;
        }
        
        const startIdx = (mobileCurrentPage - 1) * MOBILE_PAGE_SIZE;
        const endIdx = startIdx + MOBILE_PAGE_SIZE;
        const pageData = data.slice(startIdx, endIdx);
        
        // Populate mobile list
        pageData.forEach(item => {
            listGroup.append(`
                <div class="list-group-item border-0 shadow-sm mb-2">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="text-capitalize">${item.name}</div>
                        <div class="btn-group">
                            ${item.action}
                        </div>
                    </div>
                </div>
            `);
        });
        
        // Update pagination controls
        $('#page-indicator').text(`Page ${mobileCurrentPage} of ${totalMobilePages}`);
        $('#prev-page').prop('disabled', mobileCurrentPage === 1);
        $('#next-page').prop('disabled', mobileCurrentPage === totalMobilePages);
        pagination.toggleClass('d-none', totalMobilePages <= 1);
    }

    // Mobile pagination handlers
    $('#prev-page').click(() => {
        if (mobileCurrentPage > 1) {
            mobileCurrentPage--;
            updateMobileView();
        }
    });

    $('#next-page').click(() => {
        if (mobileCurrentPage < totalMobilePages) {
            mobileCurrentPage++;
            updateMobileView();
        }
    });

    // Search functionality
    $('#search-input').on('keyup', debounce(function() {
        table.search(this.value).draw();
        mobileCurrentPage = 1; // Reset to first page on search
    }, 300));

    // Edit Catalogue Handler
    $(document).on('click', '.edit-catalogue', function() {
        const id = $(this).data('catalogue-id');
        const name = $(this).data('catalogue-name');
        
        const form = $('#catalogue-form');
        form[0].reset();
        form.find('.is-invalid').removeClass('is-invalid');
        
        $('#catalogueModalLabel').text('Edit Catalogue');
        form.attr('action', `/catalogue/${id}`);
        form.append('<input type="hidden" name="_method" value="PUT">');
        $('#catalogue').val(name);
        $('#save-button').html('<span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span> Update Catalogue');
        
        $('#catalogueModal').modal('show');
    });

    // Delete Catalogue Handler
    $(document).on('click', '.delete-catalogue', function() {
        const id = $(this).data('catalogue-id');
        
        Swal.fire({
            title: 'Delete Catalogue',
            text: 'Are you sure you want to delete this catalogue? This action cannot be undone.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel',
            input: 'password',
            inputPlaceholder: 'Enter your password to confirm',
            inputAttributes: {
                autocapitalize: 'off',
                autocorrect: 'off'
            },
            preConfirm: (password) => {
                if (!password) {
                    Swal.showValidationMessage('Password is required');
                    return false;
                }
                return password;
            }
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Deleting...',
                    html: 'Please wait while we delete the catalogue.',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                
                $.ajax({
                    url: `/catalogue/${id}`,
                    type: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}',
                        password: result.value
                    },
                    success: function(response) {
                        Swal.fire({
                            title: 'Deleted!',
                            text: response.message || 'Catalogue has been deleted.',
                            icon: 'success'
                        }).then(() => {
                            table.ajax.reload(null, false);
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

    // Form validation and submission
    $('#catalogue-form').validate({
        rules: {
            catalogue: {
                required: true,
                minlength: 2,
                maxlength: 255
            }
        },
        messages: {
            catalogue: {
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
        submitHandler: function(form, event) {
            event.preventDefault();
            const submitButton = $('#save-button');
            const spinner = submitButton.find('.spinner-border');
            
            Swal.fire({
                title: 'Confirm Action',
                text: 'Are you sure you want to proceed?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Yes, proceed',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    submitButton.prop('disabled', true);
                    spinner.removeClass('d-none');
                    
                    $.ajax({
                        url: $(form).attr('action'),
                        type: $(form).find('input[name="_method"]').val() || 'POST',
                        data: new FormData(form),
                        contentType: false,
                        processData: false,
                        success: function(response) {
                            Swal.fire({
                                title: 'Success!',
                                text: response.message || 'Operation completed successfully',
                                icon: 'success'
                            }).then(() => {
                                table.ajax.reload(null, false);
                                $('#catalogueModal').modal('hide');
                            });
                        },
                        error: function(xhr) {
                            let errorMessage = 'An error occurred. Please try again.';
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                errorMessage = xhr.responseJSON.message;
                            } else if (xhr.statusText) {
                                errorMessage = xhr.statusText;
                            }
                            Swal.fire('Error!', errorMessage, 'error');
                        },
                        complete: function() {
                            submitButton.prop('disabled', false);
                            spinner.addClass('d-none');
                        }
                    });
                }
            });
        }
    });

    // Utility functions
    function debounce(func, wait) {
        let timeout;
        return function() {
            const context = this, args = arguments;
            clearTimeout(timeout);
            timeout = setTimeout(() => {
                func.apply(context, args);
            }, wait);
        };
    }
});
</script>
@endpush