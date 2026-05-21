<x-guest-layout>
    @if(session('status'))
        <div class="alert alert-success mb-3">{{ session('status') }}</div>
    @endif

    <h5 class="fw-bold mb-4 text-center">Accedi al tuo account</h5>

    <form method="POST" action="{{ route('login') }}">
        @csrf
        <div class="mb-3">
            <label for="email" class="form-label fw-semibold">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}"
                   class="form-control @error('email') is-invalid @enderror"
                   required autofocus autocomplete="username">
            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="mb-3">
            <label for="password" class="form-label fw-semibold">Password</label>
            <input id="password" type="password" name="password"
                   class="form-control @error('password') is-invalid @enderror"
                   required autocomplete="current-password">
            @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="mb-3 form-check">
            <input id="remember_me" type="checkbox" name="remember" class="form-check-input">
            <label for="remember_me" class="form-check-label">Ricordami</label>
        </div>
        <div class="d-flex align-items-center justify-content-between">
            @if(Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="text-decoration-none small text-muted">Password dimenticata?</a>
            @endif
            <button type="submit" class="btn btn-primary px-4">Accedi</button>
        </div>
    </form>
</x-guest-layout>
