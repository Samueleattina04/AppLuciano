<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label fw-semibold">Contratto <span class="text-danger">*</span></label>
        <select name="contract_id" class="form-select @error('contract_id') is-invalid @enderror" required>
            <option value="">— Seleziona contratto —</option>
            @foreach($contracts as $ct)
                <option value="{{ $ct->id }}" {{ old('contract_id', $container?->contract_id ?? request('contract_id')) == $ct->id ? 'selected' : '' }}>
                    {{ $ct->contract_number }} — {{ $ct->supplier->name }}
                </option>
            @endforeach
        </select>
        @error('contract_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-6">
        <label class="form-label fw-semibold">N° Container <span class="text-danger">*</span></label>
        <input type="text" name="container_number" class="form-control @error('container_number') is-invalid @enderror"
            value="{{ old('container_number', $container?->container_number) }}" required placeholder="es. TCKU3456789">
        @error('container_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-4">
        <label class="form-label fw-semibold">Stato <span class="text-danger">*</span></label>
        <select name="status" class="form-select @error('status') is-invalid @enderror" required>
            @foreach(\App\Models\Container::STATUS_LABELS as $val => $label)
                <option value="{{ $val }}" {{ old('status', $container?->status ?? 'in_production') === $val ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </select>
        @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-4">
        <label class="form-label fw-semibold">Nome Nave</label>
        <input type="text" name="vessel_name" class="form-control @error('vessel_name') is-invalid @enderror"
            value="{{ old('vessel_name', $container?->vessel_name) }}">
        @error('vessel_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-4">
        <label class="form-label fw-semibold">N° Viaggio</label>
        <input type="text" name="voyage_number" class="form-control @error('voyage_number') is-invalid @enderror"
            value="{{ old('voyage_number', $container?->voyage_number) }}">
        @error('voyage_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-3">
        <label class="form-label fw-semibold">ETD</label>
        <input type="date" name="etd" class="form-control @error('etd') is-invalid @enderror"
            value="{{ old('etd', $container?->etd?->format('Y-m-d')) }}">
        @error('etd')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-3">
        <label class="form-label fw-semibold">ETA</label>
        <input type="date" name="eta" class="form-control @error('eta') is-invalid @enderror"
            value="{{ old('eta', $container?->eta?->format('Y-m-d')) }}">
        @error('eta')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-3">
        <label class="form-label fw-semibold">Porto di Carico</label>
        <input type="text" name="port_of_loading" class="form-control @error('port_of_loading') is-invalid @enderror"
            value="{{ old('port_of_loading', $container?->port_of_loading) }}" placeholder="es. Shanghai">
        @error('port_of_loading')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-3">
        <label class="form-label fw-semibold">Porto di Scarico</label>
        <input type="text" name="port_of_discharge" class="form-control @error('port_of_discharge') is-invalid @enderror"
            value="{{ old('port_of_discharge', $container?->port_of_discharge) }}" placeholder="es. Genova">
        @error('port_of_discharge')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-12">
        <label class="form-label fw-semibold">Note</label>
        <textarea name="notes" rows="3" class="form-control">{{ old('notes', $container?->notes) }}</textarea>
    </div>
</div>
