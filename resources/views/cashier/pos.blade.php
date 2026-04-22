@extends('layouts.app')
@section('title','New Sale')
@section('sidebar-nav')@include(auth()->user()->role . '._nav')@endsection

@push('styles')
<style>
.cat-tabs { display:flex; gap:6px; flex-wrap:wrap; margin-bottom:12px; }
.cat-tab  { padding:5px 13px; border-radius:20px; border:1px solid var(--border2); background:var(--surface); color:var(--text-muted); font-size:.75rem; font-weight:600; cursor:pointer; transition:all .12s; font-family:inherit; }
.cat-tab:hover,.cat-tab.active { background:var(--accent); border-color:var(--accent); color:#fff; }
.cart-empty { display:flex; flex-direction:column; align-items:center; justify-content:center; flex:1; color:var(--text-muted); gap:8px; padding:30px; }
.cart-empty svg { width:36px; height:36px; opacity:.2; }
</style>
@endpush

@section('content')
<div class="page-header">
    <h1>New Sale</h1>
    <p>Tap a product to add it to the cart</p>
</div>

<div class="pos-layout">
    {{-- Product Panel --}}
    <div class="pos-products">
        <div class="search-bar">
            <i data-lucide="search"></i>
            <input type="text" id="product-search" placeholder="Search products…" oninput="filterProducts(this.value)">
        </div>
        <div class="cat-tabs" id="cat-tabs">
            <button class="cat-tab active" onclick="filterByCategory(null,this)">All</button>
            @foreach($categories as $cat)
            <button class="cat-tab" onclick="filterByCategory({{ $cat->id }},this)">{{ $cat->icon }} {{ $cat->name }}</button>
            @endforeach
        </div>
        <div class="product-grid" id="product-grid">
            @foreach($products as $product)
            <div class="product-card" onclick="addToCart({{ $product->id }},'{{ addslashes($product->name) }}',{{ $product->price }},'{{ $product->icon ?? '📦' }}',{{ $product->stock }})" data-cat="{{ $product->category_id }}">
                <div class="p-icon">{{ $product->icon ?? '📦' }}</div>
                <div class="p-name">{{ $product->name }}</div>
                <div class="p-price">₱{{ number_format($product->price, 2) }}</div>
                <div style="font-size:.65rem;color:var(--text-muted)">{{ $product->stock }} left</div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Cart Panel --}}
    <div class="pos-cart">
        <div class="cart-header">
            <div class="cart-title"><i data-lucide="shopping-cart" style="width:16px;height:16px"></i> Cart <span id="cart-count" style="font-size:.78rem;color:var(--text-muted);font-weight:400">(0 items)</span></div>
        </div>
        <div class="cart-items" id="cart-items">
            <div class="cart-empty" id="cart-empty">
                <i data-lucide="shopping-cart"></i>
                <p style="font-size:.8rem">No items added yet</p>
            </div>
        </div>
        <div class="cart-footer">
            <div class="cart-line"><span>Subtotal</span><span id="subtotal">₱0.00</span></div>
            <div class="cart-line"><span>Tax (12%)</span><span id="tax">₱0.00</span></div>
            <div class="cart-line total"><span>TOTAL</span><span id="total">₱0.00</span></div>
            <button class="btn-charge" id="charge-btn" onclick="openPaymentModal()" disabled>Charge ₱0.00</button>
        </div>
    </div>
</div>

