@extends('layouts.app')
@section('title', 'New Shipment — SupplyManager')
@section('content')

<div class="d-flex align-items-center gap-2 mb-4">
    <a href="{{ route('shipments.index') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left"></i></a>
    <h1 class="page-title mb-0">New Shipment</h1>
</div>

<form method="POST" action="{{ route('shipments.store') }}">
    @csrf
    <div class="row g-3">
        <div class="col-md-8">
            <div class="card mb-3">
                <div class="card-header">Contract & Supplier</div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Contract *</label>
                            <select name="contract_id" class="form-select @error('contract_id') is-invalid @enderror" required>
                                <option value="">Select contract...</option>
                                @foreach($contracts as $c)
                                    <option value="{{ $c->id }}" data-supplier="{{ $c->supplier_id }}" {{ (old('contract_id') == $c->id || ($selectedContract && $selectedContract->id == $c->id)) ? 'selected' : '' }}>
                                        {{ $c->contract_number }} — {{ $c->supplier->name ?? '' }}
                                    </option>
                                @endforeach
                            </select>
                            @error('contract_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Supplier *</label>
                            <select name="supplier_id" class="form-select" required>
                                <option value="">Select supplier...</option>
                                @foreach($suppliers as $s)
                                    <option value="{{ $s->id }}" {{ (old('supplier_id') == $s->id || ($selectedContract && $selectedContract->supplier_id == $s->id)) ? 'selected' : '' }}>{{ $s->name }}</option>
                                @endforeach
                            </select>
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
                            <label class="form-label fw-semibold">Quantity Shipped *</label>
                            <input type="number" name="quantity_shipped" step="0.001" class="form-control" value="{{ old('quantity_shipped') }}" required>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header">Container & Vessel</div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4"><label class="form-label fw-semibold">Container #</label><input type="text" name="container_number" class="form-control" value="{{ old('container_number') }}" placeholder="XXXX1234567"></div>
                        <div class="col-md-4"><label class="form-label fw-semibold">Seal #</label><input type="text" name="seal_number" class="form-control" value="{{ old('seal_number') }}"></div>
                        <div class="col-md-4"><label class="form-label fw-semibold">BL Number</label><input type="text" name="bl_number" class="form-control" value="{{ old('bl_number') }}"></div>
                        <div class="col-md-4"><label class="form-label fw-semibold">Vessel Name</label><input type="text" name="vessel_name" class="form-control" value="{{ old('vessel_name') }}"></div>
                        <div class="col-md-3"><label class="form-label fw-semibold">Voyage #</label><input type="text" name="voyage_number" class="form-control" value="{{ old('voyage_number') }}"></div>
                        <div class="col-md-3"><label class="form-label fw-semibold">Carrier</label><input type="text" name="carrier" class="form-control" value="{{ old('carrier') }}"></div>
                        <div class="col-md-4"><label class="form-label fw-semibold">Forwarder</label><input type="text" name="forwarder" class="form-control" value="{{ old('forwarder') }}"></div>
                    </div>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header">Ports & Dates</div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4"><label class="form-label fw-semibold">Port of Loading</label><input type="text" name="port_of_loading" class="form-control" value="{{ old('port_of_loading', $selectedContract?->port_of_loading) }}"></div>
                        <div class="col-md-4"><label class="form-label fw-semibold">Port of Discharge</label><input type="text" name="port_of_discharge" class="form-control" value="{{ old('port_of_discharge', $selectedContract?->port_of_discharge) }}"></div>
                        <div class="col-md-4"><label class="form-label fw-semibold">ETD</label><input type="date" name="etd" class="form-control" value="{{ old('etd') }}"></div>
                        <div class="col-md-4"><label class="form-label fw-semibold">ETA</label><input type="date" name="eta" class="form-control" value="{{ old('eta') }}"></div>
                        <div class="col-md-4"><label class="form-label fw-semibold">Actual Arrival</label><input type="date" name="actual_arrival_date" class="form-control" value="{{ old('actual_arrival_date') }}"></div>
                        <div class="col-md-4"><label class="form-label fw-semibold">Warehouse Arrival</label><input type="date" name="warehouse_arrival_date" class="form-control" value="{{ old('warehouse_arrival_date') }}"></div>
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
                            @foreach(\App\Models\Shipment::STATUS_LABELS as $k => $v)
                                <option value="{{ $k }}" {{ old('status','in_production') == $k ? 'selected' : '' }}>{{ $v }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Notes</label>
                        <textarea name="notes" class="form-control" rows="4">{{ old('notes') }}</textarea>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <button type="submit" class="btn btn-success w-100 mb-2">
                        <i class="bi bi-check-circle me-1"></i>Create Shipment
                    </button>
                    <a href="{{ route('shipments.index') }}" class="btn btn-outline-secondary w-100">Cancel</a>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection
