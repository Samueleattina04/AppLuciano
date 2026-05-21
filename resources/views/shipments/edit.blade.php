@extends('layouts.app')
@section('title', 'Modifica Spedizione — SupplyManager')
@section('content')

<div class="d-flex align-items-center gap-2 mb-4">
    <a href="{{ route('shipments.show', $shipment) }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left"></i></a>
    <h1 class="page-title mb-0">Modifica {{ $shipment->shipment_code }}</h1>
</div>

<form method="POST" action="{{ route('shipments.update', $shipment) }}">
    @csrf @method('PUT')
    <div class="row g-3">
        <div class="col-md-8">
            <div class="card mb-3">
                <div class="card-header">Contratto &amp; Fornitore</div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Contratto</label>
                            <select name="contract_id" class="form-select" required>
                                @foreach($contracts as $c)
                                    <option value="{{ $c->id }}" {{ old('contract_id', $shipment->contract_id) == $c->id ? 'selected' : '' }}>{{ $c->contract_number }} — {{ $c->supplier->name ?? '' }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Fornitore</label>
                            <select name="supplier_id" class="form-select" required>
                                @foreach($suppliers as $s)
                                    <option value="{{ $s->id }}" {{ old('supplier_id', $shipment->supplier_id) == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Prodotto</label>
                            <select name="product_id" class="form-select">
                                <option value="">Nessuno</option>
                                @foreach($products as $p)
                                    <option value="{{ $p->id }}" {{ old('product_id', $shipment->product_id) == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Quantità Spedita</label>
                            <input type="number" name="quantity_shipped" step="0.001" class="form-control" value="{{ old('quantity_shipped', $shipment->quantity_shipped) }}" required>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header">Container &amp; Nave</div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4"><label class="form-label fw-semibold">N° Container</label><input type="text" name="container_number" class="form-control" value="{{ old('container_number', $shipment->container_number) }}"></div>
                        <div class="col-md-4"><label class="form-label fw-semibold">N° Sigillo</label><input type="text" name="seal_number" class="form-control" value="{{ old('seal_number', $shipment->seal_number) }}"></div>
                        <div class="col-md-4"><label class="form-label fw-semibold">N° BL</label><input type="text" name="bl_number" class="form-control" value="{{ old('bl_number', $shipment->bl_number) }}"></div>
                        <div class="col-md-4"><label class="form-label fw-semibold">Nome Nave</label><input type="text" name="vessel_name" class="form-control" value="{{ old('vessel_name', $shipment->vessel_name) }}"></div>
                        <div class="col-md-3"><label class="form-label fw-semibold">N° Viaggio</label><input type="text" name="voyage_number" class="form-control" value="{{ old('voyage_number', $shipment->voyage_number) }}"></div>
                        <div class="col-md-3"><label class="form-label fw-semibold">Vettore</label><input type="text" name="carrier" class="form-control" value="{{ old('carrier', $shipment->carrier) }}"></div>
                        <div class="col-md-4"><label class="form-label fw-semibold">Spedizioniere</label><input type="text" name="forwarder" class="form-control" value="{{ old('forwarder', $shipment->forwarder) }}"></div>
                    </div>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header">Porti &amp; Date</div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4"><label class="form-label fw-semibold">Porto di Imbarco</label><input type="text" name="port_of_loading" class="form-control" value="{{ old('port_of_loading', $shipment->port_of_loading) }}"></div>
                        <div class="col-md-4"><label class="form-label fw-semibold">Porto di Scarico</label><input type="text" name="port_of_discharge" class="form-control" value="{{ old('port_of_discharge', $shipment->port_of_discharge) }}"></div>
                        <div class="col-md-4"><label class="form-label fw-semibold">ETD</label><input type="date" name="etd" class="form-control" value="{{ old('etd', $shipment->etd?->format('Y-m-d')) }}"></div>
                        <div class="col-md-4"><label class="form-label fw-semibold">ETA</label><input type="date" name="eta" class="form-control" value="{{ old('eta', $shipment->eta?->format('Y-m-d')) }}"></div>
                        <div class="col-md-4"><label class="form-label fw-semibold">Arrivo Effettivo</label><input type="date" name="actual_arrival_date" class="form-control" value="{{ old('actual_arrival_date', $shipment->actual_arrival_date?->format('Y-m-d')) }}"></div>
                        <div class="col-md-4"><label class="form-label fw-semibold">Arrivo Magazzino</label><input type="date" name="warehouse_arrival_date" class="form-control" value="{{ old('warehouse_arrival_date', $shipment->warehouse_arrival_date?->format('Y-m-d')) }}"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card mb-3">
                <div class="card-header">Stato &amp; Note</div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Stato</label>
                        <select name="status" class="form-select">
                            @foreach(\App\Models\Shipment::STATUS_LABELS as $k => $v)
                                <option value="{{ $k }}" {{ old('status', $shipment->status) == $k ? 'selected' : '' }}>{{ $v }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="form-label fw-semibold">Note</label>
                        <textarea name="notes" class="form-control" rows="4">{{ old('notes', $shipment->notes) }}</textarea>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <button type="submit" class="btn btn-primary w-100 mb-2"><i class="bi bi-check-circle me-1"></i>Aggiorna Spedizione</button>
                    <a href="{{ route('shipments.show', $shipment) }}" class="btn btn-outline-secondary w-100">Annulla</a>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection
