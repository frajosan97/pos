@extends('layouts.app')

@section('pageTitle', 'Payments')

@section('content')

@if(auth()->user()->hasPermission('payment_view'))

<!-- Control buttons -->
<!-- <ul class="nav nav-pills rounded bg-white mb-3">
    <li class="nav-item dropdown">
        <a class="nav-link" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="fas fa-print"></i> Print
        </a>
        <ul class="dropdown-menu rounded-0 border-0 shadow-sm p-0">
            <li><a href="" target="_blank" class="dropdown-item"><i class="fas fa-file-pdf text-danger"></i> PDF</a></li>
            <li><a href="" class="dropdown-item"><i class="fas fa-file-excel text-success"></i> Excel</a></li>
        </ul>
    </li>
</ul> -->
<!-- /Control buttons -->

<div class="row">
    <div class="col-12">
        <!-- Desktop Table View -->
        <div class="card border-0 shadow-sm d-none d-md-block">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle text-nowrap" id="payments-table">
                        <thead class="table-dark text-center">
                            <tr>
                                <th>Branch</th>
                                <th>Sale</th>
                                <th>Amount</th>
                                <th>Payment Method</th>
                                <th>Status</th>
                                <th>Payment Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Data will be loaded via AJAX -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Mobile View -->
        <div class="d-block d-md-none">
            <div class="input-group mb-3 shadow-sm">
                <input type="search" class="form-control" id="search-input" placeholder="Search payments...">
                <span class="input-group-text bg-primary text-white">
                    <i class="fas fa-search"></i>
                </span>
            </div>
            <div class="list-group" id="payments-list">
                <!-- Mobile list data will be appended here -->
            </div>
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
        var table = $('#payments-table').DataTable({
            processing: true,
            serverSide: true,
            order: [
                [5, 'desc']
            ],
            ajax: "{{ route('payment.index') }}",
            columns: [{
                    data: 'branch',
                    name: 'branch'
                },
                {
                    data: 'sale',
                    name: 'sale'
                },
                {
                    data: 'amount',
                    name: 'amount'
                },
                {
                    data: 'payment_method',
                    name: 'payment_method'
                },
                {
                    data: 'status',
                    name: 'status'
                },
                {
                    data: 'payment_date',
                    name: 'payment_date'
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
                var listGroup = $('#payments-list');
                listGroup.empty();

                if (data.length === 0) {
                    listGroup.append(`
                        <div class="list-group-item text-center border-0 bg-light">
                            <p class="text-muted mb-0">No payments found</p>
                        </div>
                    `);
                } else {
                    $.each(data, function(index, value) {
                        listGroup.append(`
                            <div class="list-group-item border rounded shadow-sm mb-2">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="m-0 text-capitalize">Sale: ${value.sale}</h6>
                                        <small class="text-muted">Amount: ${value.amount}</small>
                                    </div>
                                    <div class="text-end">
                                        <span class="badge bg-info text-dark">${value.status}</span>
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