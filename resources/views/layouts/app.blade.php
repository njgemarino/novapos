<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title','NovaPOS') — Gemarino Store</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js"></script>
    <style>
        :root {
            --bg:         #f4f3ef;
            --surface:    #ffffff;
            --surface2:   #f9f8f5;
            --border:     #e8e6e0;
            --border2:    #dddbd4;
            --text:       #1c1b18;
            --text-muted: #6b6860;
            --text-dim:   #a09e97;
            --accent:     #0e4f5c;
            --accent-mid: #1a7a8a;
            --accent-soft:#e8f4f6;
            --accent-border:#b8dde3;
            --green:      #2d6a4f;
            --green-soft: #eaf4ee;
            --red:        #b94040;
            --red-soft:   #fdf1f1;
            --yellow:     #9a6c1a;
            --yellow-soft:#fdf6e8;
            --purple:     #5a4a8a;
            --radius:     10px;
            --radius-sm:  7px;
            --shadow:     0 1px 3px rgba(0,0,0,.05), 0 1px 2px rgba(0,0,0,.03);
            --shadow-md:  0 4px 16px rgba(0,0,0,.07);
            --sidebar-w:  220px;
            --topbar-h:   56px;
        }
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        html, body { height: 100%; overflow: hidden; }
        html { font-size: 15.5px; }
        body {
            font-family: 'DM Sans', sans-serif;
            background: var(--bg);
            color: var(--text);
            display: flex;
            flex-direction: column;
        }

        /* ── TOPBAR ──────────────────────────────────────── */
        .topbar {
            height: var(--topbar-h);
            min-height: var(--topbar-h);
            background: var(--accent);
            display: flex; align-items: center;
            padding: 0 20px; gap: 14px;
            flex-shrink: 0;
            z-index: 100;
        }
        .topbar-brand { display: flex; align-items: center; gap: 8px; text-decoration: none; }
        .brand-icon {
            width: 28px; height: 28px;
            background: rgba(255,255,255,.15);
            border-radius: 7px;
            display: flex; align-items: center; justify-content: center;
            border: 1px solid rgba(255,255,255,.2);
        }
        .brand-name {
            font-family: 'DM Sans', sans-serif;
            font-size: 1.1rem; font-weight: 700;
            color: #fff; letter-spacing: .01em;
        }
        .brand-name span { opacity: .65; font-weight: 500; }
        .topbar-sep { width: 1px; height: 18px; background: rgba(255,255,255,.2); }
        .role-chip {
            font-size: .63rem; font-weight: 600;
            letter-spacing: .07em; text-transform: uppercase;
            background: rgba(255,255,255,.12);
            color: rgba(255,255,255,.85);
            border: 1px solid rgba(255,255,255,.2);
            padding: 3px 9px; border-radius: 20px;
        }
        .topbar-right { margin-left: auto; display: flex; align-items: center; gap: 10px; }
        .user-pill { display: flex; align-items: center; gap: 7px; font-size: .8rem; color: rgba(255,255,255,.8); }
        .avatar {
            width: 28px; height: 28px; border-radius: 50%;
            background: rgba(255,255,255,.2);
            border: 1px solid rgba(255,255,255,.3);
            display: flex; align-items: center; justify-content: center;
            font-size: .82rem; font-weight: 700; color: #fff;
        }
        .btn-signout {
            padding: 5px 12px;
            background: transparent;
            border: 1px solid rgba(255,255,255,.25);
            border-radius: var(--radius-sm);
            color: rgba(255,255,255,.75);
            font-size: .75rem; font-family: inherit; cursor: pointer;
            transition: all .15s;
        }
        .btn-signout:hover { background: rgba(255,255,255,.12); color: #fff; border-color: rgba(255,255,255,.4); }

        /* ── LAYOUT ──────────────────────────────────────── */
        .app-body {
            display: flex;
            flex: 1;
            overflow: hidden; /* prevent body scroll — children scroll independently */
            min-height: 0;    /* critical for flex children to respect overflow */
        }

        /* ── SIDEBAR ─────────────────────────────────────── */
        .sidebar {
            width: var(--sidebar-w);
            min-width: var(--sidebar-w);
            background: var(--surface);
            border-right: 1px solid var(--border);
            padding: 16px 8px;
            display: flex; flex-direction: column; gap: 1px;
            flex-shrink: 0;
            overflow-y: auto;
        }
        .nav-section {
            font-size: .6rem; font-weight: 700;
            color: var(--text-dim); text-transform: uppercase;
            letter-spacing: .1em; padding: 12px 10px 4px;
        }
        .nav-link {
            display: flex; align-items: center; gap: 9px;
            padding: 9px 11px; border-radius: var(--radius-sm);
            color: var(--text-muted); font-size: .87rem; font-weight: 500;
            text-decoration: none; transition: all .1s;
        }
        .nav-link svg { width: 16px; height: 16px; flex-shrink: 0; }
        .nav-link:hover { background: var(--surface2); color: var(--text); }
        .nav-link.active {
            background: var(--accent-soft);
            color: var(--accent);
            font-weight: 600;
        }
        .nav-link .nav-badge {
            margin-left: auto;
            background: var(--red); color: #fff;
            font-size: .6rem; font-weight: 700;
            padding: 1px 5px; border-radius: 8px;
        }
        .nav-divider { height: 1px; background: var(--border); margin: 6px 8px; }

        /* ── CONTENT ─────────────────────────────────────── */
        .content {
            flex: 1;
            padding: 25px;
            overflow-y: auto;  /* content scrolls, not the whole page */
            min-width: 0;      /* prevent flex blowout */
        }

        /* ── FLASH ───────────────────────────────────────── */
        .flash {
            padding: 10px 14px; border-radius: var(--radius-sm);
            margin-bottom: 18px; font-size: .8rem;
            display: flex; align-items: center; gap: 8px;
        }
        .flash-success { background: var(--green-soft); border: 1px solid #b7e0c8; color: var(--green); }
        .flash-error   { background: var(--red-soft);   border: 1px solid #f5c6c6; color: var(--red); }

        /* ── PAGE HEADER ─────────────────────────────────── */
        .page-header { margin-bottom: 26px; }
        .page-header h1 {
            font-family: 'DM Sans', sans-serif;
            font-size: 1.55rem; font-weight: 700; color: var(--text);
            letter-spacing: -.02em; line-height: 1.2;
        }
        .page-header p { font-size: .88rem; color: var(--text-muted); margin-top: 4px; }

        /* ── STAT CARDS ──────────────────────────────────── */
        .stat-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(175px, 1fr));
            gap: 12px; margin-bottom: 24px;
        }
        .stat-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 16px 18px;
            box-shadow: var(--shadow);
        }
        .stat-card .sc-top { display: flex; align-items: center; justify-content: space-between; margin-bottom: 16px; }
        .stat-card .sc-dot { width: 8px; height: 8px; border-radius: 50%; }
        .sc-dot.blue   { background: var(--accent-mid); }
        .sc-dot.green  { background: var(--green); }
        .sc-dot.red    { background: var(--red); }
        .sc-dot.yellow { background: var(--yellow); }
        .sc-dot.purple { background: var(--purple); }
        .stat-card .sc-icon { color: var(--text-dim); }
        .stat-card .sc-icon svg { width: 18px; height: 18px; }
        .stat-card .sc-val {
            font-family: 'DM Sans', sans-serif;
            font-size: 2.2rem; font-weight: 700;
            color: var(--text); line-height: 1;
            margin-bottom: 8px; letter-spacing: -.02em;
        }
        .stat-card .sc-label {
            font-size: .72rem; font-weight: 600;
            text-transform: uppercase; letter-spacing: .07em;
            color: var(--text-muted);
        }
        .stat-card .sc-sub { font-size: .78rem; color: var(--text-dim); margin-top: 3px; }

        /* ── CARDS ───────────────────────────────────────── */
        .card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            overflow: hidden;
            margin-bottom: 16px;
        }
        .card-header {
            padding: 13px 16px;
            border-bottom: 1px solid var(--border);
            display: flex; align-items: center;
            justify-content: space-between; gap: 10px;
        }
        .card-title {
            font-family: 'DM Sans', sans-serif;
            font-size: 1rem; font-weight: 600; color: var(--text);
            display: flex; align-items: center; gap: 7px;
        }
        .card-title svg { width: 13px; height: 13px; color: var(--text-dim); }
        .card-body { padding: 16px; }

        .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
        @media(max-width:900px) { .grid-2 { grid-template-columns: 1fr; } }

        /* ── TABLES ──────────────────────────────────────── */
        .table-wrap { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; font-size: .88rem; }
        thead th {
            padding: 10px 16px; text-align: left;
            font-size: .68rem; font-weight: 700;
            color: var(--text-dim); text-transform: uppercase;
            letter-spacing: .08em;
            border-bottom: 1px solid var(--border);
            background: var(--surface2);
            white-space: nowrap;
        }
        tbody tr { border-bottom: 1px solid var(--border); transition: background .1s; }
        tbody tr:hover { background: var(--surface2); }
        tbody tr:last-child { border-bottom: none; }
        tbody td { padding: 12px 16px; vertical-align: middle; }

        /* ── BADGES ──────────────────────────────────────── */
        .badge {
            display: inline-flex; align-items: center; gap: 4px;
            padding: 2px 8px; border-radius: 20px;
            font-size: .65rem; font-weight: 600;
            text-transform: uppercase; letter-spacing: .04em;
            white-space: nowrap;
        }
        .badge-green  { background: var(--green-soft); color: var(--green); }
        .badge-red    { background: var(--red-soft);   color: var(--red); }
        .badge-yellow { background: var(--yellow-soft);color: var(--yellow); }
        .badge-blue   { background: var(--accent-soft);color: var(--accent); }
        .badge-purple { background: #f0eefb; color: var(--purple); }
        .badge-gray   { background: var(--surface2);   color: var(--text-muted); }

        /* ── BUTTONS ─────────────────────────────────────── */
        .btn {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 9px 17px; border-radius: var(--radius-sm);
            font-size: .85rem; font-weight: 600; font-family: inherit;
            border: none; cursor: pointer;
            text-decoration: none; transition: all .12s; white-space: nowrap;
        }
        .btn svg { width: 14px; height: 14px; }
        .btn-primary { background: var(--accent); color: #fff; }
        .btn-primary:hover { background: var(--accent-mid); }
        .btn-secondary { background: var(--surface); color: var(--text-muted); border: 1px solid var(--border2); }
        .btn-secondary:hover { background: var(--surface2); color: var(--text); }
        .btn-danger { background: var(--red-soft); color: var(--red); border: 1px solid #f5c6c6; }
        .btn-danger:hover { background: #fde8e8; }
        .btn-success { background: var(--green-soft); color: var(--green); }
        .btn-sm { padding: 6px 13px; font-size: .8rem; }
        .btn-xs { padding: 4px 9px; font-size: .75rem; }

        /* ── FORMS ───────────────────────────────────────── */
        .form-group { margin-bottom: 14px; }
        .form-group label {
            display: block; font-size: .68rem; font-weight: 700;
            color: var(--text-muted); text-transform: uppercase;
            letter-spacing: .07em; margin-bottom: 5px;
        }
        .form-control {
            width: 100%; padding: 8px 11px;
            background: var(--surface);
            border: 1px solid var(--border2);
            border-radius: var(--radius-sm);
            color: var(--text); font-size: .82rem; font-family: inherit;
            transition: border-color .12s, box-shadow .12s;
        }
        .form-control:focus {
            outline: none;
            border-color: var(--accent-mid);
            box-shadow: 0 0 0 3px rgba(14,79,92,.08);
        }
        textarea.form-control { resize: vertical; min-height: 80px; }
        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
        @media(max-width:600px) { .form-row { grid-template-columns: 1fr; } }

        /* ── MODALS ──────────────────────────────────────── */
        .modal-backdrop {
            display: none; position: fixed; inset: 0;
            background: rgba(0,0,0,.3); backdrop-filter: blur(3px);
            z-index: 200; align-items: center; justify-content: center;
        }
        .modal-backdrop.open { display: flex; }
        .modal {
            background: var(--surface); border: 1px solid var(--border);
            border-radius: 12px; width: 90%; max-width: 500px;
            max-height: 90vh; overflow-y: auto;
            box-shadow: 0 20px 60px rgba(0,0,0,.12);
        }
        .modal-header {
            padding: 14px 18px; border-bottom: 1px solid var(--border);
            display: flex; align-items: center; justify-content: space-between;
        }
        .modal-header h3 {
            font-family: 'DM Sans', sans-serif;
            font-size: 1rem; font-weight: 600;
        }
        .modal-close { background: none; border: none; color: var(--text-muted); cursor: pointer; padding: 4px; border-radius: 5px; }
        .modal-close:hover { background: var(--surface2); }
        .modal-body { padding: 18px; }
        .modal-footer {
            padding: 12px 18px; border-top: 1px solid var(--border);
            display: flex; gap: 8px; justify-content: flex-end;
            background: var(--surface2);
        }

        /* ── POS SPECIFIC ────────────────────────────────── */
        .pos-layout {
            display: grid;
            grid-template-columns: 1fr 300px;
            gap: 16px;
            /* fill remaining viewport height after topbar + page padding */
            height: calc(100vh - var(--topbar-h) - 50px);
            min-height: 0;
        }
        .pos-products { overflow-y: auto; display: flex; flex-direction: column; gap: 12px; min-height: 0; }
        .search-bar {
            display: flex; align-items: center; gap: 8px;
            background: var(--surface); border: 1px solid var(--border2);
            border-radius: var(--radius-sm); padding: 8px 12px; box-shadow: var(--shadow);
        }
        .search-bar svg { width: 14px; height: 14px; color: var(--text-dim); flex-shrink: 0; }
        .search-bar input { border: none; outline: none; background: transparent; color: var(--text); font-size: .82rem; font-family: inherit; flex: 1; }
        .product-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(115px, 1fr)); gap: 9px; }
        .product-card {
            background: var(--surface); border: 1px solid var(--border);
            border-radius: var(--radius); padding: 13px 10px;
            cursor: pointer; text-align: center;
            transition: all .15s; box-shadow: var(--shadow);
        }
        .product-card:hover { border-color: var(--accent-mid); box-shadow: 0 4px 14px rgba(14,79,92,.1); transform: translateY(-1px); }
        .product-card:active { transform: scale(.97); }
        .p-icon { font-size: 22px; margin-bottom: 7px; }
        .p-name { font-size: .73rem; font-weight: 600; color: var(--text); margin-bottom: 3px; }
        .p-price { font-size: .75rem; font-weight: 700; color: var(--accent); }
        .pos-cart {
            background: var(--surface); border: 1px solid var(--border);
            border-radius: var(--radius); display: flex; flex-direction: column;
            overflow: hidden; box-shadow: var(--shadow-md);
            min-height: 0;
        }
        .cart-header { padding: 12px 14px; border-bottom: 1px solid var(--border); flex-shrink: 0; }
        .cart-title { font-family: 'DM Sans', sans-serif; font-size: .95rem; font-weight: 600; display: flex; align-items: center; gap: 6px; }
        .cart-items { flex: 1; overflow-y: auto; padding: 8px; display: flex; flex-direction: column; gap: 5px; min-height: 0; }
        .cart-item {
            display: flex; align-items: center; gap: 7px;
            padding: 8px 9px; border-radius: var(--radius-sm);
            background: var(--surface2); border: 1px solid var(--border);
        }
        .ci-name { flex: 1; font-size: .76rem; font-weight: 600; color: var(--text); }
        .ci-qty { display: flex; align-items: center; gap: 4px; }
        .ci-qty button {
            width: 20px; height: 20px; border-radius: 5px;
            border: 1px solid var(--border2); background: var(--surface);
            color: var(--text); cursor: pointer; font-size: .8rem;
            display: flex; align-items: center; justify-content: center; transition: all .1s;
        }
        .ci-qty button:hover { background: var(--accent); border-color: var(--accent); color: #fff; }
        .ci-price { font-size: .76rem; font-weight: 700; color: var(--accent); width: 52px; text-align: right; }
        .cart-footer { padding: 12px 14px; border-top: 1px solid var(--border); flex-shrink: 0; }
        .cart-line { display: flex; justify-content: space-between; font-size: .78rem; color: var(--text-muted); margin-bottom: 5px; }
        .cart-line.total { font-family: 'DM Sans', sans-serif; font-size: 1.05rem; font-weight: 700; color: var(--text); margin-top: 8px; padding-top: 8px; border-top: 1px dashed var(--border2); }
        .btn-charge {
            width: 100%; margin-top: 10px; padding: 12px;
            background: var(--accent); border: none;
            border-radius: var(--radius-sm); color: #fff;
            font-family: 'DM Sans', sans-serif;
            font-size: .92rem; font-weight: 700; cursor: pointer;
            transition: all .15s;
        }
        .btn-charge:hover { background: var(--accent-mid); }

        /* ── CHART BARS ──────────────────────────────────── */
        .chart-bars { display: flex; align-items: flex-end; gap: 5px; height: 90px; }
        .bar-col { flex: 1; display: flex; flex-direction: column; align-items: center; gap: 4px; }
        .bar-fill { width: 100%; border-radius: 3px 3px 0 0; background: var(--accent-soft); border: 1px solid var(--accent-border); border-bottom: none; transition: all .2s; }
        .bar-fill:hover { background: var(--accent-mid); border-color: var(--accent-mid); }
        .bar-label { font-size: .62rem; color: var(--text-dim); }

        /* ── FILTER TABS ─────────────────────────────────── */
        .filter-bar { display: flex; gap: 5px; flex-wrap: wrap; margin-bottom: 14px; }
        .filter-btn {
            padding: 5px 13px; border-radius: 20px;
            border: 1px solid var(--border2); background: var(--surface);
            color: var(--text-muted); font-size: .73rem; font-weight: 600;
            cursor: pointer; transition: all .1s; font-family: inherit;
        }
        .filter-btn:hover, .filter-btn.active { background: var(--accent); border-color: var(--accent); color: #fff; }

        /* ── EMPTY STATE ─────────────────────────────────── */
        .empty-state { text-align: center; padding: 32px 16px; color: var(--text-dim); }
        .empty-state svg { width: 32px; height: 32px; opacity: .2; margin: 0 auto 8px; display: block; }
        .empty-state p { font-size: .8rem; }

        /* ── PAGINATION ──────────────────────────────────── */
        .pagination { display: flex; gap: 3px; justify-content: center; flex-wrap: wrap; margin-top: 14px; }
        .pagination a, .pagination span { padding: 5px 10px; border-radius: var(--radius-sm); font-size: .74rem; text-decoration: none; }
        .pagination a { background: var(--surface); border: 1px solid var(--border2); color: var(--text-muted); transition: all .1s; }
        .pagination a:hover { background: var(--accent-soft); color: var(--accent); border-color: var(--accent-border); }
        .pagination span.active { background: var(--accent); color: #fff; font-weight: 700; }

        /* ── PROGRESS ────────────────────────────────────── */
        .progress-bar { height: 5px; background: var(--surface2); border-radius: 3px; overflow: hidden; }
        .progress-fill { height: 100%; border-radius: 3px; transition: width .3s; }
        .progress-fill.blue   { background: var(--accent-mid); }
        .progress-fill.green  { background: var(--green); }
        .progress-fill.yellow { background: var(--yellow); }
        .progress-fill.red    { background: var(--red); }

        /* ── SCROLLBAR ───────────────────────────────────── */
        ::-webkit-scrollbar { width: 4px; height: 4px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: var(--border2); border-radius: 2px; }
    </style>
    @stack('styles')
</head>
<body>
    <header class="topbar">
        <a href="{{ route(auth()->user()->role . '.dashboard') }}" class="topbar-brand">
            <div class="brand-icon">
                <i data-lucide="shopping-cart" style="width:14px;height:14px;color:#fff"></i>
            </div>
            <span class="brand-name">Nova<span>POS</span></span>
        </a>
        <div class="topbar-sep"></div>
        <span class="role-chip">{{ auth()->user()->getRoleLabel() }}</span>
        <div class="topbar-right">
            <div class="user-pill">
                <div class="avatar">{{ substr(auth()->user()->name, 0, 1) }}</div>
                <span>{{ auth()->user()->name }}</span>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn-signout">Sign out</button>
            </form>
        </div>
    </header>

    <div class="app-body">
        <nav class="sidebar">
            @yield('sidebar-nav')
        </nav>
        <main class="content">
            @if(session('success'))
            <div class="flash flash-success">
                <i data-lucide="check-circle" style="width:14px;height:14px"></i>
                {{ session('success') }}
            </div>
            @endif
            @if(session('error'))
            <div class="flash flash-error">
                <i data-lucide="alert-circle" style="width:14px;height:14px"></i>
                {{ session('error') }}
            </div>
            @endif
            @if($errors->any())
            <div class="flash flash-error">
                <i data-lucide="alert-circle" style="width:14px;height:14px"></i>
                <div>@foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach</div>
            </div>
            @endif
            @yield('content')
        </main>
    </div>

    <script>
    lucide.createIcons();
    function openModal(id)  { document.getElementById(id).classList.add('open'); }
    function closeModal(id) { document.getElementById(id).classList.remove('open'); }
    document.querySelectorAll('.modal-backdrop').forEach(m => {
        m.addEventListener('click', e => { if(e.target === m) m.classList.remove('open'); });
    });
    function confirmDelete(formId) {
        if(confirm('Are you sure? This cannot be undone.')) document.getElementById(formId).submit();
    }
    </script>
    @stack('scripts')
</body>
</html>