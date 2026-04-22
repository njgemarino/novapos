<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NovaPOS — Sign In</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&family=Syne:wght@700;800&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js"></script>
    <style>
        :root { --accent:#4f63ff; --red:#ef4444; --green:#10b981; --text:#1a1d2e; --muted:#6b7280; --border:#e2e6f0; --border2:#d0d5e8; --surface:#fff; --bg:#f5f6fa; }
        *{box-sizing:border-box;margin:0;padding:0;}
        body{font-family:'Plus Jakarta Sans',sans-serif;background:var(--bg);min-height:100vh;display:flex;align-items:center;justify-content:center;padding:20px;}

        .login-wrapper{width:100%;max-width:440px;}
        .login-card{background:var(--surface);border:1px solid var(--border);border-radius:18px;padding:36px 32px 32px;box-shadow:0 8px 32px rgba(0,0,0,.08);}

        .login-brand{display:flex;align-items:center;gap:10px;margin-bottom:28px;}
        .brand-icon{width:42px;height:42px;background:linear-gradient(135deg,#4f63ff,#7c3aed);border-radius:11px;display:flex;align-items:center;justify-content:center;box-shadow:0 4px 12px rgba(79,99,255,.3);}
        .brand-name{font-family:'Syne',sans-serif;font-size:1.3rem;font-weight:800;color:var(--text);}
        .brand-name span{color:var(--accent);}
        .brand-sub{font-size:.7rem;color:var(--muted);letter-spacing:.06em;text-transform:uppercase;margin-top:1px;}

        .login-card h2{font-family:'Syne',sans-serif;font-size:1.15rem;font-weight:800;color:var(--text);margin-bottom:4px;}
        .login-card .sub{font-size:.78rem;color:var(--muted);margin-bottom:24px;}

        .role-grid{display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-bottom:22px;}
        .role-opt{padding:10px 12px;border-radius:9px;background:var(--bg);border:1.5px solid var(--border);cursor:pointer;transition:all .12s;}
        .role-opt:hover{border-color:var(--accent);}
        .role-opt.selected{border-color:var(--accent);background:#eef0ff;}
        .r-icon{font-size:18px;margin-bottom:4px;}
        .r-name{font-size:.77rem;font-weight:700;color:var(--text);}
        .r-desc{font-size:.67rem;color:var(--muted);margin-top:1px;}

        .form-group{margin-bottom:14px;}
        .form-group label{display:block;font-size:.67rem;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.08em;margin-bottom:5px;}
        .input-wrap{position:relative;}
        .input-wrap i{position:absolute;left:11px;top:50%;transform:translateY(-50%);width:15px;height:15px;color:var(--muted);pointer-events:none;}
        .form-control{width:100%;padding:9px 11px 9px 34px;background:var(--bg);border:1.5px solid var(--border2);border-radius:8px;color:var(--text);font-size:.83rem;font-family:inherit;transition:border-color .12s,box-shadow .12s;}
        .form-control:focus{outline:none;border-color:var(--accent);box-shadow:0 0 0 3px rgba(79,99,255,.1);background:#fff;}

        .btn-login{width:100%;padding:11px;border-radius:9px;background:linear-gradient(135deg,#4f63ff,#7c3aed);border:none;color:#fff;font-size:.88rem;font-weight:700;font-family:'Syne',sans-serif;cursor:pointer;box-shadow:0 4px 12px rgba(79,99,255,.3);transition:all .15s;margin-top:6px;}
        .btn-login:hover{transform:translateY(-1px);box-shadow:0 6px 18px rgba(79,99,255,.4);}

        .error-box{background:#fef2f2;border:1px solid #fecaca;border-radius:8px;padding:9px 12px;font-size:.78rem;color:var(--red);margin-bottom:14px;display:flex;align-items:center;gap:7px;}

        .demo-hint{margin-top:20px;padding:13px;background:var(--bg);border:1px solid var(--border);border-radius:9px;}
        .demo-hint .dh-title{font-size:.63rem;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.09em;margin-bottom:8px;}
        .demo-row{display:flex;justify-content:space-between;padding:4px 0;border-bottom:1px solid var(--border);font-size:.72rem;}
        .demo-row:last-child{border:none;}
        .demo-row .dr{font-weight:600;color:var(--text);}
        .demo-row .de{color:var(--muted);font-family:monospace;}
    </style>
</head>
<body>
    <div class="login-wrapper">
        <div class="login-card">
            <div class="login-brand">
                <div class="brand-icon"><i data-lucide="shopping-cart" style="width:20px;height:20px;color:#fff"></i></div>
                <div>
                    <div class="brand-name">Nova<span>POS</span></div>
                    <div class="brand-sub">Gemarino Store</div>
                </div>
            </div>
            <h2>Welcome back</h2>
            <p class="sub">Sign in to your account to continue</p>

            @if($errors->any())
            <div class="error-box"><i data-lucide="alert-circle"></i>{{ $errors->first() }}</div>
            @endif

            <form method="POST" action="{{ route('login.post') }}">
                @csrf
                <div class="form-group">
                    <label>Email Address</label>
                    <div class="input-wrap">
                        <i data-lucide="mail"></i>
                        <input type="email" name="email" value="{{ old('email') }}" class="form-control" placeholder="you@store.com" required autofocus>
                    </div>
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <div class="input-wrap">
                        <i data-lucide="lock"></i>
                        <input type="password" name="password" class="form-control" placeholder="••••••••" required>
                    </div>
                </div>
                <button type="submit" class="btn-login">Sign In →</button>
            </form>

            <div class="demo-hint">
                <div class="dh-title">Demo Accounts — password: password</div>
                <div class="demo-row"><span class="dr">Business Owner</span><span class="de">admin@store.com</span></div>
                <div class="demo-row"><span class="dr">Store Manager</span><span class="de">manager@store.com</span></div>
                <div class="demo-row"><span class="dr">Cashier</span><span class="de">cashier@store.com</span></div>
                <div class="demo-row"><span class="dr">Inventory Staff</span><span class="de">inventory@store.com</span></div>
            </div>
        </div>
    </div>
    <script>lucide.createIcons();</script>
</body>
</html>
