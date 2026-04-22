@extends('layouts.app')
@section('title','Dashboard')
@section('sidebar-nav')@include('cashier._nav')@endsection

@section('content')
<div class="page-header">
    <h1>Good {{ now()->hour < 12 ? 'Morning' : (now()->hour < 17 ? 'Afternoon' : 'Evening') }}, {{ explode(' ', auth()->user()->name)[0] }}</h1>
    <p>Shift summary for {{ now()->format('l, F d, Y') }}</p>
</div>

<div class="stat-grid">
    <div class="stat-card">
        <div class="sc-top">
            <div class="sc-dot blue"></div>
            <div class="sc-icon"><i data-lucide="banknote"></i></div>
        </div>
        <div class="sc-val">₱{{ number_format($myTodaySales, 0) }}</div>
        <div class="sc-label">My Sales Today</div>
    </div>
    <div class="stat-card">
        <div class="sc-top">
            <div class="sc-dot green"></div>
            <div class="sc-icon"><i data-lucide="receipt"></i></div>
        </div>
        <div class="sc-val">{{ $myTodayCount }}</div>
        <div class="sc-label">Transactions</div>
    </div>
    <div class="stat-card">
        <div class="sc-top">
            <div class="sc-dot yellow"></div>
            <div class="sc-icon"><i data-lucide="calculator"></i></div>
        </div>
        <div class="sc-val">₱{{ number_format($myAvg, 0) }}</div>
        <div class="sc-label">Avg. Transaction</div>
    </div>
    <div class="stat-card">
        <div class="sc-top">
            <div class="sc-dot red"></div>
            <div class="sc-icon"><i data-lucide="rotate-ccw"></i></div>
        </div>
        <div class="sc-val">{{ $myRefunds }}</div>
        <div class="sc-label">Refunds Today</div>
    </div>
</div>

<div class="grid-2">
    <div class="card">
        <div class="card-header">
            <div class="card-title"><i data-lucide="receipt"></i> Recent Transactions</div>
            <a href="{{ route('cashier.transactions') }}" class="btn btn-secondary btn-sm">View all →</a>
        </div>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Receipt #</th>
                        <th>Customer</th>
                        <th>Total</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($recentTx as $tx)
                <tr>
                    <td style="font-family:monospace;font-size:.75rem;font-weight:600;color:var(--text-muted)">{{ $tx->receipt_no }}</td>
                    <td style="font-weight:500">{{ $tx->customer?->name ?? 'Walk-in' }}</td>
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
                        <div class="empty-state" style="padding:20px">
                            <p>No transactions yet today.</p>
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
            <div class="card-title"><i data-lucide="zap"></i> Quick Actions</div>
        </div>
        <div class="card-body" style="display:flex;flex-direction:column;gap:8px">
            <a href="{{ route('cashier.pos') }}" class="btn btn-primary" style="justify-content:center">
                <i data-lucide="shopping-cart"></i> Start New Sale
            </a>
            <a href="{{ route('cashier.transactions') }}" class="btn btn-secondary" style="justify-content:center">
                <i data-lucide="receipt"></i> My Transactions
            </a>
            <a href="{{ route('cashier.customers') }}" class="btn btn-secondary" style="justify-content:center">
                <i data-lucide="users"></i> Customer Lookup
            </a>
        </div>
    </div>
</div>
@endsection