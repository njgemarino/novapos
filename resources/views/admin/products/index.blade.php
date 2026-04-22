@extends('layouts.app')
@section('title','Products')
@section('sidebar-nav')@include('admin._nav')@endsection

@section('content')
<div class="page-header" style="display:flex;justify-content:space-between;align-items:flex-start">
    <div><h1>Products</h1><p>Manage your product catalog</p></div>
    <a href="{{ route('admin.products.create') }}" class="btn btn-primary"><i data-lucide="plus"></i> Add Product</a>
</div>

@if($lowStock > 0)
<div class="flash flash-error" style="margin-bottom:16px">
    <i data-lucide="alert-triangle" style="width:15px;height:15px"></i>
    <strong>{{ $lowStock }} product(s)</strong> are at or below their low stock threshold.
    <a href="{{ route('inventory.low_stock') }}" style="color:var(--red);font-weight:600;margin-left:6px">View →</a>
</div>
@endif

<div class="card">
    <div class="card-header">
        <form method="GET" style="display:flex;gap:8px;flex-wrap:wrap;margin:0">
            <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Search products…" style="width:200px">
            <select name="category" class="form-control" style="width:auto">
                <option value="">All Categories</option>
                @foreach($categories as $cat)
                <option value="{{ $cat->id }}" {{ request('category')==$cat->id?'selected':'' }}>{{ $cat->icon }} {{ $cat->name }}</option>
                @endforeach
            </select>
            <button class="btn btn-secondary" type="submit"><i data-lucide="search"></i> Filter</button>
        </form>
        <span style="color:var(--text-muted);font-size:.78rem">{{ $products->total() }} products</span>
    </div>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Product</th><th>Category</th><th>Price</th><th>Cost</th><th>Stock</th><th>Status</th><th>Actions</th></tr></thead>
            <tbody>
            @forelse($products as $product)
            <tr>
                <td>
                    <div style="display:flex;align-items:center;gap:10px">
                        <span style="font-size:22px">{{ $product->icon ?? '📦' }}</span>
                        <div>
                            <div style="font-weight:600;font-size:.83rem">{{ $product->name }}</div>
                            @if($product->barcode)<div style="font-size:.7rem;color:var(--text-muted);font-family:monospace">{{ $product->barcode }}</div>@endif
                        </div>
                    </div>
                </td>
                <td style="font-size:.8rem;color:var(--text-muted)">{{ $product->category?->name ?? '—' }}</td>
                <td style="font-weight:700;color:var(--accent)">₱{{ number_format($product->price, 2) }}</td>
                <td style="color:var(--text-muted)">₱{{ number_format($product->cost_price, 2) }}</td>
                <td>
                    <span style="font-weight:700;color:{{ $product->isLowStock() ? 'var(--red)' : 'var(--text)' }}">{{ $product->stock }}</span>
                    <span style="color:var(--text-muted);font-size:.75rem"> {{ $product->unit }}</span>
                    @if($product->isLowStock())<span class="badge badge-red" style="margin-left:4px">LOW</span>@endif
                </td>
                <td>
                    <span class="badge badge-{{ $product->is_active ? 'green' : 'gray' }}">{{ $product->is_active ? 'Active' : 'Inactive' }}</span>
                </td>
                <td>
                    <div style="display:flex;gap:5px">
                        <a href="{{ route('admin.products.edit',$product) }}" class="btn btn-secondary btn-xs"><i data-lucide="pencil"></i></a>
                        <button class="btn btn-success btn-xs" onclick="openModal('stock-{{ $product->id }}')"><i data-lucide="package-plus"></i></button>
                        <form id="del-{{ $product->id }}" method="POST" action="{{ route('admin.products.destroy',$product) }}">@csrf @method('DELETE')</form>
                        <button class="btn btn-danger btn-xs" onclick="confirmDelete('del-{{ $product->id }}')"><i data-lucide="trash-2"></i></button>
                    </div>
                </td>
            </tr>
            {{-- Stock Adjust Modal --}}
            <div class="modal-backdrop" id="stock-{{ $product->id }}">
                <div class="modal" style="max-width:380px">
                    <div class="modal-header">
                        <h3>Adjust Stock — {{ $product->name }}</h3>
                        <button class="modal-close" onclick="closeModal('stock-{{ $product->id }}')"><i data-lucide="x" style="width:16px;height:16px"></i></button>
                    </div>
                    <div class="modal-body">
                        <p style="font-size:.8rem;color:var(--text-muted);margin-bottom:14px">Current stock: <strong style="color:var(--text)">{{ $product->stock }} {{ $product->unit }}</strong></p>
                        <form method="POST" action="{{ route('admin.products.stock',$product) }}">
                            @csrf
                            <div class="form-group">
                                <label>Type</label>
                                <select name="type" class="form-control">
                                    <option value="in">Stock In (Add)</option>
                                    <option value="out">Stock Out (Remove)</option>
                                    <option value="adjustment">Set Exact Quantity</option>
                                </select>
                            </div>
                            <div class="form-group"><label>Quantity</label><input type="number" name="quantity" class="form-control" min="1" required></div>
                            <div class="form-group"><label>Notes (optional)</label><input type="text" name="notes" class="form-control" placeholder="Reason for adjustment"></div>
                            <div style="display:flex;gap:8px">
                                <button type="submit" class="btn btn-primary"><i data-lucide="save"></i> Save</button>
                                <button type="button" class="btn btn-secondary" onclick="closeModal('stock-{{ $product->id }}')">Cancel</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            @empty
            <tr><td colspan="7"><div class="empty-state"><i data-lucide="package"></i><p>No products found.</p></div></td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div style="padding:14px 18px">{{ $products->withQueryString()->links('vendor.pagination.simple') }}</div>
</div>
@endsection
