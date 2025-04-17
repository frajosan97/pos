@extends('layouts.app')

@section('pageTitle', 'Payments')

@section('content')

    <!-- Control Buttons -->
    <ul class="nav nav-pills rounded bg-white mb-3 shadow-sm">
        <li class="nav-item dropdown">
            <a class="nav-link" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="fas fa-print me-1"></i>Print
            </a>
            <ul class="dropdown-menu rounded-0 border-0 shadow-sm p-0">
                <li><a href="" target="_blank" class="dropdown-item"><i class="fas fa-file-pdf text-danger me-2"></i>PDF</a></li>
                <li><a href="{{ url('excel/payments') }}" class="dropdown-item"><i class="fas fa-file-excel text-success me-2"></i>Excel</a></li>
            </ul>
        </li>
    </ul>

    <!-- Desktop Table -->
    <div class="card shadow-sm d-none d-md-block">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle" id="payments-table">
                    <thead class="table-dark text-center text-nowrap">
                        <tr>
                            <th>Date</th>
                            <th>Branch</th>
                            <th>Sale</th>
                            <th>Method</th>
                            <th>Amount (KES)</th>
                            <th>Status</th>
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
            <input type="search" class="form-control" id="search-input" placeholder="Search payments...">
            <button class="btn btn-primary" type="button">
                <i class="fas fa-search"></i>
            </button>
        </div>
        <div class="list-group" id="payments-list"></div>
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
    $('#payments-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('payment.index') }}",
            type: "GET",
            error: function(xhr) {
                showErrorAlert('Failed to load payments. Please try again.');
            }
        },
        columns: [
            { 
                data: 'payment_date',
                name: 'payment_date',
                className: 'text-nowrap'
            },
            { 
                data: 'branch',
                name: 'branch',
                render: d => `<span class="text-capitalize">${d}</span>`
            },
            { 
                data: 'sale',
                name: 'sale',
                className: 'text-nowrap'
            },
            { 
                data: 'payment_method',
                name: 'payment_method',
                className: 'text-capitalize',
                render: d => `<span class="text-capitalize">${d}</span>`
            },
            { 
                data: 'amount',
                name: 'amount',
                className: 'text-end',
                render: d => `<strong>${d}</strong>`
            },
            { 
                data: 'status',
                name: 'status',
                className: 'text-center',
                render: function(data) {
                    const statusClass = data === 'completed' ? 'badge bg-success' : 
                                     data === 'pending' ? 'badge bg-warning' : 'badge bg-secondary';
                    return `<span class="${statusClass}">${data}</span>`;
                }
            }
        ],
        language: {
            emptyTable: "No payments found",
            info: "Showing _START_ to _END_ of _TOTAL_ payments",
            infoEmpty: "Showing 0 to 0 of 0 payments",
            loadingRecords: "Loading payments...",
            processing: '<div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div>'
        },
        order: [[0, 'desc']]
    });

    // Fetch all payments for mobile independently
    function fetchMobilePayments(query = '') {
        $.ajax({
            url: "{{ route('payment.index') }}",
            data: { search: query },
            success: function (response) {
                mobileData = response.data || [];
                mobileCurrentPage = 1;
                renderMobileList();
            },
            error: function () {
                showErrorAlert('Unable to load mobile payments.');
            }
        });
    }

    // Render mobile list
    function renderMobileList() {
        const list = $('#payments-list');
        const pagination = $('#mobile-pagination');
        list.empty();

        const totalPages = Math.ceil(mobileData.length / MOBILE_PAGE_SIZE) || 1;
        const start = (mobileCurrentPage - 1) * MOBILE_PAGE_SIZE;
        const end = start + MOBILE_PAGE_SIZE;
        const pageData = mobileData.slice(start, end);

        if (!pageData.length) {
            list.append(`<div class="list-group-item text-center py-4">
                <i class="fas fa-receipt fa-2x text-muted mb-2"></i>
                <p class="text-muted mb-0">No payments found</p>
            </div>`);
            pagination.addClass('d-none');
            return;
        }

        pageData.forEach(item => {
            const statusClass = item.status === 'completed' ? 'badge bg-success' : 
                              item.status === 'pending' ? 'badge bg-warning' : 'badge bg-secondary';
            list.append(`
                <div class="list-group-item border-0 shadow-sm mb-2">
                    <div class="row align-items-center">
                        <div class="col-8">
                            <div class="d-flex flex-wrap">
                                <small class="w-100">Sale: <strong>${item.sale}</strong></small>
                                <small class="w-100">Branch: <strong>${item.branch}</strong></small>
                                <small class="w-100">Amount: <strong>KES ${item.amount}</strong></small>
                            </div>
                            <small class="text-muted">Method: ${item.payment_method}</small>
                        </div>
                        <div class="col-4 text-end">
                            <span class="">${item.payment_date}</span>
                            <span class="${statusClass}">${item.status}</span>
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
        fetchMobilePayments(this.value);
    }, 300));

    fetchMobilePayments();

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