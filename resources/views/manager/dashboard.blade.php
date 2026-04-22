@extends('layouts.app')
@section('title','Store Dashboard')
@section('sidebar-nav')@include('manager._nav')@endsection

@section('content')
<div class="page-header">
    <h1>Store Dashboard</h1>
    <p>Operations overview — {{ now()->format('F d, Y') }}</p>
</div>

<div class="stat-grid">
    <div class="stat-card">
        <div class="sc-top">
            <div class="sc-dot blue"></div>
            <div class="sc-icon"><i data-lucide="banknote"></i></div>
        </div>
        <div class="sc-val">₱{{ number_format($todaySales, 0) }}</div>
        <div class="sc-label">Today's Sales</div>
    </div>
    <div class="stat-card">
        <div class="sc-top">
            <div class="sc-dot green"></div>
            <div class="sc-icon"><i data-lucide="receipt"></i></div>
        </div>
        <div class="sc-val">{{ $todayCount }}</div>
        <div class="sc-label">Transactions Today</div>
    </div>
    <div class="stat-card">
        <div class="sc-top">
            <div class="sc-dot yellow"></div>
            <div class="sc-icon"><i data-lucide="users"></i></div>
        </div>
        <div class="sc-val">{{ $activeStaff }}</div>
        <div class="sc-label">Active Cashiers</div>
    </div>
    <div class="stat-card">
        <div class="sc-top">
            <div class="sc-dot red"></div>
            <div class="sc-icon"><i data-lucide="alert-triangle"></i></div>
        </div>
        <div class="sc-val">{{ $lowStockCount }}</div>
        <div class="sc-label">Low Stock Items</div>
    </div>
</div>

<div class="grid-2" style="margin-bottom:16px">
    <div class="card">
        <div class="card-header">
            <div class="card-title"><i data-lucide="trending-up"></i> Sales This Week</div>
        </div>
        <div class="card-body">
            @php
                $days = ['Sun','Mon','Tue','Wed','Thu','Fri','Sat'];
                $maxVal = collect($weekSales)->max('total') ?: 1;
            @endphp
            <div class="chart-bars">
                @for($d = 1; $d <= 7; $d++)
                @php $val = $weekSales->get($d)?->total ?? 0; $pct = round(($val/$maxVal)*100); @endphp
                <div class="bar-col">
                    <div class="bar-fill" style="height:{{ max($pct, 2) }}%" title="₱{{ number_format($val) }}"></div>
                    <span class="bar-label">{{ $days[$d % 7] }}</span>
                </div>
                @endfor
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <div class="card-title"><i data-lucide="receipt"></i> Recent Transactions</div>
            <a href="{{ route('manager.sales') }}" class="btn btn-secondary btn-sm">View all →</a>
        </div>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Receipt #</th>
                        <th>Cashier</th>
                        <th>Total</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($recentTx as $tx)
                <tr>
                    <td style="font-family:monospace;font-size:.75rem;font-weight:600;color:var(--text-muted)">{{ $tx->receipt_no }}</td>
                    <td style="font-size:.8rem;font-weight:500">{{ $tx->cashier->name }}</td>
                    <td style="font-weight:600">₱{{ number_format($tx->total_amount, 2) }}</td>
                    <td>
                        <span class="badge badge-{{ $tx->status==='completed'?'green':($tx->status==='refunded'?'yellow':'red') }}">
                            {{ ucfirst($tx->status) }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4">
                        <div class="empty-state" style="padding:16px">
                            <p>No transactions yet.</p>
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