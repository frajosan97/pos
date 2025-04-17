@extends('layouts.app')

@section('pageTitle', 'Products')

@section('content')

    <!-- Control Buttons -->
    <ul class="nav nav-pills rounded bg-white mb-3 shadow-sm">
        @if(auth()->user()->hasPermission('product_create'))
        <li class="nav-item">
            <a href="{{ route('product.create') }}" class="nav-link">
                <i class="fas fa-plus-circle me-1"></i>Create
            </a>
        </li>
        @endif

        @include('layouts.partials.filters')

        <li class="nav-item dropdown">
            <a class="nav-link" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="fas fa-print me-1"></i>Print
            </a>
            <ul class="dropdown-menu rounded-0 border-0 shadow-sm p-0">
                <li><a href="{{ route('inventory.pdf') }}" target="_blank" class="dropdown-item"><i class="fas fa-file-pdf text-danger me-2"></i>PDF</a></li>
                <li><a href="{{ url('excel/inventory') }}" class="dropdown-item"><i class="fas fa-file-excel text-success me-2"></i>Excel</a></li>
            </ul>
        </li>
    </ul>

    <!-- Desktop Table -->
    <div class="card shadow-sm d-none d-md-block">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle" id="products-table">
                    <thead class="table-dark text-center text-nowrap">
                        <tr>
                            <th>Image</th>
                            <th>Product</th>
                            <th>Price</th>
                            <th>Qnty</th>
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
            <input type="search" class="form-control" id="search-input" placeholder="Search products...">
            <button class="btn btn-primary" type="button">
                <i class="fas fa-search"></i>
            </button>
        </div>
        <div class="list-group" id="products-list"></div>
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
    $('#products-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('product.index') }}",
            type: "GET",
            data: function(d) {
                d.branch = $('.filter-branch.active').data('value');
                d.catalogue = $('.filter-catalogue.active').data('value');
                d.employee = $('.filter-employee.active').data('value');
            },
            error: function(xhr) {
                showErrorAlert('Failed to load products. Please try again.');
            }
        },
        columns: [
            { data: 'image', name: 'image', render: d => d || '<i class="fas fa-box-open text-muted fa-2x"></i>' },
            { data: 'name', name: 'name', render: d => `<span class="text-capitalize">${d}</span>` },
            { data: 'price_list', name: 'price_list' },
            { data: 'quantity', name: 'quantity' },
            { data: 'status', name: 'status' },
            { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center' }
        ],
        language: {
            emptyTable: "No products found",
            info: "Showing _START_ to _END_ of _TOTAL_ products",
            infoEmpty: "Showing 0 to 0 of 0 products",
            loadingRecords: "Loading products...",
            processing: '<div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div>'
        },
        order: [[1, 'asc']]
    });

    // Fetch all products for mobile independently
    function fetchMobileProducts(query = '') {
        $.ajax({
            url: "{{ route('product.index') }}",
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
                showErrorAlert('Unable to load mobile products.');
            }
        });
    }

    // Render mobile list
    function renderMobileList() {
        const list = $('#products-list');
        const pagination = $('#mobile-pagination');
        list.empty();

        const totalPages = Math.ceil(mobileData.length / MOBILE_PAGE_SIZE) || 1;
        const start = (mobileCurrentPage - 1) * MOBILE_PAGE_SIZE;
        const end = start + MOBILE_PAGE_SIZE;
        const pageData = mobileData.slice(start, end);

        if (!pageData.length) {
            list.append(`<div class="list-group-item text-center py-4">
                <i class="fas fa-box-open fa-2x text-muted mb-2"></i>
                <p class="text-muted mb-0">No products found</p>
            </div>`);
            pagination.addClass('d-none');
            return;
        }

        pageData.forEach(item => {
            const statusClass = item.status === 'Active' ? 'badge bg-success' : 'badge bg-danger';
            list.append(`
                <div class="list-group-item border-0 shadow-sm mb-2">
                    <div class="row align-items-center">
                        <div class="col-3 text-center">
                            ${item.image || '<i class="fas fa-box-open text-muted fa-2x"></i>'}
                        </div>
                        <div class="col-6">
                            <h6 class="mb-1 text-capitalize">${item.name}</h6>
                            <div class="d-flex flex-wrap">
                                <small class="me-2"><strong>Price:</strong> ${item.price_list}</small>
                                <small><strong>Qty:</strong> ${item.quantity}</small>
                            </div>
                            <span class="${statusClass}">${item.status}</span>
                        </div>
                        <div class="col-3 text-end">${item.action}</div>
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
        fetchMobileProducts(this.value);
    }, 300));

    // Filters
    $(document).on('click', '.filter-employee, .filter-branch, .filter-catalogue', function (e) {
        e.preventDefault();
        const filterClass = '.' + $(this).attr('class').split(' ')[0];
        $(filterClass).removeClass('active');
        $(this).addClass('active');
        fetchMobileProducts($('#search-input').val());
    });

    fetchMobileProducts();

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
