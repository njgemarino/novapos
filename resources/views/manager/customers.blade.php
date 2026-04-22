@extends('layouts.app')
@section('title','Customers')
@section('sidebar-nav')@include('manager._nav')@endsection
@section('content')
<div class="page-header"><h1>Customers</h1><p>Customer records and purchase history</p></div>
<div class="card">
    <div class="card-header">
        <form method="GET" style="display:flex;gap:8px;margin:0">
            <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Search name or email…" style="width:220px">
            <button class="btn btn-secondary" type="submit"><i data-lucide="search"></i></button>
        </form>
        <span style="color:var(--text-muted);font-size:.78rem">{{ $customers->total() }} customers</span>
    </div>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Name</th><th>Email</th><th>Phone</th><th>Purchases</th><th>Total Spent</th><th>Last Visit</th></tr></thead>
            <tbody>
            @forelse($customers as $c)
            <tr>
                <td style="font-weight:600">{{ $c->name }}</td>
                <td style="color:var(--text-muted);font-size:.8rem">{{ $c->email ?? '—' }}</td>
                <td style="color:var(--text-muted);font-size:.8rem">{{ $c->phone ?? '—' }}</td>
                <td>{{ $c->total_purchases }}</td>
                <td style="font-weight:700;color:var(--accent)">₱{{ number_format($c->total_spent,0) }}</td>
                <td style="color:var(--text-muted);font-size:.78rem">{{ $c->last_visit_at?->diffForHumans() ?? '—' }}</td>
            </tr>
            @empty
            <tr><td colspan="6"><div class="empty-state"><i data-lucide="users"></i><p>No customers yet.</p></div></td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div style="padding:14px 18px">{{ $customers->withQueryString()->links('vendor.pagination.simple') }}</div>
</div>
@endsection
