@extends('layouts.app')
@section('title','Users')
@section('sidebar-nav')@include('admin._nav')@endsection

@section('content')
<div class="page-header" style="display:flex;justify-content:space-between;align-items:flex-start">
    <div><h1>User Management</h1><p>Manage staff accounts and roles</p></div>
    <button class="btn btn-primary" onclick="openModal('modal-create-user')"><i data-lucide="user-plus"></i> Add User</button>
</div>

<div class="card">
    <div class="card-header">
        <form method="GET" style="display:flex;gap:8px;margin:0">
            <select name="role" class="form-control" style="width:auto">
                <option value="">All Roles</option>
                @foreach(['admin'=>'Business Owner','manager'=>'Store Manager','cashier'=>'Cashier','inventory'=>'Inventory Staff'] as $val=>$label)
                <option value="{{ $val }}" {{ request('role')===$val?'selected':'' }}>{{ $label }}</option>
                @endforeach
            </select>
            <button class="btn btn-secondary" type="submit"><i data-lucide="filter"></i> Filter</button>
        </form>
    </div>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Name</th><th>Role</th><th>Email</th><th>Status</th><th>Last Login</th><th>Actions</th></tr></thead>
            <tbody>
            @forelse($users as $user)
            <tr>
                <td>
                    <div style="display:flex;align-items:center;gap:8px">
                        <div style="width:30px;height:30px;border-radius:50%;background:var(--accent-soft);border:1px solid var(--border);display:flex;align-items:center;justify-content:center;font-size:.75rem;font-weight:700;color:var(--accent)">{{ substr($user->name,0,1) }}</div>
                        <span style="font-weight:600;font-size:.82rem">{{ $user->name }}</span>
                    </div>
                </td>
                <td><span class="badge badge-{{ $user->role==='admin'?'purple':($user->role==='manager'?'blue':($user->role==='cashier'?'green':'yellow')) }}">{{ $user->getRoleLabel() }}</span></td>
                <td style="color:var(--text-muted);font-size:.8rem">{{ $user->email }}</td>
                <td><span class="badge badge-{{ $user->status==='active'?'green':'red' }}">{{ ucfirst($user->status) }}</span></td>
                <td style="color:var(--text-muted);font-size:.78rem">{{ $user->last_login_at?->diffForHumans() ?? 'Never' }}</td>
                <td>
                    <div style="display:flex;gap:5px">
                        <button class="btn btn-secondary btn-xs" onclick="openModal('edit-user-{{ $user->id }}')"><i data-lucide="pencil"></i></button>
                        @if($user->id !== auth()->id())
                        <form id="del-user-{{ $user->id }}" method="POST" action="{{ route('admin.users.destroy',$user) }}">@csrf @method('DELETE')</form>
                        <button class="btn btn-danger btn-xs" onclick="confirmDelete('del-user-{{ $user->id }}')"><i data-lucide="trash-2"></i></button>
                        @endif
                    </div>
                </td>
            </tr>
            {{-- Edit User Modal --}}
            <div class="modal-backdrop" id="edit-user-{{ $user->id }}">
                <div class="modal"><div class="modal-header"><h3>Edit: {{ $user->name }}</h3><button class="modal-close" onclick="closeModal('edit-user-{{ $user->id }}')"><i data-lucide="x" style="width:16px;height:16px"></i></button></div>
                <div class="modal-body">
                    <form method="POST" action="{{ route('admin.users.update',$user) }}">
                        @csrf @method('PUT')
                        <div class="form-row">
                            <div class="form-group"><label>Name</label><input type="text" name="name" value="{{ $user->name }}" class="form-control" required></div>
                            <div class="form-group"><label>Email</label><input type="email" name="email" value="{{ $user->email }}" class="form-control" required></div>
                        </div>
                        <div class="form-row">
                            <div class="form-group"><label>Role</label><select name="role" class="form-control"><option value="admin" {{ $user->role==='admin'?'selected':'' }}>Business Owner</option><option value="manager" {{ $user->role==='manager'?'selected':'' }}>Store Manager</option><option value="cashier" {{ $user->role==='cashier'?'selected':'' }}>Cashier</option><option value="inventory" {{ $user->role==='inventory'?'selected':'' }}>Inventory Staff</option></select></div>
                            <div class="form-group"><label>Status</label><select name="status" class="form-control"><option value="active" {{ $user->status==='active'?'selected':'' }}>Active</option><option value="inactive" {{ $user->status==='inactive'?'selected':'' }}>Inactive</option></select></div>
                        </div>
                        <div class="form-row">
                            <div class="form-group"><label>New Password</label><input type="password" name="password" class="form-control" placeholder="Leave blank to keep"></div>
                            <div class="form-group"><label>Confirm Password</label><input type="password" name="password_confirmation" class="form-control"></div>
                        </div>
                        <div style="display:flex;gap:8px"><button type="submit" class="btn btn-primary"><i data-lucide="save"></i> Save</button><button type="button" class="btn btn-secondary" onclick="closeModal('edit-user-{{ $user->id }}')">Cancel</button></div>
                    </form>
                </div></div>
            </div>
            @empty
            <tr><td colspan="6"><div class="empty-state"><i data-lucide="users"></i><p>No users found.</p></div></td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div style="padding:14px 18px">{{ $users->withQueryString()->links('vendor.pagination.simple') }}</div>
</div>

{{-- Create User Modal --}}
<div class="modal-backdrop" id="modal-create-user">
    <div class="modal"><div class="modal-header"><h3>Add New User</h3><button class="modal-close" onclick="closeModal('modal-create-user')"><i data-lucide="x" style="width:16px;height:16px"></i></button></div>
    <div class="modal-body">
        <form method="POST" action="{{ route('admin.users.store') }}">
            @csrf
            <div class="form-row">
                <div class="form-group"><label>Full Name *</label><input type="text" name="name" class="form-control" required></div>
                <div class="form-group"><label>Email *</label><input type="email" name="email" class="form-control" required></div>
            </div>
            <div class="form-row">
                <div class="form-group"><label>Password *</label><input type="password" name="password" class="form-control" required></div>
                <div class="form-group"><label>Confirm Password *</label><input type="password" name="password_confirmation" class="form-control" required></div>
            </div>
            <div class="form-row">
                <div class="form-group"><label>Role *</label><select name="role" class="form-control"><option value="cashier">Cashier</option><option value="manager">Store Manager</option><option value="inventory">Inventory Staff</option><option value="admin">Business Owner</option></select></div>
                <div class="form-group"><label>Status *</label><select name="status" class="form-control"><option value="active">Active</option><option value="inactive">Inactive</option></select></div>
            </div>
            <div style="display:flex;gap:8px"><button type="submit" class="btn btn-primary"><i data-lucide="user-plus"></i> Create User</button><button type="button" class="btn btn-secondary" onclick="closeModal('modal-create-user')">Cancel</button></div>
        </form>
    </div></div>
</div>
@endsection
