@extends('layouts.app')

@section('pageTitle', 'Products')

@section('content')

<!-- control buttons -->
<ul class="nav nav-pills rounded bg-white mb-3">

    <!-- Create button -->
    @if(auth()->user()->hasPermission('product_create'))
    <li class="nav-item">
        <a href="{{ route('product.create') }}" class="nav-link">
            <i class="fas fa-plus-circle"></i> Create / Update product
        </a>
    </li>
    @endif

    @include('layouts.partials.filters')

    <li class="nav-item dropdown">
        <a class="nav-link" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="fas fa-print"></i> Print
        </a>
        <ul class="dropdown-menu rounded-0 border-0 shadow-sm p-0">
            <li><a href="{{ route('inventory.pdf') }}" target="_blank" class="dropdown-item"><i class="fas fa-file-pdf text-danger"></i> PDF</a></li>
            <!-- <li><a href="#" class="dropdown-item"><i class="fas fa-file-excel text-success"></i> Excel</a></li> -->
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
                    <table class="table table-striped table-hover align-middle" id="products-table">
                        <thead class="table-dark text-center text-nowrap">
                            <tr>
                                <th>Image</th>
                                <th>Product</th>
                                <th>Price</th>
                                <th>Qnty</th>
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
                <input type="search" class="form-control" id="search-input" placeholder="Search products...">
                <span class="input-group-text bg-primary text-white">
                    <i class="fas fa-search"></i>
                </span>
            </div>
            <div class="list-group" id="products-list">
                <!-- Mobile list data will be appended here -->
            </div>
        </div>
    </div>
</div>

@endsection

@push('script')
<script>
    $(document).ready(function() {
        // Initialize DataTable
        var table = $('#products-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('product.index') }}",
                data: function(d) {
                    d.branch = $('.filter-branch.active').data('value');
                    d.catalogue = $('.filter-catalogue.active').data('value');
                    d.employee = $('.filter-employee.active').data('value');
                }
            },
            columns: [{
                    data: 'image',
                    name: 'image'
                },
                {
                    data: 'name',
                    name: 'name'
                },
                {
                    data: 'price_list',
                    name: 'price_list'
                },
                {
                    data: 'quantity',
                    name: 'quantity'
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
            order: [
                [1, 'asc']
            ],
            drawCallback: function(settings) {
                // Handle custom rendering for mobile list view
                var data = this.api().rows({
                    page: 'current'
                }).data();
                var listGroup = $('#products-list');
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
                                    <div class="col-2">${value.image}</div>
                                    <div class="col-8">
                                        <h6 class="m-0 text-capitalize">${value.name}</h6>
                                        <small class="p-0 text-muted">N Price: ${value.normal_price}</small>
                                        <small class="p-0 text-muted">W Price: ${value.whole_sale_price}</small>
                                        <small class="p-0 text-muted">A Price: ${value.agent_price}</small><br>
                                        <small class="p-0 text-muted">Quantity: ${value.quantity}</small>
                                    </div>
                                    <div class="col-2 text-end">
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
        $(document).on('click', '.filter-employee, .filter-branch, .filter-catalogue, .filter-product', function(e) {
            e.preventDefault();
            var filterClass = '.' + $(this).attr('class').split(' ')[0];

            $(filterClass).removeClass('active');
            $(this).addClass('active');
            table.ajax.reload();
        });

        // Search functionality for mobile view
        $('#search-input').on('keyup', function() {
            table.search(this.value).draw();
        });
    });
</script>
@endpush