{{-- Payment Modal --}}
<div class="modal-backdrop" id="payment-modal">
    <div class="modal" style="max-width:440px">
        <div class="modal-header">
            <h3><i data-lucide="credit-card" style="width:15px;height:15px;display:inline;vertical-align:middle;margin-right:6px"></i>Process Payment</h3>
            <button class="modal-close" onclick="closeModal('payment-modal')"><i data-lucide="x" style="width:16px;height:16px"></i></button>
        </div>
        <div class="modal-body">
            <form method="POST" action="{{ route('cashier.pos.process') }}" id="payment-form">
                @csrf
                <div id="form-items"></div>
                <div class="form-group">
                    <label>Customer (optional)</label>
                    <select name="customer_id" class="form-control">
                        <option value="">Walk-in Customer</option>
                        @foreach($customers as $c)
                        <option value="{{ $c->id }}">{{ $c->name }} — {{ $c->email ?? $c->phone }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>Payment Method *</label>
                    <div style="display:flex;gap:8px;flex-wrap:wrap">
                        @foreach(['cash'=>'💵 Cash','card'=>'💳 Card','gcash'=>'📱 GCash','other'=>'Other'] as $val=>$label)
                        <label style="display:flex;align-items:center;gap:6px;cursor:pointer;padding:8px 12px;border:1.5px solid var(--border2);border-radius:8px;font-size:.8rem;font-weight:600;transition:all .12s" id="pm-{{ $val }}">
                            <input type="radio" name="payment_method" value="{{ $val }}" {{ $val==='cash'?'checked':'' }} style="display:none" onchange="updatePayMethod()"> {{ $label }}
                        </label>
                        @endforeach
                    </div>
                </div>
                <div class="form-group">
                    <label>Order Summary</label>
                    <div style="background:var(--surface2);border:1px solid var(--border);border-radius:8px;padding:12px;font-size:.8rem" id="payment-summary"></div>
                </div>
                <div class="form-group">
                    <label>Amount Tendered (₱) *</label>
                    <input type="number" name="amount_tendered" id="amount-tendered" class="form-control" step="0.01" min="0" required oninput="calcChange()">
                </div>
                <div style="background:var(--green-soft);border:1px solid #a7f3d0;border-radius:8px;padding:10px 14px;font-size:.83rem;font-weight:600;color:var(--green);display:flex;justify-content:space-between;margin-bottom:14px">
                    <span>Change</span><span id="change-display">₱0.00</span>
                </div>
                <div style="display:flex;gap:8px">
                    <button type="submit" class="btn btn-primary" style="flex:1;justify-content:center"><i data-lucide="check"></i> Complete Sale</button>
                    <button type="button" class="btn btn-secondary" onclick="closeModal('payment-modal')">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
const TAX_RATE = 0.12;
let cart = {};

function addToCart(id, name, price, icon, stock) {
    if (cart[id]) {
        if (cart[id].qty >= stock) { alert('Not enough stock!'); return; }
        cart[id].qty++;
    } else {
        cart[id] = { id, name, price, icon, stock, qty: 1 };
    }
    renderCart();
}

function changeQty(id, delta) {
    if (!cart[id]) return;
    cart[id].qty += delta;
    if (cart[id].qty <= 0) delete cart[id];
    renderCart();
}

function renderCart() {
    const items = Object.values(cart);
    const el = document.getElementById('cart-items');
    const count = items.reduce((s,i)=>s+i.qty,0);
    const subtotal = items.reduce((s,i)=>s+i.price*i.qty,0);
    const tax = subtotal * TAX_RATE;
    const total = subtotal + tax;

    document.getElementById('cart-count').textContent = `(${count} item${count!==1?'s':''})`;
    document.getElementById('subtotal').textContent = '₱' + subtotal.toLocaleString('en-PH',{minimumFractionDigits:2});
    document.getElementById('tax').textContent      = '₱' + tax.toLocaleString('en-PH',{minimumFractionDigits:2});
    document.getElementById('total').textContent    = '₱' + total.toLocaleString('en-PH',{minimumFractionDigits:2});

    const btn = document.getElementById('charge-btn');
    btn.textContent = `Charge ₱${total.toLocaleString('en-PH',{minimumFractionDigits:2})}`;
    btn.disabled = items.length === 0;

    if (items.length === 0) {
        el.innerHTML = '<div class="cart-empty" id="cart-empty"><svg data-lucide="shopping-cart"></svg><p style="font-size:.8rem">No items added yet</p></div>';
        lucide.createIcons();
        return;
    }

    el.innerHTML = items.map(item => `
        <div class="cart-item">
            <span style="font-size:20px">${item.icon}</span>
            <div class="ci-name">${item.name}</div>
            <div class="ci-qty">
                <button onclick="changeQty(${item.id},-1)">−</button>
                <span style="font-size:.8rem;font-weight:700;min-width:20px;text-align:center">${item.qty}</span>
                <button onclick="changeQty(${item.id},1)">+</button>
            </div>
            <div class="ci-price">₱${(item.price*item.qty).toLocaleString('en-PH',{minimumFractionDigits:2})}</div>
        </div>`).join('');
    lucide.createIcons();
}

function openPaymentModal() {
    const items = Object.values(cart);
    if (!items.length) return;
    const subtotal = items.reduce((s,i)=>s+i.price*i.qty,0);
    const tax = subtotal * TAX_RATE;
    const total = subtotal + tax;

    let formItems = '';
    items.forEach((item,idx) => {
        formItems += `<input type="hidden" name="items[${idx}][product_id]" value="${item.id}">`;
        formItems += `<input type="hidden" name="items[${idx}][quantity]" value="${item.qty}">`;
    });
    document.getElementById('form-items').innerHTML = formItems;

    document.getElementById('payment-summary').innerHTML = items.map(i=>
        `<div style="display:flex;justify-content:space-between;padding:3px 0"><span>${i.qty}× ${i.name}</span><span style="font-weight:600">₱${(i.price*i.qty).toLocaleString('en-PH',{minimumFractionDigits:2})}</span></div>`
    ).join('') + `<div style="border-top:1px dashed var(--border2);margin-top:6px;padding-top:6px;display:flex;justify-content:space-between;font-weight:700"><span>Total (incl. tax)</span><span style="color:var(--accent)">₱${total.toLocaleString('en-PH',{minimumFractionDigits:2})}</span></div>`;

    document.getElementById('amount-tendered').value = Math.ceil(total/100)*100;
    calcChange();
    openModal('payment-modal');
}

function calcChange() {
    const items = Object.values(cart);
    const subtotal = items.reduce((s,i)=>s+i.price*i.qty,0);
    const total = subtotal * (1 + TAX_RATE);
    const tendered = parseFloat(document.getElementById('amount-tendered').value) || 0;
    const change = Math.max(0, tendered - total);
    document.getElementById('change-display').textContent = '₱' + change.toLocaleString('en-PH',{minimumFractionDigits:2});
}

function filterProducts(val) {
    document.querySelectorAll('.product-card').forEach(card => {
        card.style.display = card.querySelector('.p-name').textContent.toLowerCase().includes(val.toLowerCase()) ? '' : 'none';
    });
}

function filterByCategory(catId, btn) {
    document.querySelectorAll('.cat-tab').forEach(t=>t.classList.remove('active'));
    btn.classList.add('active');
    document.querySelectorAll('.product-card').forEach(card => {
        card.style.display = (catId === null || card.dataset.cat == catId) ? '' : 'none';
    });
}

function updatePayMethod() {
    document.querySelectorAll('[id^="pm-"]').forEach(el => {
        const radio = el.querySelector('input[type=radio]');
        el.style.borderColor = radio.checked ? 'var(--accent)' : '';
        el.style.background  = radio.checked ? 'var(--accent-soft)' : '';
        el.style.color       = radio.checked ? 'var(--accent)' : '';
    });
}
updatePayMethod();
</script>
@endpush
@endsection