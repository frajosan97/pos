<div class="btn-group ms-2 d-flex w-100 justify-content-end text-nowrap" role="group">
    @if(auth()->user()->hasPermission('user_view'))
    <!-- Manage Button -->
    <a href="{{ route('employee.show', $user->id) }}"
        class="btn btn-outline-primary"
        aria-label="Manage employee">
        <i class="fas fa-briefcase"></i>
        <span class="d-none d-sm-inline-block">Manage</span>
    </a>
    @endif

    @if(auth()->user()->hasPermission('user_delete'))
    <!-- Delete Button -->
    <button type="button"
        class="btn btn-outline-danger delete-employee"
        data-employee-id="{{ $user->id }}"
        aria-label="Delete employee">
        <i class="fas fa-trash-alt"></i>
        <span class="d-none d-sm-inline-block">Delete</span>
    </button>
    @endif
</div>