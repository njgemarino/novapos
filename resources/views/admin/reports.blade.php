@extends('layouts.app')
@section('title','Reports')
@section('sidebar-nav')@include('admin._nav')@endsection
@section('content')
<div class="page-header"><h1>Reports & Analytics</h1><p>Business performance overview</p></div>

<div class="stat-grid">
    <div class="stat-card blue"><div class="sc-icon"><i data-lucide="banknote"></i></div><div class="sc-val">₱{{ number_format($totalRevenue,0) }}</div><div class="sc-label">Total Revenue</div></div>
    <div class="stat-card yellow"><div class="sc-icon"><i data-lucide="percent"></i></div><div class="sc-val">₱{{ number_format($totalTax,0) }}</div><div class="sc-label">Total Tax Collected</div></div>
    @foreach($txByStatus as $row)
    <div class="stat-card {{ $row->status==='completed'?'green':($row->status==='refunded'?'yellow':'red') }}">
        <div class="sc-val">{{ $row->total }}</div>
        <div class="sc-label">{{ ucfirst($row->status) }}</div>
    </div>
    @endforeach
</div>

<div class="grid-2">
    <div class="card">
        <div class="card-header"><div class="card-title"><i data-lucide="trophy"></i> Top Selling Products</div></div>
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
                <tr><td colspan="3"><div class="empty-state" style="padding:20px"><p>No data.</p></div></td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="card">
        <div class="card-header"><div class="card-title"><i data-lucide="trending-up"></i> Daily Sales (Last 7 Days)</div></div>
        <div class="card-body">
            @php $maxVal = $dailySales->max('total') ?: 1; @endphp
            <div class="chart-bars">
                @foreach($dailySales as $day)
                @php $pct = round(($day->total / $maxVal) * 100); @endphp
                <div class="bar-col">
                    <div class="bar-fill" style="height:{{ $pct }}%;min-height:4px" title="₱{{ number_format($day->total) }}"></div>
                    <span class="bar-label">{{ \Carbon\Carbon::parse($day->date)->format('M d') }}</span>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection
