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
                            <option value="{{ auth()->user()->branch?->id }}">{{ auth()->user()->branch?->name }}
                            </option>
                            @endif
                        </select>
                    </div>
                </div>

                <!-- Product Search -->
                <div class="col-md-12 input-group border rounded d-flex align-items-center mb-3">
                    <span class="mx-2 text-muted">
                        <i class="fas fa-search"></i>
                    </span>
                    <input type="text" class="form-control border-0"
                        placeholder="Search for product by Barcode (Scan or Enter Manually)" id="barcode"
                        name="barcode">
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
                                        <th colspan="2" class="text-nowrap">Ksh <span id="total-price">0.00</span></th>
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

                            // Check if available quantity is sufficient
                            if (existingProduct) {
                                if (existingProduct.quantity + 1 <= product.quantity) {
                                    existingProduct.quantity++; // Increment quantity
                                    existingProduct.total = existingProduct.quantity * existingProduct.product.price; // Recalculate total
                                } else {
                                    Swal.fire('Error!', 'Not enough stock available for this product!', 'error');
                                }
                            } else {
                                if (product.quantity > 0) {
                                    cart.push({
                                        product: product,
                                        quantity: 1,
                                        total: product.price
                                    });
                                } else {
                                    Swal.fire('Error!', 'Product is out of stock!', 'error');
                                }
                            }
                        } else {
                            Swal.fire('Error!', 'No product found matching the search barcode!', 'error');
                        }

                        $('#barcode').val('');
                        updateCart();
                    },
                    error: function(xhr) {
                        Swal.fire('Error!', xhr.responseJSON.error || xhr.responseJSON.message,
                            'error');
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
                                <input type="number" value="${item.quantity}" min="1" class="form-control quantity-input" data-id="${item.product.id}" hidden />
                                ${item.quantity}
                            </td>
                            <td>Ksh ${total}</td>
                            <td>
                                <button class="btn btn-outline-danger remove-item w-100" data-id="${item.product.id}">
                                    <i class="fas fa-times-circle"></i>
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

        // // Update quantity in cart
        // $('#cart-table').on('change', '.quantity-input', function() {
        //     var productId = $(this).data('id');
        //     var quantity = parseInt($(this).val());
        //     var product = cart.find(item => item.product.id === productId);

        //     if (product) {
        //         product.quantity = quantity;
        //         product.total = quantity * product.product.price;
        //         updateCart();
        //     }
        // });

        // Remove item from cart
        $('#cart-table').on('click', '.remove-item', function() {
            var productId = $(this).data('id');
            cart = cart.filter(item => item.product.id !== productId);
            updateCart();
        });

        // Complete the sale
        $('#complete-sale').on('click', function() {
            if (cart.length === 0) {
                Swal.fire('No items in cart', 'Please add products to the cart before completing the sale.',
                    'warning');
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
                    const errorMessage = xhr.responseJSON?.error || xhr.responseJSON?.message ||
                        'An error occurred while fetching transactions.';
                    Swal.fire('Error!', errorMessage, 'error');
                }
            });
        }

        // Function to calculate the remaining balance
        function remainingBalance() {
            let remaining = Number(totalPrice) || 0; // Ensure totalPrice is a number

            paymentMethods.forEach(function(payment) {
                const amount = Number(payment.amount) || 0;
                remaining -= amount;
            });

            return remaining;
        }

        // Delegate event listener for dynamically created buttons
        $(document).on('click', '.selected-payment-method', function() {
            const payModeId = $(this).data('pay-id');
            const payModeName = $(this).data('pay-name');

            // Function to bind payment
            function bindPayment(amount, reference = 'NULL') {
                paymentMethods.push({
                    id: payModeId,
                    name: payModeName,
                    amount: amount,
                    reference: reference, // Placeholder for reference
                });

                // Update remaining balance
                const remainingAmount = remainingBalance();

                // After confirming the payment, check if remaining amount is greater than zero
                if (remainingAmount > 0) {
                    Swal.fire({
                        title: 'Add another payment method?',
                        text: `You have successfully paid Ksh ${amount}. The remaining balance is Ksh ${remainingAmount}.`,
                        icon: 'success',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Yes, add payment',
                        cancelButtonText: 'No, finalize payment'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Load payment methods
                            loadPaymentMethods();
                        }
                    });
                } else {
                    finalizeSale();
                }
            }

            // Check the payment method selected
            if (payModeName == 'cash') {
                // Handle cash payment
                Swal.fire({
                    title: `Enter payment for cash payment`,
                    icon: 'info',
                    html: `
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <input type="number" id="amountPaid" class="form-control form-control-lg" placeholder="Enter amount" required />
                            </div>
                        </div>
                    `,
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Proceed with payment',
                    cancelButtonText: 'Cancel Transaction',
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Bind Payment
                        bindPayment($('#amountPaid').val());
                    }
                });
            } else if (payModeName == 'card') {
                // Handle card payment
            } else if (payModeName == 'mpesa') {
                // Handle mpesa payment
                Swal.fire({
                    title: 'M-Pesa Payments',
                    html: `
                        <nav>
                            <div class="nav nav-tabs" id="nav-tab" role="tablist">
                                <button class="nav-link active" id="nav-recent-transactions-tab" data-bs-toggle="tab" data-bs-target="#nav-recent-transactions" type="button" role="tab" aria-controls="nav-recent-transactions" aria-selected="true">
                                    Transactions
                                </button>
                                <button class="nav-link" id="nav-initiate-transaction-tab" data-bs-toggle="tab" data-bs-target="#nav-initiate-transaction" type="button" role="tab" aria-controls="nav-initiate-transaction" aria-selected="false">
                                    STK Push
                                </button>
                            </div>
                        </nav>

                        <div class="tab-content border mt-1" id="nav-tabContent">
                            <div class="tab-pane fade show active" id="nav-recent-transactions" role="tabpanel" aria-labelledby="nav-recent-transactions-tab" tabindex="0">
                                <div class="p-1">
                                    <div class="table-responsive">
                                        <table class="table table-sm table-bordered table-striped table-hover">
                                            <thead class="table-dark">
                                                <tr>
                                                    <th>TransID</th>
                                                    <th>Name</th>
                                                    <th>Phone</th>
                                                    <th>Amount</th>
                                                </tr>
                                            </thead>
                                            <tbody id="mpesa-transactions-body">
                                                <tr>
                                                    <td colspan="4" class="text-center">Loading...</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="nav-initiate-transaction" role="tabpanel" aria-labelledby="nav-initiate-transaction-tab" tabindex="0">
                                <div class="p-1">
                                    <form id="transaction-form" method="POST">
                                        <input type="text" id="stk_phone" name="stk_phone" class="form-control mb-2" placeholder="Enter phone number" required>
                                        <input type="number" id="stk_amount" name="stk_amount" class="form-control mb-2" placeholder="Enter amount" required>
                                        <button type="submit" id="initiate-transaction" class="btn btn-success w-100">Initiate Payment</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    `,
                    showCancelButton: true,
                    showConfirmButton: false,
                    cancelButtonColor: '#d33',
                    cancelButtonText: 'Cancel Transaction',
                    didOpen: () => {
                        // Initiate transaction
                        $('#transaction-form').on('submit', function (e) {
                            e.preventDefault(); // Prevent the default form submission

                            const stk_phone = $('#stk_phone').val();
                            const stk_amount = $('#stk_amount').val();

                            // Basic validation
                            if (stk_phone === '' || stk_amount === '') {
                                Swal.fire('Error!', 'Please fill in both fields.', 'error');
                                return;
                            }

                            Swal.fire({
                                title: 'Initiate transaction for customer',
                                text: 'Are you sure you want to initiate the transaction for the customer?',
                                icon: 'warning',
                                showCancelButton: true,
                                confirmButtonColor: '#3085d6',
                                cancelButtonColor: '#d33',
                                confirmButtonText: 'Yes, Initiate',
                                cancelButtonText: 'Cancel'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    Swal.fire({
                                        title: 'Processing...',
                                        text: 'Please wait while the transaction is being initiated.',
                                        allowOutsideClick: false,
                                        didOpen: () => {
                                            Swal.showLoading();
                                        }
                                    });

                                    // Prepare the data to send in AJAX request
                                    const formData = new FormData();
                                    formData.append('phoneNumber', stk_phone);
                                    formData.append('amount', stk_amount);

                                    // Get the CSRF token from the meta tag
                                    const csrfToken = $('meta[name="csrf-token"]').attr('content');
                                    
                                    $.ajax({
                                        url: '/mpesa/stkpush', // Replace with your actual API endpoint
                                        type: 'POST',
                                        data: formData,
                                        contentType: false,
                                        processData: false,
                                        headers: {
                                            'X-CSRF-TOKEN': csrfToken // Send CSRF token in the header
                                        },
                                        success: function (response) {
                                            Swal.fire({
                                                title: 'Success',
                                                text: response.success || 'Transaction initiated successfully!',
                                                icon: 'success',
                                            }).then(() => {
                                                // Optionally reset the form or take any other actions
                                                $('#transaction-form')[0].reset();
                                            });
                                        },
                                        error: function (xhr) {
                                            Swal.fire('Error!', xhr.responseJSON.error || xhr.responseJSON.message || 'An error occurred while initiating the transaction.', 'error');
                                        }
                                    });
                                }
                            });
                        });

                        // Fetch and bind transaction
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
                                            <tr class="transaction-row cursor-pointer" 
                                                data-id="${transaction.id}" 
                                                data-transaction_id="${transaction.transaction_id}" 
                                                data-name="${transaction.name}" 
                                                data-phone="${transaction.phone}" 
                                                data-amount="${transaction.amount}">
                                                <td>${transaction.transaction_id}</td>
                                                <td>${transaction.name}</td>
                                                <td>${transaction.phone}</td>
                                                <td>${transaction.amount}</td>
                                            </tr>
                                        `);
                                    });

                                    // Bind click event to dynamically added rows
                                    $('#mpesa-transactions-body').on('click', '.transaction-row', function() {
                                        const selectedTransaction = {
                                            id: $(this).data('id'),
                                            transaction_id: $(this).data('transaction_id'),
                                            name: $(this).data('name'),
                                            phone: $(this).data('phone'),
                                            amount: $(this).data('amount')
                                        };

                                        const transactionDetailsHtml = `
                                            <div class="table-responsive">
                                                <table class="table table-bordered">
                                                    <tbody>
                                                        <tr>
                                                            <th>Transaction ID</th>
                                                            <td>${selectedTransaction.transaction_id}</td>
                                                        </tr>
                                                        <tr>
                                                            <th>Name</th>
                                                            <td>${selectedTransaction.name}</td>
                                                        </tr>
                                                        <tr>
                                                            <th>Phone</th>
                                                            <td>${selectedTransaction.phone}</td>
                                                        </tr>
                                                        <tr>
                                                            <th>Amount (Ksh)</th>
                                                            <td>${selectedTransaction.amount}</td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        `;

                                        Swal.fire({
                                            title: 'Selected Transaction Details',
                                            html: transactionDetailsHtml,
                                            showCancelButton: true,
                                            confirmButtonColor: '#3085d6',
                                            cancelButtonColor: '#d33',
                                            confirmButtonText: 'Proceed with Payment',
                                            cancelButtonText: 'Cancel Transaction',
                                        }).then((result) => {
                                            if (result.isConfirmed) {
                                                bindPayment(selectedTransaction.amount, selectedTransaction.id);
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
                                const errorMessage = xhr.responseJSON?.error || xhr
                                    .responseJSON?.message ||
                                    'An error occurred while fetching transactions.';
                                Swal.fire('Error!', errorMessage, 'error');
                            }
                        });
                    }
                });
            } else {
                Swal.fire('Error!', 'No payment method found matching your selection, kindly try again!',
                    'error');
            }
        });

        // Finalize the sale
        function finalizeSale() {
            const branch_id = $('#branch_id').val(); // Branch ID
            const customer_id = ''; // Placeholder for customer ID
            let totalPaid = 0;

            // Calculate the total amount paid
            paymentMethods.forEach(payment => {
                const amount = parseFloat(payment.amount) || 0; // Ensure numeric conversion
                totalPaid += amount;
            });

            const data = {
                total_price: parseFloat(totalPrice) || 0, // Total price of the sale
                cart: cart, // Cart items
                payments: paymentMethods, // Payment methods
                total_paid: totalPaid, // Total amount paid
            };

            // Check if total paid is less than the total price
            if (data.total_paid < data.total_price) {
                Swal.fire({
                    title: 'Payment Error',
                    text: `The total amount paid (${totalPaid.toFixed(2)}) is less than the total bill (${data.totalAmount.toFixed(2)}). Please ensure full payment is made before proceeding.`,
                    icon: 'error',
                });
                return; // Stop execution if payment is insufficient
            }

            // Confirmation dialog for sale submission
            Swal.fire({
                title: 'Confirm Sale',
                text: 'Are you sure you want to submit this sale?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Proceed with Payment',
                cancelButtonText: 'Cancel Transaction',
            }).then(result => {
                if (result.isConfirmed) {
                    // Show loading while processing
                    Swal.fire({
                        title: 'Processing...',
                        text: 'Please wait while we process your request.',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    // AJAX request to submit the sale
                    $.ajax({
                        url: "{{ route('sale.store') }}",
                        method: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            customer_id: customer_id,
                            sale_type: getCookie('sale_type') || 'normal_price',
                            branch_id: branch_id,
                            data: data, // Send the prepared data object
                        },
                        success: function(response) {
                            Swal.fire({
                                title: 'Sale Successful',
                                text: response.success || 'The sale has been completed successfully.',
                                icon: 'success',
                            }).then(() => {
                                cart = []; // Clear cart
                                paymentMethods = []; // Clear paymentMethods
                                totalPrice = 0; // Reset total price
                                updateCart(); // Update UI to reflect cleared cart
                                const saleUrl = "{{ route('sale.show', ':sale_id') }}".replace(':sale_id', response.sale_id);
                                window.location.href = saleUrl; // Redirect to sale details
                            });
                        },
                        error: function(xhr) {
                            Swal.fire({
                                title: 'Error',
                                text: xhr.responseJSON?.error || xhr.responseJSON
                                    ?.message ||
                                    'An error occurred during the sale submission.',
                                icon: 'error',
                            }).then(() => {
                                paymentMethods = []; // Clear paymentMethods
                            });
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
                text: 'Are you sure you want to change the sale type to ' + swalTitle +
                    ' customer?',
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
                    document.cookie = "sale_type=" + sale_type + ";expires=" + expirationDate
                        .toUTCString() + ";path=/";

                    // Simulate a delay to mimic processing time
                    setTimeout(() => {
                        Swal.fire({
                            title: 'Success',
                            text: 'The sale type has been successfully changed to ' +
                                swalTitle + '.',
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

        // When the "Initiate Transaction" button is clicked
        $('#initiate-transaction').click(function(e) {
            e.preventDefault(); // Prevent form submission

            // Get values from the input fields
            var phone = $('#phone').val();
            var amount = $('#amount').val();

            // Check if both fields are filled
            if (phone === "" || amount === "") {
                Swal.fire({
                    title: 'Error!',
                    text: 'Please enter both phone number and amount.',
                    icon: 'error',
                });
                return;
            }

            // Show confirmation dialog
            Swal.fire({
                title: 'Initiate transaction',
                text: 'Are you sure you want to initiate the transaction?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, initiate!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Show loading dialog while processing
                    Swal.fire({
                        title: 'Processing...',
                        text: 'Please wait while the transaction is being initiated.',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    // AJAX call to initiate the transaction
                    $.ajax({
                        url: '/mpesa/stkpush', // Replace with your URL endpoint
                        type: 'POST',
                        data: {
                            phone: phone,
                            amount: amount,
                            _token: '{{ csrf_token() }}', // CSRF token for Laravel
                        },
                        success: function(response) {
                            Swal.fire({
                                title: 'Success!',
                                text: 'Transaction initiated successfully.',
                                icon: 'success',
                            });
                        },
                        error: function(xhr) {
                            Swal.fire({
                                title: 'Error!',
                                text: xhr.responseJSON.message || 'Something went wrong!',
                                icon: 'error',
                            });
                        }
                    });
                }
            });
        });
    });
</script>
@endpush