@extends('layouts.app')

@section('pageTitle', 'Products')

@section('content')

<!-- control buttons -->
<ul class="nav nav-pills rounded bg-white mb-3">
    @if (in_array(Auth::user()->role?->role,[2,3]))
    <li class="nav-item">
        <a href="{{ route('product.create') }}" class="nav-link">
            <i class="fas fa-plus-circle"></i> Create/Update product
        </a>
    </li>
    @endif
    <li class="nav-item dropdown">
        <a class="nav-link" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="fas fa-print"></i> Print
        </a>
        <ul class="dropdown-menu rounded-0 border-0 shadow-sm p-0">
            <li><a href="{{ route('inventory.pdf') }}" target="_blank" class="dropdown-item"><i class="fas fa-file-pdf text-danger"></i> PDF</a></li>
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
                    <table class="table table-striped table-hover align-middle" id="products-table">
                        <thead class="table-dark text-center text-nowrap">
                            <tr>
                                <th>Image</th>
                                <th>Product</th>
                                <th>N</th>
                                <th>W/S</th>
                                <th>A</th>
                                <th>Qnty</th>
                                <th>S/B</th>
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
        var table = $('#products-table').DataTable({
            processing: true,
            serverSide: true,
            order: [
                [0, 'asc']
            ],
            ajax: "{{ route('product.index') }}",
            columns: [{
                    data: 'image',
                    name: 'image'
                },
                {
                    data: 'name',
                    name: 'name'
                },
                {
                    data: 'normal_price',
                    name: 'normal_price'
                },
                {
                    data: 'whole_sale_price',
                    name: 'whole_sale_price'
                },
                {
                    data: 'agent_price',
                    name: 'agent_price'
                },
                {
                    data: 'quantity',
                    name: 'quantity'
                },
                {
                    data: 'stock_balance',
                    name: 'stock_balance',
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
                var listGroup = $('#products-list');
                listGroup.empty();

                if (data.length === 0) {
                    listGroup.append(`
                    <div class="list-group-item text-center border-0 bg-light">
                        <p class="text-muted mb-0">No products available</p>
                    </div>
                `);
                } else {
                    $.each(data, function(index, value) {
                        listGroup.append(`
                        <div class="list-group-item border rounded shadow-sm mb-2">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="col-2">
                                    ${value.image}
                                </div>
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

        // Delete product
        $(document).on('click', '.delete-product', function() {
            var productId = $(this).data('product-id');

            Swal.fire({
                title: 'Are you sure you want to delete the product?',
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
                    autocomplete: 'new-password', // Prevent autofill
                    required: true
                },
                preConfirm: (password) => {
                    return new Promise((resolve) => {
                        if (password) {
                            resolve(password);
                        } else {
                            Swal.showValidationMessage('Please enter your password');
                        }
                    });
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Processing...',
                        text: 'Please wait while the product is being deleted.',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    $.ajax({
                        url: `{{ route('product.destroy', '') }}/${productId}`,
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
                            }).then(() => {
                                table.ajax.reload(null, true);
                            });
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
        });

        // Search in mobile view
        $('#search-input').on('keyup', function() {
            table.search(this.value).draw();
        });
    });
</script>
@endpush