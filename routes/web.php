<?php
// routes/web.php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Manager\ManagerController;
use App\Http\Controllers\Cashier\CashierController;
use App\Http\Controllers\Inventory\InventoryController;

Route::get('/', fn() => redirect()->route('login'));
Route::get('/login',  [LoginController::class,'showLogin'])->name('login');
Route::post('/login', [LoginController::class,'login'])->name('login.post');
Route::post('/logout',[LoginController::class,'logout'])->name('logout');

// ── Admin (Business Owner) ──────────────────────────────────────────
Route::prefix('admin')->name('admin.')->middleware(['auth','role:admin'])->group(function () {
    Route::get('/dashboard', [AdminController::class,'dashboard'])->name('dashboard');

    Route::get('/products',                  [AdminController::class,'products'])->name('products');
    Route::get('/products/create',           [AdminController::class,'createProduct'])->name('products.create');
    Route::post('/products',                 [AdminController::class,'storeProduct'])->name('products.store');
    Route::get('/products/{product}/edit',   [AdminController::class,'editProduct'])->name('products.edit');
    Route::put('/products/{product}',        [AdminController::class,'updateProduct'])->name('products.update');
    Route::delete('/products/{product}',     [AdminController::class,'destroyProduct'])->name('products.destroy');
    Route::post('/products/{product}/stock', [AdminController::class,'adjustStock'])->name('products.stock');

    Route::get('/customers',           [AdminController::class,'customers'])->name('customers');
    Route::post('/customers',          [AdminController::class,'storeCustomer'])->name('customers.store');
    Route::delete('/customers/{customer}',[AdminController::class,'destroyCustomer'])->name('customers.destroy');

    Route::get('/users',           [AdminController::class,'users'])->name('users');
    Route::post('/users',          [AdminController::class,'storeUser'])->name('users.store');
    Route::put('/users/{user}',    [AdminController::class,'updateUser'])->name('users.update');
    Route::delete('/users/{user}', [AdminController::class,'destroyUser'])->name('users.destroy');

    Route::get('/transactions',                  [AdminController::class,'transactions'])->name('transactions');
    Route::post('/transactions/{transaction}/void',[AdminController::class,'voidTransaction'])->name('transactions.void');

    Route::get('/reports',  [AdminController::class,'reports'])->name('reports');
    Route::get('/settings', [AdminController::class,'settings'])->name('settings');
    Route::post('/settings',[AdminController::class,'updateSettings'])->name('settings.update');
});

// ── Manager ─────────────────────────────────────────────────────────
Route::prefix('manager')->name('manager.')->middleware(['auth','role:manager,admin'])->group(function () {
    Route::get('/dashboard', [ManagerController::class,'dashboard'])->name('dashboard');
    Route::get('/sales',     [ManagerController::class,'sales'])->name('sales');
    Route::get('/inventory', [ManagerController::class,'inventory'])->name('inventory');
    Route::get('/customers', [ManagerController::class,'customers'])->name('customers');
    Route::get('/reports',   [ManagerController::class,'reports'])->name('reports');
});

// ── Cashier ──────────────────────────────────────────────────────────
Route::prefix('cashier')->name('cashier.')->middleware(['auth','role:cashier,admin,manager'])->group(function () {
    Route::get('/dashboard',         [CashierController::class,'dashboard'])->name('dashboard');
    Route::get('/pos',               [CashierController::class,'pos'])->name('pos');
    Route::post('/pos/process',      [CashierController::class,'processTransaction'])->name('pos.process');
    Route::get('/receipt/{transaction}',[CashierController::class,'receipt'])->name('receipt');
    Route::get('/transactions',      [CashierController::class,'transactions'])->name('transactions');
    Route::get('/customers',         [CashierController::class,'customers'])->name('customers');
    Route::post('/customers',        [CashierController::class,'storeCustomer'])->name('customers.store');
});

// ── Inventory ────────────────────────────────────────────────────────
Route::prefix('inventory')->name('inventory.')->middleware(['auth','role:inventory,admin,manager'])->group(function () {
    Route::get('/dashboard',   [InventoryController::class,'dashboard'])->name('dashboard');
    Route::get('/products',    [InventoryController::class,'products'])->name('products');
    Route::get('/stock',       [InventoryController::class,'stockMovement'])->name('stock');
    Route::post('/products/{product}/adjust',[InventoryController::class,'adjustStock'])->name('products.adjust');
    Route::get('/low-stock',   [InventoryController::class,'lowStock'])->name('low_stock');
});
