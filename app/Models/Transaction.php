<?php
// app/Models/Transaction.php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Transaction extends Model {
    protected $fillable = ['receipt_no','cashier_id','customer_id','subtotal','tax_amount','discount_amount','total_amount','amount_tendered','change_amount','payment_method','status','notes'];
    protected $casts = ['subtotal'=>'decimal:2','tax_amount'=>'decimal:2','total_amount'=>'decimal:2'];
    public function cashier()  { return $this->belongsTo(User::class,'cashier_id'); }
    public function customer() { return $this->belongsTo(Customer::class); }
    public function items()    { return $this->hasMany(TransactionItem::class); }
}
