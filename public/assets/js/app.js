/* =======================================================================
== PAGE CUSTOM JAVASCRIPT
=========================================================================*/

// jQuery validation defaults
jQuery.validator.setDefaults({
    errorElement: 'div',
    errorClass: 'invalid-feedback',
    highlight: function (element) {
        $(element).addClass('is-invalid');
    },
    unhighlight: function (element) {
        $(element).removeClass('is-invalid');
    }
});

// Handle County change event
$('#county').change(function () {
    var county_id = $(this).val();

    // Fetch constituencies
    $.ajax({
        url: '/api/fetch-data/constituency/' + county_id,
        method: 'GET',
        success: function (response) {
            $('#constituency').empty().append('<option value="">--Select Constituency--</option>');
            $('#ward').empty().append('<option value="">--Select Ward--</option>');
            $('#location').empty().append('<option value="">--Select Location--</option>');

            $.each(response, function (key, value) {
                $('#constituency').append('<option value="' + value.id + '">' + value.name + '</option>');
            });
        },
        error: function (xhr) {
            Swal.fire('Error!', xhr.responseJSON.error || xhr.responseJSON.message, 'error');
        }
    });
});

// Handle Constituency change event
$('#constituency').change(function () {
    var constituency_id = $(this).val();

    // Fetch wards
    $.ajax({
        url: '/api/fetch-data/ward/' + constituency_id,
        method: 'GET',
        success: function (response) {
            $('#ward').empty().append('<option value="">--Select Ward--</option>');
            $('#location').empty().append('<option value="">--Select Location--</option>');

            $.each(response, function (key, value) {
                $('#ward').append('<option value="' + value.id + '">' + value.name + '</option>');
            });
        },
        error: function (xhr) {
            Swal.fire('Error!', xhr.responseJSON.error || xhr.responseJSON.message, 'error');
        }
    });
});

// Handle Ward change event
$('#ward').change(function () {
    var ward_id = $(this).val();

    // Fetch locations
    $.ajax({
        url: '/api/fetch-data/location/' + ward_id,
        method: 'GET',
        success: function (response) {
            $('#location').empty().append('<option value="">--Select Location--</option>');

            $.each(response, function (key, value) {
                $('#location').append('<option value="' + value.id + '">' + value.name + '</option>');
            });
        },
        error: function (xhr) {
            Swal.fire('Error!', xhr.responseJSON.error || xhr.responseJSON.message, 'error');
        }
    });
});

// Toggle password visibility
$('#show-password-icon').click(function () {
    var passwordInput = $('#password');
    var icon = $(this);

    if (passwordInput.attr('type') === 'password') {
        passwordInput.attr('type', 'text');
        icon.removeClass('fa-eye').addClass('fa-eye-slash');
    } else {
        passwordInput.attr('type', 'password');
        icon.removeClass('fa-eye-slash').addClass('fa-eye');
    }
});

// Get cookie by name
function getCookie(name) {
    const value = `; ${document.cookie}`;
    const parts = value.split(`; ${name}=`);
    if (parts.length === 2) return parts.pop().split(';').shift();
    return null;
}

// Print div content
function printDiv(divId) {
    var divContents = $('#' + divId).html();
    var originalContents = $('body').html();

    $('body').html(divContents);
    window.print();

    $('body').html(originalContents);
}
