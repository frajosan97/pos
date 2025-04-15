<!-- POS Menu -->
<li class="nav-item">
    <a href="/" class="nav-link">
        <i class="nav-icon fas fa-cash-register"></i>
        <p>Point of Sale</p>
    </a>
</li>

<!-- Analytics -->
<li class="nav-item">
    <a href="{{ route('analytics.index') }}" class="nav-link">
        <i class="nav-icon fas fa-chart-line"></i>
        <p>Analytics</p>
    </a>
</li>

<!-- Products -->
<li class="nav-item">
    <a class="nav-link" href="javascript:void(0)">
        <i class="nav-icon fas fa-box-open"></i>
        <p>
            Products
            <i class="right fas fa-angle-left"></i>
        </p>
    </a>
    <ul class="nav nav-treeview">
        <li class="nav-item">
            <a href="{{ route('catalogue.index') }}" class="nav-link">
                <i class="nav-icon fas fa-book-open"></i>
                <p>Catalogue</p>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('product.index') }}" class="nav-link">
                <i class="nav-icon fas fa-list"></i>
                <p>Products List</p>
            </a>
        </li>
    </ul>
</li>

<!-- Reports -->
<li class="nav-item">
    <a class="nav-link" href="javascript:void(0)">
        <i class="nav-icon fas fa-book"></i>
        <p>
            Reports
            <i class="right fas fa-angle-left"></i>
        </p>
    </a>
    <ul class="nav nav-treeview">
        <li class="nav-item">
            <a href="{{ route('sale.index') }}" class="nav-link">
                <i class="nav-icon fas fa-chart-bar"></i>
                <p>Sales</p>
            </a>
        </li>

        <li class="nav-item">
            <a href="{{ route('sale.catalogue') }}" class="nav-link">
                <i class="nav-icon fas fa-tags"></i>
                <p>Brand Sales</p>
            </a>
        </li>

        <li class="nav-item">
            <a href="{{ route('sale.product') }}" class="nav-link">
                <i class="nav-icon fas fa-box"></i>
                <p>Product Sales</p>
            </a>
        </li>

        <li class="nav-item">
            <a href="{{ route('payment.index') }}" class="nav-link">
                <i class="nav-icon fas fa-receipt"></i>
                <p>Payments</p>
            </a>
        </li>
    </ul>
</li>

<!-- Employees -->
<li class="nav-item">
    <a class="nav-link" href="javascript:void(0)">
        <i class="nav-icon fas fa-users"></i>
        <p>
            Employees
            <i class="right fas fa-angle-left"></i>
        </p>
    </a>
    <ul class="nav nav-treeview">
        <li class="nav-item">
            <a href="{{ route('employee.index') }}" class="nav-link">
                <i class="nav-icon fas fa-user"></i>
                <p>Employees List</p>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('employee.create') }}" class="nav-link">
                <i class="nav-icon fas fa-user-plus"></i>
                <p>Add Employee</p>
            </a>
        </li>
    </ul>
</li>

<li class="nav-item">
    <a href="{{ route('chat.index') }}" class="nav-link">
        <i class="nav-icon fas fa-sms"></i>
        <p>Chats</p>
    </a>
</li>

<!-- Settings -->
<li class="nav-item">
    <a class="nav-link" href="javascript:void(0)">
        <i class="nav-icon fas fa-cogs"></i>
        <p>
            Settings
            <i class="right fas fa-angle-left"></i>
        </p>
    </a>
    <ul class="nav nav-treeview">
        <li class="nav-item">
            <a href="{{ route('setting.county') }}" class="nav-link">
                <i class="nav-icon fas fa-map"></i>
                <p>Counties</p>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('setting.constituency') }}" class="nav-link">
                <i class="nav-icon fas fa-map-signs"></i>
                <p>Constituencies</p>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('setting.ward') }}" class="nav-link">
                <i class="nav-icon fas fa-map-marker"></i>
                <p>Wards</p>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('setting.location') }}" class="nav-link">
                <i class="nav-icon fas fa-map-pin"></i>
                <p>Locations</p>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('setting.branch') }}" class="nav-link">
                <i class="nav-icon fas fa-store-alt"></i>
                <p>Branches / Shops</p>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('setting.company') }}" class="nav-link">
                <i class="nav-icon fas fa-building"></i>
                <p>Company Information</p>
            </a>
        </li>
    </ul>
</li>

<li class="nav-item">
    <a href="{{ route('clear-cache.form') }}" class="nav-link">
        <i class="nav-icon fas fa-broom"></i>
        <p>Clear Cache</p>
    </a>
</li>

<!-- Logout -->
<li class="nav-item">
    <a class="nav-link" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
        <i class="nav-icon fas fa-sign-out-alt"></i>
        <p>Logout</p>
    </a>
    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
        @csrf
    </form>
</li>