@php $incotermsOptions = ['EXW','FCA','FAS','FOB','CFR','CIF','CPT','CIP','DAP','DPU','DDP']; @endphp

<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label fw-semibold">N° Contratto <span class="text-danger">*</span></label>
        <input type="text" name="contract_number" class="form-control @error('contract_number') is-invalid @enderror"
            value="{{ old('contract_number', $contract?->contract_number) }}" required>
        @error('contract_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-6">
        <label class="form-label fw-semibold">Fornitore <span class="text-danger">*</span></label>
        <select name="supplier_id" class="form-select @error('supplier_id') is-invalid @enderror" required>
            <option value="">— Seleziona fornitore —</option>
            @foreach($suppliers as $s)
                <option value="{{ $s->id }}" {{ old('supplier_id', $contract?->supplier_id) == $s->id ? 'selected' : '' }}>
                    {{ $s->name }} ({{ $s->country }})
                </option>
            @endforeach
        </select>
        @error('supplier_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-4">
        <label class="form-label fw-semibold">Data Contratto <span class="text-danger">*</span></label>
        <input type="date" name="contract_date" class="form-control @error('contract_date') is-invalid @enderror"
            value="{{ old('contract_date', $contract?->contract_date?->format('Y-m-d')) }}" required>
        @error('contract_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-4">
        <label class="form-label fw-semibold">Incoterms <span class="text-danger">*</span></label>
        <select name="incoterms" class="form-select @error('incoterms') is-invalid @enderror" required>
            @foreach($incotermsOptions as $inc)
                <option value="{{ $inc }}" {{ old('incoterms', $contract?->incoterms) == $inc ? 'selected' : '' }}>{{ $inc }}</option>
            @endforeach
        </select>
        @error('incoterms')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-4">
        <label class="form-label fw-semibold">Valuta <span class="text-danger">*</span></label>
        <select name="currency" class="form-select @error('currency') is-invalid @enderror" required>
            @foreach(['USD','EUR','CNY','GBP','JPY'] as $cur)
                <option value="{{ $cur }}" {{ old('currency', $contract?->currency ?? 'USD') == $cur ? 'selected' : '' }}>{{ $cur }}</option>
            @endforeach
        </select>
        @error('currency')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-6">
        <label class="form-label fw-semibold">Valore Totale <span class="text-danger">*</span></label>
        <div class="input-group">
            <span class="input-group-text"><i class="bi bi-currency-dollar"></i></span>
            <input type="number" step="0.01" min="0" name="total_value" class="form-control @error('total_value') is-invalid @enderror"
                value="{{ old('total_value', $contract?->total_value) }}" required>
            @error('total_value')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>
    <div class="col-12">
        <label class="form-label fw-semibold">Note</label>
        <textarea name="notes" rows="3" class="form-control @error('notes') is-invalid @enderror">{{ old('notes', $contract?->notes) }}</textarea>
        @error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
</div>
