@extends('layouts.app')

@section('pageTitle', 'Sales')

@section('content')

    <!-- Control Buttons -->
    <ul class="nav nav-pills rounded bg-white mb-3 shadow-sm">
        @if(auth()->user()->hasPermission('sale_create'))
        <li class="nav-item">
            <a href="{{ route('sale.create') }}" class="nav-link">
                <i class="fas fa-plus-circle me-1"></i>Make New Sale
            </a>
        </li>
        @endif

        @include('layouts.partials.filters')

        <li class="nav-item dropdown">
            <a class="nav-link" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="fas fa-print me-1"></i>Print
            </a>
            <ul class="dropdown-menu rounded-0 border-0 shadow-sm p-0">
                <li><a href="{{ route('sales.pdf') }}" target="_blank" class="dropdown-item"><i class="fas fa-file-pdf text-danger me-2"></i>PDF</a></li>
                <li><a href="{{ url('excel/sales') }}" class="dropdown-item"><i class="fas fa-file-excel text-success me-2"></i>Excel</a></li>
            </ul>
        </li>
    </ul>

    <!-- Desktop Table -->
    <div class="card shadow-sm d-none d-md-block">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle" id="sales-table">
                    <thead class="table-dark text-center text-nowrap">
                        <tr>
                            <th>Invoice No.</th>
                            <th>Billed</th>
                            <th>Paid</th>
                            <th>Pay Mode</th>
                            <th>Done By</th>
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
            <input type="search" class="form-control" id="search-input" placeholder="Search sales...">
            <button class="btn btn-primary" type="button">
                <i class="fas fa-search"></i>
            </button>
        </div>
        <div class="list-group" id="sales-list"></div>
        <div class="d-flex justify-content-between mt-3 d-none" id="mobile-pagination">
            <button class="btn btn-outline-primary" id="prev-page" disabled>
                <i class="fas fa-chevron-left me-1"></i>Previous
            </button>
            <span class="align-self-center text-muted" id="page-indicator">Page 1</span>
            <button class="btn btn-outline-primary" id="next-page">Next<i class="fas fa-chevron-right ms-1"></i></button>
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
    $('#sales-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('sale.index') }}",
            type: "GET",
            data: function(d) {
                d.branch = $('.filter-branch.active').data('value');
                d.catalogue = $('.filter-catalogue.active').data('value');
                d.employee = $('.filter-employee.active').data('value');
            },
            error: function(xhr) {
                showErrorAlert('Failed to load sales. Please try again.');
            }
        },
        columns: [
            { data: 'invoice_number', name: 'invoice_number' },
            { data: 'total_billed', name: 'total_billed' },
            { data: 'total_paid', name: 'total_paid' },
            { data: 'pay_method', name: 'pay_method' },
            { data: 'cashier', name: 'cashier' },
            { data: 'status', name: 'status' },
            { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center' }
        ],
        language: {
            emptyTable: "No sales found",
            info: "Showing _START_ to _END_ of _TOTAL_ sales",
            infoEmpty: "Showing 0 to 0 of 0 sales",
            loadingRecords: "Loading sales...",
            processing: '<div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div>'
        },
        order: [[0, 'desc']]
    });

    // Fetch all sales for mobile independently
    function fetchMobileSales(query = '') {
        $.ajax({
            url: "{{ route('sale.index') }}",
            data: {
                search: query,
                branch: $('.filter-branch.active').data('value'),
                catalogue: $('.filter-catalogue.active').data('value'),
                employee: $('.filter-employee.active').data('value')
            },
            success: function (response) {
                mobileData = response.data || [];
                mobileCurrentPage = 1;
                renderMobileList();
            },
            error: function () {
                showErrorAlert('Unable to load mobile sales.');
            }
        });
    }

    // Render mobile list
    function renderMobileList() {
        const list = $('#sales-list');
        const pagination = $('#mobile-pagination');
        list.empty();

        const totalPages = Math.ceil(mobileData.length / MOBILE_PAGE_SIZE) || 1;
        const start = (mobileCurrentPage - 1) * MOBILE_PAGE_SIZE;
        const end = start + MOBILE_PAGE_SIZE;
        const pageData = mobileData.slice(start, end);

        if (!pageData.length) {
            list.append(`<div class="list-group-item text-center py-4">
                <i class="fas fa-receipt fa-2x text-muted mb-2"></i>
                <p class="text-muted mb-0">No sales found</p>
            </div>`);
            pagination.addClass('d-none');
            return;
        }

        pageData.forEach(item => {
            list.append(`
                <div class="list-group-item border-0 shadow-sm mb-2">
                    <div class="row align-items-center">
                        <div class="col-10">
                            <h6 class="mb-1">${item.invoice_number}</h6>
                            <div class="d-flex flex-wrap">
                                <small class="me-2"><strong>Billed:</strong> ${item.total_billed}</small>
                                <small class="me-2"><strong>Paid:</strong> ${item.total_paid}</small>
                                <small><strong>Method:</strong> ${item.pay_method}</small>
                            </div>
                            <small class="text-muted">Cashier: ${item.cashier}</small>
                        </div>
                        <div class="col-2 text-end">
                            <span>${item.status}</span>
                            <span>${item.action}</span>
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
        fetchMobileSales(this.value);
    }, 300));

    // Filters
    $(document).on('click', '.filter-employee, .filter-branch, .filter-catalogue', function (e) {
        e.preventDefault();
        const filterClass = '.' + $(this).attr('class').split(' ')[0];
        $(filterClass).removeClass('active');
        $(this).addClass('active');
        fetchMobileSales($('#search-input').val());
    });

    fetchMobileSales();

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