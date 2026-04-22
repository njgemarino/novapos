@extends('layouts.app')
@section('title','My Transactions')
@section('sidebar-nav')@include('cashier._nav')@endsection
@section('content')
<div class="page-header"><h1>My Transactions</h1><p>All sales processed by your account</p></div>
<div class="card">
    <div class="card-header">
        <form method="GET" style="display:flex;gap:8px;margin:0">
            <select name="status" class="form-control" style="width:auto">
                <option value="">All Status</option>
                @foreach(['completed','refunded','voided'] as $s)
                <option value="{{ $s }}" {{ request('status')===$s?'selected':'' }}>{{ ucfirst($s) }}</option>
                @endforeach
            </select>
            <button class="btn btn-secondary" type="submit"><i data-lucide="filter"></i></button>
        </form>
    </div>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Receipt #</th><th>Customer</th><th>Total</th><th>Method</th><th>Status</th><th>Date</th><th></th></tr></thead>
            <tbody>
            @forelse($transactions as $tx)
            <tr>
                <td style="font-family:monospace;font-size:.78rem;font-weight:600">{{ $tx->receipt_no }}</td>
                <td>{{ $tx->customer?->name ?? 'Walk-in' }}</td>
                <td style="font-weight:700;color:var(--accent)">₱{{ number_format($tx->total_amount,2) }}</td>
                <td><span class="badge badge-blue" style="text-transform:capitalize">{{ $tx->payment_method }}</span></td>
                <td><span class="badge badge-{{ $tx->status==='completed'?'green':($tx->status==='refunded'?'yellow':'red') }}">{{ ucfirst($tx->status) }}</span></td>
                <td style="color:var(--text-muted);font-size:.78rem">{{ $tx->created_at->format('M d, Y H:i') }}</td>
                <td><a href="{{ route('cashier.receipt',$tx) }}" class="btn btn-secondary btn-xs"><i data-lucide="eye"></i></a></td>
            </tr>
            @empty
            <tr><td colspan="7"><div class="empty-state"><i data-lucide="receipt"></i><p>No transactions yet.</p></div></td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div style="padding:14px 18px">{{ $transactions->withQueryString()->links('vendor.pagination.simple') }}</div>
</div>
@endsection
