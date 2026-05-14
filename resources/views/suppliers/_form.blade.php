<div class="row g-3">
    <div class="col-md-8">
        <label class="form-label fw-semibold">Ragione Sociale <span class="text-danger">*</span></label>
        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
            value="{{ old('name', $supplier?->name) }}" required>
        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-4">
        <label class="form-label fw-semibold">Paese</label>
        <input type="text" name="country" class="form-control @error('country') is-invalid @enderror"
            value="{{ old('country', $supplier?->country) }}" placeholder="es. China">
        @error('country')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-6">
        <label class="form-label fw-semibold">Email Contatto</label>
        <input type="email" name="contact_email" class="form-control @error('contact_email') is-invalid @enderror"
            value="{{ old('contact_email', $supplier?->contact_email) }}">
        @error('contact_email')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-6">
        <label class="form-label fw-semibold">Telefono</label>
        <input type="text" name="contact_phone" class="form-control @error('contact_phone') is-invalid @enderror"
            value="{{ old('contact_phone', $supplier?->contact_phone) }}">
        @error('contact_phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-12">
        <label class="form-label fw-semibold">Note</label>
        <textarea name="notes" rows="3" class="form-control">{{ old('notes', $supplier?->notes) }}</textarea>
    </div>
</div>
