<?php
// app/Http/Controllers/Cashier/CashierController.php
namespace App\Http\Controllers\Cashier;
use App\Http\Controllers\Controller;
use App\Models\{Product,Customer,Transaction,TransactionItem,Category,StockMovement};
use Illuminate\Http\Request;

class CashierController extends Controller {

    public function dashboard() {
        $myTodaySales = Transaction::where('cashier_id',auth()->id())->whereDate('created_at',today())->where('status','completed')->sum('total_amount');
        $myTodayCount = Transaction::where('cashier_id',auth()->id())->whereDate('created_at',today())->count();
        $myAvg = $myTodayCount > 0 ? $myTodaySales / $myTodayCount : 0;
        $myRefunds = Transaction::where('cashier_id',auth()->id())->whereDate('created_at',today())->where('status','refunded')->count();
        $recentTx = Transaction::where('cashier_id',auth()->id())->with('customer')->latest()->take(5)->get();
        return view('cashier.dashboard', compact('myTodaySales','myTodayCount','myAvg','myRefunds','recentTx'));
    }

    public function pos() {
        $products   = Product::with('category')->where('is_active',true)->where('stock','>',0)->get();
        $categories = Category::all();
        $customers  = Customer::orderBy('name')->get();
        return view('cashier.pos', compact('products','categories','customers'));
    }

    public function processTransaction(Request $request) {
        $data = $request->validate([
            'items'          => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity'   => 'required|integer|min:1',
            'customer_id'    => 'nullable|exists:customers,id',
            'payment_method' => 'required|in:cash,card,gcash,other',
            'amount_tendered'=> 'required|numeric|min:0',
        ]);

        $subtotal = 0;
        $lineItems = [];
        foreach ($data['items'] as $item) {
            $product = Product::findOrFail($item['product_id']);
            if ($product->stock < $item['quantity']) {
                return back()->withErrors(["Insufficient stock for {$product->name}."]);
            }
            $sub = $product->price * $item['quantity'];
            $subtotal += $sub;
            $lineItems[] = ['product'=>$product,'quantity'=>$item['quantity'],'subtotal'=>$sub];
        }

        $taxRate   = (float) \App\Models\Setting::get('tax_rate', 12) / 100;
        $tax       = round($subtotal * $taxRate, 2);
        $total     = $subtotal + $tax;
        $change    = $data['amount_tendered'] - $total;

        $tx = Transaction::create([
            'receipt_no'     => 'TXN-' . str_pad(Transaction::max('id') + 1, 4, '0', STR_PAD_LEFT),
            'cashier_id'     => auth()->id(),
            'customer_id'    => $data['customer_id'] ?? null,
            'subtotal'       => $subtotal,
            'tax_amount'     => $tax,
            'total_amount'   => $total,
            'amount_tendered'=> $data['amount_tendered'],
            'change_amount'  => max(0, $change),
            'payment_method' => $data['payment_method'],
            'status'         => 'completed',
        ]);

        foreach ($lineItems as $line) {
            TransactionItem::create([
                'transaction_id' => $tx->id,
                'product_id'     => $line['product']->id,
                'product_name'   => $line['product']->name,
                'unit_price'     => $line['product']->price,
                'quantity'       => $line['quantity'],
                'subtotal'       => $line['subtotal'],
            ]);
            $before = $line['product']->stock;
            $line['product']->decrement('stock', $line['quantity']);
            StockMovement::create(['product_id'=>$line['product']->id,'user_id'=>auth()->id(),'type'=>'sale','quantity'=>-$line['quantity'],'stock_before'=>$before,'stock_after'=>$before-$line['quantity'],'reference'=>$tx->receipt_no]);
        }

        if ($data['customer_id']) {
            $customer = Customer::find($data['customer_id']);
            $customer->increment('total_spent', $total);
            $customer->increment('total_purchases');
            $customer->update(['last_visit_at'=>now()]);
        }

        return redirect()->route('cashier.receipt', $tx)->with('success','Payment processed!');
    }

    public function receipt(Transaction $transaction) {
        $transaction->load(['items','customer','cashier']);
        $settings = \App\Models\Setting::pluck('value','key');
        return view('cashier.receipt', compact('transaction','settings'));
    }

    public function transactions(Request $request) {
        $transactions = Transaction::where('cashier_id',auth()->id())
            ->with('customer')->when($request->status,fn($q)=>$q->where('status',$request->status))
            ->latest()->paginate(15);
        return view('cashier.transactions', compact('transactions'));
    }

    public function customers(Request $request) {
        $customers = Customer::when($request->search,fn($q)=>$q->where('name','like',"%{$request->search}%"))->latest()->paginate(15);
        return view('cashier.customers', compact('customers'));
    }

    public function storeCustomer(Request $request) {
        $data = $request->validate(['name'=>'required|string|max:100','email'=>'nullable|email|unique:customers,email','phone'=>'nullable|string|max:20']);
        Customer::create($data);
        return redirect()->route('cashier.customers')->with('success','Customer added.');
    }
}
