@extends('layouts.app')
@section('title', 'Modifica Pagamento — SupplyManager')
@section('content')

<div class="d-flex align-items-center gap-2 mb-4">
    <a href="{{ route('payments.index') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left"></i></a>
    <h1 class="page-title mb-0">Modifica Pagamento</h1>
</div>

@php $isAdvance = old('payment_type', $payment->payment_type) === 'advance'; @endphp

<form method="POST" action="{{ route('payments.update', $payment) }}">
    @csrf @method('PUT')
    <div class="row g-3">
        <div class="col-md-8">

            {{-- TIPO --}}
            <div class="card mb-3">
                <div class="card-header">Tipo di Pagamento</div>
                <div class="card-body">
                    <div class="d-flex gap-3">
                        <label class="d-flex align-items-center gap-2 border rounded px-4 py-3 flex-grow-1 payment-type-card {{ !$isAdvance ? 'border-primary bg-primary bg-opacity-10' : '' }}"
                            style="cursor:pointer" for="type_shipment">
                            <input type="radio" id="type_shipment" name="payment_type" value="shipment_payment" {{ !$isAdvance ? 'checked' : '' }}>
                            <div>
                                <div class="fw-semibold"><i class="bi bi-box-seam me-1 text-primary"></i>Pagamento Spedizione</div>
                                <div class="text-muted" style="font-size:0.8rem">Collegato a una specifica spedizione</div>
                            </div>
                        </label>
                        <label class="d-flex align-items-center gap-2 border rounded px-4 py-3 flex-grow-1 payment-type-card {{ $isAdvance ? 'border-info bg-info bg-opacity-10' : '' }}"
                            style="cursor:pointer" for="type_advance">
                            <input type="radio" id="type_advance" name="payment_type" value="advance" {{ $isAdvance ? 'checked' : '' }}>
                            <div>
                                <div class="fw-semibold"><i class="bi bi-cash-coin me-1 text-info"></i>Acconto Contratto</div>
                                <div class="text-muted" style="font-size:0.8rem">Anticipo sul contratto, non su spedizione</div>
                            </div>
                        </label>
                    </div>
                </div>
            </div>

            {{-- CONTRATTO + SPEDIZIONE --}}
            <div class="card mb-3">
                <div class="card-header">Contratto &amp; Fornitore</div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Contratto *</label>
                            <select id="contract_select" name="contract_id" class="form-select" required>
                                @foreach($contracts as $c)
                                    <option value="{{ $c->id }}" {{ old('contract_id', $payment->contract_id) == $c->id ? 'selected' : '' }}>
                                        {{ $c->contract_number }} — {{ $c->supplier->name ?? '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Fornitore</label>
                            <div id="supplier_display" class="form-control bg-light">{{ $payment->supplier->name ?? '—' }}</div>
                        </div>

                        {{-- SPEDIZIONE autocomplete --}}
                        <div class="col-12" id="shipment_row" style="{{ $isAdvance ? 'display:none' : '' }}">
                            <label class="form-label fw-semibold">Spedizione</label>
                            <input type="hidden" id="shipment_id" name="shipment_id" value="{{ old('shipment_id', $payment->shipment_id) }}">
                            <div class="position-relative">
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                                    <input type="text" id="shipment_search" class="form-control"
                                        placeholder="Cerca per codice, container, BL, nave..."
                                        autocomplete="off">
                                    <button type="button" id="shipment_clear" class="btn btn-outline-secondary"
                                        style="{{ $payment->shipment_id ? '' : 'display:none' }}" title="Rimuovi">
                                        <i class="bi bi-x"></i>
                                    </button>
                                </div>
                                <div id="shipment_dropdown" class="position-absolute w-100 border rounded bg-white shadow-sm"
                                    style="display:none;z-index:1000;max-height:280px;overflow-y:auto;top:100%;margin-top:2px">
                                </div>
                            </div>
                            <div id="shipment_selected_info" class="mt-2" style="{{ $payment->shipment_id ? '' : 'display:none' }}">
                                <div class="alert alert-success py-2 px-3 mb-0 d-flex align-items-center gap-2" style="font-size:0.85rem">
                                    <i class="bi bi-check-circle-fill text-success"></i>
                                    <span id="shipment_selected_text">
                                        @if($payment->shipment)
                                            {{ $payment->shipment->shipment_code }}{{ $payment->shipment->container_number ? ' · Container: '.$payment->shipment->container_number : '' }}{{ $payment->shipment->bl_number ? ' · BL: '.$payment->shipment->bl_number : '' }}
                                        @endif
                                    </span>
                                </div>
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
                                    <option value="{{ $k }}" {{ old('doc_ref_type', $payment->doc_ref_type) == $k ? 'selected' : '' }}>{{ $v }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-8">
                            <label class="form-label fw-semibold">Numero / Riferimento Documento</label>
                            <input type="text" name="doc_ref_number" class="form-control"
                                value="{{ old('doc_ref_number', $payment->doc_ref_number) }}"
                                placeholder="es. FAT-2024-0123 / PRO-456 / BL ABCD123456">
                        </div>
                    </div>
                </div>
            </div>

            {{-- IMPORTI --}}
            <div class="card mb-3">
                <div class="card-header">Importi, Date &amp; Riferimenti Bancari</div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-3"><label class="form-label fw-semibold">Importo Dovuto *</label>
                            <input type="number" name="amount_due" step="0.01" class="form-control"
                                value="{{ old('amount_due', $payment->amount_due) }}" required></div>
                        <div class="col-md-3"><label class="form-label fw-semibold">Importo Pagato</label>
                            <input type="number" name="amount_paid" step="0.01" class="form-control"
                                value="{{ old('amount_paid', $payment->amount_paid) }}"></div>
                        <div class="col-md-2"><label class="form-label fw-semibold">Valuta</label>
                            <select id="currency_select" name="currency" class="form-select">
                                @foreach(['USD','EUR','GBP','CNY'] as $c)
                                    <option value="{{ $c }}" {{ old('currency', $payment->currency) == $c ? 'selected' : '' }}>{{ $c }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4"><label class="form-label fw-semibold">Stato</label>
                            <select name="status" class="form-select">
                                @foreach(\App\Models\Payment::STATUS_LABELS as $k => $v)
                                    <option value="{{ $k }}" {{ old('status', $payment->status) == $k ? 'selected' : '' }}>{{ $v }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4"><label class="form-label fw-semibold">Data Scadenza *</label>
                            <input type="date" name="due_date" class="form-control"
                                value="{{ old('due_date', $payment->due_date?->format('Y-m-d')) }}" required></div>
                        <div class="col-md-4"><label class="form-label fw-semibold">Data Pagamento</label>
                            <input type="date" name="payment_date" class="form-control"
                                value="{{ old('payment_date', $payment->payment_date?->format('Y-m-d')) }}"></div>
                        <div class="col-md-4"><label class="form-label fw-semibold">Riferimento Bancario / SWIFT</label>
                            <input type="text" name="bank_reference" class="form-control"
                                value="{{ old('bank_reference', $payment->bank_reference) }}"
                                placeholder="es. TRF-20240301-001"></div>
                        <div class="col-12"><label class="form-label fw-semibold">Note</label>
                            <textarea name="notes" class="form-control" rows="2">{{ old('notes', $payment->notes) }}</textarea></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card mb-3" id="advance_info_card" style="{{ $isAdvance ? '' : 'display:none' }}">
                <div class="card-body" style="background:#e0f2fe;border-radius:8px">
                    <div class="fw-semibold text-info mb-1"><i class="bi bi-info-circle me-1"></i>Acconto contratto</div>
                    <p class="text-muted mb-0" style="font-size:0.85rem">
                        Non associato a nessuna spedizione.<br>Scalato dal saldo totale del contratto.
                    </p>
                </div>
            </div>
            <div class="card mb-3">
                <div class="card-body" style="background:#f0fdf4;border-radius:8px">
                    <div class="fw-semibold mb-1" style="color:#166534"><i class="bi bi-file-earmark-check me-1"></i>Riferimento documento</div>
                    <p class="text-muted mb-0" style="font-size:0.82rem">
                        Il riferimento permette di:<br>
                        • Riconciliare i pagamenti con le fatture<br>
                        • Chiudere le partite contabili aperte<br>
                        • Verificare la situazione contabile corrente
                    </p>
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <button type="submit" class="btn btn-primary w-100 mb-2">
                        <i class="bi bi-check-circle me-1"></i>Aggiorna Pagamento
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
    div.addEventListener('mousedown', (e) => { e.preventDefault(); selectShipment(s); });
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

function hideDropdown() { shipmentDrop.style.display = 'none'; }

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
    showDropdown(pool.filter(s => s.search.includes(q)).slice(0, 30));
});
shipmentSearch.addEventListener('focus', function () { if (this.value.trim()) this.dispatchEvent(new Event('input')); });
shipmentSearch.addEventListener('blur', () => setTimeout(hideDropdown, 150));
shipmentClear.addEventListener('click', clearShipment);

function onContractChange() {
    const c = CONTRACTS[contractSel.value];
    if (c) {
        supplierDisp.textContent = c.supplier_name;
        supplierDisp.classList.remove('text-muted');
        [...currencySel.options].forEach(o => { o.selected = o.value === c.currency; });
        currentShipments = c.shipments;
        if (shipmentSearch.value) shipmentSearch.dispatchEvent(new Event('input'));
    } else {
        supplierDisp.textContent = '—';
        currentShipments = [];
    }
}

contractSel.addEventListener('change', onContractChange);

document.querySelectorAll('input[name="payment_type"]').forEach(radio => {
    radio.addEventListener('change', function () {
        const isAdv = this.value === 'advance';
        shipmentRow.style.display = isAdv ? 'none' : '';
        adviceCard.style.display  = isAdv ? '' : 'none';
        document.querySelectorAll('.payment-type-card').forEach(c => {
            c.classList.remove('border-primary','bg-primary','bg-opacity-10','border-info','bg-info');
        });
        if (isAdv) document.querySelector('label[for="type_advance"]').classList.add('border-info','bg-info','bg-opacity-10');
        else       document.querySelector('label[for="type_shipment"]').classList.add('border-primary','bg-primary','bg-opacity-10');
    });
});

// Init
if (contractSel.value) onContractChange();
</script>
@endpush
@endsection
