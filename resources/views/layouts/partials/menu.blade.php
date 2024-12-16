<li class="nav-item">
    <a href="/" class="nav-link">
        <i class="nav-icon fas fa-tag"></i>
        <p>Sell</p>
    </a>
</li>

<li class="nav-item">
    <a href="{{ route('analytics.index') }}" class="nav-link">
        <i class="nav-icon fas fa-chart-bar"></i>
        <p>Analytics</p>
    </a>
</li>

<li class="nav-item">
    <a class="nav-link" href="javascript:void(0)">
        <i class="nav-icon fas fa-boxes"></i>
        <p>Products<i class="right fas fa-angle-left"></i></p>
    </a>
    <ul class="nav nav-treeview" style="margin-left: 15px;">
        <li class="nav-item">
            <a href="{{ route('catalogue.index') }}" class="nav-link">
                <i class="nav-icon fas fa-tags"></i>
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

<li class="nav-item">
    <a class="nav-link" href="javascript:void(0)">
        <i class="nav-icon fas fa-folder-open"></i>
        <p>Reports<i class="right fas fa-angle-left"></i></p>
    </a>
    <ul class="nav nav-treeview" style="margin-left: 15px;">
        <li class="nav-item">
            <a href="{{ route('product.index') }}" class="nav-link">
                <i class="nav-icon fas fa-warehouse"></i>
                <p>Inventory</p>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('sale.index') }}" class="nav-link">
                <i class="nav-icon fas fa-shopping-cart"></i>
                <p>Sales</p>
            </a>
        </li>
        <li class="nav-item">
            <a href="#" class="nav-link">
                <i class="nav-icon fas fa-wallet"></i>
                <p>Payments</p>
            </a>
        </li>
    </ul>
</li>

<li class="nav-item">
    <a class="nav-link" href="javascript:void(0)">
        <i class="nav-icon fas fa-user-tie"></i>
        <p>Employees<i class="right fas fa-angle-left"></i></p>
    </a>
    <ul class="nav nav-treeview" style="margin-left: 15px;">
        <li class="nav-item">
            <a href="{{ route('employee.index') }}" class="nav-link">
                <i class="nav-icon fas fa-users"></i>
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
    <a class="nav-link" href="javascript:void(0)">
        <i class="nav-icon fas fa-user-tie"></i>
        <p>Customers<i class="right fas fa-angle-left"></i></p>
    </a>
    <ul class="nav nav-treeview" style="margin-left: 15px;">
        <li class="nav-item">
            <a href="{{ route('customer.index') }}" class="nav-link">
                <i class="nav-icon fas fa-users"></i>
                <p>Customers</p>
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

<!-- MPESA INTERGRATION FOR SYSTEM -->
<li class="nav-item">
    <a class="nav-link" href="javascript:void(0)">
        <i class="nav-icon fas fa-wallet"></i>
        <p>MPESA API<i class="right fas fa-angle-left"></i></p>
    </a>
    <ul class="nav nav-treeview" style="margin-left: 15px;">
        <li class="nav-item">
            <a href="{{ route('mpesa.simulate.form') }}" class="nav-link">
                <i class="nav-icon fas fa-wallet"></i>
                <p>C2B Simulation</p>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('mpesa.stkpush.form') }}" class="nav-link">
                <i class="nav-icon fas fa-wallet"></i>
                <p>STK Push</p>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('mpesa.b2c.form') }}" class="nav-link">
                <i class="nav-icon fas fa-wallet"></i>
                <p>B2C Simulation</p>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('mpesa.account-balance.form') }}" class="nav-link">
                <i class="nav-icon fas fa-wallet"></i>
                <p>Acc Balance</p>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('mpesa.transaction-status.form') }}" class="nav-link">
                <i class="nav-icon fas fa-wallet"></i>
                <p>Trans Status</p>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('mpesa.reverse.form') }}" class="nav-link">
                <i class="nav-icon fas fa-wallet"></i>
                <p>Reversal</p>
            </a>
        </li>
    </ul>
</li>

@if (Auth::user()->userRoleInfo()->role > 2)
<li class="nav-item">
    <a class="nav-link" href="javascript:void(0)">
        <i class="nav-icon fas fa-cogs"></i>
        <p>Settings<i class="right fas fa-angle-left"></i></p>
    </a>
    <ul class="nav nav-treeview" style="margin-left: 15px;">
        <li class="nav-item">
            <a href="{{ route('setting.county') }}" class="nav-link">
                <i class="nav-icon fas fa-map"></i>
                <p>Counties</p>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('setting.constituency') }}" class="nav-link">
                <i class="nav-icon fas fa-map-marked"></i>
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
                <i class="nav-icon fas fa-user-cog"></i>
                <p>System Roles</p>
            </a>
        </li>
    </ul>
</li>
@endif

<!-- for ever user -->
<li class="nav-item menu-open">
    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
        @csrf
    </form>
    <a class="nav-link" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
        <i class="nav-icon fas fa-sign-out-alt"></i>
        <p>Logout</p>
    </a>
</li>