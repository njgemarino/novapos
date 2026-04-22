<?php
// app/Models/User.php
namespace App\Models;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable {
    use Notifiable, SoftDeletes;
    protected $fillable = ['name','email','password','role','status','last_login_at'];
    protected $hidden   = ['password','remember_token'];
    protected $casts    = ['last_login_at'=>'datetime'];

    public function getRoleLabel(): string {
        return match($this->role) {
            'admin'     => 'Business Owner',
            'manager'   => 'Store Manager',
            'cashier'   => 'Cashier',
            'inventory' => 'Inventory Staff',
            default     => ucfirst($this->role),
        };
    }
    public function transactions() { return $this->hasMany(Transaction::class,'cashier_id'); }
}
