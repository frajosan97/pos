@if(auth()->user()->hasPermission('manager_self'))
<li class="nav-item dropdown d-flex align-items-center mx-2">
    <a href="#" class="nav-link p-0 dropdown-toggle" id="employeeDropdown" role="button" data-bs-toggle="dropdown">
        <i class="fas fa-filter"></i> <span class="ms-1">Employee</span>
    </a>
    <ul class="dropdown-menu dropdown-scroll">
        <li>
            <a class="dropdown-item filter-employee" href="#" data-value="{{ auth()->user()->id }}">
                {{ ucwords(auth()->user()->name) }}
            </a>
        </li>
    </ul>
</li>
@endif

@if(auth()->user()->hasPermission('manager_branch'))
<li class="nav-item dropdown d-flex align-items-center mx-2">
    <a href="#" class="nav-link p-0 dropdown-toggle" id="branchDropdown" role="button" data-bs-toggle="dropdown">
        <i class="fas fa-filter"></i> <span class="ms-1">Branch</span>
    </a>
    <ul class="dropdown-menu dropdown-scroll">
        @foreach ($branches as $key => $value)
        @if(in_array($value->id, auth()->user()->selectedBranches()->pluck('id')->toArray()))
        <li><a class="dropdown-item filter-branch" href="#" data-value="{{ $value->id }}">{{ ucwords($value->name) }}</a></li>
        @endif
        @endforeach
    </ul>
</li>
@endif

@if(auth()->user()->hasPermission('manager_general'))
<li class="nav-item dropdown d-flex align-items-center mx-2">
    <a href="#" class="nav-link p-0 dropdown-toggle" id="allEmployeesDropdown" role="button" data-bs-toggle="dropdown">
        <i class="fas fa-filter"></i> <span class="ms-1">All Employees</span>
    </a>
    <ul class="dropdown-menu dropdown-scroll">
        <li><a class="dropdown-item filter-employee" href="#" data-value="">All Employees</a></li>
        @foreach ($employees as $key => $value)
        <li><a class="dropdown-item filter-employee" href="#" data-value="{{ $value->id }}">{{ ucwords($value->name) }}</a></li>
        @endforeach
    </ul>
</li>

<li class="nav-item dropdown d-flex align-items-center mx-2">
    <a href="#" class="nav-link p-0 dropdown-toggle" id="allBranchesDropdown" role="button" data-bs-toggle="dropdown">
        <i class="fas fa-filter"></i> <span class="ms-1">All Branches</span>
    </a>
    <ul class="dropdown-menu dropdown-scroll">
        <li><a class="dropdown-item filter-branch" href="#" data-value="">All Branches</a></li>
        @foreach ($branches as $key => $value)
        <li><a class="dropdown-item filter-branch" href="#" data-value="{{ $value->id }}">{{ ucwords($value->name) }}</a></li>
        @endforeach
    </ul>
</li>

<li class="nav-item dropdown d-flex align-items-center mx-2">
    <a href="#" class="nav-link p-0 dropdown-toggle" id="catalogueDropdown" role="button" data-bs-toggle="dropdown">
        <i class="fas fa-filter"></i> <span class="ms-1">Catalogue</span>
    </a>
    <ul class="dropdown-menu dropdown-scroll">
        <li><a class="dropdown-item filter-catalogue" href="#" data-value="">All Brands</a></li>
        @foreach ($catalogue as $key => $value)
        <li><a class="dropdown-item filter-catalogue" href="#" data-value="{{ $value->id }}">{{ ucwords($value->name) }}</a></li>
        @endforeach
    </ul>
</li>

<li class="nav-item dropdown d-flex align-items-center mx-2">
    <a href="#" class="nav-link p-0 dropdown-toggle" id="productDropdown" role="button" data-bs-toggle="dropdown">
        <i class="fas fa-filter"></i> <span class="ms-1">Products</span>
    </a>
    <ul class="dropdown-menu dropdown-scroll">
        <li><a class="dropdown-item filter-product" href="#" data-value="">All Products</a></li>
        @foreach ($products as $key => $value)
        <li><a class="dropdown-item filter-product" href="#" data-value="{{ $value->id }}">{{ ucwords($value->name) }}</a></li>
        @endforeach
    </ul>
</li>
@endif