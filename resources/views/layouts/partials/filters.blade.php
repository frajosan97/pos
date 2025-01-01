@if(auth()->user()->hasPermission('manager_self'))
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
@endif

@if(auth()->user()->hasPermission('manager_branch'))
<li class="nav-item dropdown d-flex align-items-center">
    <a href="" class="nav-link p-0"><i class="fas fa-filter"></i></a>
    <select name="branch" id="branch" class="form-control border-0">
        @foreach ($branches as $key => $value)
        @if(in_array($value->id, auth()->user()->selectedBranches()->pluck('id')->toArray()))
        <option value="{{ $value->id }}">
            {{ ucwords($value->name) }}
        </option>
        @endif
        @endforeach
    </select>
</li>
@endif

@if(auth()->user()->hasPermission('manager_general'))
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
<li class="nav-item dropdown d-flex align-items-center">
    <a href="" class="nav-link p-0"><i class="fas fa-filter"></i></a>
    <select name="catalogue" id="catalogue" class="form-control border-0">
        <option value="">All Brands</option>
        @foreach ($catalogue as $key => $value)
        <option value="{{ $value->id }}">{{ ucwords($value->name) }}</option>
        @endforeach
    </select>
</li>
<li class="nav-item dropdown d-flex align-items-center">
    <a href="" class="nav-link p-0"><i class="fas fa-filter"></i></a>
    <select name="product" id="product" class="form-control border-0">
        <option value="">All Product</option>
        @foreach ($products as $key => $value)
        <option value="{{ $value->id }}">{{ ucwords($value->name) }}</option>
        @endforeach
    </select>
</li>
@endif