@extends('layouts.app')
@section('title','Inventory View')
@section('sidebar-nav')@include('manager._nav')@endsection
@section('content')
<div class="page-header">
    <h1>Inventory Overview</h1>
    <p>Read-only inventory status</p>
</div>

@if($lowStock > 0)
<div class="flash flash-error" style="margin-bottom:16px">
    <i data-lucide="alert-triangle" style="width:15px;height:15px"></i>
    <strong>{{ $lowStock }} item(s)</strong> are at or below their low stock threshold and need reordering.
</div>
@endif

<div class="card">
    <div class="table-wrap">
        <table>
            <thead><tr><th>Product</th><th>Category</th><th>Price</th><th>Stock</th><th>Threshold</th><th>Status</th></tr></thead>
            <tbody>
            @forelse($products as $product)
            <tr>
                <td>
                    <div style="display:flex;align-items:center;gap:10px">
                        <span style="font-size:20px">{{ $product->icon ?? '📦' }}</span>
                        <span style="font-weight:600;font-size:.83rem">{{ $product->name }}</span>
                    </div>
                </td>
                <td style="color:var(--text-muted);font-size:.8rem">{{ $product->category?->name ?? '—' }}</td>
                <td style="font-weight:600;color:var(--accent)">₱{{ number_format($product->price,2) }}</td>
                <td>
                    <span style="font-weight:700;color:{{ $product->isLowStock() ? 'var(--red)' : 'var(--green)' }}">{{ $product->stock }}</span>
                    <span style="color:var(--text-muted);font-size:.75rem"> {{ $product->unit }}</span>
                </td>
                <td style="color:var(--text-muted)">{{ $product->low_stock_threshold }}</td>
                <td>
                    @if(!$product->is_active)
                    <span class="badge badge-gray">Inactive</span>
                    @elseif($product->stock == 0)
                    <span class="badge badge-red">Out of Stock</span>
                    @elseif($product->isLowStock())
                    <span class="badge badge-yellow">Low Stock</span>
                    @else
                    <span class="badge badge-green">In Stock</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr><td colspan="6"><div class="empty-state"><i data-lucide="package"></i><p>No products found.</p></div></td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div style="padding:14px 18px">{{ $products->withQueryString()->links('vendor.pagination.simple') }}</div>
</div>
@endsection
