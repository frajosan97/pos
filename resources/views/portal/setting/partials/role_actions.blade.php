<div class="btn-group ms-2 d-flex w-100 justify-content-end text-nowrap" role="group">
    @if(auth()->user()->hasPermission('role_view'))
    <a href="{{ route('setting.role.show',$role->id) }}"
        class="btn btn-outline-primary manage-role">
        <i class="fas fa-cog"></i>
        <span class="d-none d-sm-inline-block">Manage</span>
    </a>
    @endif

    @if(auth()->user()->hasPermission('role_edit'))
    <!-- Edit Button -->
    <button type="button"
        class="btn btn-outline-success edit-role"
        data-role-id="{{ $role->id }}"
        data-role-name="{{ $role->name }}"
        data-role-description="{{ $role->description }}"
        aria-label="Edit role">
        <i class="fas fa-pencil-alt"></i>
        <span class="d-none d-sm-inline-block">Edit</span>
    </button>
    @endif

    @if(auth()->user()->hasPermission('role_delete'))
    <!-- Delete Button -->
    <button type="button"
        class="btn btn-outline-danger delete-role"
        data-role-id="{{ $role->id }}"
        data-role-name="{{ $role->name }}"
        aria-label="Delete role">
        <i class="fas fa-trash-alt"></i>
        <span class="d-none d-sm-inline-block">Delete</span>
    </button>
    @endif
</div>