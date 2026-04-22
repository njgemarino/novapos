@extends('layouts.app')
@section('title','Edit Product')
@section('sidebar-nav')@include('admin._nav')@endsection

@section('content')
<div class="page-header" style="display:flex;justify-content:space-between;align-items:flex-start">
    <div><h1>Edit: {{ $product->name }}</h1><p>Admin / Products / Edit</p></div>
    <a href="{{ route('admin.products') }}" class="btn btn-secondary"><i data-lucide="arrow-left"></i> Back</a>
</div>

<div class="card" style="max-width:640px">
    <div class="card-body">
        <form method="POST" action="{{ route('admin.products.update',$product) }}">
            @csrf @method('PUT')
            <div class="form-row">
                <div class="form-group"><label>Product Name *</label><input type="text" name="name" value="{{ old('name',$product->name) }}" class="form-control" required></div>
                <div class="form-group"><label>Icon (emoji)</label><input type="text" name="icon" value="{{ old('icon',$product->icon) }}" class="form-control" maxlength="10"></div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Category</label>
                    <select name="category_id" class="form-control">
                        <option value="">— Uncategorized —</option>
                        @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ old('category_id',$product->category_id)==$cat->id?'selected':'' }}>{{ $cat->icon }} {{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>Status</label>
                    <select name="is_active" class="form-control">
                        <option value="1" {{ $product->is_active?'selected':'' }}>Active</option>
                        <option value="0" {{ !$product->is_active?'selected':'' }}>Inactive</option>
                    </select>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group"><label>Selling Price (₱) *</label><input type="number" step="0.01" name="price" value="{{ old('price',$product->price) }}" class="form-control" required min="0"></div>
                <div class="form-group"><label>Cost Price (₱) *</label><input type="number" step="0.01" name="cost_price" value="{{ old('cost_price',$product->cost_price) }}" class="form-control" required min="0"></div>
            </div>
            <div class="form-row">
                <div class="form-group"><label>Low Stock Threshold *</label><input type="number" name="low_stock_threshold" value="{{ old('low_stock_threshold',$product->low_stock_threshold) }}" class="form-control" required min="0"></div>
                <div class="form-group"><label>Unit *</label><input type="text" name="unit" value="{{ old('unit',$product->unit) }}" class="form-control"></div>
            </div>
            <div style="display:flex;gap:8px;margin-top:8px">
                <button type="submit" class="btn btn-primary"><i data-lucide="save"></i> Save Changes</button>
                <a href="{{ route('admin.products') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
