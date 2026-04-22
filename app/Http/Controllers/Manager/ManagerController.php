<?php
// app/Http/Controllers/Manager/ManagerController.php
namespace App\Http\Controllers\Manager;
use App\Http\Controllers\Controller;
use App\Models\{Product,Customer,Transaction,TransactionItem,User};
use Illuminate\Http\Request;

class ManagerController extends Controller {

    public function dashboard() {
        $todaySales   = Transaction::whereDate('created_at',today())->where('status','completed')->sum('total_amount');
        $todayCount   = Transaction::whereDate('created_at',today())->where('status','completed')->count();
        $activeStaff  = User::where('status','active')->whereIn('role',['cashier'])->count();
        $lowStockCount= \App\Models\Product::whereRaw('stock <= low_stock_threshold')->count();
        $recentTx     = Transaction::with(['cashier','customer'])->latest()->take(6)->get();
        $weekSales    = Transaction::selectRaw('DAYOFWEEK(created_at) as day, SUM(total_amount) as total')
            ->where('status','completed')->where('created_at','>=',now()->startOfWeek())
            ->groupBy('day')->orderBy('day')->get()->keyBy('day');
        return view('manager.dashboard', compact('todaySales','todayCount','activeStaff','lowStockCount','recentTx','weekSales'));
    }

    public function sales(Request $request) {
        $transactions = Transaction::with(['cashier','customer'])
            ->when($request->cashier, fn($q)=>$q->where('cashier_id',$request->cashier))
            ->when($request->status, fn($q)=>$q->where('status',$request->status))
            ->when($request->date_from, fn($q)=>$q->whereDate('created_at','>=',$request->date_from))
            ->when($request->date_to,   fn($q)=>$q->whereDate('created_at','<=',$request->date_to))
            ->latest()->paginate(20);
        $totalRevenue = Transaction::where('status','completed')->sum('total_amount');
        $cashiers = User::where('role','cashier')->get();
        return view('manager.sales', compact('transactions','totalRevenue','cashiers'));
    }

    public function inventory() {
        $products  = Product::with('category')->latest()->paginate(20);
        $lowStock  = Product::whereRaw('stock <= low_stock_threshold')->count();
        return view('manager.inventory', compact('products','lowStock'));
    }

    public function customers(Request $request) {
        $customers = Customer::when($request->search,fn($q)=>$q->where('name','like',"%{$request->search}%")->orWhere('email','like',"%{$request->search}%"))->orderByDesc('total_spent')->paginate(15);
        return view('manager.customers', compact('customers'));
    }

    public function reports() {
        $totalRevenue = Transaction::where('status','completed')->sum('total_amount');
        $totalTax     = Transaction::where('status','completed')->sum('tax_amount');
        $topProducts  = TransactionItem::selectRaw('product_name, SUM(quantity) as qty_sold, SUM(subtotal) as revenue')
            ->groupBy('product_name')->orderByDesc('qty_sold')->take(5)->get();
        $byCashier    = Transaction::selectRaw('cashier_id, SUM(total_amount) as total, COUNT(*) as count')
            ->where('status','completed')->groupBy('cashier_id')->with('cashier')->get();
        $dailySales   = Transaction::selectRaw('DATE(created_at) as date, SUM(total_amount) as total')
            ->where('status','completed')->where('created_at','>=',now()->subDays(7))
            ->groupBy('date')->orderBy('date')->get();
        return view('manager.reports', compact('totalRevenue','totalTax','topProducts','byCashier','dailySales'));
    }
}
