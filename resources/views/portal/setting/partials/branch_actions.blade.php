<div class="btn-group ms-2 d-flex w-100 justify-content-end text-nowrap" role="group">
    <!-- Edit Button -->
    <button type="button"
        class="btn btn-outline-success edit-branch"
        data-county-id="{{ $branch->county_id }}"
        data-constituency-id="{{ $branch->constituency_id }}"
        data-ward-id="{{ $branch->ward_id }}"
        data-location-id="{{ $branch->location_id }}"
        data-branch-id="{{ $branch->id }}"
        data-branch-name="{{ $branch->name }}"
        aria-label="Edit branch">
        <i class="fas fa-pencil-alt"></i>
        <span class="d-none d-sm-inline-block">Edit</span>
    </button>

    <!-- Delete Button -->
    <button type="button"
        class="btn btn-outline-danger delete-branch"
        data-branch-id="{{ $branch->id }}"
        aria-label="Delete branch">
        <i class="fas fa-trash-alt"></i>
        <span class="d-none d-sm-inline-block">Delete</span>
    </button>
</div>