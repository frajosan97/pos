@extends('layouts.app')

@section('content')

<h2><span>Account Activation</span></h2>

@include('auth.partials.alerts')

<form method="POST" action="{{ route('account.activate', $email) }}" class="custom-form" autocomplete="off" id="activationForm">
    <!-- Csrf token -->
    @csrf

    <!-- <p>
        To continue with account activation, Kindly enter the password sent to your email and enter a new password to secure your account!
    </p> -->

    <!-- Error messages -->
    <div id="errorMessages" class="alert alert-danger" style="display: none;"></div>

    <!-- old password input group -->
    <div class="input-group">
        <div class="input-group-prepend">
            <i class="fas fa-lock"></i>
        </div>
        <div class="floating-label-container">
            <input type="password" name="old_password" id="old_password" placeholder="" required />
            <label for="old_password">Old password*</label>
        </div>
        <div class="input-group-prepend">
            <i class="fas fa-eye" id="show-old-password-icon"></i>
        </div>
    </div>

    <!-- new password input group -->
    <div class="input-group">
        <div class="input-group-prepend">
            <i class="fas fa-lock"></i>
        </div>
        <div class="floating-label-container">
            <input type="password" name="password" id="password" placeholder="" required />
            <label for="password">New password*</label>
        </div>
        <div class="input-group-prepend">
            <i class="fas fa-eye" id="show-password-icon"></i>
        </div>
    </div>

    <small id="passwordHelp" class="form-text text-muted">
        Password must be at least 8 characters long and include an uppercase letter, a lowercase letter, a number, and a special character.
    </small>

    <!-- password confirmation input group -->
    <div class="input-group">
        <div class="input-group-prepend">
            <i class="fas fa-lock"></i>
        </div>
        <div class="floating-label-container">
            <input type="password" name="password_confirmation" id="password_confirmation" placeholder="" required />
            <label for="password_confirmation">Confirm new password*</label>
        </div>
    </div>

    <button type="submit" class="btn btn-filled rounded-4 w-100">
        Submit & Activate
    </button>
</form>

@endsection

@push('script')
<script>
    $(document).ready(function() {
        $('#activationForm').on('submit', function(e) {
            const password = $('#password').val();
            const confirmPassword = $('#password_confirmation').val();

            // Clear previous errors
            const errorMessages = $('#errorMessages');
            errorMessages.html('').hide();

            // Individual password validation rules
            const errors = [];
            if (password.length < 8) {
                errors.push('Password must be at least 8 characters long.');
            }
            if (!/[A-Z]/.test(password)) {
                errors.push('Password must contain at least one uppercase letter.');
            }
            if (!/[a-z]/.test(password)) {
                errors.push('Password must contain at least one lowercase letter.');
            }
            if (!/\d/.test(password)) {
                errors.push('Password must contain at least one number.');
            }
            if (!/[@$!%*?&]/.test(password)) {
                errors.push('Password must contain at least one special character (@, $, !, %, *, ?, &).');
            }
            if (password !== confirmPassword) {
                errors.push('Passwords do not match. Please confirm your new password.');
            }

            // Display errors or submit the form
            if (errors.length > 0) {
                e.preventDefault();
                errorMessages.html(errors.map(error => `<p class="m-0">${error}</p>`).join('')).show();
                return false;
            }
        });

        // Toggle password visibility
        $('#show-old-password-icon').on('click', function() {
            togglePasswordVisibility('#old_password');
        });

        $('#show-password-icon').on('click', function() {
            togglePasswordVisibility('#password');
        });

        function togglePasswordVisibility(inputId) {
            const input = $(inputId);
            const type = input.attr('type') === 'password' ? 'text' : 'password';
            input.attr('type', type);
        }
    });
</script>
@endpush