@extends('layouts.admin')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom">
    <h1 class="h2 fw-bold text-dark">User Management</h1>
    <a href="{{ route('admin.users.create') }}" class="btn btn-primary fw-semibold">
        <i class="fa-solid fa-user-plus me-2"></i>Add New User
    </a>
</div>

<!-- Search and Filter Form Card -->
<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body p-4">
        <form method="GET" action="{{ route('admin.users.index') }}" class="row g-3">
            <!-- Search field -->
            <div class="col-12 col-md-4">
                <label for="search" class="form-label fw-semibold text-muted small">Search Name/Email</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="fa-solid fa-magnifying-glass"></i></span>
                    <input type="text" name="search" id="search" class="form-control bg-light border-start-0" placeholder="Type query..." value="{{ request('search') }}">
                </div>
            </div>

            <!-- Role filter -->
            <div class="col-12 col-sm-6 col-md-3">
                <label for="role" class="form-label fw-semibold text-muted small">Filter by Role</label>
                <select name="role" id="role" class="form-select bg-light">
                    <option value="">All Roles</option>
                    <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                    <option value="staff" {{ request('role') === 'staff' ? 'selected' : '' }}>Staff</option>
                    <option value="delivery" {{ request('role') === 'delivery' ? 'selected' : '' }}>Delivery</option>
                    <option value="customer" {{ request('role') === 'customer' ? 'selected' : '' }}>Customer</option>
                </select>
            </div>

            <!-- Status filter -->
            <div class="col-12 col-sm-6 col-md-3">
                <label for="status" class="form-label fw-semibold text-muted small">Filter by Status</label>
                <select name="status" id="status" class="form-select bg-light">
                    <option value="">All Statuses</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>

            <!-- Filter Buttons -->
            <div class="col-12 col-md-2 d-flex align-items-end gap-2">
                <button type="submit" class="btn btn-primary w-100 fw-bold">Filter</button>
                <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary w-100 fw-bold">Clear</a>
            </div>
        </form>
    </div>
</div>

<!-- Users List Card -->
<div class="card border-0 shadow-sm rounded-4">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="border-0 px-4">#</th>
                        <th class="border-0">Name</th>
                        <th class="border-0">Email</th>
                        <th class="border-0">Role</th>
                        <th class="border-0">Phone</th>
                        <th class="border-0">Status</th>
                        <th class="border-0">Created At</th>
                        <th class="border-0 px-4 text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        <tr>
                            <td class="px-4 text-muted">{{ $user->id }}</td>
                            <td class="fw-semibold text-dark">{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>
                                @switch($user->role)
                                    @case('admin')
                                        <span class="badge bg-danger text-uppercase">Admin</span>
                                        @break
                                    @case('staff')
                                        <span class="badge bg-primary text-uppercase">Staff</span>
                                        @break
                                    @case('delivery')
                                        <span class="badge bg-warning text-dark text-uppercase">Delivery</span>
                                        @break
                                    @case('customer')
                                        <span class="badge bg-success text-uppercase">Customer</span>
                                        @break
                                @endswitch
                            </td>
                            <td>{{ $user->phone ?? 'N/A' }}</td>
                            <td>
                                @if($user->is_active)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-secondary">Inactive</span>
                                @endif
                            </td>
                            <td class="text-muted small">{{ $user->created_at->format('M d, Y') }}</td>
                            <td class="px-4 text-end">
                                <div class="d-inline-flex gap-2">
                                    <!-- Toggle Status Button -->
                                    <form method="POST" action="{{ route('admin.users.toggleStatus', $user->id) }}" class="m-0">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-sm btn-outline-secondary" title="Toggle Status">
                                            @if($user->is_active)
                                                <i class="fa-solid fa-user-slash text-danger"></i>
                                            @else
                                                <i class="fa-solid fa-user-check text-success"></i>
                                            @endif
                                        </button>
                                    </form>

                                    <!-- Edit Button -->
                                    <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-sm btn-outline-primary" title="Edit">
                                        <i class="fa-solid fa-pen"></i>
                                    </a>

                                    <!-- Delete Button -->
                                    <form method="POST" action="{{ route('admin.users.destroy', $user->id) }}" class="m-0 delete-form">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete" {{ $user->id === auth()->id() ? 'disabled' : '' }}>
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                <i class="fa-solid fa-users-slash fs-2 mb-2 d-block text-secondary"></i>
                                No users matched the filters.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($users->hasPages())
        <div class="card-footer bg-white border-0 py-3">
            {{ $users->links('pagination::bootstrap-5') }}
        </div>
    @endif
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Confirmation dialog for delete actions
        const deleteForms = document.querySelectorAll('.delete-form');
        deleteForms.forEach(function (form) {
            form.addEventListener('submit', function (event) {
                event.preventDefault();
                if (confirm('Are you absolutely sure you want to delete this user? This action cannot be undone.')) {
                    form.submit();
                }
            });
        });
    });
</script>
@endsection
