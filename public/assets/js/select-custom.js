$(document).ready(function () {
    // Initialize select2 for multiple selection dropdowns
    $('.select2-multiple').select2({
        placeholder: "Select options",
        allowClear: true
    });

    // Toggle visibility of branch select when manager_branch checkbox is checked
    $('#manager_branch').on('change', function () {
        $('.branch-select').toggle(this.checked);
    });

    // Toggle visibility of catalogue select when manager_catalogue checkbox is checked
    $('#manager_catalogue').on('change', function () {
        $('.catalogue-select').toggle(this.checked);
    });

    // Toggle visibility of product select when manager_product checkbox is checked
    $('#manager_product').on('change', function () {
        $('.product-select').toggle(this.checked);
    });
});