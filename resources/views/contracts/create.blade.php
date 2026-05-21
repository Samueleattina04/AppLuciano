@extends('layouts.app')
@section('title', 'New Contract — SupplyManager')
@section('content')

<div class="d-flex align-items-center gap-2 mb-4">
    <a href="{{ route('contracts.index') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left"></i></a>
    <h1 class="page-title mb-0">New Contract</h1>
</div>

<form method="POST" action="{{ route('contracts.store') }}">
    @csrf
    <div class="row g-3">
        <div class="col-md-8">
            <div class="card mb-3">
                <div class="card-header">Contract Information</div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Contract Number *</label>
                            <input type="text" name="contract_number" class="form-control @error('contract_number') is-invalid @enderror"
                                value="{{ old('contract_number', 'ANT-' . date('Y') . '-') }}" required>
                            @error('contract_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Supplier *</label>
                            <select name="supplier_id" class="form-select @error('supplier_id') is-invalid @enderror" required>
                                <option value="">Select supplier...</option>
                                @foreach($suppliers as $s)
                                    <option value="{{ $s->id }}" {{ old('supplier_id') == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                                @endforeach
                            </select>
                            @error('supplier_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Product</label>
                            <select name="product_id" class="form-select">
                                <option value="">Select product...</option>
                                @foreach($products as $p)
                                    <option value="{{ $p->id }}" {{ old('product_id') == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Type</label>
                            <select name="type" class="form-select">
                                <option value="single" {{ old('type') == 'single' ? 'selected' : '' }}>Single</option>
                                <option value="framework" {{ old('type') == 'framework' ? 'selected' : '' }}>Framework</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Contract Date</label>
                            <input type="date" name="contract_date" class="form-control" value="{{ old('contract_date') }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Crop Season</label>
                            <input type="text" name="crop_season" class="form-control" value="{{ old('crop_season') }}" placeholder="e.g. 2024/2025">
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header">Commercial Terms</div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Quantity *</label>
                            <input type="number" name="quantity_contracted" step="0.001" class="form-control" value="{{ old('quantity_contracted') }}" required>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label fw-semibold">Unit</label>
                            <input type="text" name="unit_of_measure" class="form-control" value="{{ old('unit_of_measure', 'kg') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Unit Price *</label>
                            <input type="number" name="unit_price" step="0.0001" class="form-control" value="{{ old('unit_price') }}" required>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label fw-semibold">Currency</label>
                            <select name="currency" class="form-select">
                                <option value="USD" {{ old('currency','USD') == 'USD' ? 'selected' : '' }}>USD</option>
                                <option value="EUR" {{ old('currency') == 'EUR' ? 'selected' : '' }}>EUR</option>
                                <option value="GBP" {{ old('currency') == 'GBP' ? 'selected' : '' }}>GBP</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label fw-semibold">Total Value *</label>
                            <input type="number" name="total_value" step="0.01" class="form-control" value="{{ old('total_value') }}" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Incoterm</label>
                            <select name="incoterm" class="form-select">
                                <option value="">Select...</option>
                                @foreach(['FOB', 'CIF', 'CFR', 'EXW', 'DAP', 'DDP', 'FCA', 'CPT', 'CIP'] as $inc)
                                    <option value="{{ $inc }}" {{ old('incoterm') == $inc ? 'selected' : '' }}>{{ $inc }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Port of Loading</label>
                            <input type="text" name="port_of_loading" class="form-control" value="{{ old('port_of_loading') }}" placeholder="e.g. Shanghai">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Port of Discharge</label>
                            <input type="text" name="port_of_discharge" class="form-control" value="{{ old('port_of_discharge') }}" placeholder="e.g. Genova">
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header">Shipment Window & Payment Terms</div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Shipment Window Start</label>
                            <input type="date" name="shipment_window_start" class="form-control" value="{{ old('shipment_window_start') }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Shipment Window End</label>
                            <input type="date" name="shipment_window_end" class="form-control" value="{{ old('shipment_window_end') }}">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Payment Terms Description</label>
                            <textarea name="payment_terms_description" class="form-control" rows="2" placeholder="e.g. 30% advance, 70% against BL copy">{{ old('payment_terms_description') }}</textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card mb-3">
                <div class="card-header">Status & Notes</div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Status</label>
                        <select name="status" class="form-select">
                            <option value="draft" {{ old('status','draft') == 'draft' ? 'selected' : '' }}>Draft</option>
                            <option value="confirmed" {{ old('status') == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                            <option value="partially_shipped" {{ old('status') == 'partially_shipped' ? 'selected' : '' }}>Partially Shipped</option>
                            <option value="completed" {{ old('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                            <option value="cancelled" {{ old('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Notes</label>
                        <textarea name="notes" class="form-control" rows="5">{{ old('notes') }}</textarea>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <button type="submit" class="btn btn-primary w-100 mb-2">
                        <i class="bi bi-check-circle me-1"></i>Create Contract
                    </button>
                    <a href="{{ route('contracts.index') }}" class="btn btn-outline-secondary w-100">Cancel</a>
                </div>
            </div>
        </div>
    </div>
</form>

@endsection
