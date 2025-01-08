@extends('layouts.app')

@section('pageTitle', 'sale')

@section('content')

@if(auth()->user()->hasPermission('sale_create'))

<style>
    .table th,
    .table td {
        vertical-align: middle;
    }

    .cash-display,
    .table tfoot {
        font-weight: bolder;
        font-size: 30px;
    }
</style>

<div class="row mb-3">
    <div class="col-md-12">
        <div class="card border-0 shadow-sm">
            <div class="card-body">

                <!-- Sale setups -->
                <div class="row">
                    <input type="hidden" id="customer_id">
                    <div class="col-md-4 mb-2">
                        <select name="sale_type" id="sale_type" class="form-control">
                            <option value="normal_price">Normal Price</option>
                            <option value="whole_sale_price">Whole Salers Price</option>
                            @if(auth()->user()->hasPermission('manager_general'))
                            <option value="agent_price">Agents Price</option>
                            @endif
                        </select>
                    </div>
                    <div class="col-md-4 mb-2">
                        <select name="branch_id" id="branch_id" class="form-control">
                            @if(auth()->user()->hasPermission('manager_general'))
                            @foreach ($branches as $key => $value)
                            <option value="{{ $value->id }}">{{ $value->name }}</option>
                            @endforeach
                            @else
                            <option value="{{ auth()->user()->branch?->id }}">{{ auth()->user()->branch?->name }}</option>
                            @endif
                        </select>
                    </div>
                </div>

                <!-- Product Search -->
                <div class="col-md-12 input-group border rounded d-flex align-items-center mb-3">
                    <span class="mx-2 text-muted">
                        <i class="fas fa-search"></i>
                    </span>
                    <input type="text" class="form-control border-0" placeholder="Search for product by Barcode (Scan or Enter Manually)" id="barcode" name="barcode">
                </div>

                <!-- Cart Section -->
                <div class="row mb-3">
                    <div class="col-md-12">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover" id="cart-table">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>Image</th>
                                        <th>Item</th>
                                        <th>Price</th>
                                        <th>Qty</th>
                                        <th>Total</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="cart-items">
                                    <tr>
                                        <td colspan="6" class="text-center empty-cart text-muted">
                                            <img src="{{ asset('assets/images/icons/cart.png') }}" alt="">
                                        </td>
                                    </tr>
                                </tbody>
                                <tfoot>
                                    <tr class="font-weight-bold">
                                        <th colspan="4" class="text-end">Totals:</th>
                                        <th colspan="2">Ksh <span id="total-price">0.00</span></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                    <div class="col-md-12 text-end">
                        <button class="btn btn-success px-5 py-2" id="complete-sale">
                            <i class="fas fa-check-circle"></i> Complete Sale
                        </button>
                    </div>
                </div>
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

        // Get data
        var cart = [];
        let paymentMethods = [];
        var totalPrice = 0;

        // Handle barcode input
        $('#barcode').on('keyup', function() {
            var barcode = $(this).val();
            if (barcode.length > 0) {
                $.ajax({
                    url: `/api/fetch-data/product/${barcode}`,
                    method: 'GET',
                    success: function(response) {
                        if (response.length > 0) {
                            var product = response[0];
                            var existingProduct = cart.find(item => item.product.id === product.id);

                            if (existingProduct) {
                                existingProduct.quantity++; // Increment quantity
                                existingProduct.total = existingProduct.quantity * existingProduct.product.price; // Recalculate total
                            } else {
                                cart.push({
                                    product: product,
                                    quantity: 1,
                                    total: product.price
                                });
                            }
                        } else {
                            Swal.fire('Error!', 'No product found matching the search barcode!', 'error');
                        }

                        $('#barcode').val('');
                        updateCart();
                    },
                    error: function(xhr) {
                        Swal.fire('Error!', xhr.responseJSON.error || xhr.responseJSON.message, 'error');
                    }
                });
            }
        });

        // Update the cart display
        function updateCart() {
            // Retreive sale type
            var saleType = getCookie('sale_type') || 'normal_price';

            // Synchronize the dropdown to reflect the sale type
            $('#sale_type').val(saleType);

            // Clear cart items and reset total price
            $('#cart-items').empty();
            totalPrice = 0;

            if (cart.length > 0) {
                cart.forEach(function(item) {
                    let price;

                    // Determine the appropriate price based on sale type
                    switch (saleType) {
                        case 'whole_sale_price':
                            price = item.product.whole_sale_price;
                            break;
                        case 'agent_price':
                            price = item.product.agent_price;
                            break;
                        default:
                            price = item.product.normal_price;
                    }

                    const total = price * item.quantity;

                    const row = `
                        <tr>
                            <td><img src="{{ asset('') }}${item.product.photo}" alt="" width="50"></td>
                            <td>${item.product.name}</td>
                            <td>Ksh ${price}</td>
                            <td>
                                <input type="number" value="${item.quantity}" min="1" class="form-control quantity-input" data-id="${item.product.id}">
                            </td>
                            <td>Ksh ${total}</td>
                            <td>
                                <button class="btn btn-outline-danger remove-item w-100" data-id="${item.product.id}">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </td>
                        </tr>
                    `;

                    $('#cart-items').append(row);
                    totalPrice += total;
                });
            } else {
                // Show empty cart message if no items
                $('#cart-items').append(`
                    <tr>
                        <td colspan="6" class="text-center empty-cart text-muted">
                            <img src="{{ asset('assets/images/icons/cart.png') }}" alt="Empty Cart">
                        </td>
                    </tr>
                `);
            }

            // Update total price display
            $('#total-price').text(totalPrice.toFixed(2));
        }

        // Update quantity in cart
        $('#cart-table').on('change', '.quantity-input', function() {
            var productId = $(this).data('id');
            var quantity = parseInt($(this).val());
            var product = cart.find(item => item.product.id === productId);

            if (product) {
                product.quantity = quantity;
                product.total = quantity * product.product.price;
                updateCart();
            }
        });

        // Remove item from cart
        $('#cart-table').on('click', '.remove-item', function() {
            var productId = $(this).data('id');
            cart = cart.filter(item => item.product.id !== productId);
            updateCart();
        });

        // Complete the sale
        $('#complete-sale').on('click', function() {
            if (cart.length === 0) {
                Swal.fire('No items in cart', 'Please add products to the cart before completing the sale.', 'warning');
                return;
            } else {
                loadPaymentMethods();
            }
        });

        function loadPaymentMethods() {
            $.ajax({
                url: '/api/fetch-data/payment-methods',
                method: 'GET',
                success: function(data) {
                    // open div
                    let content = `<div class="row">`;
                    // Loop through the fetched payment methods and create buttons
                    data.forEach(paymentMethod => {
                        content += `
                                <div class="col">
                                    <button class="btn btn-lg w-100 border selected-payment-method"
                                        data-pay-id="${paymentMethod.id}" 
                                        data-pay-name="${paymentMethod.name}">
                                            <h6>${paymentMethod.name}</h6>
                                            <img src="{{ asset('') }}${paymentMethod.image}" width="100">
                                    </button>
                                </div>
                            `;
                    });
                    // close div
                    content += `</div>`;

                    // Open SweetAlert modal
                    Swal.fire({
                        title: 'Select Payment Method',
                        icon: 'info',
                        html: content,
                        showCancelButton: true,
                        showConfirmButton: false,
                        cancelButtonColor: '#d33',
                        cancelButtonText: 'Cancel Transaction',
                    });
                },
                error: function(xhr) {
                    const errorMessage = xhr.responseJSON?.error || xhr.responseJSON?.message || 'An error occurred while fetching transactions.';
                    Swal.fire('Error!', errorMessage, 'error');
                }
            });
        }

        // Function to calculate the remaining balance
        function remainingBalance() {
            // Initialize remaining with totalPrice
            let remaining = totalPrice;

            // Subtract each payment's amount from the remaining balance
            paymentMethods.forEach(function(payment) {
                remaining -= payment.amount;
            });

            // Return the remaining balance
            return remaining;
        }

        // Delegate event listener for dynamically created buttons
        $(document).on('click', '.selected-payment-method', function() {
            const payModeId = $(this).data('pay-id');
            const payModeName = $(this).data('pay-name');

            function showPaymentAlert(paymentMethod) {
                // Update remaining balance
                let remainingAmount = remainingBalance();

                Swal.fire({
                    title: `Enter payment for ${paymentMethod}`,
                    icon: 'info',
                    html: `
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <input type="number" id="amountPaid" class="form-control form-control-lg" placeholder="Enter amount" required />
                    </div>
                    <div class="col-md-12 text-end cash-display mb-3">
                        Remaining Amount: <span id="remaining-amount">${remainingAmount}</span>
                    </div>
                </div>
            `,
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Proceed with payment',
                    cancelButtonText: 'Cancel Transaction',
                    didOpen: () => {
                        $('#amountPaid').on('keyup', function() {
                            const amountPaid = parseInt($(this).val()) || 0; // Ensure value is a number
                            const remaining = remainingAmount - amountPaid;
                            $('#remaining-amount').text(remaining >= 0 ? remaining : 0);
                        });
                    },
                    preConfirm: () => {
                        // Save payment data for the selected payment method
                        paymentMethods.push({
                            payment_method_name: paymentMethod,
                            amount: amountPaid,
                            payment_method_id: payModeId
                        });

                        return true;
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Update remaining balance
                        remainingAmount = remainingBalance();

                        // If remaining amount is still greater than 0, ask for another payment method
                        if (remainingAmount > 0) {
                            Swal.fire({
                                title: 'Add another payment method?',
                                text: `Remaining balance: Ksh ${remainingAmount}`,
                                showCancelButton: true,
                                confirmButtonText: 'Yes, add payment',
                                cancelButtonText: 'No, finalize payment'
                            }).then((res) => {
                                if (res.isConfirmed) {
                                    // Load the next available payment methods
                                    loadPaymentMethods();
                                } else {
                                    // Proceed with payment processing
                                    finalizeSale();
                                }
                            });
                        } else {
                            // Finalize the payment once the full amount is paid
                            finalizeSale();
                        }
                    }
                });
            }

            if (payModeName == 'cash') {
                showPaymentAlert('Cash');
            } else if (payModeName == 'mpesa') {
                // Show M-Pesa transaction selection
                Swal.fire({
                    title: 'Select M-Pesa Transaction',
                    html: `
                <div class="col-md-12">
                    <table class="table table-sm table-bordered table-striped table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>TransId</th>
                                <th>TransName</th>
                                <th>TransAmnt</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="mpesa-transactions-body">
                            <tr>
                                <td colspan="4" class="text-center">Loading...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            `,
                    showCancelButton: true,
                    showConfirmButton: false,
                    cancelButtonColor: '#d33',
                    cancelButtonText: 'Cancel Transaction',
                    didOpen: () => {
                        $.ajax({
                            url: '/api/fetch-data/mpesa-payments',
                            type: 'GET',
                            dataType: 'json',
                            success: function(response) {
                                const tableBody = $('#mpesa-transactions-body');
                                tableBody.empty();
                                if (response.length > 0) {
                                    response.forEach(transaction => {
                                        tableBody.append(`
                                    <tr>
                                        <td>${transaction.transaction_id}</td>
                                        <td>${transaction.name}</td>
                                        <td>${transaction.amount}</td>
                                        <td>
                                            <button class="btn btn-sm btn-primary select-transaction" 
                                                    data-id="${transaction.id}" 
                                                    data-transaction_id="${transaction.transaction_id}" 
                                                    data-name="${transaction.name}" 
                                                    data-amount="${transaction.amount}">
                                                Select
                                            </button>
                                        </td>
                                    </tr>
                                `);
                                    });

                                    // Bind click event to dynamically added buttons
                                    $('#mpesa-transactions-body').on('click', '.select-transaction', function() {
                                        const selectedTransaction = {
                                            id: $(this).data('id'),
                                            transaction_id: $(this).data('transaction_id'),
                                            name: $(this).data('name'),
                                            amount: $(this).data('amount')
                                        };

                                        const transactionDetailsHtml = `
                                    <div class="alert alert-success mt-3" role="alert">
                                        <h5>Selected Transaction Details</h5>
                                        <p><strong>Transaction ID:</strong> ${selectedTransaction.transaction_id}</p>
                                        <p><strong>Transaction Name:</strong> ${selectedTransaction.name}</p>
                                        <p><strong>Transaction Amount:</strong> Ksh ${selectedTransaction.amount}</p>
                                    </div>
                                `;

                                        Swal.fire({
                                            title: 'Transaction Details',
                                            html: transactionDetailsHtml,
                                            showCancelButton: true,
                                            confirmButtonColor: '#3085d6',
                                            cancelButtonColor: '#d33',
                                            confirmButtonText: 'Proceed with payment',
                                            cancelButtonText: 'Cancel Transaction',
                                            preConfirm: () => {
                                                // Add M-Pesa payment to array
                                                paymentMethods.push({
                                                    payment_method_name: 'mpesa',
                                                    amount: selectedTransaction.amount,
                                                    payment_method_id: payModeId
                                                });

                                                return true;
                                            }
                                        }).then(() => {
                                            // Update remaining balance
                                            const remainingAmount = remainingBalance();

                                            // After confirming the payment, check if remaining amount is greater than zero
                                            if (remainingAmount > 0) {
                                                loadPaymentMethods(); // Trigger the loading of next payment method
                                            } else {
                                                finalizeSale(paymentMethods);
                                            }
                                        });
                                    });
                                } else {
                                    tableBody.append(`
                                <tr>
                                    <td colspan="4" class="text-center">No recent payment made</td>
                                </tr>
                            `);
                                }
                            },
                            error: function(xhr) {
                                const errorMessage = xhr.responseJSON?.error || xhr.responseJSON?.message || 'An error occurred while fetching transactions.';
                                Swal.fire('Error!', errorMessage, 'error');
                            }
                        });
                    }
                });
            } else {
                Swal.fire('Error!', 'No payment method found matching your selection, kindly try again!', 'error');
            }
        });

        // Finalize the sale
        function finalizeSale() {
            var customer_id = '';
            var branch_id = $('#branch_id').val();
            var data = cart;
            var paymentData = paymentMethods;
            var totalAmount = data.total;
            var totalPaid = 0;

            // Calculate the total amount paid from the payments data
            paymentData.forEach(function(payment) {
                totalPaid += payment.amount;
            });

            // Check if total amount paid is greater than or equal to the total amount
            if (totalPaid < totalAmount) {
                Swal.fire({
                    title: 'Error',
                    text: 'The total amount paid is less than the total bill. Please ensure payment is sufficient before proceeding.',
                    icon: 'error',
                });
                return; // Stop the function execution if payment is insufficient
            }

            // Proceed with the sale submission if the payment is sufficient
            Swal.fire({
                title: 'Sale submission',
                text: 'Are you sure you want to submit this sale?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Proceed with payment',
                cancelButtonText: 'Cancel Transaction',
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Processing...',
                        text: 'Please wait while we process your request.',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    $.ajax({
                        url: "{{ route('sale.store') }}",
                        method: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            customer_id: customer_id,
                            sale_type: getCookie('sale_type') || 'normal_price',
                            branch_id: branch_id,
                            payments: paymentData,
                            data
                        },
                        success: function(response) {
                            Swal.fire({
                                title: 'Success',
                                text: response.success,
                                icon: 'success',
                            }).then((result) => {
                                cart = [];
                                updateCart();
                                window.location.href = "{{ route('sale.show', ':sale_id') }}".replace(':sale_id', response.sale_id);
                            });
                        },
                        error: function(xhr) {
                            Swal.fire('Error!', xhr.responseJSON.error || xhr.responseJSON.message, 'error');
                        }
                    });
                }
            });
        }

        $('#sale_type').on('change', function() {
            var sale_type = $(this).val();

            // Get title
            switch (sale_type) {
                case 'whole_sale_price':
                    swalTitle = 'Whole Sale';
                    break;
                case 'agent_price':
                    swalTitle = 'Agent Sale';
                    break;
                default:
                    swalTitle = 'Normal Sale';
            }

            Swal.fire({
                title: 'Switch to ' + swalTitle,
                text: 'Are you sure you want to change the sale type to ' + swalTitle + ' customer?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, Change',
                cancelButtonText: 'No, Cancel',
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Processing...',
                        text: 'Please wait while we process your request.',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    // Set a cookie for one year
                    var expirationDate = new Date();
                    expirationDate.setFullYear(expirationDate.getFullYear() + 1);
                    document.cookie = "sale_type=" + sale_type + ";expires=" + expirationDate.toUTCString() + ";path=/";

                    // Simulate a delay to mimic processing time
                    setTimeout(() => {
                        Swal.fire({
                            title: 'Success',
                            text: 'The sale type has been successfully changed to ' + swalTitle + '.',
                            icon: 'success',
                            confirmButtonColor: '#3085d6',
                            confirmButtonText: 'OK'
                        }).then((result) => {
                            updateCart();
                        });
                    }, 1500);
                }
            });
        });
    });
</script>
@endpush