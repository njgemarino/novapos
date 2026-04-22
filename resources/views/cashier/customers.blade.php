@extends('layouts.app')
@section('title','Customers')
@section('sidebar-nav')@include('cashier._nav')@endsection
@section('content')
<div class="page-header" style="display:flex;justify-content:space-between;align-items:flex-start">
    <div><h1>Customers</h1><p>Look up customer accounts</p></div>
    <button class="btn btn-primary" onclick="openModal('modal-add-cust')"><i data-lucide="user-plus"></i> Add Customer</button>
</div>
<div class="card">
    <div class="card-header">
        <form method="GET" style="display:flex;gap:8px;margin:0">
            <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Search name or email…" style="width:220px">
            <button class="btn btn-secondary" type="submit"><i data-lucide="search"></i></button>
        </form>
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
            <tr><td colspan="6"><div class="empty-state"><i data-lucide="users"></i><p>No customers found.</p></div></td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div style="padding:14px 18px">{{ $customers->withQueryString()->links('vendor.pagination.simple') }}</div>
</div>

<div class="modal-backdrop" id="modal-add-cust">
    <div class="modal" style="max-width:400px">
        <div class="modal-header"><h3>Add Customer</h3><button class="modal-close" onclick="closeModal('modal-add-cust')"><i data-lucide="x" style="width:16px;height:16px"></i></button></div>
        <div class="modal-body">
            <form method="POST" action="{{ route('cashier.customers.store') }}">
                @csrf
                <div class="form-group"><label>Full Name *</label><input type="text" name="name" class="form-control" required></div>
                <div class="form-group"><label>Email</label><input type="email" name="email" class="form-control"></div>
                <div class="form-group"><label>Phone</label><input type="text" name="phone" class="form-control" placeholder="+63 9XX XXX XXXX"></div>
                <div style="display:flex;gap:8px">
                    <button type="submit" class="btn btn-primary"><i data-lucide="save"></i> Save</button>
                    <button type="button" class="btn btn-secondary" onclick="closeModal('modal-add-cust')">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
