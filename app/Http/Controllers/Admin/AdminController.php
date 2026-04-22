<?php
// app/Http/Controllers/Admin/AdminController.php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\{User,Product,Category,Customer,Transaction,StockMovement,Setting};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller {

    // ── Dashboard ──────────────────────────────────────────────────
    public function dashboard() {
        $todaySales   = Transaction::whereDate('created_at',today())->where('status','completed')->sum('total_amount');
        $todayTxCount = Transaction::whereDate('created_at',today())->where('status','completed')->count();
        $totalCustomers = Customer::count();
        $lowStockCount  = Product::whereRaw('stock <= low_stock_threshold')->where('is_active',true)->count();
        $recentTx = Transaction::with(['cashier','customer'])->latest()->take(8)->get();
        $monthlySales = Transaction::selectRaw('MONTH(created_at) as month, SUM(total_amount) as total')
            ->whereYear('created_at',date('Y'))->where('status','completed')
            ->groupBy('month')->orderBy('month')->get()->keyBy('month');
        return view('admin.dashboard', compact('todaySales','todayTxCount','totalCustomers','lowStockCount','recentTx','monthlySales'));
    }

    // ── Products ────────────────────────────────────────────────────
    public function products(Request $request) {
        $products = Product::with('category')
            ->when($request->search, fn($q) => $q->where('name','like',"%{$request->search}%"))
            ->when($request->category, fn($q) => $q->where('category_id',$request->category))
            ->latest()->paginate(15);
        $categories = Category::all();
        $lowStock = Product::whereRaw('stock <= low_stock_threshold')->count();
        return view('admin.products.index', compact('products','categories','lowStock'));
    }
    public function createProduct() { return view('admin.products.create', ['categories'=>Category::all()]); }
    public function storeProduct(Request $request) {
        $data = $request->validate([
            'name'               => 'required|string|max:100',
            'category_id'        => 'nullable|exists:categories,id',
            'price'              => 'required|numeric|min:0',
            'cost_price'         => 'required|numeric|min:0',
            'stock'              => 'required|integer|min:0',
            'low_stock_threshold'=> 'required|integer|min:0',
            'unit'               => 'required|string|max:20',
            'icon'               => 'nullable|string|max:10',
            'barcode'            => 'nullable|string|unique:products,barcode',
        ]);
        $data['is_active'] = true;
        Product::create($data);
        return redirect()->route('admin.products')->with('success','Product created.');
    }
    public function editProduct(Product $product) { return view('admin.products.edit', compact('product'), ['categories'=>Category::all()]); }
    public function updateProduct(Request $request, Product $product) {
        $data = $request->validate([
            'name'               => 'required|string|max:100',
            'category_id'        => 'nullable|exists:categories,id',
            'price'              => 'required|numeric|min:0',
            'cost_price'         => 'required|numeric|min:0',
            'low_stock_threshold'=> 'required|integer|min:0',
            'unit'               => 'required|string|max:20',
            'icon'               => 'nullable|string|max:10',
            'is_active'          => 'boolean',
        ]);
        $product->update($data);
        return redirect()->route('admin.products')->with('success','Product updated.');
    }
    public function destroyProduct(Product $product) {
        $product->delete();
        return redirect()->route('admin.products')->with('success','Product deleted.');
    }
    public function adjustStock(Request $request, Product $product) {
        $data = $request->validate([
            'quantity' => 'required|integer',
            'type'     => 'required|in:in,out,adjustment',
            'notes'    => 'nullable|string|max:200',
        ]);
        $before = $product->stock;
        $product->stock = max(0, $product->stock + ($data['type']==='out' ? -abs($data['quantity']) : abs($data['quantity'])));
        if ($data['type']==='adjustment') $product->stock = abs($data['quantity']);
        $product->save();
        StockMovement::create(['product_id'=>$product->id,'user_id'=>auth()->id(),'type'=>$data['type'],'quantity'=>$data['quantity'],'stock_before'=>$before,'stock_after'=>$product->stock,'notes'=>$data['notes']??null]);
        return redirect()->back()->with('success','Stock adjusted.');
    }

    // ── Customers ───────────────────────────────────────────────────
    public function customers(Request $request) {
        $customers = Customer::when($request->search, fn($q)=>$q->where('name','like',"%{$request->search}%")->orWhere('email','like',"%{$request->search}%"))->latest()->paginate(15);
        return view('admin.customers.index', compact('customers'));
    }
    public function storeCustomer(Request $request) {
        $data = $request->validate(['name'=>'required|string|max:100','email'=>'nullable|email|unique:customers,email','phone'=>'nullable|string|max:20','address'=>'nullable|string|max:200']);
        Customer::create($data);
        return redirect()->route('admin.customers')->with('success','Customer added.');
    }
    public function destroyCustomer(Customer $customer) {
        $customer->delete();
        return redirect()->route('admin.customers')->with('success','Customer deleted.');
    }

    // ── Users ───────────────────────────────────────────────────────
    public function users(Request $request) {
        $users = User::when($request->role, fn($q)=>$q->where('role',$request->role))->latest()->paginate(15);
        return view('admin.users.index', compact('users'));
    }
    public function storeUser(Request $request) {
        $data = $request->validate(['name'=>'required|string|max:100','email'=>'required|email|unique:users','password'=>'required|min:8|confirmed','role'=>'required|in:admin,manager,cashier,inventory','status'=>'required|in:active,inactive']);
        $data['password'] = Hash::make($data['password']);
        User::create($data);
        return redirect()->route('admin.users')->with('success','User created.');
    }
    public function updateUser(Request $request, User $user) {
        $data = $request->validate(['name'=>'required|string|max:100','email'=>"required|email|unique:users,email,{$user->id}",'role'=>'required|in:admin,manager,cashier,inventory','status'=>'required|in:active,inactive']);
        if ($request->filled('password')) {
            $request->validate(['password'=>'min:8|confirmed']);
            $data['password'] = Hash::make($request->password);
        }
        $user->update($data);
        return redirect()->route('admin.users')->with('success','User updated.');
    }
    public function destroyUser(User $user) {
        if ($user->id === auth()->id()) return back()->with('error','Cannot delete yourself.');
        $user->delete();
        return redirect()->route('admin.users')->with('success','User deleted.');
    }

    // ── Transactions ────────────────────────────────────────────────
    public function transactions(Request $request) {
        $transactions = Transaction::with(['cashier','customer'])
            ->when($request->status, fn($q)=>$q->where('status',$request->status))
            ->when($request->date, fn($q)=>$q->whereDate('created_at',$request->date))
            ->latest()->paginate(20);
        $totalRevenue = Transaction::where('status','completed')->sum('total_amount');
        return view('admin.transactions.index', compact('transactions','totalRevenue'));
    }
    public function voidTransaction(Transaction $transaction) {
        $transaction->update(['status'=>'voided']);
        return redirect()->back()->with('success','Transaction voided.');
    }

    // ── Reports ─────────────────────────────────────────────────────
    public function reports() {
        $totalRevenue = Transaction::where('status','completed')->sum('total_amount');
        $totalTax     = Transaction::where('status','completed')->sum('tax_amount');
        $txByStatus   = Transaction::selectRaw('status, count(*) as total')->groupBy('status')->get();
        $topProducts  = TransactionItem::selectRaw('product_name, SUM(quantity) as qty_sold, SUM(subtotal) as revenue')
            ->groupBy('product_name')->orderByDesc('qty_sold')->take(5)->get();
        $dailySales   = Transaction::selectRaw('DATE(created_at) as date, SUM(total_amount) as total')
            ->where('status','completed')->where('created_at','>=',now()->subDays(7))
            ->groupBy('date')->orderBy('date')->get();
        return view('admin.reports', compact('totalRevenue','totalTax','txByStatus','topProducts','dailySales'));
    }

    // ── Settings ────────────────────────────────────────────────────
    public function settings() {
        $settings = Setting::pluck('value','key');
        return view('admin.settings', compact('settings'));
    }
    public function updateSettings(Request $request) {
        $data = $request->validate([
            'store_name'    => 'required|string|max:100',
            'store_address' => 'nullable|string|max:200',
            'store_phone'   => 'nullable|string|max:20',
            'tax_rate'      => 'required|numeric|min:0|max:100',
            'currency_symbol'     => 'required|string|max:5',
            'receipt_header'      => 'nullable|string|max:100',
            'receipt_footer'      => 'nullable|string|max:100',
            'low_stock_threshold' => 'required|integer|min:0',
        ]);
        foreach ($data as $k => $v) Setting::set($k, $v);
        return redirect()->route('admin.settings')->with('success','Settings saved.');
    }
}
