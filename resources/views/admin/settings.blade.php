@extends('layouts.app')
@section('title','Settings')
@section('sidebar-nav')@include('admin._nav')@endsection
@section('content')
<div class="page-header"><h1>System Settings</h1><p>Configure store preferences</p></div>
<div style="display:grid;grid-template-columns:1fr 1fr;gap:18px;max-width:900px">
    <div class="card">
        <div class="card-header"><div class="card-title"><i data-lucide="store"></i> Store Information</div></div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.settings.update') }}">
                @csrf
                <div class="form-group"><label>Store Name *</label><input type="text" name="store_name" value="{{ $settings['store_name'] ?? '' }}" class="form-control" required></div>
                <div class="form-group"><label>Address</label><textarea name="store_address" class="form-control">{{ $settings['store_address'] ?? '' }}</textarea></div>
                <div class="form-group"><label>Phone</label><input type="text" name="store_phone" value="{{ $settings['store_phone'] ?? '' }}" class="form-control"></div>
                <div class="form-group"><label>Tax Rate (%)</label><input type="number" step="0.01" name="tax_rate" value="{{ $settings['tax_rate'] ?? 12 }}" class="form-control"></div>
                <div class="form-group"><label>Currency Symbol</label><input type="text" name="currency_symbol" value="{{ $settings['currency_symbol'] ?? '₱' }}" class="form-control" maxlength="5" style="max-width:100px"></div>
                <div class="form-group"><label>Low Stock Threshold</label><input type="number" name="low_stock_threshold" value="{{ $settings['low_stock_threshold'] ?? 15 }}" class="form-control" min="0" style="max-width:120px"></div>
                <div class="form-group"><label>Receipt Header</label><input type="text" name="receipt_header" value="{{ $settings['receipt_header'] ?? '' }}" class="form-control"></div>
                <div class="form-group"><label>Receipt Footer</label><input type="text" name="receipt_footer" value="{{ $settings['receipt_footer'] ?? '' }}" class="form-control"></div>
                <button type="submit" class="btn btn-primary"><i data-lucide="save"></i> Save Settings</button>
            </form>
        </div>
    </div>
    <div class="card">
        <div class="card-header"><div class="card-title"><i data-lucide="info"></i> System Info</div></div>
        <div class="card-body">
            @foreach([['Laravel Version', app()->version()],['PHP Version', PHP_VERSION],['App Environment', config('app.env')],['Timezone', config('app.timezone')]] as [$k,$v])
            <div style="display:flex;justify-content:space-between;padding:9px 0;border-bottom:1px solid var(--border);font-size:.82rem">
                <span style="color:var(--text-muted)">{{ $k }}</span>
                <span style="font-family:monospace;font-weight:600">{{ $v }}</span>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
