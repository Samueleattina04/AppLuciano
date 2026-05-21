@extends('layouts.app')
@section('title', 'Nuovo Task Comunicazione — SupplyManager')
@section('content')
<div class="d-flex align-items-center gap-2 mb-4">
    <a href="{{ route('communications.index') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left"></i></a>
    <h1 class="page-title mb-0">Nuovo Task Comunicazione</h1>
    @if($selectedShipment)
        <span class="badge bg-info-subtle text-info border border-info-subtle">
            <i class="bi bi-box-seam me-1"></i>{{ $selectedShipment->shipment_code }}
            @if($selectedShipment->container_number) · {{ $selectedShipment->container_number }}@endif
        </span>
    @elseif($selectedContract)
        <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle">
            <i class="bi bi-file-text me-1"></i>{{ $selectedContract->contract_number }}
        </span>
    @endif
</div>
<form method="POST" action="{{ route('communications.store') }}">
    @csrf
    <div class="row g-3">
        <div class="col-md-8">
            <div class="card mb-3">
                <div class="card-header">Dettagli Task</div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-semibold">Oggetto *</label>
                            <input type="text" name="subject" class="form-control @error('subject') is-invalid @enderror"
                                value="{{ old('subject', $defaultSubject) }}" required>
                            @error('subject') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Mittente</label>
                            <input type="text" name="sender" class="form-control" value="{{ old('sender', $defaultSender) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Destinatario</label>
                            <input type="text" name="recipient" class="form-control" value="{{ old('recipient', $defaultRecipient) }}"
                                placeholder="es. Nome <email@fornitore.com>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Categoria</label>
                            <select name="category" class="form-select">
                                @foreach(\App\Models\CommunicationTask::CATEGORY_LABELS as $k => $v)
                                    <option value="{{ $k }}" {{ old('category', $defaultCategory) == $k ? 'selected' : '' }}>{{ $v }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Priorità</label>
                            <select name="priority" class="form-select">
                                @foreach(\App\Models\CommunicationTask::PRIORITY_LABELS as $k => $v)
                                    <option value="{{ $k }}" {{ old('priority','normal') == $k ? 'selected' : '' }}>{{ $v }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Stato</label>
                            <select name="status" class="form-select">
                                @foreach(\App\Models\CommunicationTask::STATUS_LABELS as $k => $v)
                                    <option value="{{ $k }}" {{ old('status','to_review') == $k ? 'selected' : '' }}>{{ $v }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Data Scadenza</label>
                            <input type="date" name="due_date" class="form-control" value="{{ old('due_date') }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Data Promemoria</label>
                            <input type="date" name="reminder_date" class="form-control" value="{{ old('reminder_date') }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Assegnato a</label>
                            <select name="assigned_to" class="form-select">
                                <option value="">Non assegnato</option>
                                @foreach($users as $u)
                                    <option value="{{ $u->id }}" {{ old('assigned_to') == $u->id ? 'selected' : '' }}>{{ $u->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card mb-3">
                <div class="card-header">Collegamento</div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Contratto</label>
                            <select name="contract_id" class="form-select">
                                <option value="">Nessuno</option>
                                @foreach($contracts as $c)
                                    <option value="{{ $c->id }}"
                                        {{ old('contract_id', $selectedContract?->id) == $c->id ? 'selected' : '' }}>
                                        {{ $c->contract_number }} — {{ $c->supplier->name ?? '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Spedizione</label>
                            <select name="shipment_id" class="form-select">
                                <option value="">Nessuna</option>
                                @foreach($shipments as $s)
                                    <option value="{{ $s->id }}"
                                        {{ old('shipment_id', $selectedShipment?->id) == $s->id ? 'selected' : '' }}>
                                        {{ $s->shipment_code }}@if($s->container_number) · {{ $s->container_number }}@endif
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Link Thread Outlook</label>
                            <div class="input-group">
                                <input type="url" name="outlook_thread_link" class="form-control"
                                    value="{{ old('outlook_thread_link') }}"
                                    placeholder="https://outlook.office.com/...">
                                <button type="button" class="btn btn-outline-secondary"
                                    onclick="if(document.querySelector('[name=outlook_thread_link]').value) window.open(document.querySelector('[name=outlook_thread_link]').value)">
                                    <i class="bi bi-box-arrow-up-right"></i> Apri
                                </button>
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Note</label>
                            <textarea name="notes" class="form-control" rows="4">{{ old('notes') }}</textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            @if($selectedShipment || $selectedContract)
            <div class="card mb-3" style="border-left:4px solid #0ea5e9">
                <div class="card-body py-2 px-3" style="font-size:0.85rem">
                    <div class="fw-semibold mb-1 text-info"><i class="bi bi-link-45deg me-1"></i>Contesto pre-compilato</div>
                    @if($selectedShipment)
                        <div><strong>Spedizione:</strong> {{ $selectedShipment->shipment_code }}</div>
                        @if($selectedShipment->container_number)<div><strong>Container:</strong> {{ $selectedShipment->container_number }}</div>@endif
                    @endif
                    @if($selectedContract)
                        <div><strong>Contratto:</strong> {{ $selectedContract->contract_number }}</div>
                        <div><strong>Fornitore:</strong> {{ $selectedContract->supplier->name ?? '—' }}</div>
                    @endif
                </div>
            </div>
            @endif
            <div class="card">
                <div class="card-body">
                    <button type="submit" class="btn btn-primary w-100 mb-2">
                        <i class="bi bi-check-circle me-1"></i>Crea Task
                    </button>
                    <a href="{{ route('communications.index') }}" class="btn btn-outline-secondary w-100">Annulla</a>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection
