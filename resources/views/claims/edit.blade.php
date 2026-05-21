@extends('layouts.app')
@section('title', 'Edit Claim — SupplyManager')
@section('content')
<div class="d-flex align-items-center gap-2 mb-4">
    <a href="{{ route('claims.index') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left"></i></a>
    <h1 class="page-title mb-0">Edit Claim</h1>
</div>
<form method="POST" action="{{ route('claims.update', $claim) }}">
    @csrf @method('PUT')
    <div class="row g-3">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4"><label class="form-label fw-semibold">Contract</label>
                            <select name="contract_id" class="form-select" required>
                                @foreach($contracts as $c)<option value="{{ $c->id }}" {{ old('contract_id',$claim->contract_id) == $c->id ? 'selected' : '' }}>{{ $c->contract_number }}</option>@endforeach
                            </select>
                        </div>
                        <div class="col-md-4"><label class="form-label fw-semibold">Shipment</label>
                            <select name="shipment_id" class="form-select">
                                <option value="">None</option>
                                @foreach($shipments as $s)<option value="{{ $s->id }}" {{ old('shipment_id',$claim->shipment_id) == $s->id ? 'selected' : '' }}>{{ $s->shipment_code }}</option>@endforeach
                            </select>
                        </div>
                        <div class="col-md-4"><label class="form-label fw-semibold">Supplier</label>
                            <select name="supplier_id" class="form-select" required>
                                @foreach($suppliers as $s)<option value="{{ $s->id }}" {{ old('supplier_id',$claim->supplier_id) == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>@endforeach
                            </select>
                        </div>
                        <div class="col-md-4"><label class="form-label fw-semibold">Claim Type</label>
                            <select name="claim_type" class="form-select">
                                @foreach(\App\Models\Claim::TYPE_LABELS as $k => $v)<option value="{{ $k }}" {{ old('claim_type',$claim->claim_type) == $k ? 'selected' : '' }}>{{ $v }}</option>@endforeach
                            </select>
                        </div>
                        <div class="col-md-3"><label class="form-label fw-semibold">Amount</label><input type="number" name="amount" step="0.01" class="form-control" value="{{ old('amount',$claim->amount) }}"></div>
                        <div class="col-md-2"><label class="form-label fw-semibold">Currency</label>
                            <select name="currency" class="form-select">@foreach(['USD','EUR','GBP'] as $c)<option value="{{ $c }}" {{ old('currency',$claim->currency) == $c ? 'selected' : '' }}>{{ $c }}</option>@endforeach</select>
                        </div>
                        <div class="col-md-3"><label class="form-label fw-semibold">Status</label>
                            <select name="status" class="form-select">
                                @foreach(\App\Models\Claim::STATUS_LABELS as $k => $v)<option value="{{ $k }}" {{ old('status',$claim->status) == $k ? 'selected' : '' }}>{{ $v }}</option>@endforeach
                            </select>
                        </div>
                        <div class="col-md-3"><label class="form-label fw-semibold">Resolved Date</label><input type="date" name="resolved_date" class="form-control" value="{{ old('resolved_date',$claim->resolved_date?->format('Y-m-d')) }}"></div>
                        <div class="col-12"><label class="form-label fw-semibold">Reason</label><textarea name="reason" class="form-control" rows="3" required>{{ old('reason',$claim->reason) }}</textarea></div>
                        <div class="col-12"><label class="form-label fw-semibold">Notes</label><textarea name="notes" class="form-control" rows="2">{{ old('notes',$claim->notes) }}</textarea></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <button type="submit" class="btn btn-primary w-100 mb-2">Update Claim</button>
                    <a href="{{ route('claims.index') }}" class="btn btn-outline-secondary w-100">Cancel</a>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection
