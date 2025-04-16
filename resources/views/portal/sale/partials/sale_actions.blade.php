<div class="btn-group d-flex w-100 justify-content-end text-nowrap" role="group">
    <!-- Receipt Button -->
    <a href="{{ route('sale.show', $sale->id) }}"
        class="btn btn-sm btn-outline-primary"
        aria-label="Receipt Sale">
        <i class="fas fa-receipt"></i>
        <span class="d-none d-sm-inline-block">Receipt</span>
    </a>
</div>