<div class="btn-group ms-2 d-flex w-100 justify-content-end text-nowrap" role="group">
    <!-- Manage Button -->
    <a href="{{ route('sale.show', $sale->id) }}"
        class="btn btn-outline-primary"
        aria-label="Manage Sale">
        <i class="fas fa-briefcase"></i>
        <span class="d-none d-sm-inline-block">Manage</span>
    </a>
    <!-- Invoice -->
    <a href="{{ route('invoice.pdf', $sale->id) }}"
        class="btn btn-outline-primary"
        aria-label="Sale Invoice">
        <i class="fas fa-print"></i>
        <span class="d-none d-sm-inline-block">Invoice</span>
    </a>
</div>