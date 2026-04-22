<?php
// app/Models/Product.php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Product extends Model {
    use SoftDeletes;
    protected $fillable = ['name','barcode','category_id','price','cost_price','stock','low_stock_threshold','unit','icon','is_active'];
    public function category() { return $this->belongsTo(Category::class); }
    public function isLowStock(): bool { return $this->stock <= $this->low_stock_threshold; }
    public function transactionItems() { return $this->hasMany(TransactionItem::class); }
    public function stockMovements() { return $this->hasMany(StockMovement::class); }
}
