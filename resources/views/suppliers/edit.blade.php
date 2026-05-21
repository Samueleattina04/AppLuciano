@extends('layouts.app')
@section('title', 'Edit Supplier — SupplyManager')
@section('content')
<div class="d-flex align-items-center gap-2 mb-4">
    <a href="{{ route('suppliers.show', $supplier) }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left"></i></a>
    <h1 class="page-title mb-0">Edit {{ $supplier->name }}</h1>
</div>
<form method="POST" action="{{ route('suppliers.update', $supplier) }}">
    @csrf @method('PUT')
    <div class="row g-3">
        <div class="col-md-7">
            <div class="card">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-8"><label class="form-label fw-semibold">Name</label><input type="text" name="name" class="form-control" value="{{ old('name',$supplier->name) }}" required></div>
                        <div class="col-md-4"><label class="form-label fw-semibold">Country</label><input type="text" name="country" class="form-control" value="{{ old('country',$supplier->country) }}"></div>
                        <div class="col-md-6"><label class="form-label fw-semibold">Contact Name</label><input type="text" name="contact_name" class="form-control" value="{{ old('contact_name',$supplier->contact_name) }}"></div>
                        <div class="col-md-6"><label class="form-label fw-semibold">Contact Email</label><input type="email" name="contact_email" class="form-control" value="{{ old('contact_email',$supplier->contact_email) }}"></div>
                        <div class="col-md-6"><label class="form-label fw-semibold">Contact Phone</label><input type="text" name="contact_phone" class="form-control" value="{{ old('contact_phone',$supplier->contact_phone) }}"></div>
                        <div class="col-12"><label class="form-label fw-semibold">Notes</label><textarea name="notes" class="form-control" rows="3">{{ old('notes',$supplier->notes) }}</textarea></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-5">
            <div class="card">
                <div class="card-body">
                    <button type="submit" class="btn btn-primary w-100 mb-2">Update Supplier</button>
                    <a href="{{ route('suppliers.show', $supplier) }}" class="btn btn-outline-secondary w-100">Cancel</a>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection
