@extends('layouts.app')

@section('pageTitle', 'Add Product')

@section('content')

@if(auth()->user()->hasPermission('product_create'))

<div class="row mb-3">
    <div class="col-md-12">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <form id="create-product-form" action="{{ route('product.store') }}" method="POST">
                    @csrf

                    <!-- Barcode -->
                    <div class="col-md-12 input-group border rounded d-flex align-items-center">
                        <span class="mx-2 text-muted">
                            <i class="fas fa-search"></i>
                        </span>
                        <input
                            type="search"
                            class="form-control border-0"
                            placeholder="Search for product by Barcode (Scan or Enter Manually)"
                            id="barcode"
                            name="barcode"
                            autofocus
                            required />
                        <!-- Button to generate barcode -->
                        <span class="mx-2 text-muted cursor-pointer" id="generateBarcode" title="Generate Barcode">
                            <i class="fas fa-random"></i>
                        </span>
                    </div>

                    <div class="row mt-3 product-data d-none">
                        <!-- Photo -->
                        <div class="col-md-2">
                            <div class="image-card border text-center p-1 mb-2">
                                <img src="{{ asset('assets/images/defaults/product.png') }}" alt="Product Image" id="product-image-preview" class="img-fluid">
                            </div>
                            <div class="upload-img">
                                <input type="file" name="product_image" id="product_image" class="d-none" accept="image/*">
                                <button type="button" id="choose-image-btn" class="btn btn-outline-primary w-100">
                                    <i class="fas fa-image"></i> Choose Image
                                </button>
                            </div>
                        </div>

                        <div class="col-md-10">
                            <div class="row g-1">
                                <!-- Branch -->
                                <div class="col-md-6">
                                    <label class="mb-0 text-capitalize" for="branch">Branch</label>
                                    <select class="form-control" id="branch" name="branch_id" required>
                                        <option value="">Select Branch</option>
                                        @foreach ($branches as $key => $value)
                                        <option value="{{ $value->id }}"
                                            {{ ($value->id == auth()->user()->branch?->id) ? 'selected' : '' }}>
                                            {{ ucwords($value->name) }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                                <!-- Catalogue -->
                                <div class="col-md-6">
                                    <label class="mb-0 text-capitalize" for="catalogue">Catalogue</label>
                                    <select class="form-control" id="catalogue" name="catalogue_id" required>
                                        <option value="">Select Catalogue</option>
                                        @foreach ($catalogue as $item)
                                        <option value="{{ $item->id }}">{{ ucwords($item->name) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <!-- Name -->
                                <div class="col-md-6">
                                    <label class="mb-0 text-capitalize" for="name">Name</label>
                                    <input type="text" name="name" id="name" placeholder="Product Name" class="form-control" required>
                                </div>
                                <!-- Price -->
                                <div class="col-md-6">
                                    <label class="mb-0 text-capitalize" for="buying_price">Buying Price</label>
                                    <input type="text" name="buying_price" id="buying_price" placeholder="Buying Price" class="form-control" required>
                                </div>
                                <!-- Price -->
                                <div class="col-md-4">
                                    <label class="mb-0 text-capitalize" for="normal_price">Normal Price</label>
                                    <input type="text" name="normal_price" id="normal_price" placeholder="Normal Price" class="form-control" required>
                                </div>
                                <!-- Price -->
                                <div class="col-md-4">
                                    <label class="mb-0 text-capitalize" for="whole_sale_price">Whole Sale Price</label>
                                    <input type="text" name="whole_sale_price" id="whole_sale_price" placeholder="Whole Sale Price" class="form-control" required>
                                </div>
                                <!-- Price -->
                                <div class="col-md-4">
                                    <label class="mb-0 text-capitalize" for="agent_price">Agent Price</label>
                                    <input type="text" name="agent_price" id="agent_price" placeholder="Agent Price" class="form-control" required>
                                </div>
                                <!-- Quantity -->
                                <div class="col-md-6">
                                    <label class="mb-0 text-capitalize" for="quantity">Quantity</label>
                                    <input type="text" name="quantity" id="quantity" placeholder="Quantity" class="form-control" required>
                                </div>
                                <!-- SKU -->
                                <div class="col-md-6">
                                    <label class="mb-0 text-capitalize" for="sku">SKU</label>
                                    <input type="text" name="sku" id="sku" placeholder="SKU" class="form-control" required>
                                </div>
                            </div>
                        </div>

                        <!-- Description -->
                        <div class="col-md-12 mb-3">
                            <label for="description">Description</label>
                            <textarea name="description" id="description" placeholder="Product Description" rows="3" class="form-control"></textarea>
                        </div>

                        <div class="col-md-12 text-end">
                            <button type="submit" class="btn btn-primary" id="save-btn">
                                <i class="fas fa-plus-circle"></i> Create New Product
                            </button>
                        </div>
                    </div>
                </form>
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
        $('#generateBarcode').on('click', function() {
            // Generate a barcode using the current timestamp
            const timestamp = Date.now(); // Get current time in milliseconds
            const generatedBarcode = `${timestamp}`; // Prefix 'BAR' for distinction
            // Insert the generated barcode into the input field
            $('#barcode').val(generatedBarcode).focus();
            search_product(generatedBarcode);
        });

        $('#barcode').on('keyup', function() {
            var barcode = $(this).val();
            search_product(barcode);
        });

        function search_product(barcode) {
            barcode = barcode;

            function resetFields() {
                $('#name, #buying_price, #normal_price, #whole_sale_price, #agent_price, #quantity, #sku, #description, #branch, #catalogue').val('');
                $('#product-image-preview').attr('src', "{{ asset('assets/images/defaults/product.png') }}");
                $('#save-btn').html('Create new product');
                $('.product-data').removeClass('d-none');
            }

            function populateFields(product) {
                $('#name').val(product.name);
                $('#buying_price').val(product.buying_price);
                $('#normal_price').val(product.normal_price);
                $('#whole_sale_price').val(product.whole_sale_price);
                $('#agent_price').val(product.agent_price);
                $('#quantity').val(product.quantity);
                $('#sku').val(product.sku);
                $('#description').val(product.description);
                $('#branch').val(product.branch_id);
                $('#catalogue').val(product.catalogue_id);
                $('#product-image-preview').attr('src', product.photo ? "{{ asset('') }}" + product.photo : "{{ asset('assets/images/defaults/product.png') }}");
                $('#save-btn').html('Update the product');
                $('.product-data').removeClass('d-none');
            }

            if (barcode.length > 0) {
                $.ajax({
                    url: `/api/fetch-data/product/${barcode}`,
                    method: 'GET',
                    success: function(response) {
                        if (response.length > 0) {
                            populateFields(response[0]);
                        } else {
                            resetFields();
                        }
                    },
                    error: function(xhr) {
                        Swal.fire('Error!', xhr.responseJSON.error || xhr.responseJSON.message, 'error');
                    }
                });
            } else {
                resetFields();
            }
        }

        $("#create-product-form").validate({
            submitHandler: function(form, event) {
                event.preventDefault();

                var dataTitle = $('#save-btn').html();

                Swal.fire({
                    title: dataTitle,
                    text: 'Are you sure you want to ' + dataTitle + '?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes ' + dataTitle,
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: 'Processing...',
                            text: 'Please wait while we ' + dataTitle,
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        $.ajax({
                            url: $(form).attr('action'),
                            type: 'POST',
                            data: new FormData(form),
                            contentType: false,
                            processData: false,
                            success: function(response) {
                                Swal.fire({
                                    title: 'Success',
                                    text: response.success,
                                    icon: 'success'
                                }).then(() => {
                                    window.location.href = "{{ route('product.index') }}";
                                });
                            },
                            error: function(xhr) {
                                Swal.fire('Error!', xhr.responseJSON.error || xhr.responseJSON.message, 'error');
                            }
                        });
                    }
                });
            }
        });

        // Handle button click to trigger file input
        $('#choose-image-btn').on('click', function() {
            $('#product_image').click();
        });

        // Handle file selection and image preview
        $('#product_image').on('change', function() {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    $('#product-image-preview').attr('src', e.target.result);
                };
                reader.readAsDataURL(file);
            }
        });
    });
</script>
@endpush