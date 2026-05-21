@extends('layouts.app')
@section('title', 'Nuovo Pagamento — SupplyManager')
@section('content')

<div class="d-flex align-items-center gap-2 mb-4">
    <a href="{{ route('payments.index') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left"></i></a>
    <h1 class="page-title mb-0">Nuovo Pagamento</h1>
</div>

<form method="POST" action="{{ route('payments.store') }}">
    @csrf
    <div class="row g-3">
        <div class="col-md-8">

            {{-- TIPO PAGAMENTO --}}
            <div class="card mb-3">
                <div class="card-header">Tipo di Pagamento</div>
                <div class="card-body">
                    <div class="d-flex gap-3">
                        <label class="d-flex align-items-center gap-2 border rounded px-4 py-3 flex-grow-1 cursor-pointer payment-type-card {{ old('payment_type','shipment_payment') === 'shipment_payment' ? 'border-primary bg-primary bg-opacity-10' : '' }}"
                            style="cursor:pointer" for="type_shipment">
                            <input type="radio" id="type_shipment" name="payment_type" value="shipment_payment"
                                {{ old('payment_type','shipment_payment') === 'shipment_payment' ? 'checked' : '' }}>
                            <div>
                                <div class="fw-semibold"><i class="bi bi-box-seam me-1 text-primary"></i>Pagamento Spedizione</div>
                                <div class="text-muted" style="font-size:0.8rem">Collegato a una specifica spedizione (saldo, BL, ecc.)</div>
                            </div>
                        </label>
                        <label class="d-flex align-items-center gap-2 border rounded px-4 py-3 flex-grow-1 cursor-pointer payment-type-card {{ old('payment_type') === 'advance' ? 'border-info bg-info bg-opacity-10' : '' }}"
                            style="cursor:pointer" for="type_advance">
                            <input type="radio" id="type_advance" name="payment_type" value="advance"
                                {{ old('payment_type') === 'advance' ? 'checked' : '' }}>
                            <div>
                                <div class="fw-semibold"><i class="bi bi-cash-coin me-1 text-info"></i>Acconto Contratto</div>
                                <div class="text-muted" style="font-size:0.8rem">Anticipo sul contratto, non legato a una spedizione</div>
                            </div>
                        </label>
                    </div>
                </div>
            </div>

            {{-- CONTRATTO + FORNITORE (readonly) --}}
            <div class="card mb-3">
                <div class="card-header">Contratto &amp; Fornitore</div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Contratto *</label>
                            <select id="contract_select" name="contract_id" class="form-select @error('contract_id') is-invalid @enderror" required>
                                <option value="">Seleziona contratto...</option>
                                @foreach($contracts as $c)
                                    <option value="{{ $c->id }}"
                                        {{ (old('contract_id') == $c->id || ($selectedContract && $selectedContract->id == $c->id)) ? 'selected' : '' }}>
                                        {{ $c->contract_number }} — {{ $c->supplier->name ?? '' }}
                                    </option>
                                @endforeach
                            </select>
                            @error('contract_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Fornitore</label>
                            <div id="supplier_display" class="form-control bg-light text-muted" style="cursor:default">
                                {{ $selectedContract ? ($selectedContract->supplier->name ?? '—') : 'Auto-compilato dal contratto' }}
                            </div>
                        </div>

                        {{-- SPEDIZIONE (solo per type=shipment_payment) --}}
                        <div class="col-12" id="shipment_row" style="{{ old('payment_type','shipment_payment') === 'advance' ? 'display:none' : '' }}">
                            <label class="form-label fw-semibold">Spedizione</label>
                            <select id="shipment_select" name="shipment_id" class="form-select @error('shipment_id') is-invalid @enderror">
                                <option value="">Nessuna / Seleziona dopo il contratto</option>
                            </select>
                            <div class="form-text">Seleziona prima il contratto per filtrare le spedizioni.</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- IMPORTI --}}
            <div class="card mb-3">
                <div class="card-header">Importi &amp; Date</div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Importo Dovuto *</label>
                            <input type="number" name="amount_due" step="0.01" class="form-control @error('amount_due') is-invalid @enderror"
                                value="{{ old('amount_due') }}" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Importo Pagato</label>
                            <input type="number" name="amount_paid" step="0.01" class="form-control" value="{{ old('amount_paid', 0) }}">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label fw-semibold">Valuta</label>
                            <select id="currency_select" name="currency" class="form-select">
                                @foreach(['USD','EUR','GBP','CNY'] as $c)
                                    <option value="{{ $c }}" {{ old('currency', $selectedContract?->currency ?? 'USD') == $c ? 'selected' : '' }}>{{ $c }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Stato</label>
                            <select name="status" class="form-select">
                                @foreach(\App\Models\Payment::STATUS_LABELS as $k => $v)
                                    <option value="{{ $k }}" {{ old('status','pending') == $k ? 'selected' : '' }}>{{ $v }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Data Scadenza *</label>
                            <input type="date" name="due_date" class="form-control" value="{{ old('due_date') }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Data Pagamento</label>
                            <input type="date" name="payment_date" class="form-control" value="{{ old('payment_date') }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Riferimento Bancario</label>
                            <input type="text" name="bank_reference" class="form-control" value="{{ old('bank_reference') }}">
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
            <div class="card mb-3" id="advance_info_card" style="{{ old('payment_type','shipment_payment') === 'advance' ? '' : 'display:none' }}">
                <div class="card-body" style="background:#e0f2fe;border-radius:8px">
                    <div class="fw-semibold text-info mb-1"><i class="bi bi-info-circle me-1"></i>Acconto contratto</div>
                    <p class="text-muted mb-0" style="font-size:0.85rem">
                        L'acconto viene registrato a livello di contratto.<br>
                        Non è associato a nessuna spedizione specifica.<br>
                        Viene scalato dal saldo totale del contratto.
                    </p>
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <button type="submit" class="btn btn-primary w-100 mb-2">
                        <i class="bi bi-check-circle me-1"></i>Crea Pagamento
                    </button>
                    <a href="{{ route('payments.index') }}" class="btn btn-outline-secondary w-100">Annulla</a>
                </div>
            </div>
        </div>
    </div>
</form>

@push('scripts')
<script>
const CONTRACTS = @json($contractsJson);

const contractSel  = document.getElementById('contract_select');
const supplierDisp = document.getElementById('supplier_display');
const shipmentSel  = document.getElementById('shipment_select');
const currencySel  = document.getElementById('currency_select');
const shipmentRow  = document.getElementById('shipment_row');
const adviceCard   = document.getElementById('advance_info_card');

function populateShipments(contractId, selectedShipmentId) {
    const c = CONTRACTS[contractId];
    shipmentSel.innerHTML = '<option value="">— Nessuna spedizione —</option>';
    if (c && c.shipments.length) {
        c.shipments.forEach(s => {
            const opt = document.createElement('option');
            opt.value = s.id;
            opt.textContent = s.code;
            if (selectedShipmentId && s.id == selectedShipmentId) opt.selected = true;
            shipmentSel.appendChild(opt);
        });
    }
}

function onContractChange() {
    const c = CONTRACTS[contractSel.value];
    if (c) {
        supplierDisp.textContent = c.supplier_name;
        supplierDisp.classList.remove('text-muted');
        // Auto-select currency from contract
        [...currencySel.options].forEach(o => { o.selected = o.value === c.currency; });
        populateShipments(contractSel.value, null);
    } else {
        supplierDisp.textContent = 'Auto-compilato dal contratto';
        supplierDisp.classList.add('text-muted');
        shipmentSel.innerHTML = '<option value="">Seleziona prima il contratto</option>';
    }
}

contractSel.addEventListener('change', onContractChange);

// Payment type toggle
document.querySelectorAll('input[name="payment_type"]').forEach(radio => {
    radio.addEventListener('change', function () {
        const isAdvance = this.value === 'advance';
        shipmentRow.style.display  = isAdvance ? 'none' : '';
        adviceCard.style.display   = isAdvance ? '' : 'none';
        // Style cards
        document.querySelectorAll('.payment-type-card').forEach(c => {
            c.classList.remove('border-primary','bg-primary','bg-opacity-10','border-info','bg-info');
        });
        if (isAdvance) {
            document.querySelector('label[for="type_advance"]').classList.add('border-info','bg-info','bg-opacity-10');
        } else {
            document.querySelector('label[for="type_shipment"]').classList.add('border-primary','bg-primary','bg-opacity-10');
        }
    });
});

// Init on load (in case of old() re-population)
if (contractSel.value) onContractChange();
@if(old('shipment_id'))
populateShipments(contractSel.value, {{ old('shipment_id') }});
@endif
</script>
@endpush
@endsection
