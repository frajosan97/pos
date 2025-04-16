@extends('layouts.app')

@section('pageTitle', 'Employees')

@section('content')
<div class="container-fluid">
    <!-- Control Buttons -->
    <ul class="nav nav-pills rounded bg-white mb-3 shadow-sm">
        <li class="nav-item">
            <a href="{{ route('employee.create') }}" class="nav-link">
                <i class="fas fa-plus-circle me-1"></i>Create New Employee
            </a>
        </li>
        <li class="nav-item dropdown">
            <a class="nav-link" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="fas fa-print me-1"></i>Print
            </a>
            <ul class="dropdown-menu rounded-0 border-0 shadow-sm p-0">
                <li><a href="{{ route('employee.pdf') }}" target="_blank" class="dropdown-item"><i class="fas fa-file-pdf text-danger me-2"></i>PDF</a></li>
                <li><a href="#" class="dropdown-item"><i class="fas fa-file-excel text-success me-2"></i>Excel</a></li>
            </ul>
        </li>
    </ul>

    <!-- Desktop Table -->
    <div class="card shadow-sm d-none d-md-block">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle" id="employees-table">
                    <thead class="table-dark text-center text-nowrap">
                        <tr>
                            <th>Branch</th>
                            <th>Full Name</th>
                            <th>Email</th>
                            <th>Phone Number</th>
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
            <input type="search" class="form-control" id="search-input" placeholder="Search employees...">
            <button class="btn btn-primary" type="button">
                <i class="fas fa-search"></i>
            </button>
        </div>
        <div class="list-group" id="employees-list"></div>
        <div class="d-flex justify-content-between mt-3 d-none" id="mobile-pagination">
            <button class="btn btn-outline-primary" id="prev-page" disabled>
                <i class="fas fa-chevron-left me-1"></i>Previous
            </button>
            <span class="align-self-center text-muted" id="page-indicator">Page 1</span>
            <button class="btn btn-outline-primary" id="next-page">Next<i class="fas fa-chevron-right ms-1"></i></button>
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
    $('#employees-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('employee.index') }}",
            type: "GET",
            error: function(xhr) {
                showErrorAlert('Failed to load employees. Please try again.');
            }
        },
        columns: [
            { 
                data: 'branch',
                name: 'branch',
                render: d => `<span class="text-capitalize">${d}</span>`
            },
            { 
                data: 'name',
                name: 'name',
                render: d => `<span class="text-capitalize">${d}</span>`
            },
            { 
                data: 'email',
                name: 'email',
                render: d => d.toLowerCase()
            },
            { 
                data: 'phone',
                name: 'phone',
                className: 'text-nowrap'
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
            emptyTable: "No employees found",
            info: "Showing _START_ to _END_ of _TOTAL_ employees",
            infoEmpty: "Showing 0 to 0 of 0 employees",
            loadingRecords: "Loading employees...",
            processing: '<div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div>'
        },
        order: [[1, 'asc']]
    });

    // Fetch all employees for mobile independently
    function fetchMobileEmployees(query = '') {
        $.ajax({
            url: "{{ route('employee.index') }}",
            data: { search: query },
            success: function (response) {
                mobileData = response.data || [];
                mobileCurrentPage = 1;
                renderMobileList();
            },
            error: function () {
                showErrorAlert('Unable to load mobile employees.');
            }
        });
    }

    // Render mobile list
    function renderMobileList() {
        const list = $('#employees-list');
        const pagination = $('#mobile-pagination');
        list.empty();

        const totalPages = Math.ceil(mobileData.length / MOBILE_PAGE_SIZE) || 1;
        const start = (mobileCurrentPage - 1) * MOBILE_PAGE_SIZE;
        const end = start + MOBILE_PAGE_SIZE;
        const pageData = mobileData.slice(start, end);

        if (!pageData.length) {
            list.append(`<div class="list-group-item text-center py-4">
                <i class="fas fa-user-slash fa-2x text-muted mb-2"></i>
                <p class="text-muted mb-0">No employees found</p>
            </div>`);
            pagination.addClass('d-none');
            return;
        }

        pageData.forEach(item => {
            list.append(`
                <div class="list-group-item border-0 shadow-sm mb-2">
                    <div class="row align-items-center">
                        <div class="col-8">
                            <h6 class="mb-1 text-capitalize"><strong>${item.name}</strong></h6>
                            <div class="d-flex flex-wrap">
                                <small class="w-100"><strong>Branch:</strong> ${item.branch}</small>
                                <small class="w-100"><strong>Phone:</strong> ${item.phone}</small>
                            </div>
                            <small class="text-muted">${item.email}</small>
                        </div>
                        <div class="col-4 text-end">
                            <span class="badge bg-info">${item.role || 'Employee'}</span>
                            <div class="mt-2">${item.action}</div>
                        </div>
                    </div>
                </div>
            `);
        });

        $('#page-indicator').text(`Page ${mobileCurrentPage} of ${totalPages}`);
        $('#prev-page').prop('disabled', mobileCurrentPage === 1);
        $('#next-page').prop('disabled', mobileCurrentPage === totalPages);
        pagination.toggleClass('d-none', totalPages <= 1);
    }

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

    $('#search-input').on('keyup', debounce(function () {
        fetchMobileEmployees(this.value);
    }, 300));

    // Delete employee
    $(document).on('click', '.delete-employee', function() {
        const employeeKey = $(this).data('employee-id');
        confirmDelete(
            'Are you sure you want to delete this employee?',
            `{{ route('employee.destroy', '') }}/${employeeKey}`,
            () => $('#employees-table').DataTable().ajax.reload(null, true)
        );
    });

    fetchMobileEmployees();

    function confirmDelete(title, url, callback) {
        Swal.fire({
            title: title,
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
                autocomplete: 'new-password',
                required: true
            },
            preConfirm: (password) => {
                if (!password) {
                    Swal.showValidationMessage('Please enter your password');
                }
                return password;
            }
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Processing...',
                    allowOutsideClick: false,
                    didOpen: () => Swal.showLoading()
                });

                $.ajax({
                    url: url,
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
                        }).then(() => callback());
                    },
                    error: function(xhr) {
                        Swal.fire(
                            'Error!',
                            xhr.responseJSON.error || xhr.responseJSON.message,
                            'error'
                        );
                    }
                });
            }
        });
    }

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
});
</script>
@endpush