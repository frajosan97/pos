@extends('layouts.app')

@section('pageTitle', 'sales')

@section('content')

<!-- control buttons -->
<ul class="nav nav-pills rounded bg-white mb-3">
    <li class="nav-item">
        <a href="{{ route('sale.create') }}" class="nav-link">
            <i class="fas fa-plus-circle"></i> Make New sale
        </a>
    </li>
    <li class="nav-item dropdown">
        <a class="nav-link" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="fas fa-print"></i> Print
        </a>
        <ul class="dropdown-menu rounded-0 border-0 shadow-sm p-0">
            <li><a href="{{ route('sales.pdf') }}" target="_blank" class="dropdown-item"><i class="fas fa-file-pdf text-danger"></i> PDF</a></li>
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
                    <table class="table table-striped table-hover align-middle text-nowrap" id="sales-table">
                        <thead class="table-dark text-center">
                            <tr>
                                <th>Invoice No.</th>
                                <th>Billed</th>
                                <th>Paid</th>
                                <th>Pay Mode</th>
                                <th>Done By</th>
                                <th>Status</th>
                                <th>Action</th>
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
                <input type="search" class="form-control" id="search-input" placeholder="Search sale...">
                <span class="input-group-text bg-primary text-white">
                    <i class="fas fa-search"></i>
                </span>
            </div>
            <div class="list-group" id="sales-list">
                <!-- Mobile list data will be appended here -->
            </div>
        </div>
    </div>
</div>

@endsection

@push('script')
<script>
    $(document).ready(function() {
        var table = $('#sales-table').DataTable({
            processing: true,
            serverSide: true,
            order: [
                [0, 'asc']
            ],
            ajax: "{{ route('sale.index') }}",
            columns: [{
                    data: 'invoice_number',
                    name: 'invoice_number'
                },
                {
                    data: 'total_billed',
                    name: 'total_billed'
                },
                {
                    data: 'total_paid',
                    name: 'total_paid'
                },
                {
                    data: 'pay_method',
                    name: 'pay_method'
                },
                {
                    data: 'cashier',
                    name: 'cashier'
                },
                {
                    data: 'status',
                    name: 'status'
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
                var listGroup = $('#sales-list');
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
                                        <h6 class="m-0 text-capitalize">${value.invoice_number}</h6>
                                        <small class="text-muted">Billed: ${value.phone} Paid: ${value.phone}</small>
                                        <small class="text-muted">Cashier: ${value.cashier}</small>
                                    </div>
                                    <div class="text-end">
                                        ${value.status}
                                        <p class="m-0">${value.action}</p>
                                    </div>
                                </div>
                            </div>
                        `);
                    });
                }
            }
        });

        // Search in mobile view
        $('#search-input').on('keyup', function() {
            table.search(this.value).draw();
        });
    });
</script>
@endpush