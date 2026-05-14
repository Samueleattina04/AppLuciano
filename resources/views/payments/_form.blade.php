<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label fw-semibold">Contratto <span class="text-danger">*</span></label>
        <select name="contract_id" class="form-select @error('contract_id') is-invalid @enderror" required>
            <option value="">— Seleziona contratto —</option>
            @foreach($contracts as $ct)
                <option value="{{ $ct->id }}" {{ old('contract_id', $payment?->contract_id ?? request('contract_id')) == $ct->id ? 'selected' : '' }}>
                    {{ $ct->contract_number }} — {{ $ct->supplier->name }}
                </option>
            @endforeach
        </select>
        @error('contract_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-6">
        <label class="form-label fw-semibold">Descrizione <span class="text-danger">*</span></label>
        <input type="text" name="description" class="form-control @error('description') is-invalid @enderror"
            value="{{ old('description', $payment?->description) }}" placeholder="es. 30% Acconto, 70% a vista BL" required>
        @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-4">
        <label class="form-label fw-semibold">Importo <span class="text-danger">*</span></label>
        <input type="number" step="0.01" min="0" name="amount" class="form-control @error('amount') is-invalid @enderror"
            value="{{ old('amount', $payment?->amount) }}" required>
        @error('amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-4">
        <label class="form-label fw-semibold">% sul Totale</label>
        <div class="input-group">
            <input type="number" step="0.01" min="0" max="100" name="percentage" class="form-control @error('percentage') is-invalid @enderror"
                value="{{ old('percentage', $payment?->percentage) }}">
            <span class="input-group-text">%</span>
        </div>
        @error('percentage')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-4">
        <label class="form-label fw-semibold">Stato <span class="text-danger">*</span></label>
        <select name="status" class="form-select @error('status') is-invalid @enderror" required>
            <option value="pending" {{ old('status', $payment?->status ?? 'pending') === 'pending' ? 'selected' : '' }}>In Attesa</option>
            <option value="paid" {{ old('status', $payment?->status) === 'paid' ? 'selected' : '' }}>Pagato</option>
            <option value="overdue" {{ old('status', $payment?->status) === 'overdue' ? 'selected' : '' }}>Scaduto</option>
        </select>
        @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-4">
        <label class="form-label fw-semibold">Data Scadenza <span class="text-danger">*</span></label>
        <input type="date" name="due_date" class="form-control @error('due_date') is-invalid @enderror"
            value="{{ old('due_date', $payment?->due_date?->format('Y-m-d')) }}" required>
        @error('due_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-4">
        <label class="form-label fw-semibold">Data Pagamento</label>
        <input type="date" name="paid_date" class="form-control @error('paid_date') is-invalid @enderror"
            value="{{ old('paid_date', $payment?->paid_date?->format('Y-m-d')) }}">
        @error('paid_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-4">
        <label class="form-label fw-semibold">Riferimento Transazione</label>
        <input type="text" name="transaction_reference" class="form-control @error('transaction_reference') is-invalid @enderror"
            value="{{ old('transaction_reference', $payment?->transaction_reference) }}" placeholder="es. WIRE-20240523">
        @error('transaction_reference')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-12">
        <label class="form-label fw-semibold">Note</label>
        <textarea name="notes" rows="2" class="form-control">{{ old('notes', $payment?->notes) }}</textarea>
    </div>
</div>
