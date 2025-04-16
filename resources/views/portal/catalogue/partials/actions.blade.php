<div class="btn-group ms-2 d-flex w-100 justify-content-end text-nowrap" role="group">
    @if(auth()->user()->hasPermission('catalogue_edit'))
    <!-- Edit Button -->
    <button type="button"
        class="btn btn-outline-success edit-catalogue"
        data-id="{{ $catalogue->id }}"
        aria-label="Edit Catalogue">
        <i class="fas fa-pencil-alt"></i>
        <span class="d-none d-sm-inline-block">Edit</span>
    </button>
    @endif

    @if(auth()->user()->hasPermission('catalogue_destroy'))
    <!-- Delete Button -->
    <button type="button"
        class="btn btn-outline-danger delete-catalogue"
        data-id="{{ $catalogue->id }}"
        data-name="{{ $catalogue->name }}"
        aria-label="Delete Catalogue">
        <i class="fas fa-trash-alt"></i>
        <span class="d-none d-sm-inline-block">Delete</span>
    </button>
    @endif
</div>