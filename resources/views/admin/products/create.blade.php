@extends('layouts.app')
@section('title','Add Product')
@section('sidebar-nav')@include('admin._nav')@endsection

@section('content')
<div class="page-header" style="display:flex;justify-content:space-between;align-items:flex-start">
    <div><h1>Add Product</h1><p>Admin / Products / New</p></div>
    <a href="{{ route('admin.products') }}" class="btn btn-secondary"><i data-lucide="arrow-left"></i> Back</a>
</div>

<div class="card" style="max-width:640px">
    <div class="card-body">
        <form method="POST" action="{{ route('admin.products.store') }}">
            @csrf
            <div class="form-row">
                <div class="form-group"><label>Product Name *</label><input type="text" name="name" value="{{ old('name') }}" class="form-control" required></div>
                <div class="form-group"><label>Icon (emoji)</label><input type="text" name="icon" value="{{ old('icon','📦') }}" class="form-control" maxlength="10"></div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Category</label>
                    <select name="category_id" class="form-control">
                        <option value="">— Uncategorized —</option>
                        @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ old('category_id')==$cat->id?'selected':'' }}>{{ $cat->icon }} {{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group"><label>Barcode</label><input type="text" name="barcode" value="{{ old('barcode') }}" class="form-control" placeholder="Optional"></div>
            </div>
            <div class="form-row">
                <div class="form-group"><label>Selling Price (₱) *</label><input type="number" step="0.01" name="price" value="{{ old('price') }}" class="form-control" required min="0"></div>
                <div class="form-group"><label>Cost Price (₱) *</label><input type="number" step="0.01" name="cost_price" value="{{ old('cost_price',0) }}" class="form-control" required min="0"></div>
            </div>
            <div class="form-row">
                <div class="form-group"><label>Initial Stock *</label><input type="number" name="stock" value="{{ old('stock',0) }}" class="form-control" required min="0"></div>
                <div class="form-group"><label>Low Stock Threshold *</label><input type="number" name="low_stock_threshold" value="{{ old('low_stock_threshold',15) }}" class="form-control" required min="0"></div>
            </div>
            <div class="form-group"><label>Unit *</label><input type="text" name="unit" value="{{ old('unit','pcs') }}" class="form-control" style="max-width:160px"></div>
            <div style="display:flex;gap:8px;margin-top:8px">
                <button type="submit" class="btn btn-primary"><i data-lucide="save"></i> Create Product</button>
                <a href="{{ route('admin.products') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
