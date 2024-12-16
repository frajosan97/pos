<div class="btn-group ms-2 d-flex w-100 justify-content-end text-nowrap" role="group">
    <!-- Edit Button -->
    <button type="button"
        class="btn btn-outline-success edit-ward"
        data-county-id="{{ $ward->county_id }}"
        data-constituency-id="{{ $ward->constituency_id }}"
        data-ward-id="{{ $ward->id }}"
        data-ward-name="{{ $ward->name }}"
        aria-label="Edit ward">
        <i class="fas fa-pencil-alt"></i>
        <span class="d-none d-sm-inline-block">Edit</span>
    </button>

    <!-- Delete Button -->
    <button type="button"
        class="btn btn-outline-danger delete-ward"
        data-ward-id="{{ $ward->id }}"
        aria-label="Delete ward">
        <i class="fas fa-trash-alt"></i>
        <span class="d-none d-sm-inline-block">Delete</span>
    </button>
</div>