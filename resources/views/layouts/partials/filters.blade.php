@if(auth()->user()->hasPermission('data_manager_self'))
<!-- The person will manage self data [Employee] -->
<!-- employee -->
<li class="nav-item dropdown d-flex align-items-center">
    <a href="" class="nav-link p-0"><i class="fas fa-filter"></i></a>
    <select name="employee" id="employee" class="form-control border-0">
        @foreach ($employees as $key => $value)
        <option value="{{ $value->id }}"
            {{ ($value->id == auth()->user()->id) ? 'selected' : '' }}>
            {{ ucwords($value->name) }}
        </option>
        @endforeach
    </select>
</li>
<!-- Branch -->
<input type="hidden" name="branch" id="branch" value="">
@endif

@if(auth()->user()->hasPermission('data_manager_branch'))
<!-- The person will manage employeed data in the branch and branch analytics [Branch Managers] -->
<!-- employee -->
<li class="nav-item dropdown d-flex align-items-center">
    <a href="" class="nav-link p-0"><i class="fas fa-filter"></i></a>
    <select name="employee" id="employee" class="form-control border-0">
        <option value="">All Employees</option>
        @foreach ($employees as $key => $value)
        <option value="{{ $value->id }}">
            {{ ucwords($value->name) }}
        </option>
        @endforeach
    </select>
</li>
<!-- branch -->
<li class="nav-item dropdown d-flex align-items-center">
    <a href="" class="nav-link p-0"><i class="fas fa-filter"></i></a>
    <select name="branch" id="branch" class="form-control border-0">
        @foreach ($branches as $key => $value)
        <option value="{{ $value->id }}"
            {{ ($value->id == auth()->user()->branch?->id) ? 'selected' : '' }}>
            {{ ucwords($value->name) }}
        </option>
        @endforeach
    </select>
</li>
@endif

@if(auth()->user()->hasPermission('data_manager_general'))
<!-- The person will manage company / all branches data [General Manager] -->
<!-- employee -->
<li class="nav-item dropdown d-flex align-items-center">
    <a href="" class="nav-link p-0"><i class="fas fa-filter"></i></a>
    <select name="employee" id="employee" class="form-control border-0">
        <option value="">All Employees</option>
        @foreach ($employees as $key => $value)
        <option value="{{ $value->id }}">
            {{ ucwords($value->name) }}
        </option>
        @endforeach
    </select>
</li>
<!-- branch -->
<li class="nav-item dropdown d-flex align-items-center">
    <a href="" class="nav-link p-0"><i class="fas fa-filter"></i></a>
    <select name="branch" id="branch" class="form-control border-0">
        <option value="">All Branches</option>
        @foreach ($branches as $key => $value)
        <option value="{{ $value->id }}">
            {{ ucwords($value->name) }}
        </option>
        @endforeach
    </select>
</li>
@endif

<!-- Brand -->
<li class="nav-item dropdown d-flex align-items-center">
    <a href="" class="nav-link p-0"><i class="fas fa-filter"></i></a>
    <select name="catalogue" id="catalogue" class="form-control border-0">
        <option value="">All Brands</option>
        @foreach ($catalogue as $key => $value)
        <option value="{{ $value->id }}">{{ ucwords($value->name) }}</option>
        @endforeach
    </select>
</li>