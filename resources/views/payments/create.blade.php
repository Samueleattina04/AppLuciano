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

            {{-- TIPO --}}
            <div class="card mb-3">
                <div class="card-header">Tipo di Pagamento</div>
                <div class="card-body">
                    <div class="d-flex gap-3">
                        <label class="d-flex align-items-center gap-2 border rounded px-4 py-3 flex-grow-1 payment-type-card {{ old('payment_type','shipment_payment') === 'shipment_payment' ? 'border-primary bg-primary bg-opacity-10' : '' }}"
                            style="cursor:pointer" for="type_shipment">
                            <input type="radio" id="type_shipment" name="payment_type" value="shipment_payment"
                                {{ old('payment_type','shipment_payment') === 'shipment_payment' ? 'checked' : '' }}>
                            <div>
                                <div class="fw-semibold"><i class="bi bi-box-seam me-1 text-primary"></i>Pagamento Spedizione</div>
                                <div class="text-muted" style="font-size:0.8rem">Collegato a una specifica spedizione (saldo, BL, ecc.)</div>
                            </div>
                        </label>
                        <label class="d-flex align-items-center gap-2 border rounded px-4 py-3 flex-grow-1 payment-type-card {{ old('payment_type') === 'advance' ? 'border-info bg-info bg-opacity-10' : '' }}"
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

            {{-- CONTRATTO + FORNITORE + SPEDIZIONE --}}
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

                        {{-- SPEDIZIONE con autocomplete --}}
                        <div class="col-12" id="shipment_row" style="{{ old('payment_type','shipment_payment') === 'advance' ? 'display:none' : '' }}">
                            <label class="form-label fw-semibold">Spedizione</label>
                            <input type="hidden" id="shipment_id" name="shipment_id" value="{{ old('shipment_id') }}">

                            {{-- Campo di ricerca visibile --}}
                            <div class="position-relative">
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                                    <input type="text" id="shipment_search" class="form-control"
                                        placeholder="Cerca per codice, container, BL, nave, spedizioniere..."
                                        autocomplete="off">
                                    <button type="button" id="shipment_clear" class="btn btn-outline-secondary" style="display:none" title="Rimuovi">
                                        <i class="bi bi-x"></i>
                                    </button>
                                </div>
                                {{-- Dropdown risultati --}}
                                <div id="shipment_dropdown" class="position-absolute w-100 border rounded bg-white shadow-sm"
                                    style="display:none;z-index:1000;max-height:280px;overflow-y:auto;top:100%;margin-top:2px">
                                </div>
                            </div>
                            {{-- Riepilogo spedizione selezionata --}}
                            <div id="shipment_selected_info" class="mt-2" style="display:none">
                                <div class="alert alert-success py-2 px-3 mb-0 d-flex align-items-center gap-2" style="font-size:0.85rem">
                                    <i class="bi bi-check-circle-fill text-success"></i>
                                    <span id="shipment_selected_text"></span>
                                </div>
                            </div>
                            <div class="form-text">
                                Seleziona prima il contratto per filtrare. Puoi cercare per numero container, BL, nave o codice spedizione.
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- RIFERIMENTO DOCUMENTO --}}
            <div class="card mb-3">
                <div class="card-header"><i class="bi bi-file-earmark-text me-1"></i>Riferimento Documento Contabile</div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Tipo Documento</label>
                            <select name="doc_ref_type" class="form-select">
                                <option value="">— Nessuno —</option>
                                @foreach(\App\Models\Payment::DOC_REF_TYPES as $k => $v)
                                    <option value="{{ $k }}" {{ old('doc_ref_type') == $k ? 'selected' : '' }}>{{ $v }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-8">
                            <label class="form-label fw-semibold">Numero / Riferimento Documento</label>
                            <input type="text" name="doc_ref_number" class="form-control"
                                value="{{ old('doc_ref_number') }}"
                                placeholder="es. FAT-2024-0123 / PRO-456 / BL ABCD123456">
                            <div class="form-text">Inserire il numero del documento che si sta pagando (fattura, proforma, BL…). Usato per riconciliazione contabile.</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- IMPORTI --}}
            <div class="card mb-3">
                <div class="card-header">Importi, Date &amp; Riferimenti Bancari</div>
                <div class="card-body">
                    <div class="row g-3">
                        @if($proposedAmount !== null && !old('amount_due'))
                        <div class="col-12">
                            <div class="alert alert-info py-2 px-3 mb-0" style="font-size:0.85rem">
                                <i class="bi bi-lightbulb me-1"></i>
                                <strong>Importo suggerito:</strong> {{ number_format($proposedAmount, 2, ',', '.') }} {{ $selectedContract?->currency ?? '' }}
                                — calcolato come valore spedizione meno già pagato.
                            </div>
                        </div>
                        @endif
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Importo Dovuto *</label>
                            <input type="number" name="amount_due" step="0.01" class="form-control @error('amount_due') is-invalid @enderror"
                                value="{{ old('amount_due', $proposedAmount) }}" required>
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
                            <label class="form-label fw-semibold">Riferimento Bancario / SWIFT</label>
                            <input type="text" name="bank_reference" class="form-control" value="{{ old('bank_reference') }}"
                                placeholder="es. TRF-20240301-001">
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
            <div class="card mb-3">
                <div class="card-body" style="background:#f0fdf4;border-radius:8px">
                    <div class="fw-semibold mb-1" style="color:#166534"><i class="bi bi-file-earmark-check me-1"></i>Riferimento documento</div>
                    <p class="text-muted mb-0" style="font-size:0.82rem">
                        Il riferimento documento permette di:<br>
                        • Riconciliare i pagamenti con le fatture<br>
                        • Ripartire il pagamento tra spedizioni<br>
                        • Chiudere le partite contabili aperte<br>
                        • Verificare la situazione contabile corrente
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

