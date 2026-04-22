<?php
// app/Http/Controllers/Auth/LoginController.php
namespace App\Http\Controllers\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller {
    public function showLogin() {
        if (Auth::check()) return $this->redirectRole(Auth::user()->role);
        return view('auth.login');
    }
    public function login(Request $request) {
        $credentials = $request->validate(['email'=>'required|email','password'=>'required']);
        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            Auth::user()->update(['last_login_at'=>now()]);
            return $this->redirectRole(Auth::user()->role);
        }
        return back()->withErrors(['email'=>'Invalid credentials.'])->onlyInput('email');
    }
    public function logout(Request $request) {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
    private function redirectRole(string $role) {
        return match($role) {
            'admin'     => redirect()->route('admin.dashboard'),
            'manager'   => redirect()->route('manager.dashboard'),
            'cashier'   => redirect()->route('cashier.dashboard'),
            'inventory' => redirect()->route('inventory.dashboard'),
            default     => redirect()->route('login'),
        };
    }
}
