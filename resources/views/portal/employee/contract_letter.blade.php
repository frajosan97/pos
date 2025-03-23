@extends('layouts.app')

@section('pageTitle', 'Employee Contract Letter')

@section('content')

<div class="row">

    @if($employee->hasCompletedKYC())

    @if(empty($employee->signature))
    <div class="col-md-9 mx-auto">
        <div class="card shadow-none rounded-0 border">
            <div class="card-body">
                <div class="contract-letter">
                    @include('layouts.partials.contract_letter')
                    <div class="page">
                        <!-- Signatories pad -->
                        <form action="{{ route('save_signature.save', $employee->id) }}" id="signature-form" method="POST">
                            @csrf
                            <table class="table table-borderless w-100 mt-3 text-center">
                                <tr>
                                    <th class="border-bottom border-dark"></th>
                                    <th class="px-3"></th>
                                    <th class="border-bottom border-dark"></th>
                                </tr>
                                <tr>
                                    <th>SIGNATURE</th>
                                    <th></th>
                                    <th>CARDINAL EMPIRE LIMITED</th>
                                </tr>
                                <tr>
                                    <th class="p-0" style="width: 30%;">
                                        <canvas id="signature-pad" style="border: 1px solid black; width: 100%; height: 80px;"></canvas>
                                        <input type="hidden" name="signature" id="signature-input">
                                    </th>
                                    <th style="width: 10%;"></th>
                                    <th class="p-0 pb-3" style="width: 30%;">
                                        <input type="text" name="" value="{{ $employee->name }}" readonly style="border: 1px solid black; width: 100%; height: 80px;" />
                                    </th>
                                </tr>
                                <tr>
                                    <th>SIGNATURE</th>
                                    <th></th>
                                    <th>EMPLOYEE NAME</th>
                                </tr>
                            </table>
                            <hr>
                            <div class="d-flex justify-content-between align-items-center">
                                <button type="button" class="btn btn-outline-danger" id="clear-button">Clear Signature</button>
                                <button type="submit" class="btn btn-outline-success">Submit Signatory</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @else

    <div class="col-md-12">
        <div class="card shadow-sm border-0">
            <div class="card-header">
                Contract Letter
            </div>
            <div class="card-body">
                <a href="{{ route('contract_letter.pdf', $employee->id) }}" target="_blank">
                    <i class="fas fa-arrow-circle-right"></i>
                    View Contract Letter
                </a>
            </div>
        </div>
    </div>

    @endif

    @else

    <div class="col-md-12">
        <div class="card shadow-sm border-0">
            <div class="card-header">
                Contract Letter
            </div>
            <div class="card-body">
                User has missing or unapproved KYC documents.
            </div>
        </div>
    </div>

    @endif
</div>

@endsection

@push('style')
<link rel="stylesheet" href="{{ asset('assets/css/contract_letter.css') }}">
@endpush

@push('script')
<script>
    $(document).ready(function() {
        // Initialize Signature Pad
        const canvas = document.getElementById('signature-pad');
        const signaturePad = new SignaturePad(canvas);

        // Clear signature
        $('#clear-button').click(function() {
            signaturePad.clear();
        });

        // Handle form submission for both create and edit
        $("#signature-form").validate({
            rules: {
                signature: {
                    required: true,
                },
            },
            messages: {
                signature: {
                    required: "Please provide a signature.",
                },
            },
            submitHandler: function(form, event) {
                event.preventDefault();

                // Check if the signature is empty
                if (signaturePad.isEmpty()) {
                    Swal.fire('Error!', 'Please provide a signature first.', 'error');
                    return;
                }

                // Save the signature as a base64 image
                const signatureInput = document.getElementById('signature-input');
                signatureInput.value = signaturePad.toDataURL();

                Swal.fire({
                    title: 'Catalogue Action',
                    text: 'Are you sure you want to proceed?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, Proceed',
                    cancelButtonText: 'Cancel'
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

                        // Submit the form via AJAX
                        $.ajax({
                            url: $(form).attr('action'),
                            type: $(form).attr('method'),
                            data: new FormData(form),
                            contentType: false,
                            processData: false,
                            success: function(response) {
                                Swal.fire({
                                    title: 'Success',
                                    text: response.message || 'Action completed successfully.',
                                    icon: 'success',
                                }).then((result) => {
                                    window.location.reload();
                                });
                            },
                            error: function(xhr) {
                                Swal.fire('Error!', xhr.responseJSON.error || xhr.responseJSON.message || 'An error occurred.', 'error');
                            }
                        });
                    }
                });
            }
        });
    });
</script>
@endpush