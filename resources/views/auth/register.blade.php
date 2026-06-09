<x-guest-layout>
    <h3 class="fw-bold mb-3 text-center">Create an Account</h3>
    <p class="text-muted text-center mb-4">Register to start managing your laundry orders</p>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Name -->
        <div class="mb-3">
            <label for="name" class="form-label fw-semibold">Full Name</label>
            <input id="name" type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required autofocus autocomplete="name">
            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Email Address -->
        <div class="mb-3">
            <label for="email" class="form-label fw-semibold">Email Address</label>
            <input id="email" type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required autocomplete="username">
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Phone -->
        <div class="mb-3">
            <label for="phone" class="form-label fw-semibold">Phone Number</label>
            <input id="phone" type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone') }}" required>
            @error('phone')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Address -->
        <div class="mb-3">
            <label for="address" class="form-label fw-semibold">Address</label>
            <textarea id="address" name="address" rows="3" class="form-control @error('address') is-invalid @enderror" required>{{ old('address') }}</textarea>
            @error('address')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Password -->
        <div class="mb-3">
            <label for="password" class="form-label fw-semibold">Password</label>
            <input id="password" type="password" name="password" class="form-control @error('password') is-invalid @enderror" required autocomplete="new-password">
            @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Confirm Password -->
        <div class="mb-3">
            <label for="password_confirmation" class="form-label fw-semibold">Confirm Password</label>
            <input id="password_confirmation" type="password" name="password_confirmation" class="form-control" required autocomplete="new-password">
        </div>

        <!-- Hidden Role -->
        <input type="hidden" name="role" value="customer">

        <div class="d-grid gap-2 mt-4">
            <button type="submit" class="btn btn-primary fw-bold">Register</button>
        </div>

        <div class="text-center mt-3">
            <span class="text-muted">Already registered?</span>
            <a href="{{ route('login') }}" class="text-decoration-none fw-semibold ms-1">Log in</a>
        </div>
    </form>
</x-guest-layout>
