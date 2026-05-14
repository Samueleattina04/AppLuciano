<form method="POST" action="{{ route('documents.store') }}" enctype="multipart/form-data" class="mb-4">
    @csrf
    <input type="hidden" name="documentable_type" value="{{ $model }}">
    <input type="hidden" name="documentable_id" value="{{ $modelId }}">
    <div class="row g-2 align-items-end">
        <div class="col-md-3">
            <label class="form-label small fw-semibold">Tipo Documento</label>
            <select name="type" class="form-select form-select-sm @error('type') is-invalid @enderror" required>
                @foreach(\App\Models\Document::TYPE_LABELS as $val => $label)
                    <option value="{{ $val }}">{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label small fw-semibold">Nome / Descrizione</label>
            <input type="text" name="name" class="form-control form-control-sm @error('name') is-invalid @enderror" placeholder="es. BL originale" required>
        </div>
        <div class="col-md-4">
            <label class="form-label small fw-semibold">File (max 20MB)</label>
            <input type="file" name="file" class="form-control form-control-sm @error('file') is-invalid @enderror" required>
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-sm btn-primary w-100">
                <i class="bi bi-upload me-1"></i>Carica
            </button>
        </div>
    </div>
    @error('file')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
</form>
