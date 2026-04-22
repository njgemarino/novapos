{{-- resources/views/manager/_nav.blade.php --}}
<div class="nav-section">Operations</div>
<a href="{{ route('manager.dashboard') }}" class="nav-link {{ request()->routeIs('manager.dashboard') ? 'active' : '' }}"><i data-lucide="layout-dashboard"></i> Dashboard</a>
<a href="{{ route('manager.sales') }}"     class="nav-link {{ request()->routeIs('manager.sales') ? 'active' : '' }}"><i data-lucide="banknote"></i> Sales</a>
<a href="{{ route('manager.inventory') }}" class="nav-link {{ request()->routeIs('manager.inventory') ? 'active' : '' }}"><i data-lucide="package"></i> Inventory</a>
<a href="{{ route('manager.customers') }}" class="nav-link {{ request()->routeIs('manager.customers') ? 'active' : '' }}"><i data-lucide="users"></i> Customers</a>
<a href="{{ route('manager.reports') }}"   class="nav-link {{ request()->routeIs('manager.reports') ? 'active' : '' }}"><i data-lucide="bar-chart-2"></i> Reports</a>
