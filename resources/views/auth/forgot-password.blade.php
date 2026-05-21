<x-guest-layout>
    <h5 class="fw-bold mb-3 text-center">Reset Password</h5>
    <p class="text-muted small mb-4 text-center">Inserisci la tua email e ti invieremo un link per reimpostare la password.</p>

    @if(session('status'))
        <div class="alert alert-success mb-3">{{ session('status') }}</div>
    @endif

    <form method="POST" action="{{ route('password.email') }}">
        @csrf
        <div class="mb-3">
            <label for="email" class="form-label fw-semibold">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}"
                   class="form-control @error('email') is-invalid @enderror" required autofocus>
            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="d-flex justify-content-between align-items-center">
            <a href="{{ route('login') }}" class="text-muted small text-decoration-none">← Torna al login</a>
            <button type="submit" class="btn btn-primary">Invia link reset</button>
        </div>
    </form>
</x-guest-layout>
