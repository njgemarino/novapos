@extends('layouts.app')
@section('title','Transactions')
@section('sidebar-nav')@include('admin._nav')@endsection
@section('content')
<div class="page-header">
    <h1>Transactions</h1>
    <p>Total revenue: <strong style="color:var(--accent)">₱{{ number_format($totalRevenue,2) }}</strong></p>
</div>
<div class="card">
    <div class="card-header">
        <form method="GET" style="display:flex;gap:8px;flex-wrap:wrap;margin:0">
            <select name="status" class="form-control" style="width:auto">
                <option value="">All Status</option>
                @foreach(['completed','refunded','voided'] as $s)
                <option value="{{ $s }}" {{ request('status')===$s?'selected':'' }}>{{ ucfirst($s) }}</option>
                @endforeach
            </select>
            <input type="date" name="date" value="{{ request('date') }}" class="form-control" style="width:auto">
            <button class="btn btn-secondary" type="submit"><i data-lucide="filter"></i> Filter</button>
        </form>
        <span style="color:var(--text-muted);font-size:.78rem">{{ $transactions->total() }} records</span>
    </div>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Receipt #</th><th>Customer</th><th>Cashier</th><th>Items</th><th>Total</th><th>Method</th><th>Status</th><th>Date</th><th></th></tr></thead>
            <tbody>
            @forelse($transactions as $tx)
            <tr>
                <td style="font-family:monospace;font-size:.78rem;font-weight:600">{{ $tx->receipt_no }}</td>
                <td>{{ $tx->customer?->name ?? 'Walk-in' }}</td>
                <td style="font-size:.8rem;color:var(--text-muted)">{{ $tx->cashier->name }}</td>
                <td>{{ $tx->items()->count() }}</td>
                <td style="font-weight:700;color:var(--accent)">₱{{ number_format($tx->total_amount,2) }}</td>
                <td><span class="badge badge-blue" style="text-transform:capitalize">{{ $tx->payment_method }}</span></td>
                <td><span class="badge badge-{{ $tx->status==='completed'?'green':($tx->status==='refunded'?'yellow':'red') }}">{{ ucfirst($tx->status) }}</span></td>
                <td style="color:var(--text-muted);font-size:.78rem">{{ $tx->created_at->format('M d, Y H:i') }}</td>
                <td>
                    @if($tx->status==='completed')
                    <form method="POST" action="{{ route('admin.transactions.void',$tx) }}">@csrf
                        <button type="submit" class="btn btn-danger btn-xs" onclick="return confirm('Void this transaction?')"><i data-lucide="x-circle"></i> Void</button>
                    </form>
                    @endif
                </td>
            </tr>
            @empty
            <tr><td colspan="9"><div class="empty-state"><i data-lucide="receipt"></i><p>No transactions found.</p></div></td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div style="padding:14px 18px">{{ $transactions->withQueryString()->links('vendor.pagination.simple') }}</div>
</div>
@endsection
