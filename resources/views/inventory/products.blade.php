@extends('layouts.app')
@section('title','Products')
@section('sidebar-nav')@include('inventory._nav')@endsection
@section('content')
<div class="page-header"><h1>Products</h1><p>Manage stock levels</p></div>

<div class="card">
    <div class="card-header">
        <form method="GET" style="display:flex;gap:8px;flex-wrap:wrap;margin:0">
            <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Search name or barcode…" style="width:200px">
            <select name="category" class="form-control" style="width:auto">
                <option value="">All Categories</option>
                @foreach($categories as $cat)
                <option value="{{ $cat->id }}" {{ request('category')==$cat->id?'selected':'' }}>{{ $cat->icon }} {{ $cat->name }}</option>
                @endforeach
            </select>
            <div class="filter-bar" style="margin:0">
                <button type="submit" name="filter" value="" class="filter-btn {{ !request('filter') ? 'active' : '' }}">All</button>
                <button type="submit" name="filter" value="low" class="filter-btn {{ request('filter')==='low' ? 'active' : '' }}">Low Stock</button>
                <button type="submit" name="filter" value="out" class="filter-btn {{ request('filter')==='out' ? 'active' : '' }}">Out of Stock</button>
            </div>
        </form>
    </div>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Product</th><th>Category</th><th>Price</th><th>Stock</th><th>Threshold</th><th>Stock Bar</th><th>Actions</th></tr></thead>
            <tbody>
            @forelse($products as $product)
            @php $pct = $product->low_stock_threshold > 0 ? min(100, round($product->stock / max($product->low_stock_threshold * 2, 1) * 100)) : 100; @endphp
            <tr>
                <td>
                    <div style="display:flex;align-items:center;gap:9px">
                        <span style="font-size:20px">{{ $product->icon ?? '📦' }}</span>
                        <div>
                            <div style="font-weight:600;font-size:.82rem">{{ $product->name }}</div>
                            @if($product->barcode)<div style="font-size:.68rem;color:var(--text-muted);font-family:monospace">{{ $product->barcode }}</div>@endif
                        </div>
                    </div>
                </td>
                <td style="color:var(--text-muted);font-size:.8rem">{{ $product->category?->name ?? '—' }}</td>
                <td style="font-weight:600;color:var(--accent)">₱{{ number_format($product->price,2) }}</td>
                <td>
                    <span style="font-weight:700;color:{{ $product->stock==0?'var(--red)':($product->isLowStock()?'var(--yellow)':'var(--green)') }}">{{ $product->stock }}</span>
                    <span style="color:var(--text-muted);font-size:.72rem"> {{ $product->unit }}</span>
                </td>
                <td style="color:var(--text-muted)">{{ $product->low_stock_threshold }}</td>
                <td style="min-width:90px">
                    <div class="progress-bar">
                        <div class="progress-fill {{ $pct < 30 ? 'red' : ($pct < 60 ? 'yellow' : 'green') }}" style="width:{{ $pct }}%"></div>
                    </div>
                </td>
                <td>
                    <button class="btn btn-primary btn-xs" onclick="openModal('adj-{{ $product->id }}')">
                        <i data-lucide="package-plus"></i> Adjust
                    </button>
                </td>
            </tr>
            {{-- Adjust Stock Modal --}}
            <div class="modal-backdrop" id="adj-{{ $product->id }}">
                <div class="modal" style="max-width:380px">
                    <div class="modal-header">
                        <h3>Adjust Stock — {{ $product->name }}</h3>
                        <button class="modal-close" onclick="closeModal('adj-{{ $product->id }}')"><i data-lucide="x" style="width:16px;height:16px"></i></button>
                    </div>
                    <div class="modal-body">
                        <div style="display:flex;justify-content:space-between;margin-bottom:14px;font-size:.82rem">
                            <span style="color:var(--text-muted)">Current stock:</span>
                            <strong style="color:{{ $product->isLowStock() ? 'var(--red)' : 'var(--text)' }}">{{ $product->stock }} {{ $product->unit }}</strong>
                        </div>
                        <form method="POST" action="{{ route('inventory.products.adjust',$product) }}">
                            @csrf
                            <div class="form-group">
                                <label>Adjustment Type</label>
                                <select name="type" class="form-control">
                                    <option value="in">Stock In — Add units</option>
                                    <option value="out">Stock Out — Remove units</option>
                                    <option value="adjustment">Set Exact Quantity</option>
                                </select>
                            </div>
                            <div class="form-group"><label>Quantity</label><input type="number" name="quantity" class="form-control" min="1" required placeholder="Enter quantity"></div>
                            <div class="form-group"><label>Notes (optional)</label><input type="text" name="notes" class="form-control" placeholder="Reason (e.g. delivery, damaged)"></div>
                            <div style="display:flex;gap:8px">
                                <button type="submit" class="btn btn-primary"><i data-lucide="save"></i> Save</button>
                                <button type="button" class="btn btn-secondary" onclick="closeModal('adj-{{ $product->id }}')">Cancel</button>
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
