@extends('layouts.staff')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <h3 class="fw-bold text-dark mb-1">Profile Settings</h3>
        <p class="text-secondary mb-0">Manage your personal details and security options.</p>
    </div>
</div>

<div class="row g-4">
    <!-- Profile Info Form (Left Card) -->
    <div class="col-12 col-lg-7">
        <div class="card border-0 shadow-sm rounded-3 bg-white p-4 h-100">
            <h5 class="fw-bold text-dark border-bottom pb-3 mb-4">
                <i class="fa-solid fa-id-card text-primary me-2"></i>Personal Details
            </h5>

            <form method="POST" action="{{ route('staff.profile.update') }}">
                @csrf
                @method('PATCH')

                <!-- Name Field -->
                <div class="mb-3">
                    <label for="name" class="form-label fw-semibold text-secondary">Full Name</label>
                    <input type="text" class="form-control bg-light @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $user->name) }}" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Email Field -->
                <div class="mb-3">
                    <label for="email" class="form-label fw-semibold text-secondary">Email Address</label>
                    <input type="email" class="form-control bg-light @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $user->email) }}" required>
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Phone Field -->
                <div class="mb-3">
                    <label for="phone" class="form-label fw-semibold text-secondary">Phone Number</label>
                    <input type="text" class="form-control bg-light @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone', $user->phone) }}" placeholder="e.g. +25261XXXXXX">
                    @error('phone')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Address Field -->
                <div class="mb-4">
                    <label for="address" class="form-label fw-semibold text-secondary">Residential Address</label>
                    <textarea class="form-control bg-light @error('address') is-invalid @enderror" id="address" name="address" rows="4" placeholder="Enter your full address details...">{{ old('address', $user->address) }}</textarea>
                    @error('address')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-primary fw-bold px-4 py-2">
                    <i class="fa-solid fa-floppy-disk me-1"></i> Save Changes
                </button>
            </form>
        </div>
    </div>

    <!-- Security settings / Password Form (Right Card) -->
    <div class="col-12 col-lg-5">
        <div class="card border-0 shadow-sm rounded-3 bg-white p-4 h-100">
            <h5 class="fw-bold text-dark border-bottom pb-3 mb-4">
                <i class="fa-solid fa-lock text-primary me-2"></i>Security Settings
            </h5>

            <form method="POST" action="{{ route('staff.profile.update') }}">
                @csrf
                @method('PATCH')

                <!-- Retain hidden values for non-password fields to prevent overriding -->
                <input type="hidden" name="name" value="{{ old('name', $user->name) }}">
                <input type="hidden" name="email" value="{{ old('email', $user->email) }}">
                <input type="hidden" name="phone" value="{{ old('phone', $user->phone) }}">
                <input type="hidden" name="address" value="{{ old('address', $user->address) }}">

                <!-- Current Password -->
                <div class="mb-3">
                    <label for="current_password" class="form-label fw-semibold text-secondary">Current Password</label>
                    <input type="password" class="form-control bg-light @error('current_password') is-invalid @enderror" id="current_password" name="current_password" placeholder="••••••••">
                    @error('current_password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <span class="small text-muted">Required only when updating password.</span>
                </div>

                <!-- New Password -->
                <div class="mb-3">
                    <label for="new_password" class="form-label fw-semibold text-secondary">New Password</label>
                    <input type="password" class="form-control bg-light @error('new_password') is-invalid @enderror" id="new_password" name="new_password" placeholder="Minimum 8 characters">
                    @error('new_password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Confirm Password -->
                <div class="mb-4">
                    <label for="new_password_confirmation" class="form-label fw-semibold text-secondary">Confirm New Password</label>
                    <input type="password" class="form-control bg-light" id="new_password_confirmation" name="new_password_confirmation" placeholder="Confirm password">
                </div>

                <button type="submit" class="btn btn-warning fw-bold text-dark px-4 py-2">
                    <i class="fa-solid fa-key me-1"></i> Update Password
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
