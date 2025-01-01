@extends('layouts.app')

@section('pageTitle', 'sales')

@section('content')

@if(auth()->user()->hasPermission('manager_catalogue'))

<!-- control buttons -->
<ul class="nav nav-pills rounded bg-white mb-3">
    @if(auth()->user()->hasPermission('sale_create'))
    <li class="nav-item">
        <a href="{{ route('sale.create') }}" class="nav-link">
            <i class="fas fa-plus-circle"></i> Make new Sale
        </a>
    </li>
    @endif

    @include('layouts.partials.filters2')
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
                                <th>Product</th>
                                <th>Quantity</th>
                                <th>Unit Price</th>
                                <th>Total Price</th>
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

@else
@include('layouts.partials.no_permission')
@endif

@endsection

@push('script')
<script>
    $(document).ready(function() {
        // Initialize DataTable
        var table = $('#sales-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('sale.cat_pro_fetch') }}",
                data: function(d) {
                    d.branch = $('#branch').val();
                    d.catalogue = $('#catalogue').val();
                    d.employee = $('#employee').val();
                }
            },
            columns: [{
                    data: 'product',
                    name: 'product'
                },
                {
                    data: 'quantity',
                    name: 'quantity'
                },
                {
                    data: 'unit_price',
                    name: 'unit_price'
                },
                {
                    data: 'total_price',
                    name: 'total_price'
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                }
            ],
            drawCallback: function(settings) {
                // Handle custom rendering for mobile list view
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
                                        <h6 class="m-0 text-capitalize">${value.product}</h6>
                                        <small class="text-muted">Quantity: ${value.quantity}</small>
                                        <small class="text-muted">Unit Price: ${value.unit_price}</small>
                                        <small class="text-muted">Total Price: ${value.total_price}</small>
                                    </div>
                                    <div class="text-end">
                                        <p class="m-0">${value.action}</p>
                                    </div>
                                </div>
                            </div>
                        `);
                    });
                }
            }
        });

        // Trigger DataTable reload on filter change
        $('#catalogue, #product').on('change', function() {
            table.ajax.reload();
        });

        // Search functionality for mobile view
        $('#search-input').on('keyup', function() {
            table.search(this.value).draw();
        });
    });
</script>
@endpush