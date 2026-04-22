@extends('layouts.app')
@section('title','Dashboard')
@section('sidebar-nav')@include('admin._nav')@endsection

@section('content')
<div class="page-header">
    <h1>Operations Dashboard</h1>
    <p>{{ now()->format('l, F d, Y') }} — Welcome back, {{ auth()->user()->name }}</p>
</div>

<div class="stat-grid">
    <div class="stat-card">
        <div class="sc-top">
            <div class="sc-dot blue"></div>
            <div class="sc-icon"><i data-lucide="banknote"></i></div>
        </div>
        <div class="sc-val">₱{{ number_format($todaySales, 0) }}</div>
        <div class="sc-label">Today's Sales</div>
        <div class="sc-sub">{{ $todayTxCount }} transactions</div>
    </div>
    <div class="stat-card">
        <div class="sc-top">
            <div class="sc-dot green"></div>
            <div class="sc-icon"><i data-lucide="receipt"></i></div>
        </div>
        <div class="sc-val">{{ $todayTxCount }}</div>
        <div class="sc-label">Transactions Today</div>
        <div class="sc-sub">completed orders</div>
    </div>
    <div class="stat-card">
        <div class="sc-top">
            <div class="sc-dot purple"></div>
            <div class="sc-icon"><i data-lucide="users"></i></div>
        </div>
        <div class="sc-val">{{ $totalCustomers }}</div>
        <div class="sc-label">Total Customers</div>
        <div class="sc-sub">registered accounts</div>
    </div>
    <div class="stat-card">
        <div class="sc-top">
            <div class="sc-dot red"></div>
            <div class="sc-icon"><i data-lucide="alert-triangle"></i></div>
        </div>
        <div class="sc-val">{{ $lowStockCount }}</div>
        <div class="sc-label">Low Stock Items</div>
        <div class="sc-sub">needs reorder</div>
    </div>
</div>

<div class="grid-2" style="margin-bottom:16px">
    <div class="card">
        <div class="card-header">
            <div class="card-title"><i data-lucide="trending-up"></i> Monthly Sales</div>
        </div>
        <div class="card-body">
            @php
                $months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
                $maxSale = $monthlySales->max('total') ?: 1;
            @endphp
            <div class="chart-bars">
                @for($m = 1; $m <= 12; $m++)
                @php $val = $monthlySales->get($m)?->total ?? 0; $pct = round(($val/$maxSale)*100); @endphp
                <div class="bar-col">
                    <div class="bar-fill" style="height:{{ $pct }}%;min-height:{{ $val>0?'4px':'0' }};" title="₱{{ number_format($val) }}"></div>
                    <span class="bar-label">{{ $months[$m-1] }}</span>
                </div>
                @endfor
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <div class="card-title"><i data-lucide="zap"></i> Quick Actions</div>
        </div>
        <div class="card-body" style="display:flex;flex-direction:column;gap:8px">
            <a href="{{ route('cashier.pos') }}" class="btn btn-primary" style="justify-content:center">
                <i data-lucide="shopping-cart"></i> Start New Sale
            </a>
            <a href="{{ route('admin.products.create') }}" class="btn btn-secondary" style="justify-content:center">
                <i data-lucide="plus"></i> Add Product
            </a>
            <a href="{{ route('admin.reports') }}" class="btn btn-secondary" style="justify-content:center">
                <i data-lucide="bar-chart-2"></i> View Reports
            </a>
            <a href="{{ route('admin.settings') }}" class="btn btn-secondary" style="justify-content:center">
                <i data-lucide="settings"></i> Settings
            </a>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <div class="card-title"><i data-lucide="receipt"></i> Recent Transactions</div>
        <a href="{{ route('admin.transactions') }}" class="btn btn-secondary btn-sm">View all →</a>
    </div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Receipt #</th>
                    <th>Customer</th>
                    <th>Cashier</th>
                    <th>Total</th>
                    <th>Method</th>
                    <th>Status</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
            @forelse($recentTx as $tx)
            <tr>
                <td style="font-family:monospace;font-size:.75rem;font-weight:600;color:var(--text-muted)">{{ $tx->receipt_no }}</td>
                <td style="font-weight:500">{{ $tx->customer?->name ?? 'Walk-in' }}</td>
                <td style="color:var(--text-muted)">{{ $tx->cashier->name }}</td>
                <td style="font-weight:600">₱{{ number_format($tx->total_amount, 2) }}</td>
                <td><span class="badge badge-blue" style="text-transform:capitalize">{{ $tx->payment_method }}</span></td>
                <td>
                    <span class="badge badge-{{ $tx->status==='completed'?'green':($tx->status==='refunded'?'yellow':'red') }}">
                        {{ ucfirst($tx->status) }}
                    </span>
                </td>
                <td style="color:var(--text-dim);font-size:.75rem">{{ $tx->created_at->format('M d, H:i') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="7">
                    <div class="empty-state">
                        <i data-lucide="receipt"></i>
                        <p>No transactions yet.</p>
                    </div>
                </td>
            </tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection