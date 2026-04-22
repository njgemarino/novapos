{{-- resources/views/admin/_nav.blade.php --}}
<div class="nav-section">Operations</div>
<a href="{{ route('admin.dashboard') }}"    class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"><i data-lucide="layout-dashboard"></i> Dashboard</a>
<a href="{{ route('cashier.pos') }}"        class="nav-link {{ request()->routeIs('cashier.pos') ? 'active' : '' }}"><i data-lucide="shopping-cart"></i> New Sale</a>
<a href="{{ route('admin.transactions') }}" class="nav-link {{ request()->routeIs('admin.transactions*') ? 'active' : '' }}"><i data-lucide="receipt"></i> Transactions</a>
<a href="{{ route('admin.customers') }}"    class="nav-link {{ request()->routeIs('admin.customers*') ? 'active' : '' }}"><i data-lucide="users"></i> Customers</a>
<div class="nav-section">Inventory</div>
<a href="{{ route('admin.products') }}"     class="nav-link {{ request()->routeIs('admin.products*') ? 'active' : '' }}"><i data-lucide="package"></i> Products</a>
<div class="nav-section">Management</div>
<a href="{{ route('admin.users') }}"        class="nav-link {{ request()->routeIs('admin.users*') ? 'active' : '' }}"><i data-lucide="user-cog"></i> Users</a>
<a href="{{ route('admin.reports') }}"      class="nav-link {{ request()->routeIs('admin.reports') ? 'active' : '' }}"><i data-lucide="bar-chart-2"></i> Reports</a>
<a href="{{ route('admin.settings') }}"     class="nav-link {{ request()->routeIs('admin.settings') ? 'active' : '' }}"><i data-lucide="settings"></i> Settings</a>
