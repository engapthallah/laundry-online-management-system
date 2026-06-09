<x-guest-layout>
    <h3 class="fw-bold mb-3 text-center">Welcome Back</h3>
    <p class="text-muted text-center mb-4">Sign in to your account</p>

    <!-- Session Status -->
    @if (session('status'))
        <div class="alert alert-success mb-3" role="alert">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address -->
        <div class="mb-3">
            <label for="email" class="form-label fw-semibold">Email Address</label>
            <input id="email" type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required autofocus autocomplete="username">
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Password -->
        <div class="mb-3">
            <label for="password" class="form-label fw-semibold">Password</label>
            <input id="password" type="password" name="password" class="form-control @error('password') is-invalid @enderror" required autocomplete="current-password">
            @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Remember Me -->
        <div class="mb-3 form-check">
            <input id="remember_me" type="checkbox" name="remember" class="form-check-input">
            <label for="remember_me" class="form-check-label text-muted">Remember me</label>
        </div>

        <div class="d-grid gap-2 mt-4">
            <button type="submit" class="btn btn-primary fw-bold">Log in</button>
        </div>

        <div class="text-center mt-3">
            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="text-decoration-none text-muted small">Forgot your password?</a>
            @endif
        </div>

        <hr class="text-muted my-4">

        <div class="text-center">
            <span class="text-muted">Don't have an account?</span>
            <a href="{{ route('register') }}" class="text-decoration-none fw-semibold ms-1">Register</a>
        </div>
    </form>
</x-guest-layout>
