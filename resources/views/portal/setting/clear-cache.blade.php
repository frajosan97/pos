@extends('layouts.app')

@section('pageTitle', 'System Maintenance Actions')

@section('content')

<div class="row">
    <div class="col-md-12 mx-auto">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">System Maintenance Actions</h5>
            </div>
            <div class="card-body">
                <!-- Clear Cache Section -->
                <div class="mb-3">
                    <h6>Clear Cache</h6>
                    <p class="text-muted">
                        This action removes cached data, ensuring the latest changes to your application are reflected immediately.
                    </p>
                    <form id="clear-cache-form" action="{{ route('cache.clear') }}" method="POST">
                        @csrf
                        <button type="button" class="btn btn-warning" onclick="confirmAction('clear-cache-form', 'Clear Cache', 'Are you sure you want to clear the cache? This action cannot be undone.')">
                            <i class="fas fa-broom"></i> Clear Cache
                        </button>
                    </form>
                </div>

                <!-- Optimize System Section -->
                <div class="mb-3">
                    <h6>Optimize System</h6>
                    <p class="text-muted">
                        This action optimizes the system by caching configuration and routes for better performance.
                    </p>
                    <form id="optimize-system-form" action="{{ route('cache.optimize') }}" method="POST">
                        @csrf
                        <button type="button" class="btn btn-danger" onclick="confirmAction('optimize-system-form', 'Optimize System', 'Are you sure you want to optimize the system? This may take a few moments.')">
                            <i class="fas fa-cogs"></i> Optimize System
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('script')
<script>
    function confirmAction(formId, title, message) {
        Swal.fire({
            title: title,
            text: message,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, Proceed',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                submitFormWithAjax(formId);
            }
        });
    }

    function submitFormWithAjax(formId) {
        const form = $(`#${formId}`);
        const actionUrl = form.attr('action');
        const formData = form.serialize();

        Swal.fire({
            title: 'Processing...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        $.ajax({
            url: actionUrl,
            type: 'POST',
            data: formData,
            success: function(response) {
                Swal.fire({
                    title: 'Success!',
                    text: response.message || 'Action completed successfully.',
                    icon: 'success',
                });
            },
            error: function(xhr) {
                const errorMessage = xhr.responseJSON?.error || xhr.responseJSON?.message || 'An unexpected error occurred.';
                Swal.fire('Error!', errorMessage, 'error');
            }
        });
    }

    // Ensure CSRF token is included in every AJAX request
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
</script>
@endpush