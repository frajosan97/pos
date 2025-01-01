@if(auth()->user()->hasPermission('manager_catalogue'))
<li class="nav-item dropdown d-flex align-items-center">
    <a href="" class="nav-link p-0"><i class="fas fa-filter"></i></a>
    <select name="catalogue" id="catalogue" class="form-control border-0">
        <option value="">All Brands</option>
        @foreach ($catalogue as $key => $value)
        @if(in_array($value->id, auth()->user()->selectedCatalogues()->pluck('id')->toArray()))
        <option value="{{ $value->id }}">
            {{ ucwords($value->name) }}
        </option>
        @endif
        @endforeach
    </select>
</li>
@endif

@if(auth()->user()->hasPermission('manager_product'))
<li class="nav-item dropdown d-flex align-items-center">
    <a href="" class="nav-link p-0"><i class="fas fa-filter"></i></a>
    <select name="product" id="product" class="form-control border-0">
        <option value="">All Product</option>
        @foreach ($products as $key => $value)
        @if(in_array($value->id, auth()->user()->selectedProducts()->pluck('id')->toArray()))
        <option value="{{ $value->id }}">
            {{ ucwords($value->name) }}
        </option>
        @endif
        @endforeach
    </select>
</li>
@endif