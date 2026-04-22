<?php
// database/seeders/DatabaseSeeder.php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\{User, Category, Product, Customer, Transaction, TransactionItem, Setting};

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Users
        $admin = User::create(['name'=>'Marco Gemarino','email'=>'admin@store.com','password'=>Hash::make('password'),'role'=>'admin','status'=>'active']);
        $manager = User::create(['name'=>'Maria Santos','email'=>'manager@store.com','password'=>Hash::make('password'),'role'=>'manager','status'=>'active']);
        $cashier = User::create(['name'=>'Juan dela Cruz','email'=>'cashier@store.com','password'=>Hash::make('password'),'role'=>'cashier','status'=>'active']);
        $inventory = User::create(['name'=>'Carlo Reyes','email'=>'inventory@store.com','password'=>Hash::make('password'),'role'=>'inventory','status'=>'active']);
        User::create(['name'=>'Ana Gomez','email'=>'ana@store.com','password'=>Hash::make('password'),'role'=>'cashier','status'=>'inactive']);

        // Categories
        $bev = Category::create(['name'=>'Beverages','icon'=>'🥤']);
        $food = Category::create(['name'=>'Food','icon'=>'🍽️']);
        $snacks = Category::create(['name'=>'Snacks','icon'=>'🍿']);

        // Products
        $products = [
            ['name'=>'Espresso','price'=>120,'cost_price'=>40,'stock'=>50,'icon'=>'☕','category_id'=>$bev->id,'low_stock_threshold'=>10],
            ['name'=>'Latte','price'=>150,'cost_price'=>55,'stock'=>40,'icon'=>'🥛','category_id'=>$bev->id,'low_stock_threshold'=>10],
            ['name'=>'Cappuccino','price'=>145,'cost_price'=>50,'stock'=>35,'icon'=>'☕','category_id'=>$bev->id,'low_stock_threshold'=>10],
            ['name'=>'Croissant','price'=>85,'cost_price'=>30,'stock'=>25,'icon'=>'🥐','category_id'=>$food->id,'low_stock_threshold'=>5],
            ['name'=>'Sandwich','price'=>180,'cost_price'=>70,'stock'=>18,'icon'=>'🥪','category_id'=>$food->id,'low_stock_threshold'=>5],
            ['name'=>'Cola','price'=>60,'cost_price'=>20,'stock'=>100,'icon'=>'🥤','category_id'=>$bev->id,'low_stock_threshold'=>20],
            ['name'=>'Water','price'=>30,'cost_price'=>8,'stock'=>200,'icon'=>'💧','category_id'=>$bev->id,'low_stock_threshold'=>30],
            ['name'=>'Muffin','price'=>95,'cost_price'=>35,'stock'=>8,'icon'=>'🧁','category_id'=>$snacks->id,'low_stock_threshold'=>10],
            ['name'=>'Orange Juice','price'=>110,'cost_price'=>40,'stock'=>12,'icon'=>'🍊','category_id'=>$bev->id,'low_stock_threshold'=>10],
            ['name'=>'Chips','price'=>55,'cost_price'=>18,'stock'=>60,'icon'=>'🍿','category_id'=>$snacks->id,'low_stock_threshold'=>15],
        ];
        foreach ($products as $p) Product::create($p);

        // Customers
        $customers = [
            ['name'=>'Maria Santos','email'=>'maria@email.com','phone'=>'09171234567','total_spent'=>4850,'total_purchases'=>12,'last_visit_at'=>now()->subHours(2)],
            ['name'=>'Juan dela Cruz','email'=>'juan@email.com','phone'=>'09181234567','total_spent'=>2310,'total_purchases'=>7,'last_visit_at'=>now()->subHours(5)],
            ['name'=>'Ana Reyes','email'=>'ana@email.com','phone'=>'09191234567','total_spent'=>8740,'total_purchases'=>19,'last_visit_at'=>now()->subDay()],
            ['name'=>'Carlo Mendoza','email'=>'carlo@email.com','phone'=>'09201234567','total_spent'=>1560,'total_purchases'=>4,'last_visit_at'=>now()->subDays(2)],
            ['name'=>'Liza Gomez','email'=>'liza@email.com','phone'=>'09211234567','total_spent'=>11200,'total_purchases'=>23,'last_visit_at'=>now()->subDays(5)],
        ];
        foreach ($customers as $c) Customer::create($c);

        // Transactions
        $txData = [
            ['cashier_id'=>$cashier->id,'customer_id'=>1,'items'=>[['product_id'=>1,'qty'=>2],['product_id'=>3,'qty'=>1]],'status'=>'completed','payment_method'=>'cash'],
            ['cashier_id'=>$cashier->id,'customer_id'=>2,'items'=>[['product_id'=>2,'qty'=>1]],'status'=>'completed','payment_method'=>'gcash'],
            ['cashier_id'=>$cashier->id,'customer_id'=>null,'items'=>[['product_id'=>4,'qty'=>2],['product_id'=>6,'qty'=>3],['product_id'=>7,'qty'=>2]],'status'=>'completed','payment_method'=>'cash'],
            ['cashier_id'=>$cashier->id,'customer_id'=>3,'items'=>[['product_id'=>1,'qty'=>1],['product_id'=>5,'qty'=>1]],'status'=>'refunded','payment_method'=>'cash'],
            ['cashier_id'=>$cashier->id,'customer_id'=>4,'items'=>[['product_id'=>8,'qty'=>2],['product_id'=>9,'qty'=>1],['product_id'=>10,'qty'=>1]],'status'=>'completed','payment_method'=>'card'],
        ];

        foreach ($txData as $i => $tx) {
            $subtotal = 0;
            $items = [];
            foreach ($tx['items'] as $item) {
                $prod = Product::find($item['product_id']);
                $sub = $prod->price * $item['qty'];
                $subtotal += $sub;
                $items[] = ['product_id'=>$prod->id,'product_name'=>$prod->name,'unit_price'=>$prod->price,'quantity'=>$item['qty'],'subtotal'=>$sub];
            }
            $tax = round($subtotal * 0.12, 2);
            $total = $subtotal + $tax;
            $t = Transaction::create([
                'receipt_no'  => 'TXN-' . str_pad($i+37, 4, '0', STR_PAD_LEFT),
                'cashier_id'  => $tx['cashier_id'],
                'customer_id' => $tx['customer_id'],
                'subtotal'    => $subtotal,
                'tax_amount'  => $tax,
                'total_amount'=> $total,
                'amount_tendered' => ceil($total/100)*100,
                'change_amount'   => ceil($total/100)*100 - $total,
                'payment_method'  => $tx['payment_method'],
                'status'          => $tx['status'],
                'created_at'      => now()->subDays(rand(0,3))->subHours(rand(0,10)),
            ]);
            foreach ($items as $item) TransactionItem::create(array_merge(['transaction_id'=>$t->id], $item));
        }

        // Settings
        $defaults = [
            'store_name'       => 'Gemarino Store',
            'store_address'    => '123 Sample Street, Quezon City, PH',
            'store_phone'      => '+63 912 345 6789',
            'tax_rate'         => '12',
            'currency_symbol'  => '₱',
            'receipt_header'   => 'Thank you for shopping!',
            'receipt_footer'   => 'Please come again!',
            'low_stock_threshold' => '15',
        ];
        foreach ($defaults as $k => $v) Setting::create(['key'=>$k,'value'=>$v]);
    }
}
