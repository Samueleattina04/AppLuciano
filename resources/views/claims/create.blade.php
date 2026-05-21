@extends('layouts.app')
@section('title', 'Nuovo Reclamo — SupplyManager')
@section('content')
<div class="d-flex align-items-center gap-2 mb-4">
    <a href="{{ route('claims.index') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left"></i></a>
    <h1 class="page-title mb-0">Nuovo Reclamo</h1>
    @if($selectedShipment)
        <span class="badge bg-warning-subtle text-warning border border-warning-subtle">
            <i class="bi bi-box-seam me-1"></i>{{ $selectedShipment->shipment_code }}
            @if($selectedShipment->container_number) · {{ $selectedShipment->container_number }}@endif
        </span>
    @elseif($selectedContract)
        <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle">
            <i class="bi bi-file-text me-1"></i>{{ $selectedContract->contract_number }}
        </span>
    @endif
</div>
<form method="POST" action="{{ route('claims.store') }}">
    @csrf
    <div class="row g-3">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Dettagli Reclamo</div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Contratto *</label>
                            <select name="contract_id" class="form-select @error('contract_id') is-invalid @enderror" required>
                                <option value="">Seleziona...</option>
                                @foreach($contracts as $c)
                                    <option value="{{ $c->id }}"
                                        {{ old('contract_id', $selectedContract?->id) == $c->id ? 'selected' : '' }}>
                                        {{ $c->contract_number }} — {{ $c->supplier->name ?? '' }}
                                    </option>
                                @endforeach
                            </select>
                            @error('contract_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Spedizione</label>
                            <select name="shipment_id" class="form-select">
                                <option value="">Nessuna</option>
                                @foreach($shipments as $s)
                                    <option value="{{ $s->id }}"
                                        {{ old('shipment_id', $selectedShipment?->id) == $s->id ? 'selected' : '' }}>
                                        {{ $s->shipment_code }}@if($s->container_number) · {{ $s->container_number }}@endif
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Fornitore *</label>
                            <select name="supplier_id" class="form-select @error('supplier_id') is-invalid @enderror" required>
                                <option value="">Seleziona...</option>
                                @foreach($suppliers as $s)
                                    <option value="{{ $s->id }}"
                                        {{ old('supplier_id', $autoSupplierId) == $s->id ? 'selected' : '' }}>
                                        {{ $s->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('supplier_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Tipo Reclamo</label>
                            <select name="claim_type" class="form-select">
                                @foreach(\App\Models\Claim::TYPE_LABELS as $k => $v)
                                    <option value="{{ $k }}" {{ old('claim_type') == $k ? 'selected' : '' }}>{{ $v }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Importo *</label>
                            <input type="number" name="amount" step="0.01" class="form-control @error('amount') is-invalid @enderror"
                                value="{{ old('amount') }}" required>
                            @error('amount') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-2">
                            <label class="form-label fw-semibold">Valuta</label>
                            <select name="currency" class="form-select">
                                @foreach(['USD','EUR','GBP','CNY'] as $c)
                                    <option value="{{ $c }}" {{ old('currency', $defaultCurrency) == $c ? 'selected' : '' }}>{{ $c }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Stato</label>
                            <select name="status" class="form-select">
                                @foreach(\App\Models\Claim::STATUS_LABELS as $k => $v)
                                    <option value="{{ $k }}" {{ old('status','open') == $k ? 'selected' : '' }}>{{ $v }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Data Risoluzione</label>
                            <input type="date" name="resolved_date" class="form-control" value="{{ old('resolved_date') }}">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Motivo *</label>
                            <textarea name="reason" class="form-control @error('reason') is-invalid @enderror" rows="3" required>{{ old('reason') }}</textarea>
                            @error('reason') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Note</label>
                            <textarea name="notes" class="form-control" rows="2">{{ old('notes') }}</textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            @if($selectedShipment || $selectedContract)
            <div class="card mb-3" style="border-left:4px solid #f59e0b">
                <div class="card-body py-2 px-3" style="font-size:0.85rem">
                    <div class="fw-semibold mb-1 text-warning"><i class="bi bi-link-45deg me-1"></i>Contesto pre-compilato</div>
                    @if($selectedShipment)
                        <div><strong>Spedizione:</strong> {{ $selectedShipment->shipment_code }}</div>
                        @if($selectedShipment->container_number)<div><strong>Container:</strong> {{ $selectedShipment->container_number }}</div>@endif
                    @endif
                    @if($selectedContract)
                        <div><strong>Contratto:</strong> {{ $selectedContract->contract_number }}</div>
                        <div><strong>Fornitore:</strong> {{ $selectedContract->supplier->name ?? '—' }}</div>
                    @endif
                </div>
            </div>
            @endif
            <div class="card">
                <div class="card-body">
                    <button type="submit" class="btn btn-warning w-100 mb-2">
                        <i class="bi bi-exclamation-triangle me-1"></i>Crea Reclamo
                    </button>
                    <a href="{{ route('claims.index') }}" class="btn btn-outline-secondary w-100">Annulla</a>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection
