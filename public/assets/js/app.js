/* =======================================================================
== PAGE CUSTOM JAVASCRIPT
=========================================================================*/

// jQuery validation
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

$('#county').change(function () {
    // Selected County
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

$('#constituency').change(function () {
    // Selected constituency
    var constituency_id = $(this).val();
    // Fetch constituencies
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

$('#ward').change(function () {
    // Selected ward
    var ward_id = $(this).val();
    // Fetch constituencies
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

// Password view
$('#show-password-icon').click(function () {
    var passwordInput = $('#password');
    var icon = $(this); // Corrected line

    if (passwordInput.attr('type') === 'password') {
        passwordInput.attr('type', 'text');
        icon.removeClass('bi-eye').addClass('bi-eye-slash');
    } else {
        passwordInput.attr('type', 'password');
        icon.removeClass('bi-eye-slash').addClass('bi-eye');
    }
});

function getCookie(name) {
    const value = `; ${document.cookie}`;
    const parts = value.split(`; ${name}=`);
    if (parts.length === 2) return parts.pop().split(';').shift();
    return null;
}

function printDiv(divId) {
    var divContents = $('#' + divId).html();
    var originalContents = $('body').html();
    $('body').html(divContents);
    window.print();
    $('body').html(originalContents);
}