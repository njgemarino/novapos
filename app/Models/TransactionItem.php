<?php
// app/Models/TransactionItem.php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class TransactionItem extends Model {
    protected $fillable = ['transaction_id','product_id','product_name','unit_price','quantity','subtotal'];
    public function transaction() { return $this->belongsTo(Transaction::class); }
    public function product()     { return $this->belongsTo(Product::class); }
}