const contractSel    = document.getElementById('contract_select');
const supplierDisp   = document.getElementById('supplier_display');
const currencySel    = document.getElementById('currency_select');
const shipmentRow    = document.getElementById('shipment_row');
const adviceCard     = document.getElementById('advance_info_card');
const shipmentSearch = document.getElementById('shipment_search');
const shipmentIdInp  = document.getElementById('shipment_id');
const shipmentDrop   = document.getElementById('shipment_dropdown');
const shipmentClear  = document.getElementById('shipment_clear');
const shipmentSelInfo= document.getElementById('shipment_selected_info');
const shipmentSelTxt = document.getElementById('shipment_selected_text');

let currentShipments = [];

// ─── Autocomplete spedizione ────────────────────────────────────────────────

function renderShipmentItem(s) {
    const div = document.createElement('div');
    div.className = 'px-3 py-2 border-bottom shipment-option';
    div.style.cssText = 'cursor:pointer;font-size:0.875rem';
    div.dataset.id = s.id;

    const details = [s.container, s.bl, s.vessel, s.eta ? 'ETA '+s.eta : ''].filter(Boolean).join(' · ');
    div.innerHTML = `
        <div class="fw-semibold">${s.code}${s.container ? ' <code style="font-size:.8em">'+s.container+'</code>' : ''}</div>
        ${details ? `<div class="text-muted" style="font-size:0.78rem">${details}</div>` : ''}
        ${s.forwarder ? `<div class="text-muted" style="font-size:0.75rem"><i class="bi bi-truck me-1"></i>${s.forwarder}</div>` : ''}
    `;
    div.addEventListener('mousedown', (e) => {
        e.preventDefault(); // prevent blur from firing first
        selectShipment(s);
    });
    div.addEventListener('mouseenter', () => div.style.background = '#f1f5f9');
    div.addEventListener('mouseleave', () => div.style.background = '');
    return div;
}

