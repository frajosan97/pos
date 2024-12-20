<!-- POS Menu -->
<li class="nav-item">
    <a href="/" class="nav-link">
        <i class="nav-icon fas fa-cash-register"></i>
        <p>Point of Sale</p>
    </a>
</li>

<li class="nav-item">
    <a href="{{ route('analytics.index') }}" class="nav-link">
        <i class="nav-icon fas fa-chart-line"></i>
        <p>Analytics</p>
    </a>
</li>

<!-- Products -->
<li class="nav-item">
    <a class="nav-link" href="javascript:void(0)">
        <i class="nav-icon fas fa-box"></i>
        <p>
            Products
            <i class="right fas fa-angle-left"></i>
        </p>
    </a>
    <ul class="nav nav-treeview">
        <li class="nav-item">
            <a href="{{ route('catalogue.index') }}" class="nav-link">
                <i class="nav-icon fas fa-book"></i>
                <p>Catalogue</p>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('product.index') }}" class="nav-link">
                <i class="nav-icon fas fa-th-list"></i>
                <p>Products List</p>
            </a>
        </li>
    </ul>
</li>

<!-- Reports -->
<li class="nav-item">
    <a class="nav-link" href="javascript:void(0)">
        <i class="nav-icon fas fa-file-alt"></i>
        <p>
            Reports
            <i class="right fas fa-angle-left"></i>
        </p>
    </a>
    <ul class="nav nav-treeview">
        <li class="nav-item">
            <a href="{{ route('product.index') }}" class="nav-link">
                <i class="nav-icon fas fa-clipboard-list"></i>
                <p>Inventory</p>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('sale.index') }}" class="nav-link">
                <i class="nav-icon fas fa-shopping-basket"></i>
                <p>Sales</p>
            </a>
        </li>
        <li class="nav-item">
            <a href="#" class="nav-link">
                <i class="nav-icon fas fa-credit-card"></i>
                <p>Payments</p>
            </a>
        </li>
    </ul>
</li>

<!-- Customers -->
<li class="nav-item">
    <a class="nav-link" href="javascript:void(0)">
        <i class="nav-icon fas fa-users"></i>
        <p>
            Customers
            <i class="right fas fa-angle-left"></i>
        </p>
    </a>
    <ul class="nav nav-treeview">
        <li class="nav-item">
            <a href="{{ route('customer.index') }}" class="nav-link">
                <i class="nav-icon fas fa-address-book"></i>
                <p>Customer List</p>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('customer.create') }}" class="nav-link">
                <i class="nav-icon fas fa-user-plus"></i>
                <p>Add Customer</p>
            </a>
        </li>
    </ul>
</li>

<!-- Employees -->
<li class="nav-item">
    <a class="nav-link" href="javascript:void(0)">
        <i class="nav-icon fas fa-users-cog"></i>
        <p>
            Employees
            <i class="right fas fa-angle-left"></i>
        </p>
    </a>
    <ul class="nav nav-treeview">
        <li class="nav-item">
            <a href="{{ route('employee.index') }}" class="nav-link">
                <i class="nav-icon fas fa-user-friends"></i>
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

<!-- M-Pesa API -->
<li class="nav-item">
    <a class="nav-link" href="javascript:void(0)">
        <i class="nav-icon fas fa-wallet"></i>
        <p>
            M-Pesa API
            <i class="right fas fa-angle-left"></i>
        </p>
    </a>
    <ul class="nav nav-treeview">
        <li class="nav-item">
            <a href="{{ route('mpesa.simulate.form') }}" class="nav-link">
                <i class="nav-icon fas fa-mobile-alt"></i>
                <p>C2B Simulation</p>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('mpesa.stkpush.form') }}" class="nav-link">
                <i class="nav-icon fas fa-receipt"></i>
                <p>STK Push</p>
            </a>
        </li>
    </ul>
</li>

@if (Auth::user()->getRoleInfo()->role > 2)
<!-- Settings -->
<li class="nav-item">
    <a class="nav-link" href="javascript:void(0)">
        <i class="nav-icon fas fa-tools"></i>
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
                <i class="nav-icon fas fa-map-marker-alt"></i>
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
                <i class="nav-icon fas fa-store"></i>
                <p>Branches / Shops</p>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('setting.role') }}" class="nav-link">
                <i class="nav-icon fas fa-user-shield"></i>
                <p>System Roles</p>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('setting.company') }}" class="nav-link">
                <i class="nav-icon fas fa-university"></i>
                <p>Company Information</p>
            </a>
        </li>
    </ul>
</li>
@endif

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