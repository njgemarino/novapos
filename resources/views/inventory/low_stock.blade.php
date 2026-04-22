@extends('layouts.app')
@section('title','Low Stock Alerts')
@section('sidebar-nav')@include('inventory._nav')@endsection
@section('content')
<div class="page-header"><h1>Low Stock Alerts</h1><p>Products that need immediate restocking</p></div>

@if($products->isEmpty())
<div class="card">
    <div class="card-body">
        <div class="empty-state" style="padding:48px">
            <i data-lucide="check-circle" style="width:48px;height:48px;opacity:.3;color:var(--green)"></i>
            <p style="margin-top:12px;font-size:.9rem">All products are well stocked! No alerts at this time. 🎉</p>
        </div>
    </div>
</div>
@else
<div class="flash flash-error" style="margin-bottom:16px">
    <i data-lucide="alert-triangle" style="width:15px;height:15px"></i>
    <strong>{{ $products->count() }} product(s)</strong> are at or below their minimum stock threshold.
</div>

<div class="card">
    <div class="table-wrap">
        <table>
            <thead><tr><th>Product</th><th>Category</th><th>Current Stock</th><th>Min Threshold</th><th>Deficit</th><th>Level</th><th>Action</th></tr></thead>
            <tbody>
            @foreach($products as $product)
            @php
                $deficit = $product->low_stock_threshold - $product->stock;
                $pct = $product->low_stock_threshold > 0 ? min(100, round($product->stock / $product->low_stock_threshold * 100)) : 0;
            @endphp
            <tr>
                <td>
                    <div style="display:flex;align-items:center;gap:9px">
                        <span style="font-size:22px">{{ $product->icon ?? '📦' }}</span>
                        <span style="font-weight:700">{{ $product->name }}</span>
                    </div>
                </td>
                <td style="color:var(--text-muted);font-size:.8rem">{{ $product->category?->name ?? '—' }}</td>
                <td>
                    <span style="font-weight:800;font-size:1.05rem;color:{{ $product->stock==0?'var(--red)':'var(--yellow)' }}">{{ $product->stock }}</span>
                    <span style="color:var(--text-muted);font-size:.75rem"> {{ $product->unit }}</span>
                </td>
                <td style="color:var(--text-muted)">{{ $product->low_stock_threshold }} {{ $product->unit }}</td>
                <td>
                    @if($deficit > 0)
                    <span class="badge badge-red">Need {{ $deficit }} more</span>
                    @else
                    <span class="badge badge-yellow">At threshold</span>
                    @endif
                </td>
                <td style="min-width:100px">
                    <div class="progress-bar">
                        <div class="progress-fill {{ $product->stock==0?'red':'yellow' }}" style="width:{{ $pct }}%"></div>
                    </div>
                    <div style="font-size:.66rem;color:var(--text-muted);margin-top:2px">{{ $pct }}% of min</div>
                </td>
                <td>
                    <button class="btn btn-primary btn-sm" onclick="openModal('restock-{{ $product->id }}')">
                        <i data-lucide="package-plus"></i> Restock
                    </button>
                </td>
            </tr>
            <div class="modal-backdrop" id="restock-{{ $product->id }}">
                <div class="modal" style="max-width:380px">
                    <div class="modal-header">
                        <h3>Restock: {{ $product->name }}</h3>
                        <button class="modal-close" onclick="closeModal('restock-{{ $product->id }}')"><i data-lucide="x" style="width:16px;height:16px"></i></button>
                    </div>
                    <div class="modal-body">
                        <div style="background:var(--yellow-soft);border:1px solid #fde68a;border-radius:8px;padding:10px 13px;font-size:.8rem;color:#92400e;margin-bottom:14px">
                            <strong>Current:</strong> {{ $product->stock }} {{ $product->unit }} &nbsp;|&nbsp; <strong>Need:</strong> at least {{ max(0,$deficit) }} more
                        </div>
                        <form method="POST" action="{{ route('inventory.products.adjust',$product) }}">
                            @csrf
                            <input type="hidden" name="type" value="in">
                            <div class="form-group"><label>Quantity to Add *</label><input type="number" name="quantity" class="form-control" min="1" value="{{ max($deficit,1) }}" required></div>
                            <div class="form-group"><label>Notes</label><input type="text" name="notes" class="form-control" placeholder="e.g. Delivery from supplier"></div>
                            <div style="display:flex;gap:8px">
                                <button type="submit" class="btn btn-primary"><i data-lucide="package-plus"></i> Add Stock</button>
                                <button type="button" class="btn btn-secondary" onclick="closeModal('restock-{{ $product->id }}')">Cancel</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif
@endsection