function showDropdown(items) {
    shipmentDrop.innerHTML = '';
    if (!items.length) {
        shipmentDrop.innerHTML = '<div class="px-3 py-2 text-muted" style="font-size:0.85rem">Nessuna spedizione trovata.</div>';
    } else {
        items.forEach(s => shipmentDrop.appendChild(renderShipmentItem(s)));
    }
    shipmentDrop.style.display = 'block';
}

function hideDropdown() {
    shipmentDrop.style.display = 'none';
}

function selectShipment(s) {
    shipmentIdInp.value = s.id;
    shipmentSearch.value = '';
    hideDropdown();

    const summary = [s.code, s.container ? 'Container: '+s.container : '', s.bl ? 'BL: '+s.bl : ''].filter(Boolean).join(' · ');
    shipmentSelTxt.textContent = summary;
    shipmentSelInfo.style.display = 'block';
    shipmentClear.style.display = 'inline-block';
}

function clearShipment() {
    shipmentIdInp.value = '';
    shipmentSearch.value = '';
    shipmentSelInfo.style.display = 'none';
    shipmentClear.style.display = 'none';
    hideDropdown();
}

shipmentSearch.addEventListener('input', function () {
    const q = this.value.trim().toLowerCase();
    if (!q) { hideDropdown(); return; }
    const pool = currentShipments.length ? currentShipments : Object.values(CONTRACTS).flatMap(c => c.shipments);
    const results = pool.filter(s => s.search.includes(q));
    showDropdown(results.slice(0, 30));
});

shipmentSearch.addEventListener('focus', function () {
    if (this.value.trim()) this.dispatchEvent(new Event('input'));
});

shipmentSearch.addEventListener('blur', function () {
    setTimeout(hideDropdown, 150);
});

shipmentClear.addEventListener('click', clearShipment);

// ─── Contratto change ────────────────────────────────────────────────────────

function onContractChange() {
    const c = CONTRACTS[contractSel.value];
    if (c) {
        supplierDisp.textContent = c.supplier_name;
        supplierDisp.classList.remove('text-muted');
        [...currencySel.options].forEach(o => { o.selected = o.value === c.currency; });
        currentShipments = c.shipments;
        if (shipmentSearch.value) shipmentSearch.dispatchEvent(new Event('input'));
    } else {
        supplierDisp.textContent = 'Auto-compilato dal contratto';
        supplierDisp.classList.add('text-muted');
        currentShipments = [];
    }
    // Reset shipment if contract changed
    clearShipment();
}

contractSel.addEventListener('change', onContractChange);

// ─── Tipo pagamento toggle ────────────────────────────────────────────────────

document.querySelectorAll('input[name="payment_type"]').forEach(radio => {
    radio.addEventListener('change', function () {
        const isAdv = this.value === 'advance';
        shipmentRow.style.display = isAdv ? 'none' : '';
        adviceCard.style.display  = isAdv ? '' : 'none';
        document.querySelectorAll('.payment-type-card').forEach(c => {
            c.classList.remove('border-primary','bg-primary','bg-opacity-10','border-info','bg-info');
        });
        if (isAdv) {
            document.querySelector('label[for="type_advance"]').classList.add('border-info','bg-info','bg-opacity-10');
        } else {
            document.querySelector('label[for="type_shipment"]').classList.add('border-primary','bg-primary','bg-opacity-10');
        }
    });
});

// ─── Init ─────────────────────────────────────────────────────────────────────
if (contractSel.value) onContractChange();

@if($selectedShipment)
// Pre-select shipment passed via URL context
(function(){
    const preShipId = {{ $selectedShipment->id }};
    const preShip = Object.values(CONTRACTS).flatMap(c=>c.shipments).find(s=>s.id===preShipId);
    if (preShip) selectShipment(preShip);
})();
@elseif(old('shipment_id'))
// Re-populate from old() after validation error
const oldShip = Object.values(CONTRACTS).flatMap(c=>c.shipments).find(s=>s.id=={{old('shipment_id')}});
if (oldShip) selectShipment(oldShip);
@endif
</script>
@endpush
@endsection
