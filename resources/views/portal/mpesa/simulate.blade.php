@extends('layouts.app')

@section('pageTitle','C2B Transaction Simulation')

@section('content')

<!-- control buttons -->
<ul class="nav nav-pills rounded bg-white mb-3">
    <li class="nav-item">
        <a href="#" class="nav-link">
            <i class="nav-icon fas fa-mobile-alt"></i> Simulate Transaction
        </a>
    </li>
</ul>
<!-- / end control buttons -->

<div class="row">
    <div class="col-md-12 mx-auto">

        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <form id="stk-push-form" action="{{ route('mpesa.registerUrl') }}" method="post">
                    @csrf
                    <ul class="list-group">
                        <li class="list-group-item p-0 border-0">
                            <button type="submit" class="btn btn-primary">1) Register Url</button>
                        </li>
                    </ul>
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
                    title: 'Register C2B URLs (Confirmation and Validation Urls)',
                    text: 'Are you sure you want to Register C2B URLs (Confirmation and Validation Urls)?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, Register',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: 'Processing...',
                            text: 'Please wait while the transaction is being registered.',
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