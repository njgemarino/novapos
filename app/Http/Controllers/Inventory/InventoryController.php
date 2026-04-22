<?php
// app/Http/Controllers/Inventory/InventoryController.php
namespace App\Http\Controllers\Inventory;
use App\Http\Controllers\Controller;
use App\Models\{Product,Category,StockMovement};
use Illuminate\Http\Request;

class InventoryController extends Controller {

    public function dashboard() {
        $totalProducts = Product::where('is_active',true)->count();
        $lowStockCount = Product::whereRaw('stock <= low_stock_threshold')->where('is_active',true)->count();
        $outOfStock    = Product::where('stock',0)->where('is_active',true)->count();
        $recentMovements = StockMovement::with(['product','user'])->latest()->take(8)->get();
        $lowStockItems = Product::whereRaw('stock <= low_stock_threshold')->where('is_active',true)->take(5)->get();
        return view('inventory.dashboard', compact('totalProducts','lowStockCount','outOfStock','recentMovements','lowStockItems'));
    }

    public function products(Request $request) {
        $products = Product::with('category')
            ->when($request->search, fn($q)=>$q->where('name','like',"%{$request->search}%")->orWhere('barcode','like',"%{$request->search}%"))
            ->when($request->category, fn($q)=>$q->where('category_id',$request->category))
            ->when($request->filter === 'low', fn($q)=>$q->whereRaw('stock <= low_stock_threshold'))
            ->when($request->filter === 'out',  fn($q)=>$q->where('stock',0))
            ->latest()->paginate(20);
        $categories = Category::all();
        return view('inventory.products', compact('products','categories'));
    }

    public function stockMovement(Request $request) {
        $movements = StockMovement::with(['product','user'])
            ->when($request->type, fn($q)=>$q->where('type',$request->type))
            ->latest()->paginate(20);
        return view('inventory.stock_movement', compact('movements'));
    }

    public function adjustStock(Request $request, Product $product) {
        $data = $request->validate([
            'quantity' => 'required|integer|not_in:0',
            'type'     => 'required|in:in,out,adjustment',
            'notes'    => 'nullable|string|max:200',
        ]);
        $before = $product->stock;
        if ($data['type'] === 'adjustment') {
            $newStock = abs($data['quantity']);
        } elseif ($data['type'] === 'in') {
            $newStock = $product->stock + abs($data['quantity']);
        } else {
            $newStock = max(0, $product->stock - abs($data['quantity']));
        }
        $product->update(['stock' => $newStock]);
        StockMovement::create(['product_id'=>$product->id,'user_id'=>auth()->id(),'type'=>$data['type'],'quantity'=>$data['quantity'],'stock_before'=>$before,'stock_after'=>$newStock,'notes'=>$data['notes']??null]);
        return redirect()->back()->with('success','Stock updated successfully.');
    }

    public function lowStock() {
        $products = Product::with('category')->whereRaw('stock <= low_stock_threshold')->where('is_active',true)->orderBy('stock')->get();
        return view('inventory.low_stock', compact('products'));
    }
}
