@extends('layouts.admin')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom">
    <h1 class="h2 fw-bold text-dark">Add New User</h1>
    <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary fw-semibold">
        <i class="fa-solid fa-arrow-left me-2"></i>Back to Users List
    </a>
</div>

<div class="card border-0 shadow-sm rounded-4">
    <div class="card-body p-4 p-md-5">
        <form method="POST" action="{{ route('admin.users.store') }}">
            @csrf

            <div class="row g-4">
                <!-- Name -->
                <div class="col-12 col-md-6">
                    <label for="name" class="form-label fw-semibold">Full Name</label>
                    <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required placeholder="e.g. John Doe">
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Email -->
                <div class="col-12 col-md-6">
                    <label for="email" class="form-label fw-semibold">Email Address</label>
                    <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required placeholder="e.g. johndoe@example.com">
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Phone -->
                <div class="col-12 col-md-6">
                    <label for="phone" class="form-label fw-semibold">Phone Number</label>
                    <input type="text" name="phone" id="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone') }}" placeholder="e.g. +25261XXXXXX">
                    @error('phone')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Role Select -->
                <div class="col-12 col-md-6">
                    <label for="role" class="form-label fw-semibold">System Role</label>
                    <select name="role" id="role" class="form-select @error('role') is-invalid @enderror" required>
                        <option value="" disabled selected>Select Role...</option>
                        <option value="customer" {{ old('role') === 'customer' ? 'selected' : '' }}>Customer (Regular User)</option>
                        <option value="staff" {{ old('role') === 'staff' ? 'selected' : '' }}>Staff (Laundry Operator)</option>
                        <option value="delivery" {{ old('role') === 'delivery' ? 'selected' : '' }}>Delivery (Courier Agent)</option>
                        <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Admin (System Supervisor)</option>
                    </select>
                    @error('role')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Password -->
                <div class="col-12 col-md-6">
                    <label for="password" class="form-label fw-semibold">Password</label>
                    <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror" required placeholder="Minimum 8 characters">
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Active Status -->
                <div class="col-12 col-md-6 d-flex align-items-center">
                    <div class="form-check form-switch mt-4">
                        <input class="form-check-input" type="checkbox" role="switch" name="is_active" id="is_active" value="1" checked>
                        <label class="form-check-label fw-semibold ms-2" for="is_active">Activate account on creation</label>
                    </div>
                </div>

                <!-- Address -->
                <div class="col-12">
                    <label for="address" class="form-label fw-semibold">Home/Office Address</label>
                    <textarea name="address" id="address" rows="3" class="form-control @error('address') is-invalid @enderror" placeholder="Street, Block, District details...">{{ old('address') }}</textarea>
                    @error('address')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- Submit Button -->
            <div class="d-flex justify-content-end gap-2 mt-5">
                <a href="{{ route('admin.users.index') }}" class="btn btn-light fw-bold px-4">Cancel</a>
                <button type="submit" class="btn btn-primary fw-bold px-4">Save User</button>
            </div>
        </form>
    </div>
</div>
@endsection
