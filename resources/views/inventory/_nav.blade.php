{{-- resources/views/inventory/_nav.blade.php --}}
<div class="nav-section">Inventory</div>
<a href="{{ route('inventory.dashboard') }}" class="nav-link {{ request()->routeIs('inventory.dashboard') ? 'active' : '' }}"><i data-lucide="layout-dashboard"></i> Dashboard</a>
<a href="{{ route('inventory.products') }}"  class="nav-link {{ request()->routeIs('inventory.products*') ? 'active' : '' }}"><i data-lucide="package"></i> Products</a>
<a href="{{ route('inventory.stock') }}"     class="nav-link {{ request()->routeIs('inventory.stock') ? 'active' : '' }}"><i data-lucide="arrow-left-right"></i> Stock Movement</a>
<a href="{{ route('inventory.low_stock') }}" class="nav-link {{ request()->routeIs('inventory.low_stock') ? 'active' : '' }}">
    <i data-lucide="alert-triangle"></i> Low Stock
    @php $lowCount = \App\Models\Product::whereRaw('stock <= low_stock_threshold')->count(); @endphp
    @if($lowCount > 0)<span class="nav-badge">{{ $lowCount }}</span>@endif
</a>
