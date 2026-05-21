@extends('layouts.app')
@section('title', 'Edit Contract — SupplyManager')
@section('content')

<div class="d-flex align-items-center gap-2 mb-4">
    <a href="{{ route('contracts.show', $contract) }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left"></i></a>
    <h1 class="page-title mb-0">Edit Contract {{ $contract->contract_number }}</h1>
</div>

<form method="POST" action="{{ route('contracts.update', $contract) }}">
    @csrf
    @method('PUT')
    <div class="row g-3">
        <div class="col-md-8">
            <div class="card mb-3">
                <div class="card-header">Contract Information</div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Contract Number *</label>
                            <input type="text" name="contract_number" class="form-control @error('contract_number') is-invalid @enderror"
                                value="{{ old('contract_number', $contract->contract_number) }}" required>
                            @error('contract_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Supplier *</label>
                            <select name="supplier_id" class="form-select" required>
                                @foreach($suppliers as $s)
                                    <option value="{{ $s->id }}" {{ old('supplier_id', $contract->supplier_id) == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Product</label>
                            <select name="product_id" class="form-select">
                                <option value="">Select product...</option>
                                @foreach($products as $p)
                                    <option value="{{ $p->id }}" {{ old('product_id', $contract->product_id) == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Type</label>
                            <select name="type" class="form-select">
                                <option value="single" {{ old('type', $contract->type) == 'single' ? 'selected' : '' }}>Single</option>
                                <option value="framework" {{ old('type', $contract->type) == 'framework' ? 'selected' : '' }}>Framework</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Contract Date</label>
                            <input type="date" name="contract_date" class="form-control" value="{{ old('contract_date', $contract->contract_date?->format('Y-m-d')) }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Crop Season</label>
                            <input type="text" name="crop_season" class="form-control" value="{{ old('crop_season', $contract->crop_season) }}">
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
                            <input type="number" name="quantity_contracted" step="0.001" class="form-control" value="{{ old('quantity_contracted', $contract->quantity_contracted) }}" required>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label fw-semibold">Unit</label>
                            <input type="text" name="unit_of_measure" class="form-control" value="{{ old('unit_of_measure', $contract->unit_of_measure) }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Unit Price *</label>
                            <input type="number" name="unit_price" step="0.0001" class="form-control" value="{{ old('unit_price', $contract->unit_price) }}" required>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label fw-semibold">Currency</label>
                            <select name="currency" class="form-select">
                                @foreach(['USD', 'EUR', 'GBP'] as $c)
                                    <option value="{{ $c }}" {{ old('currency', $contract->currency) == $c ? 'selected' : '' }}>{{ $c }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label fw-semibold">Total Value *</label>
                            <input type="number" name="total_value" step="0.01" class="form-control" value="{{ old('total_value', $contract->total_value) }}" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Incoterm</label>
                            <select name="incoterm" class="form-select">
                                <option value="">Select...</option>
                                @foreach(['FOB', 'CIF', 'CFR', 'EXW', 'DAP', 'DDP', 'FCA', 'CPT', 'CIP'] as $inc)
                                    <option value="{{ $inc }}" {{ old('incoterm', $contract->incoterm) == $inc ? 'selected' : '' }}>{{ $inc }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Port of Loading</label>
                            <input type="text" name="port_of_loading" class="form-control" value="{{ old('port_of_loading', $contract->port_of_loading) }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Port of Discharge</label>
                            <input type="text" name="port_of_discharge" class="form-control" value="{{ old('port_of_discharge', $contract->port_of_discharge) }}">
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
                            <input type="date" name="shipment_window_start" class="form-control" value="{{ old('shipment_window_start', $contract->shipment_window_start?->format('Y-m-d')) }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Shipment Window End</label>
                            <input type="date" name="shipment_window_end" class="form-control" value="{{ old('shipment_window_end', $contract->shipment_window_end?->format('Y-m-d')) }}">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Payment Terms Description</label>
                            <textarea name="payment_terms_description" class="form-control" rows="2">{{ old('payment_terms_description', $contract->payment_terms_description) }}</textarea>
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
                            @foreach(['draft' => 'Draft', 'confirmed' => 'Confirmed', 'partially_shipped' => 'Partially Shipped', 'completed' => 'Completed', 'cancelled' => 'Cancelled'] as $k => $v)
                                <option value="{{ $k }}" {{ old('status', $contract->status) == $k ? 'selected' : '' }}>{{ $v }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Notes</label>
                        <textarea name="notes" class="form-control" rows="5">{{ old('notes', $contract->notes) }}</textarea>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <button type="submit" class="btn btn-primary w-100 mb-2">
                        <i class="bi bi-check-circle me-1"></i>Update Contract
                    </button>
                    <a href="{{ route('contracts.show', $contract) }}" class="btn btn-outline-secondary w-100">Cancel</a>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection
