<?php
// app/Models/Customer.php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Customer extends Model {
    use SoftDeletes;
    protected $fillable = ['name','email','phone','address','total_spent','total_purchases','last_visit_at'];
    protected $casts = ['last_visit_at'=>'datetime','total_spent'=>'decimal:2'];
    public function transactions() { return $this->hasMany(Transaction::class); }
}
