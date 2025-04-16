@extends('layouts.app')

@section('pageTitle', $product->name)

@section('content')

    <div class="row">
        <div class="col-md-12">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="row g-4">

                        {{-- Product Image & Barcode --}}
                        <div class="col-lg-4 col-md-5">
                            <img src="{{ asset($product->photo) }}" alt="{{ $product->name }}" class="img-fluid rounded shadow-sm w-100">
                            <hr>
                            <div class="text-center">
                                <img src="data:image/png;base64,{{ $barcode }}" alt="Barcode">
                                <p class="mt-2">{{ $product->barcode }}</p>
                            </div>
                        </div>

                        {{-- Product Info --}}
                        <div class="col-lg-8 col-md-7">
                            <h1 class="h4 fw-bold">{{ $product->name }}</h1>

                            <p class="text-muted mb-2">
                                <i class="bi bi-tags"></i>
                                <strong>Category:</strong> {{ ucwords($product->catalogue->name) }}
                            </p>

                            <div class="table-responsive">
                                <table class="table table-sm table-borderless m-0 text-nowrap">
                                    <tr>
                                        <th class="pe-3" style="width: 10%;">Normal Price:</th>
                                        <td class="text-success">
                                            <i class="bi bi-currency-exchange"></i> Ksh {{ number_format($product->normal_price, 2) }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="pe-3">Wholesale Price:</th>
                                        <td class="text-warning">
                                            <i class="bi bi-currency-exchange"></i> Ksh {{ number_format($product->whole_sale_price, 2) }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="pe-3">Agent Price:</th>
                                        <td class="text-primary">
                                            <i class="bi bi-currency-exchange"></i> Ksh {{ number_format($product->agent_price, 2) }}
                                        </td>
                                    </tr>
                                </table>
                            </div>

                            <p class="mt-3">
                                <strong>Stock Quantity:</strong> {{ $product->quantity }} units
                            </p>
                            <p>
                                <strong>Available Stock Balance:</strong> {{ $product->quantity - $product->sold_quantity }} units
                            </p>
                            <p>
                                @if ($product->is_verified)
                                    Verified by: <strong>{{ $product->verified_by }}</strong>
                                    Date: <strong>{{ $product->verified_at }}</strong>
                                @else
                                    <strong>Product Not Verified</strong>
                                @endif
                            </p>

                            <div class="mt-3 d-flex flex-wrap gap-2">
                                <!-- <button class="btn btn-primary btn-sm">
                                    <i class="bi bi-cart-plus"></i> Add to Cart
                                </button>
                                <button class="btn btn-outline-secondary btn-sm">
                                    <i class="bi bi-heart"></i> Add to Wishlist
                                </button> -->

                                @if(auth()->user()->hasPermission('product_verify'))
                                    @unless($product->is_verified)
                                        <form id="product-verification-form" method="POST" action="{{ route('product.verify', $product->id) }}" class="d-inline">
                                            @csrf
                                            <button class="btn btn-success btn-sm">
                                                <i class="bi bi-patch-check"></i> Verify Product
                                            </button>
                                        </form>
                                    @endunless
                                @endif
                            </div>
                        </div>

                        {{-- Product Description --}}
                        <div class="col-12 mt-4">
                            <h2 class="h5 fw-bold">Description</h2>
                            <p class="text-muted">{{ $product->description }}</p>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('script')
<script>
    $(document).ready(function() {
        // Validate and handle form submission
        $("#product-verification-form").validate({
            submitHandler: function(form, event) {
                event.preventDefault();

                // Confirmation Dialog Before Submitting
                Swal.fire({
                    title: 'Verify Product',
                    text: 'Are you sure you want to verify product?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, Verify',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Show Processing Indicator
                        Swal.fire({
                            title: 'Processing...',
                            text: 'Please wait while the product is being verified.',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        // AJAX Form Submission
                        $.ajax({
                            url: $(form).attr('action'),
                            type: 'POST',
                            data: new FormData(form),
                            contentType: false,
                            processData: false,
                            success: function(response) {
                                // Success Notification and Redirect
                                Swal.fire({
                                    title: 'Success',
                                    text: response.success,
                                    icon: 'success',
                                }).then((result) => {
                                    window.location.reload();
                                });
                            },
                            error: function(xhr) {
                                // Error Notification
                                Swal.fire('Error!', xhr.responseJSON.error || xhr.responseJSON.message, 'error');
                            }
                        });
                    }
                });
            }
        });
    });
</script>
@endpush
