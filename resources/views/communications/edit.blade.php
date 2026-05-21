@extends('layouts.app')
@section('title', 'Modifica Task Comunicazione — SupplyManager')
@section('content')
<div class="d-flex align-items-center gap-2 mb-4">
    <a href="{{ route('communications.show', $communication) }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left"></i></a>
    <h1 class="page-title mb-0">Modifica Task</h1>
</div>
<form method="POST" action="{{ route('communications.update', $communication) }}">
    @csrf @method('PUT')
    <div class="row g-3">
        <div class="col-md-8">
            <div class="card mb-3">
                <div class="card-header">Dettagli Task</div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-12"><label class="form-label fw-semibold">Oggetto</label><input type="text" name="subject" class="form-control" value="{{ old('subject',$communication->subject) }}" required></div>
                        <div class="col-md-6"><label class="form-label fw-semibold">Mittente</label><input type="text" name="sender" class="form-control" value="{{ old('sender',$communication->sender) }}"></div>
                        <div class="col-md-6"><label class="form-label fw-semibold">Destinatario</label><input type="text" name="recipient" class="form-control" value="{{ old('recipient',$communication->recipient) }}"></div>
                        <div class="col-md-4"><label class="form-label fw-semibold">Categoria</label>
                            <select name="category" class="form-select">
                                @foreach(\App\Models\CommunicationTask::CATEGORY_LABELS as $k => $v)<option value="{{ $k }}" {{ old('category',$communication->category) == $k ? 'selected' : '' }}>{{ $v }}</option>@endforeach
                            </select>
                        </div>
                        <div class="col-md-4"><label class="form-label fw-semibold">Priorità</label>
                            <select name="priority" class="form-select">
                                @foreach(\App\Models\CommunicationTask::PRIORITY_LABELS as $k => $v)<option value="{{ $k }}" {{ old('priority',$communication->priority) == $k ? 'selected' : '' }}>{{ $v }}</option>@endforeach
                            </select>
                        </div>
                        <div class="col-md-4"><label class="form-label fw-semibold">Stato</label>
                            <select name="status" class="form-select">
                                @foreach(\App\Models\CommunicationTask::STATUS_LABELS as $k => $v)<option value="{{ $k }}" {{ old('status',$communication->status) == $k ? 'selected' : '' }}>{{ $v }}</option>@endforeach
                            </select>
                        </div>
                        <div class="col-md-4"><label class="form-label fw-semibold">Data Scadenza</label><input type="date" name="due_date" class="form-control" value="{{ old('due_date',$communication->due_date?->format('Y-m-d')) }}"></div>
                        <div class="col-md-4"><label class="form-label fw-semibold">Data Promemoria</label><input type="date" name="reminder_date" class="form-control" value="{{ old('reminder_date',$communication->reminder_date?->format('Y-m-d')) }}"></div>
                        <div class="col-md-4"><label class="form-label fw-semibold">Assegnato a</label>
                            <select name="assigned_to" class="form-select">
                                <option value="">Non assegnato</option>
                                @foreach($users as $u)<option value="{{ $u->id }}" {{ old('assigned_to',$communication->assigned_to) == $u->id ? 'selected' : '' }}>{{ $u->name }}</option>@endforeach
                            </select>
                        </div>
                        <div class="col-md-6"><label class="form-label fw-semibold">Contratto</label>
                            <select name="contract_id" class="form-select">
                                <option value="">Nessuno</option>
                                @foreach($contracts as $c)<option value="{{ $c->id }}" {{ old('contract_id',$communication->contract_id) == $c->id ? 'selected' : '' }}>{{ $c->contract_number }}</option>@endforeach
                            </select>
                        </div>
                        <div class="col-md-6"><label class="form-label fw-semibold">Spedizione</label>
                            <select name="shipment_id" class="form-select">
                                <option value="">Nessuna</option>
                                @foreach($shipments as $s)<option value="{{ $s->id }}" {{ old('shipment_id',$communication->shipment_id) == $s->id ? 'selected' : '' }}>{{ $s->shipment_code }}</option>@endforeach
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Link Thread Outlook</label>
                            <input type="url" name="outlook_thread_link" class="form-control" value="{{ old('outlook_thread_link',$communication->outlook_thread_link) }}">
                        </div>
                        <div class="col-12"><label class="form-label fw-semibold">Note</label><textarea name="notes" class="form-control" rows="4">{{ old('notes',$communication->notes) }}</textarea></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <button type="submit" class="btn btn-primary w-100 mb-2">Aggiorna Task</button>
                    <a href="{{ route('communications.show', $communication) }}" class="btn btn-outline-secondary w-100">Annulla</a>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection
