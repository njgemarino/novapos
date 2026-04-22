{{-- resources/views/cashier/_nav.blade.php --}}
<div class="nav-section">My Shift</div>
<a href="{{ route('cashier.dashboard') }}"   class="nav-link {{ request()->routeIs('cashier.dashboard') ? 'active' : '' }}"><i data-lucide="layout-dashboard"></i> Dashboard</a>
<a href="{{ route('cashier.pos') }}"         class="nav-link {{ request()->routeIs('cashier.pos') ? 'active' : '' }}"><i data-lucide="shopping-cart"></i> New Sale</a>
<a href="{{ route('cashier.transactions') }}" class="nav-link {{ request()->routeIs('cashier.transactions*') ? 'active' : '' }}"><i data-lucide="receipt"></i> My Transactions</a>
<a href="{{ route('cashier.customers') }}"   class="nav-link {{ request()->routeIs('cashier.customers*') ? 'active' : '' }}"><i data-lucide="users"></i> Customers</a>
