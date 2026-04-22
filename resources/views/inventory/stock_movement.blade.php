@extends('layouts.app')
@section('title','Stock Movement')
@section('sidebar-nav')@include('inventory._nav')@endsection
@section('content')
<div class="page-header"><h1>Stock Movement</h1><p>All inventory changes and adjustments</p></div>
<div class="card">
    <div class="card-header">
        <form method="GET" style="display:flex;gap:8px;margin:0">
            <select name="type" class="form-control" style="width:auto">
                <option value="">All Types</option>
                @foreach(['in'=>'Stock In','out'=>'Stock Out','sale'=>'Sale','adjustment'=>'Adjustment','return'=>'Return'] as $val=>$label)
                <option value="{{ $val }}" {{ request('type')===$val?'selected':'' }}>{{ $label }}</option>
                @endforeach
            </select>
            <button class="btn btn-secondary" type="submit"><i data-lucide="filter"></i></button>
        </form>
        <span style="color:var(--text-muted);font-size:.78rem">{{ $movements->total() }} records</span>
    </div>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Product</th><th>Type</th><th>Qty Change</th><th>Before</th><th>After</th><th>Reference</th><th>Notes</th><th>By</th><th>Date</th></tr></thead>
            <tbody>
            @forelse($movements as $m)
            <tr>
                <td>
                    <div style="display:flex;align-items:center;gap:8px">
                        <span style="font-size:17px">{{ $m->product->icon ?? '📦' }}</span>
                        <span style="font-weight:600;font-size:.81rem">{{ $m->product->name }}</span>
                    </div>
                </td>
                <td><span class="badge badge-{{ $m->type==='in'?'green':($m->type==='sale'?'blue':($m->type==='out'?'red':($m->type==='return'?'purple':'yellow'))) }}" style="text-transform:capitalize">{{ $m->type }}</span></td>
                <td style="font-weight:700;color:{{ $m->quantity > 0 ? 'var(--green)' : 'var(--red)' }}">
                    {{ $m->quantity > 0 ? '+' : '' }}{{ $m->quantity }}
                </td>
                <td style="color:var(--text-muted)">{{ $m->stock_before }}</td>
                <td style="font-weight:600">{{ $m->stock_after }}</td>
                <td style="font-family:monospace;font-size:.75rem;color:var(--text-muted)">{{ $m->reference ?? '—' }}</td>
                <td style="font-size:.78rem;color:var(--text-muted);max-width:160px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">{{ $m->notes ?? '—' }}</td>
                <td style="font-size:.78rem;color:var(--text-muted)">{{ $m->user->name }}</td>
                <td style="font-size:.72rem;color:var(--text-muted);white-space:nowrap">{{ $m->created_at->format('M d, H:i') }}</td>
            </tr>
            @empty
            <tr><td colspan="9"><div class="empty-state"><i data-lucide="arrow-left-right"></i><p>No stock movements yet.</p></div></td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div style="padding:14px 18px">{{ $movements->withQueryString()->links('vendor.pagination.simple') }}</div>
</div>
@endsection
