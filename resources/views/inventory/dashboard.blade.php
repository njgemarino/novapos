@extends('layouts.app')
@section('title','Inventory Dashboard')
@section('sidebar-nav')@include('inventory._nav')@endsection

@section('content')
<div class="page-header">
    <h1>Inventory Dashboard</h1>
    <p>Stock health at a glance — {{ now()->format('F d, Y') }}</p>
</div>

<div class="stat-grid">
    <div class="stat-card">
        <div class="sc-top">
            <div class="sc-dot blue"></div>
            <div class="sc-icon"><i data-lucide="package"></i></div>
        </div>
        <div class="sc-val">{{ $totalProducts }}</div>
        <div class="sc-label">Total Products</div>
    </div>
    <div class="stat-card">
        <div class="sc-top">
            <div class="sc-dot yellow"></div>
            <div class="sc-icon"><i data-lucide="alert-triangle"></i></div>
        </div>
        <div class="sc-val">{{ $lowStockCount }}</div>
        <div class="sc-label">Low Stock</div>
    </div>
    <div class="stat-card">
        <div class="sc-top">
            <div class="sc-dot red"></div>
            <div class="sc-icon"><i data-lucide="package-x"></i></div>
        </div>
        <div class="sc-val">{{ $outOfStock }}</div>
        <div class="sc-label">Out of Stock</div>
    </div>
</div>

<div class="grid-2">
    <div class="card">
        <div class="card-header">
            <div class="card-title"><i data-lucide="alert-triangle"></i> Low Stock Items</div>
            <a href="{{ route('inventory.low_stock') }}" class="btn btn-secondary btn-sm">View all →</a>
        </div>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Stock</th>
                        <th>Min</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($lowStockItems as $p)
                <tr>
                    <td>
                        <div style="display:flex;align-items:center;gap:8px">
                            <span style="font-size:16px">{{ $p->icon ?? '📦' }}</span>
                            <span style="font-weight:500;font-size:.8rem">{{ $p->name }}</span>
                        </div>
                    </td>
                    <td style="font-weight:600;color:{{ $p->stock==0?'var(--red)':'var(--yellow)' }}">
                        {{ $p->stock }} {{ $p->unit }}
                    </td>
                    <td style="color:var(--text-muted)">{{ $p->low_stock_threshold }}</td>
                    <td>
                        <span class="badge badge-{{ $p->stock==0?'red':'yellow' }}">
                            {{ $p->stock == 0 ? 'Out of Stock' : 'Low' }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4">
                        <div class="empty-state" style="padding:16px">
                            <p>All products are well stocked 🎉</p>
                        </div>
                    </td>
                </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <div class="card-title"><i data-lucide="arrow-left-right"></i> Recent Stock Movements</div>
            <a href="{{ route('inventory.stock') }}" class="btn btn-secondary btn-sm">View all →</a>
        </div>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Type</th>
                        <th>Qty</th>
                        <th>By</th>
                        <th>When</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($recentMovements as $m)
                <tr>
                    <td style="font-size:.78rem;font-weight:500">{{ $m->product->name }}</td>
                    <td>
                        <span class="badge badge-{{ $m->type==='in'?'green':($m->type==='sale'?'blue':($m->type==='out'?'red':'yellow')) }}" style="text-transform:capitalize">
                            {{ $m->type }}
                        </span>
                    </td>
                    <td style="font-weight:600;color:{{ $m->quantity > 0 ? 'var(--green)' : 'var(--red)' }}">
                        {{ $m->quantity > 0 ? '+' : '' }}{{ $m->quantity }}
                    </td>
                    <td style="color:var(--text-muted);font-size:.75rem">{{ $m->user->name }}</td>
                    <td style="color:var(--text-dim);font-size:.72rem">{{ $m->created_at->diffForHumans() }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5">
                        <div class="empty-state" style="padding:16px">
                            <p>No movements recorded yet.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection