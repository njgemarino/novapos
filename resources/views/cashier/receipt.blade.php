@extends('layouts.app')
@section('title','Receipt')
@section('sidebar-nav')@include('cashier._nav')@endsection

@push('styles')
<style>
.receipt-paper{max-width:380px;margin:0 auto;background:#fff;border:1px solid var(--border);border-radius:var(--radius);padding:28px 24px;box-shadow:var(--shadow-md);font-size:.83rem;}
.receipt-paper .rh{text-align:center;margin-bottom:20px;padding-bottom:16px;border-bottom:2px dashed var(--border);}
.receipt-paper .store-name{font-family:'Syne',sans-serif;font-size:1.2rem;font-weight:800;margin-bottom:3px;}
.receipt-paper .store-info{font-size:.72rem;color:var(--text-muted);}
.receipt-paper .receipt-no{font-family:monospace;font-size:.78rem;background:var(--surface2);padding:5px 10px;border-radius:6px;margin-top:10px;display:inline-block;}
.r-item{display:flex;justify-content:space-between;padding:5px 0;border-bottom:1px solid var(--border);}
.r-item:last-child{border:none;}
.r-total{border-top:2px solid var(--text);margin-top:8px;padding-top:8px;}
.r-footer{text-align:center;margin-top:18px;padding-top:14px;border-top:2px dashed var(--border);font-size:.78rem;color:var(--text-muted);}
@media print{body *{visibility:hidden}.receipt-paper,.receipt-paper *{visibility:visible}.receipt-paper{position:fixed;top:0;left:0;width:100%;border:none;box-shadow:none;}}
</style>
@endpush

@section('content')
<div class="page-header" style="display:flex;justify-content:space-between;align-items:flex-start">
    <div><h1>Receipt</h1><p>Transaction completed successfully</p></div>
    <div style="display:flex;gap:8px">
        <button onclick="window.print()" class="btn btn-secondary"><i data-lucide="printer"></i> Print</button>
        <a href="{{ route('cashier.pos') }}" class="btn btn-primary"><i data-lucide="plus"></i> New Sale</a>
    </div>
</div>

<div class="receipt-paper">
    <div class="rh">
        <div class="store-name">{{ $settings['store_name'] ?? 'Gemarino Store' }}</div>
        <div class="store-info">{{ $settings['store_address'] ?? '' }}</div>
        <div class="store-info">{{ $settings['store_phone'] ?? '' }}</div>
        <div class="store-info" style="margin-top:4px">{{ $settings['receipt_header'] ?? 'Thank you for shopping!' }}</div>
        <div class="receipt-no">{{ $transaction->receipt_no }}</div>
    </div>

    <div style="font-size:.72rem;color:var(--text-muted);margin-bottom:12px">
        <div>Date: {{ $transaction->created_at->format('M d, Y H:i:s') }}</div>
        <div>Cashier: {{ $transaction->cashier->name }}</div>
        @if($transaction->customer)<div>Customer: {{ $transaction->customer->name }}</div>@endif
    </div>

    @foreach($transaction->items as $item)
    <div class="r-item">
        <div>
            <div style="font-weight:600">{{ $item->product_name }}</div>
            <div style="font-size:.72rem;color:var(--text-muted)">{{ $item->quantity }} × ₱{{ number_format($item->unit_price,2) }}</div>
        </div>
        <div style="font-weight:700">₱{{ number_format($item->subtotal,2) }}</div>
    </div>
    @endforeach

    <div class="r-item r-total" style="display:flex;justify-content:space-between">
        <span style="color:var(--text-muted)">Subtotal</span><span>₱{{ number_format($transaction->subtotal,2) }}</span>
    </div>
    <div class="r-item" style="display:flex;justify-content:space-between">
        <span style="color:var(--text-muted)">Tax (VAT)</span><span>₱{{ number_format($transaction->tax_amount,2) }}</span>
    </div>
    <div style="display:flex;justify-content:space-between;font-family:'Syne',sans-serif;font-size:1rem;font-weight:800;padding:8px 0;border-top:2px solid var(--text)">
        <span>TOTAL</span><span style="color:var(--accent)">₱{{ number_format($transaction->total_amount,2) }}</span>
    </div>
    <div style="display:flex;justify-content:space-between;font-size:.82rem;padding:4px 0;color:var(--text-muted)">
        <span>Tendered ({{ strtoupper($transaction->payment_method) }})</span><span>₱{{ number_format($transaction->amount_tendered,2) }}</span>
    </div>
    <div style="display:flex;justify-content:space-between;font-size:.82rem;padding:4px 0;color:var(--green);font-weight:600">
        <span>Change</span><span>₱{{ number_format($transaction->change_amount,2) }}</span>
    </div>

    <div class="r-footer">{{ $settings['receipt_footer'] ?? 'Please come again!' }}</div>
</div>
@endsection
