<div class="btn-group ms-2 d-flex w-100 justify-content-end text-nowrap" role="group">
    <!-- Edit Button -->
    <button type="button"
        class="btn btn-outline-success edit-role"
        data-role-id="{{ $role->id }}"
        data-role-role="{{ $role->role }}"
        data-role-name="{{ $role->name }}"
        data-role-description="{{ $role->description }}"
        aria-label="Edit role">
        <i class="fas fa-pencil-alt"></i>
        <span class="d-none d-sm-inline-block">Edit</span>
    </button>

    <!-- Delete Button -->
    <button type="button"
        class="btn btn-outline-danger delete-role"
        data-role-id="{{ $role->id }}"
        aria-label="Delete role">
        <i class="fas fa-trash-alt"></i>
        <span class="d-none d-sm-inline-block">Delete</span>
    </button>
</div>