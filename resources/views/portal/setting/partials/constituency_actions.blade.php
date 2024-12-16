<div class="btn-group ms-2 d-flex w-100 justify-content-end text-nowrap" role="group">
    <!-- Edit Button -->
    <button type="button"
        class="btn btn-outline-success edit-constituency"
        data-county-id="{{ $constituency->county_id }}"
        data-constituency-id="{{ $constituency->id }}"
        data-constituency-name="{{ $constituency->name }}"
        aria-label="Edit constituency">
        <i class="fas fa-pencil-alt"></i>
        <span class="d-none d-sm-inline-block">Edit</span>
    </button>

    <!-- Delete Button -->
    <button type="button"
        class="btn btn-outline-danger delete-constituency"
        data-constituency-id="{{ $constituency->id }}"
        aria-label="Delete constituency">
        <i class="fas fa-trash-alt"></i>
        <span class="d-none d-sm-inline-block">Delete</span>
    </button>
</div>