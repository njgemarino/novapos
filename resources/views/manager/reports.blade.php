@extends('layouts.app')
@section('title','Reports')
@section('sidebar-nav')@include('manager._nav')@endsection
@section('content')
<div class="page-header"><h1>Reports</h1><p>Sales & performance overview</p></div>

<div class="stat-grid">
    <div class="stat-card blue"><div class="sc-icon"><i data-lucide="banknote"></i></div><div class="sc-val">₱{{ number_format($totalRevenue,0) }}</div><div class="sc-label">Total Revenue</div></div>
    <div class="stat-card yellow"><div class="sc-icon"><i data-lucide="percent"></i></div><div class="sc-val">₱{{ number_format($totalTax,0) }}</div><div class="sc-label">Tax Collected</div></div>
</div>

<div class="grid-2">
    <div class="card">
        <div class="card-header"><div class="card-title"><i data-lucide="trophy"></i> Top Products</div></div>
        <div class="table-wrap">
            <table>
                <thead><tr><th>Product</th><th>Qty Sold</th><th>Revenue</th></tr></thead>
                <tbody>
                @forelse($topProducts as $p)
                <tr>
                    <td style="font-weight:600">{{ $p->product_name }}</td>
                    <td>{{ $p->qty_sold }}</td>
                    <td style="font-weight:700;color:var(--accent)">₱{{ number_format($p->revenue,2) }}</td>
                </tr>
                @empty
                <tr><td colspan="3"><div class="empty-state" style="padding:16px"><p>No data.</p></div></td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="card">
        <div class="card-header"><div class="card-title"><i data-lucide="users"></i> Sales by Cashier</div></div>
        <div class="table-wrap">
            <table>
                <thead><tr><th>Cashier</th><th>Transactions</th><th>Total Sales</th></tr></thead>
                <tbody>
                @forelse($byCashier as $row)
                <tr>
                    <td style="font-weight:600">{{ $row->cashier?->name ?? 'Unknown' }}</td>
                    <td>{{ $row->count }}</td>
                    <td style="font-weight:700;color:var(--accent)">₱{{ number_format($row->total,2) }}</td>
                </tr>
                @empty
                <tr><td colspan="3"><div class="empty-state" style="padding:16px"><p>No data.</p></div></td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="card" style="grid-column:1/-1">
        <div class="card-header"><div class="card-title"><i data-lucide="trending-up"></i> Daily Sales (Last 7 Days)</div></div>
        <div class="card-body">
            @php $maxVal = $dailySales->max('total') ?: 1; @endphp
            <div class="chart-bars" style="height:120px">
                @foreach($dailySales as $day)
                @php $pct = round(($day->total / $maxVal) * 100); @endphp
                <div class="bar-col">
                    <div style="font-size:.7rem;font-weight:600;color:var(--text-muted);margin-bottom:2px">₱{{ number_format($day->total/1000,1) }}k</div>
                    <div class="bar-fill" style="height:{{ max($pct,2) }}%" title="₱{{ number_format($day->total) }}"></div>
                    <span class="bar-label">{{ \Carbon\Carbon::parse($day->date)->format('M d') }}</span>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection
