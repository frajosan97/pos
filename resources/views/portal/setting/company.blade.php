@extends('layouts.app')

@section('pageTitle', 'Company Information')

@section('content')

<!-- Control Buttons -->
<ul class="nav nav-pills rounded bg-white mb-3">
    <li class="nav-item dropdown">
        <a class="nav-link" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="fas fa-print"></i> Print
        </a>
        <ul class="dropdown-menu rounded-0 border-0 shadow-sm p-0">
            <li>
                <a href="{{ route('company.pdf') }}" target="_blank" class="dropdown-item">
                    <i class="fas fa-file-pdf text-danger"></i> PDF
                </a>
            </li>
        </ul>
    </li>
</ul>
<!-- / End Control Buttons -->

<div class="row">
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-body">
                <form id="company-update-form" action="{{ route('setting.company.update', $company->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <!-- Company Details -->
                    <h5 class="bg-light p-2 mb-3">Company Details</h5>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="name" class="form-label">Company Name</label>
                            <input
                                type="text"
                                name="name"
                                id="name"
                                class="form-control"
                                value="{{ old('name', $company->name) }}"
                                required>
                        </div>
                        <div class="col-md-6">
                            <label for="address" class="form-label">Address</label>
                            <input
                                type="text"
                                name="address"
                                id="address"
                                class="form-control"
                                value="{{ old('address', $company->address) }}"
                                required>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="phone" class="form-label">Phone</label>
                            <input
                                type="text"
                                name="phone"
                                id="phone"
                                class="form-control"
                                value="{{ old('phone', $company->phone) }}"
                                required>
                        </div>
                        <div class="col-md-6">
                            <label for="email" class="form-label">Email</label>
                            <input
                                type="email"
                                name="email"
                                id="email"
                                class="form-control"
                                value="{{ old('email', $company->email) }}"
                                required>
                        </div>
                    </div>

                    <!-- Branding -->
                    <h5 class="bg-light p-2 mb-3">Branding</h5>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="logo" class="form-label">Logo</label>
                            <input type="file" name="logo" id="logo" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label for="color" class="form-label">Theme Color</label>
                            <input
                                type="color"
                                name="color"
                                id="color"
                                class="form-control form-control-color"
                                value="{{ old('color', $company->color) }}">
                        </div>
                    </div>

                    <!-- SMS Settings -->
                    <h5 class="bg-light p-2 mb-3">SMS Settings</h5>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="sms_mode" class="form-label">SMS Mode</label>
                            <select name="sms_mode" id="sms_mode" class="form-select">
                                <option value="online" {{ old('sms_mode', $company->sms_mode) === 'online' ? 'selected' : '' }}>Online</option>
                                <option value="offline" {{ old('sms_mode', $company->sms_mode) === 'offline' ? 'selected' : '' }}>Offline</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="sms_partner_id" class="form-label">SMS Partner ID</label>
                            <input
                                type="text"
                                name="sms_partner_id"
                                id="sms_partner_id"
                                class="form-control"
                                value="{{ old('sms_partner_id', $company->sms_partner_id) }}">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="sms_api_key" class="form-label">SMS API Key</label>
                            <input
                                type="text"
                                name="sms_api_key"
                                id="sms_api_key"
                                class="form-control"
                                value="{{ old('sms_api_key', $company->sms_api_key) }}">
                        </div>
                        <div class="col-md-6">
                            <label for="sms_sender_id" class="form-label">SMS Sender ID</label>
                            <input
                                type="text"
                                name="sms_sender_id"
                                id="sms_sender_id"
                                class="form-control"
                                value="{{ old('sms_sender_id', $company->sms_sender_id) }}">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="sms_api_url" class="form-label">SMS API Url</label>
                            <input
                                type="text"
                                name="sms_api_url"
                                id="sms_api_url"
                                class="form-control"
                                value="{{ old('sms_api_url', $company->sms_api_url) }}">
                        </div>
                    </div>
                    
                    <hr>

                    <!-- Submit Button -->
                    <div class="row">
                        <div class="col-md-12 text-end">
                            <button type="submit" class="btn btn-primary">Update Information</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@push('script')
<script>
    $(document).ready(function() {
        // Handle form submission for both create and edit
        $("#company-update-form").validate({
            submitHandler: function(form, event) {
                event.preventDefault();

                Swal.fire({
                    title: 'Company Update',
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

                        $.ajax({
                            url: $(form).attr('action'),
                            type: $(form).attr('method'),
                            data: new FormData(form),
                            contentType: false,
                            processData: false,
                            success: function(response) {
                                Swal.fire({
                                    title: 'Success',
                                    text: response.success,
                                    icon: 'success',
                                }).then(() => {
                                    window.location.reload();
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
    });
</script>
@endpush