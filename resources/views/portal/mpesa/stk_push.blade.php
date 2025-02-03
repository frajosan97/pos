@extends('layouts.app')

@section('pageTitle','STK Push')

@section('content')

<!-- control buttons -->
<ul class="nav nav-pills rounded bg-white mb-3">
    <li class="nav-item">
        <a href="#" class="nav-link">
            <i class="nav-icon fas fa-receipt"></i> STK Push
        </a>
    </li>
</ul>
<!-- / end control buttons -->

<div class="row">
    <div class="col-md-12 mx-auto">

        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <form id="stk-push-form" action="{{ route('mpesa.stkpush') }}" method="post">
                    @csrf
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label for="phoneNumber">Phone Number</label>
                            <input type="text" class="form-control" id="phoneNumber" name="phoneNumber" placeholder="Phone Number" required>
                        </div>
                        <div class="col-md-6 form-group">
                            <label for="amount">Amount</label>
                            <input type="number" class="form-control" id="amount" name="amount" placeholder="Amount" required>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">Initiate STK Push</button>
                </form>
            </div>
        </div>

    </div>
</div>

@endsection

@push('script')
<script>
    $(document).ready(function() {
        // Validate and handle form submission
        $("#stk-push-form").validate({
            submitHandler: function(form, event) {
                event.preventDefault();

                Swal.fire({
                    title: 'Initiate transaction for customer',
                    text: 'Are you sure you want to Initiate transaction for customer?',
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
                                    icon: 'success',
                                }).then((result) => {

                                });
                            },
                            error: function(xhr) {
                                Swal.fire('Error!', xhr.responseJSON.error || xhr
                                    .responseJSON.message, 'error');
                            }
                        });
                    }
                });
                
            }
        });
    });
</script>
@endpush