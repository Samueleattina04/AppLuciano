@extends('layouts.app')
@section('title', 'Nuovo Fornitore — SupplyManager')
@section('content')
<div class="d-flex align-items-center gap-2 mb-4">
    <a href="{{ route('suppliers.index') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left"></i></a>
    <h1 class="page-title mb-0">Nuovo Fornitore</h1>
</div>
<form method="POST" action="{{ route('suppliers.store') }}">
    @csrf
    <div class="row g-3">
        <div class="col-md-7">
            <div class="card">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-8"><label class="form-label fw-semibold">Nome *</label><input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>@error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                        <div class="col-md-4"><label class="form-label fw-semibold">Paese</label><input type="text" name="country" class="form-control" value="{{ old('country') }}"></div>
                        <div class="col-md-6"><label class="form-label fw-semibold">Nome Contatto</label><input type="text" name="contact_name" class="form-control" value="{{ old('contact_name') }}"></div>
                        <div class="col-md-6"><label class="form-label fw-semibold">Email Contatto</label><input type="email" name="contact_email" class="form-control" value="{{ old('contact_email') }}"></div>
                        <div class="col-md-6"><label class="form-label fw-semibold">Telefono Contatto</label><input type="text" name="contact_phone" class="form-control" value="{{ old('contact_phone') }}"></div>
                        <div class="col-12"><label class="form-label fw-semibold">Note</label><textarea name="notes" class="form-control" rows="3">{{ old('notes') }}</textarea></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-5">
            <div class="card">
                <div class="card-body">
                    <button type="submit" class="btn btn-primary w-100 mb-2">Crea Fornitore</button>
                    <a href="{{ route('suppliers.index') }}" class="btn btn-outline-secondary w-100">Annulla</a>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection
