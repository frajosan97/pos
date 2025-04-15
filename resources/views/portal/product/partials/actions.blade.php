<div class="btn-group ms-2 d-flex w-100 justify-content-end text-nowrap" role="group">
    @if(auth()->user()->hasPermission('product_show'))
    <!-- Manage Button -->
    <a href="{{ route('product.show', $product->id) }}"
        class="btn btn-outline-primary"
        aria-label="Manage product">
        <i class="fas fa-briefcase"></i>
        <span class="d-none d-sm-inline-block">Manage</span>
    </a>
    @endif

    @if(auth()->user()->hasPermission('product_destroy'))
    <!-- Delete Button -->
    <button type="button"
        class="btn btn-outline-danger delete-product"
        data-product-id="{{ $product->id }}"
        aria-label="Delete product">
        <i class="fas fa-trash-alt"></i>
        <span class="d-none d-sm-inline-block">Delete</span>
    </button>
    @endif
</div>