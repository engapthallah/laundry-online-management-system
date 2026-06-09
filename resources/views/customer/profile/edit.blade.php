@extends('layouts.customer')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom">
    <h1 class="h2 fw-bold text-dark">Profile Settings</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <span class="badge bg-primary px-3 py-2 text-uppercase fs-7 fw-semibold">Settings</span>
    </div>
</div>

<form method="POST" action="{{ route('customer.profile.update') }}">
    @csrf
    @method('PATCH')

    <div class="row g-4">
        <!-- Personal Information Card -->
        <div class="col-12 col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 bg-white mb-4">
                <div class="card-header bg-white border-0 py-3 px-4 border-bottom">
                    <h5 class="fw-bold text-dark mb-0"><i class="fa-solid fa-user-gear text-primary me-2"></i>Personal Profile Details</h5>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <!-- Name -->
                        <div class="col-12 col-sm-6">
                            <label for="name" class="form-label fw-bold text-dark">Full Name</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted"><i class="fa-solid fa-user"></i></span>
                                <input type="text" 
                                       name="name" 
                                       id="name" 
                                       class="form-control @error('name') is-invalid @enderror" 
                                       value="{{ old('name', $user->name) }}" 
                                       required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Email -->
                        <div class="col-12 col-sm-6">
                            <label for="email" class="form-label fw-bold text-dark">Email Address</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted"><i class="fa-solid fa-envelope"></i></span>
                                <input type="email" 
                                       name="email" 
                                       id="email" 
                                       class="form-control @error('email') is-invalid @enderror" 
                                       value="{{ old('email', $user->email) }}" 
                                       required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Phone -->
                        <div class="col-12 col-sm-6">
                            <label for="phone" class="form-label fw-bold text-dark">Phone Number</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted"><i class="fa-solid fa-phone"></i></span>
                                <input type="text" 
                                       name="phone" 
                                       id="phone" 
                                       class="form-control @error('phone') is-invalid @enderror" 
                                       value="{{ old('phone', $user->phone) }}" 
                                       placeholder="e.g. +252 XXXXXXX">
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Address -->
                        <div class="col-12">
                            <label for="address" class="form-label fw-bold text-dark">Default Delivery Address</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted"><i class="fa-solid fa-map-location-dot"></i></span>
                                <textarea name="address" 
                                          id="address" 
                                          rows="3" 
                                          class="form-control @error('address') is-invalid @enderror" 
                                          placeholder="Enter your street address, building number, or general area details for delivery.">{{ old('address', $user->address) }}</textarea>
                                @error('address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-text text-muted small mt-1">This address will be prefilled automatically when you place a new laundry order.</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Security & Password Card -->
        <div class="col-12 col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 bg-white mb-4">
                <div class="card-header bg-white border-0 py-3 px-4 border-bottom">
                    <h5 class="fw-bold text-dark mb-0"><i class="fa-solid fa-key text-primary me-2"></i>Change Password</h5>
                </div>
                <div class="card-body p-4">
                    <!-- Current Password -->
                    <div class="mb-3">
                        <label for="current_password" class="form-label fw-bold text-dark">Current Password</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-muted"><i class="fa-solid fa-lock"></i></span>
                            <input type="password" 
                                   name="current_password" 
                                   id="current_password" 
                                   class="form-control @error('current_password') is-invalid @enderror" 
                                   placeholder="Required to change password">
                            @error('current_password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- New Password -->
                    <div class="mb-3">
                        <label for="new_password" class="form-label fw-bold text-dark">New Password</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-muted"><i class="fa-solid fa-lock-open"></i></span>
                            <input type="password" 
                                   name="new_password" 
                                   id="new_password" 
                                   class="form-control @error('new_password') is-invalid @enderror" 
                                   placeholder="Min 8 characters">
                            @error('new_password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Confirm Password -->
                    <div class="mb-4">
                        <label for="new_password_confirmation" class="form-label fw-bold text-dark">Confirm New Password</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-muted"><i class="fa-solid fa-shield"></i></span>
                            <input type="password" 
                                   name="new_password_confirmation" 
                                   id="new_password_confirmation" 
                                   class="form-control" 
                                   placeholder="Re-type new password">
                        </div>
                    </div>

                    <div class="bg-light rounded-4 p-3 small text-muted mb-4">
                        <i class="fa-solid fa-shield-halved text-primary me-1"></i>
                        Leave password fields blank if you do not wish to change your account password.
                    </div>
                </div>
            </div>

            <!-- Submit Button Grid -->
            <button type="submit" class="btn btn-primary w-100 fw-bold py-2.5 rounded-3 shadow-sm">
                <i class="fa-solid fa-circle-check me-2"></i>Save Account Changes
            </button>
        </div>
    </div>
</form>
@endsection
