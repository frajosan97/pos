<div class="btn-group ms-2 d-flex w-100 justify-content-end text-nowrap" role="group">
    <!-- Manage Button -->
    <a href="{{ route('catalogue.show', $catalogue->id) }}"
        class="btn btn-outline-primary"
        aria-label="Manage Catalogue">
        <i class="fas fa-briefcase"></i>
        <span class="d-none d-sm-inline-block">Manage</span>
    </a>

    @if (in_array(Auth::user()->role?->role,[2,3]))
    <!-- Edit Button -->
    <button type="button"
        class="btn btn-outline-success edit-catalogue"
        data-catalogue-id="{{ $catalogue->id }}"
        data-catalogue-name="{{ $catalogue->name }}"
        aria-label="Edit Catalogue">
        <i class="fas fa-pencil-alt"></i>
        <span class="d-none d-sm-inline-block">Edit</span>
    </button>

    <!-- Delete Button -->
    <button type="button"
        class="btn btn-outline-danger delete-catalogue"
        data-catalogue-id="{{ $catalogue->id }}"
        aria-label="Delete Catalogue">
        <i class="fas fa-trash-alt"></i>
        <span class="d-none d-sm-inline-block">Delete</span>
    </button>
    @endif
</div>