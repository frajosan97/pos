@if(auth()->user()->hasPermission('manager_catalogue'))
<li class="nav-item dropdown d-flex align-items-center mx-2">
    <a href="#" class="nav-link p-0 dropdown-toggle" id="catalogueDropdown" role="button" data-bs-toggle="dropdown">
        <i class="fas fa-filter"></i> <span class="ms-1">Catalogue</span>
    </a>
    <ul class="dropdown-menu">
        <li><a class="dropdown-item filter-catalogue" href="#" data-value="">All Brands</a></li>
        @foreach ($catalogue as $key => $value)
        @if(in_array($value->id, auth()->user()->selectedCatalogues()->pluck('id')->toArray()))
        <li><a class="dropdown-item filter-catalogue" href="#" data-value="{{ $value->id }}">{{ ucwords($value->name) }}</a></li>
        @endif
        @endforeach
    </ul>
</li>
@endif

@if(auth()->user()->hasPermission('manager_product'))
<li class="nav-item dropdown d-flex align-items-center mx-2">
    <a href="#" class="nav-link p-0 dropdown-toggle" id="productDropdown" role="button" data-bs-toggle="dropdown">
        <i class="fas fa-filter"></i> <span class="ms-1">Products</span>
    </a>
    <ul class="dropdown-menu">
        <li><a class="dropdown-item filter-product" href="#" data-value="">All Products</a></li>
        @foreach ($products as $key => $value)
        @if(in_array($value->id, auth()->user()->selectedProducts()->pluck('id')->toArray()))
        <li><a class="dropdown-item filter-product" href="#" data-value="{{ $value->id }}">{{ ucwords($value->name) }}</a></li>
        @endif
        @endforeach
    </ul>
</li>
@endif