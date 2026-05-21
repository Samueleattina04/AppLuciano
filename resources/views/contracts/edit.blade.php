@extends('layouts.app')
@section('title', 'Modifica Contratto — SupplyManager')
@section('content')

<div class="d-flex align-items-center gap-2 mb-4">
    <a href="{{ route('contracts.show', $contract) }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left"></i></a>
    <h1 class="page-title mb-0">Modifica Contratto {{ $contract->contract_number }}</h1>
</div>

<form method="POST" action="{{ route('contracts.update', $contract) }}">
    @csrf
    @method('PUT')
    <div class="row g-3">
        <div class="col-md-8">
            <div class="card mb-3">
                <div class="card-header">Informazioni Contratto</div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">N° Contratto *</label>
                            <input type="text" name="contract_number" class="form-control @error('contract_number') is-invalid @enderror"
                                value="{{ old('contract_number', $contract->contract_number) }}" required>
                            @error('contract_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Fornitore *</label>
                            <select name="supplier_id" class="form-select" required>
                                @foreach($suppliers as $s)
                                    <option value="{{ $s->id }}" {{ old('supplier_id', $contract->supplier_id) == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Prodotto</label>
                            <select name="product_id" class="form-select">
                                <option value="">Seleziona prodotto...</option>
                                @foreach($products as $p)
                                    <option value="{{ $p->id }}" {{ old('product_id', $contract->product_id) == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Tipo</label>
                            <select name="type" class="form-select">
                                <option value="single" {{ old('type', $contract->type) == 'single' ? 'selected' : '' }}>Singolo</option>
                                <option value="framework" {{ old('type', $contract->type) == 'framework' ? 'selected' : '' }}>Quadro</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Data Contratto</label>
                            <input type="date" name="contract_date" class="form-control" value="{{ old('contract_date', $contract->contract_date?->format('Y-m-d')) }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Stagione/Campagna</label>
                            <input type="text" name="crop_season" class="form-control" value="{{ old('crop_season', $contract->crop_season) }}">
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header">Condizioni Commerciali</div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Quantità *</label>
                            <input type="number" id="qty" name="quantity_contracted" step="0.001" class="form-control"
                                value="{{ old('quantity_contracted', $contract->quantity_contracted) }}" required>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label fw-semibold">Unità</label>
                            <select id="uom" name="unit_of_measure" class="form-select">
                                @foreach(['kg','MT','t','lb','lbs','bag50','bag25','sacchi'] as $u)
                                    <option value="{{ $u }}" {{ old('unit_of_measure', $contract->unit_of_measure) == $u ? 'selected' : '' }}>{{ $u }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label fw-semibold">kg / Unità</label>
                            <input type="number" id="kg_per_unit" name="kg_per_unit" step="0.0001" class="form-control"
                                value="{{ old('kg_per_unit', $contract->kg_per_unit) }}" required>
                            <div class="form-text" id="qty_kg_preview"></div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Prezzo Unitario *</label>
                            <input type="number" id="unit_price" name="unit_price" step="0.0001" class="form-control"
                                value="{{ old('unit_price', $contract->unit_price) }}" required>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label fw-semibold">Valuta</label>
                            <select id="currency" name="currency" class="form-select">
                                @foreach(['USD','EUR','GBP','CNY'] as $c)
                                    <option value="{{ $c }}" {{ old('currency', $contract->currency) == $c ? 'selected' : '' }}>{{ $c }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label fw-semibold">Cambio → EUR</label>
                            <input type="number" id="exchange_rate" name="exchange_rate_to_eur" step="0.000001" class="form-control"
                                value="{{ old('exchange_rate_to_eur', $contract->exchange_rate_to_eur) }}" required>
                            <div class="form-text" id="eur_preview"></div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Valore Totale <span class="text-muted">(calcolato)</span></label>
                            <div id="total_value_display" class="form-control bg-light fw-semibold text-end" style="min-height:38px">{{ number_format($contract->total_value, 2, ',', '.') }}</div>
                            <div class="form-text" id="total_eur_preview"></div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Incoterm</label>
                            <select name="incoterm" class="form-select">
                                <option value="">Seleziona...</option>
                                @foreach(['FOB', 'CIF', 'CFR', 'EXW', 'DAP', 'DDP', 'FCA', 'CPT', 'CIP'] as $inc)
                                    <option value="{{ $inc }}" {{ old('incoterm', $contract->incoterm) == $inc ? 'selected' : '' }}>{{ $inc }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Porto di Imbarco</label>
                            <input type="text" name="port_of_loading" class="form-control" value="{{ old('port_of_loading', $contract->port_of_loading) }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Porto di Scarico</label>
                            <input type="text" name="port_of_discharge" class="form-control" value="{{ old('port_of_discharge', $contract->port_of_discharge) }}">
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header">Finestra di Spedizione &amp; Termini di Pagamento</div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Inizio Finestra Spedizione</label>
                            <input type="date" name="shipment_window_start" class="form-control" value="{{ old('shipment_window_start', $contract->shipment_window_start?->format('Y-m-d')) }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Fine Finestra Spedizione</label>
                            <input type="date" name="shipment_window_end" class="form-control" value="{{ old('shipment_window_end', $contract->shipment_window_end?->format('Y-m-d')) }}">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Descrizione Termini di Pagamento</label>
                            <textarea name="payment_terms_description" class="form-control" rows="2">{{ old('payment_terms_description', $contract->payment_terms_description) }}</textarea>
                        </div>
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
                            @foreach(['draft' => 'Bozza', 'confirmed' => 'Confermato', 'partially_shipped' => 'Parz. Spedito', 'completed' => 'Completato', 'cancelled' => 'Annullato'] as $k => $v)
                                <option value="{{ $k }}" {{ old('status', $contract->status) == $k ? 'selected' : '' }}>{{ $v }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Note</label>
                        <textarea name="notes" class="form-control" rows="5">{{ old('notes', $contract->notes) }}</textarea>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <button type="submit" class="btn btn-primary w-100 mb-2">
                        <i class="bi bi-check-circle me-1"></i>Aggiorna Contratto
                    </button>
                    <a href="{{ route('contracts.show', $contract) }}" class="btn btn-outline-secondary w-100">Annulla</a>
                </div>
            </div>
        </div>
    </div>
</form>
@push('scripts')
<script>
const UOM_FACTORS = {kg:1,MT:1000,t:1000,lb:0.453592,lbs:0.453592,bag50:50,bag25:25,sacchi:50};
function fmt(n){return n.toLocaleString('it-IT',{minimumFractionDigits:0,maximumFractionDigits:2});}
function updatePreviews(){
    const qty     = parseFloat(document.getElementById('qty').value)||0;
    const kpu     = parseFloat(document.getElementById('kg_per_unit').value)||1;
    const price   = parseFloat(document.getElementById('unit_price').value)||0;
    const rate    = parseFloat(document.getElementById('exchange_rate').value)||1;
    const totalKg = qty*kpu;
    const total   = qty*price;
    const totalEur = total*rate;
    document.getElementById('qty_kg_preview').textContent = totalKg ? '≈ '+fmt(totalKg)+' kg totali' : '';
    document.getElementById('total_value_display').textContent = (qty&&price) ? fmt(total) : '—';
    document.getElementById('total_eur_preview').textContent = totalEur && rate!==1 ? '≈ € '+fmt(totalEur) : '';
}
document.getElementById('uom').addEventListener('change',function(){
    const s=UOM_FACTORS[this.value];
    if(s!==undefined) document.getElementById('kg_per_unit').value=s;
    updatePreviews();
});
['qty','kg_per_unit','unit_price','exchange_rate'].forEach(id=>{
    document.getElementById(id).addEventListener('input',updatePreviews);
});
updatePreviews();
</script>
@endpush
@endsection
