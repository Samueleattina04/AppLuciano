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

            {{-- CONTRATTO --}}
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
                            <div id="supplier_display" class="form-control bg-light">
                                {{ $payment->supplier->name ?? '—' }}
                            </div>
                        </div>
                        <div class="col-12" id="shipment_row" style="{{ $isAdvance ? 'display:none' : '' }}">
                            <label class="form-label fw-semibold">Spedizione</label>
                            <select id="shipment_select" name="shipment_id" class="form-select">
                                <option value="">— Nessuna spedizione —</option>
                                @foreach($contracts->firstWhere('id', $payment->contract_id)?->shipments ?? [] as $s)
                                    <option value="{{ $s->id }}" {{ old('shipment_id', $payment->shipment_id) == $s->id ? 'selected' : '' }}>
                                        {{ $s->shipment_code }}{{ $s->container_number ? ' — ' . $s->container_number : '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            {{-- IMPORTI --}}
            <div class="card mb-3">
                <div class="card-header">Importi &amp; Date</div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-3"><label class="form-label fw-semibold">Importo Dovuto *</label>
                            <input type="number" name="amount_due" step="0.01" class="form-control" value="{{ old('amount_due', $payment->amount_due) }}" required></div>
                        <div class="col-md-3"><label class="form-label fw-semibold">Importo Pagato</label>
                            <input type="number" name="amount_paid" step="0.01" class="form-control" value="{{ old('amount_paid', $payment->amount_paid) }}"></div>
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
                            <input type="date" name="due_date" class="form-control" value="{{ old('due_date', $payment->due_date?->format('Y-m-d')) }}" required></div>
                        <div class="col-md-4"><label class="form-label fw-semibold">Data Pagamento</label>
                            <input type="date" name="payment_date" class="form-control" value="{{ old('payment_date', $payment->payment_date?->format('Y-m-d')) }}"></div>
                        <div class="col-md-4"><label class="form-label fw-semibold">Riferimento Bancario</label>
                            <input type="text" name="bank_reference" class="form-control" value="{{ old('bank_reference', $payment->bank_reference) }}"></div>
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
                        Non associato a nessuna spedizione.<br>Viene scalato dal saldo totale del contratto.
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
const contractSel  = document.getElementById('contract_select');
const supplierDisp = document.getElementById('supplier_display');
const shipmentSel  = document.getElementById('shipment_select');
const currencySel  = document.getElementById('currency_select');
const shipmentRow  = document.getElementById('shipment_row');
const adviceCard   = document.getElementById('advance_info_card');

function populateShipments(contractId, selectedId) {
    const c = CONTRACTS[contractId];
    const current = shipmentSel.value;
    shipmentSel.innerHTML = '<option value="">— Nessuna spedizione —</option>';
    if (c && c.shipments.length) {
        c.shipments.forEach(s => {
            const opt = document.createElement('option');
            opt.value = s.id;
            opt.textContent = s.code;
            if ((selectedId && s.id == selectedId) || (!selectedId && s.id == current)) opt.selected = true;
            shipmentSel.appendChild(opt);
        });
    }
}

contractSel.addEventListener('change', function () {
    const c = CONTRACTS[this.value];
    supplierDisp.textContent = c ? c.supplier_name : '—';
    if (c) [...currencySel.options].forEach(o => { o.selected = o.value === c.currency; });
    populateShipments(this.value, null);
});

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
</script>
@endpush
@endsection
