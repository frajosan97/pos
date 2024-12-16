<div class="btn-group ms-2 d-flex w-100 justify-content-end text-nowrap" role="group">
    <!-- Edit Button -->
    <button type="button"
        class="btn btn-outline-success edit-location"
        data-county-id="{{ $location->county_id }}"
        data-constituency-id="{{ $location->constituency_id }}"
        data-ward-id="{{ $location->ward_id }}"
        data-location-id="{{ $location->id }}"
        data-location-name="{{ $location->name }}"
        aria-label="Edit location">
        <i class="fas fa-pencil-alt"></i>
        <span class="d-none d-sm-inline-block">Edit</span>
    </button>

    <!-- Delete Button -->
    <button type="button"
        class="btn btn-outline-danger delete-location"
        data-location-id="{{ $location->id }}"
        aria-label="Delete location">
        <i class="fas fa-trash-alt"></i>
        <span class="d-none d-sm-inline-block">Delete</span>
    </button>
</div>