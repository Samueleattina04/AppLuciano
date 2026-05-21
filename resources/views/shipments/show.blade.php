@extends('layouts.app')
@section('title', $shipment->shipment_code . ' — SupplyManager')

@section('content')

<!-- INTESTAZIONE -->
<div class="d-flex align-items-start gap-3 mb-3">
    <a href="{{ route('shipments.index') }}" class="btn btn-sm btn-outline-secondary mt-1"><i class="bi bi-arrow-left"></i></a>
    <div class="flex-grow-1">
        <div class="d-flex align-items-center gap-3 flex-wrap">
            <div>
                <h1 class="page-title mb-0">{{ $shipment->container_number ?? $shipment->shipment_code }}</h1>
                <small class="text-muted">{{ $shipment->shipment_code }} · {{ $shipment->supplier->name ?? '' }}</small>
            </div>
            <span class="badge-status status-{{ $shipment->status }}" style="font-size:0.85rem;padding:0.45em 1em">{{ $shipment->status_label }}</span>
        </div>
        <div class="d-flex gap-3 mt-2 flex-wrap" style="font-size:0.85rem;color:#64748b">
            @if($shipment->vessel_name)<span><i class="bi bi-ship me-1"></i>{{ $shipment->vessel_name }} @if($shipment->voyage_number)/ {{ $shipment->voyage_number }}@endif</span>@endif
            @if($shipment->etd)<span><i class="bi bi-calendar-minus me-1"></i>ETD: {{ $shipment->etd->format('d/m/Y') }}</span>@endif
            @if($shipment->eta)<span><i class="bi bi-calendar-check me-1"></i>ETA: {{ $shipment->eta->format('d/m/Y') }}</span>@endif
            @if($shipment->carrier)<span><i class="bi bi-truck me-1"></i>{{ $shipment->carrier }}</span>@endif
        </div>
    </div>
    <div class="d-flex gap-2">
        <!-- Aggiornamento Stato Rapido -->
        <form method="POST" action="{{ route('shipments.update-status', $shipment) }}" class="d-flex gap-1">
            @csrf
            <select name="status" class="form-select form-select-sm" style="width:auto">
                @foreach(\App\Models\Shipment::STATUS_LABELS as $k => $v)
                    <option value="{{ $k }}" {{ $shipment->status === $k ? 'selected' : '' }}>{{ $v }}</option>
                @endforeach
            </select>
            <button type="submit" class="btn btn-sm btn-primary">Aggiorna</button>
        </form>
        <a href="{{ route('shipments.edit', $shipment) }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-pencil"></i></a>
    </div>
</div>

<!-- AVVISI -->
@if(count($shipment->missing_critical_documents) > 0)
<div class="alert-bar mb-3" style="background:#fee2e2;color:#991b1b">
    <i class="bi bi-exclamation-triangle-fill"></i>
    <strong>Documenti Critici Mancanti:</strong> {{ implode(', ', array_map(fn($d) => \App\Models\Document::TYPE_LABELS[$d] ?? $d, $shipment->missing_critical_documents)) }}
</div>
@endif

@php $overduePayments = $shipment->payments->where('status', 'overdue'); @endphp
@if($overduePayments->count() > 0)
<div class="alert-bar mb-3" style="background:#fef9c3;color:#92400e">
    <i class="bi bi-clock-history"></i>
    <strong>Pagamenti Scaduti:</strong> {{ $overduePayments->count() }} pagamento/i scaduto/i per questa spedizione.
</div>
@endif

@if($shipment->eta && now()->diffInDays($shipment->eta, false) <= 3 && !in_array($shipment->status, ['arrived_pod', 'customs_clearance', 'delivered_warehouse', 'closed']))
<div class="alert-bar mb-3" style="background:#fff7ed;color:#c2410c">
    <i class="bi bi-geo-alt-fill"></i>
    <strong>ETA in Arrivo!</strong> Questa spedizione è attesa in {{ max(0, now()->diffInDays($shipment->eta, false)) }} giorno/i.
</div>
@endif

<!-- TIMELINE AVANZAMENTO -->
@php
$statuses = array_keys(\App\Models\Shipment::STATUS_LABELS);
$currentOrder = \App\Models\Shipment::STATUS_ORDER[$shipment->status] ?? 0;
@endphp
<div class="card mb-4">
    <div class="card-body py-3">
        <div class="shipment-progress">
            @foreach(\App\Models\Shipment::STATUS_LABELS as $key => $label)
            @php $order = \App\Models\Shipment::STATUS_ORDER[$key] ?? 0; @endphp
            <div class="progress-step {{ $order < $currentOrder ? 'completed' : ($order === $currentOrder ? 'active' : '') }}">
                <div class="step-circle">
                    @if($order < $currentOrder)
                    <i class="bi bi-check"></i>
                    @else
                    {{ $order }}
                    @endif
                </div>
                <span class="step-label">{{ $label }}</span>
            </div>
            @endforeach
        </div>
    </div>
</div>

<!-- TAB -->
<ul class="nav nav-tabs mb-3">
    <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#logistics-tab">Logistica</a></li>
    <li class="nav-item">
        <a class="nav-link" data-bs-toggle="tab" href="#checklist-tab">
            <i class="bi bi-list-check me-1"></i>Checklist Arrivo
            @php $done = $shipment->arrivalTasks->where('completed', true)->count(); $tot = $shipment->arrivalTasks->count(); @endphp
            <span class="badge {{ $done === $tot && $tot > 0 ? 'bg-success' : 'bg-secondary' }}">{{ $done }}/{{ $tot }}</span>
        </a>
    </li>
    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#docs-tab">Documenti <span class="badge bg-secondary">{{ $shipment->documents->count() }}</span></a></li>
    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#pay-tab">Pagamenti <span class="badge bg-secondary">{{ $shipment->payments->count() }}</span></a></li>
    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#claims-tab2">Reclami <span class="badge bg-secondary">{{ $shipment->claims->count() }}</span></a></li>
    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#comms-tab2">Comunicazioni <span class="badge bg-secondary">{{ $shipment->communicationTasks->count() }}</span></a></li>
    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#activity-tab2">Attività</a></li>
</ul>

<div class="tab-content">
    <!-- LOGISTICA -->
    <div class="tab-pane fade show active" id="logistics-tab">
        <div class="row g-3">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">Dettagli Spedizione</div>
                    <div class="card-body">
                        <table class="table table-sm table-borderless mb-0">
                            <tr><td class="text-muted" style="width:40%">Codice Spedizione</td><td><strong>{{ $shipment->shipment_code }}</strong></td></tr>
                            <tr><td class="text-muted">Contratto</td><td>@if($shipment->contract)<a href="{{ route('contracts.show', $shipment->contract) }}">{{ $shipment->contract->contract_number }}</a>@else —@endif</td></tr>
                            <tr><td class="text-muted">Fornitore</td><td>{{ $shipment->supplier->name ?? '—' }}</td></tr>
                            <tr><td class="text-muted">Prodotto</td><td>{{ $shipment->product->name ?? '—' }}</td></tr>
                            <tr><td class="text-muted">N° Container</td><td><code>{{ $shipment->container_number ?? '—' }}</code></td></tr>
                            <tr><td class="text-muted">N° Sigillo</td><td>{{ $shipment->seal_number ?? '—' }}</td></tr>
                            <tr><td class="text-muted">N° BL</td><td><strong>{{ $shipment->bl_number ?? '—' }}</strong></td></tr>
                            <tr><td class="text-muted">Quantità</td><td>
                                <strong>{{ number_format($shipment->quantity_shipped, 0) }} {{ $shipment->contract->unit_of_measure ?? '' }}</strong>
                                @if($shipment->contract?->kg_per_unit != 1)
                                    <span class="text-muted ms-1">({{ number_format($shipment->quantity_kg, 0) }} kg)</span>
                                @endif
                            </td></tr>
                            @if($shipment->value_eur > 0)
                            <tr><td class="text-muted">Valore Stimato</td><td>
                                {{ number_format($shipment->quantity_shipped * ($shipment->contract->unit_price ?? 0), 0) }} {{ $shipment->contract->currency ?? '' }}
                                @if($shipment->contract?->exchange_rate_to_eur != 1)
                                    <span class="text-success ms-1">≈ € {{ number_format($shipment->value_eur, 0) }}</span>
                                @endif
                            </td></tr>
                            @endif
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">Nave &amp; Date</div>
                    <div class="card-body">
                        <table class="table table-sm table-borderless mb-0">
                            <tr><td class="text-muted" style="width:40%">Nave</td><td><strong>{{ $shipment->vessel_name ?? '—' }}</strong></td></tr>
                            <tr><td class="text-muted">N° Viaggio</td><td>{{ $shipment->voyage_number ?? '—' }}</td></tr>
                            <tr><td class="text-muted">Vettore</td><td>{{ $shipment->carrier ?? '—' }}</td></tr>
                            <tr><td class="text-muted">Spedizioniere</td><td>{{ $shipment->forwarder ?? '—' }}</td></tr>
                            <tr><td class="text-muted">Porto di Imbarco</td><td>{{ $shipment->port_of_loading ?? '—' }}</td></tr>
                            <tr><td class="text-muted">Porto di Scarico</td><td>{{ $shipment->port_of_discharge ?? '—' }}</td></tr>
                            <tr><td class="text-muted">ETD</td><td>{{ $shipment->etd?->format('d/m/Y') ?? '—' }}</td></tr>
                            <tr><td class="text-muted">ETA</td><td>{{ $shipment->eta?->format('d/m/Y') ?? '—' }}</td></tr>
                            <tr><td class="text-muted">Arrivo Effettivo</td><td>{{ $shipment->actual_arrival_date?->format('d/m/Y') ?? '—' }}</td></tr>
                            <tr><td class="text-muted">Arrivo Magazzino</td><td>{{ $shipment->warehouse_arrival_date?->format('d/m/Y') ?? '—' }}</td></tr>
                        </table>
                    </div>
                </div>
                @if($shipment->notes)
                <div class="card mt-3">
                    <div class="card-header">Note</div>
                    <div class="card-body"><p class="mb-0" style="font-size:0.875rem">{{ $shipment->notes }}</p></div>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- CHECKLIST ARRIVO -->
    <div class="tab-pane fade" id="checklist-tab">
        <div class="row g-3">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <span><i class="bi bi-list-check me-1"></i>Task Pre-Arrivo Container</span>
                        @if($shipment->arrivalTasks->count() === 0)
                        <form method="POST" action="{{ route('shipments.arrival-tasks.defaults', $shipment) }}">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-magic me-1"></i>Aggiungi task predefiniti
                            </button>
                        </form>
                        @else
                        <small class="text-muted">{{ $shipment->arrivalTasks->where('completed',true)->count() }}/{{ $shipment->arrivalTasks->count() }} completati</small>
                        @endif
                    </div>
                    <div class="card-body p-0">
                        @forelse($shipment->arrivalTasks as $task)
                        @php
                            $daysLeft = $task->due_date ? now()->startOfDay()->diffInDays($task->due_date->startOfDay(), false) : null;
                        @endphp
                        <div class="d-flex align-items-start gap-3 px-3 py-2 border-bottom {{ $task->completed ? 'bg-light' : '' }}">
                            <form method="POST" action="{{ route('shipments.arrival-tasks.toggle', [$shipment, $task]) }}" style="margin-top:2px">
                                @csrf @method('PATCH')
                                <button type="submit" class="btn btn-sm p-0 border-0" title="{{ $task->completed ? 'Riapri' : 'Segna completato' }}"
                                    style="width:22px;height:22px;border-radius:50%;background:{{ $task->completed ? '#10b981' : 'transparent' }};border:2px solid {{ $task->completed ? '#10b981' : '#cbd5e1' }} !important;display:flex;align-items:center;justify-content:center">
                                    @if($task->completed)<i class="bi bi-check text-white" style="font-size:0.7rem"></i>@endif
                                </button>
                            </form>
                            <div class="flex-grow-1">
                                <div class="{{ $task->completed ? 'text-muted text-decoration-line-through' : 'fw-semibold' }}" style="font-size:0.9rem">
                                    {{ $task->title }}
                                </div>
                                @if($task->description)
                                <div class="text-muted" style="font-size:0.8rem">{{ $task->description }}</div>
                                @endif
                                @if($task->completed && $task->completedBy)
                                <div style="font-size:0.75rem;color:#10b981">
                                    <i class="bi bi-check-circle me-1"></i>Completato da {{ $task->completedBy->name }} il {{ $task->completed_at->format('d/m/Y H:i') }}
                                </div>
                                @elseif($task->due_date)
                                <div style="font-size:0.75rem;color:{{ $daysLeft < 0 ? '#dc2626' : ($daysLeft <= 3 ? '#f59e0b' : '#64748b') }}">
                                    <i class="bi bi-calendar me-1"></i>
                                    @if($daysLeft < 0)
                                        <strong>In ritardo di {{ abs($daysLeft) }}g</strong> ({{ $task->due_date->format('d/m/Y') }})
                                    @elseif($daysLeft === 0)
                                        <strong>Scade oggi</strong>
                                    @else
                                        Scade {{ $task->due_date->format('d/m/Y') }} (tra {{ $daysLeft }}g)
                                    @endif
                                    @if($task->days_before_eta)<span class="text-muted ms-1">· {{ $task->days_before_eta }}gg prima ETA</span>@endif
                                </div>
                                @endif
                            </div>
                            <form method="POST" action="{{ route('shipments.arrival-tasks.destroy', [$shipment, $task]) }}">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-link text-danger p-0" title="Elimina">
                                    <i class="bi bi-trash" style="font-size:0.75rem"></i>
                                </button>
                            </form>
                        </div>
                        @empty
                        <div class="text-center text-muted py-4">
                            <i class="bi bi-list-check" style="font-size:2rem;opacity:.3"></i>
                            <p class="mt-2 mb-0">Nessun task. Aggiungi i predefiniti o crea il tuo.</p>
                        </div>
                        @endforelse
                        @if($shipment->arrivalTasks->count() > 0)
                        <div class="p-2 text-end">
                            <form method="POST" action="{{ route('shipments.arrival-tasks.defaults', $shipment) }}">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-outline-secondary">
                                    <i class="bi bi-magic me-1"></i>Aggiungi task predefiniti mancanti
                                </button>
                            </form>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">Aggiungi Task</div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('shipments.arrival-tasks.store', $shipment) }}">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Attività *</label>
                                <input type="text" name="title" class="form-control" placeholder="es. Notificare il magazzino" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Descrizione</label>
                                <textarea name="description" class="form-control" rows="2" placeholder="Dettagli opzionali..."></textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Giorni prima dell'ETA</label>
                                <input type="number" name="days_before_eta" class="form-control" min="0" placeholder="es. 7"
                                    {{ $shipment->eta ? '' : 'disabled title=ETA non impostata' }}>
                                @if($shipment->eta)
                                <div class="form-text">ETA: {{ $shipment->eta->format('d/m/Y') }}</div>
                                @else
                                <div class="form-text text-warning">Imposta prima l'ETA nella spedizione.</div>
                                @endif
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Data Scadenza (manuale)</label>
                                <input type="date" name="due_date" class="form-control">
                                <div class="form-text">Lascia vuoto per calcolare da giorni prima ETA.</div>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bi bi-plus me-1"></i>Aggiungi Task
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- DOCUMENTI -->
    <div class="tab-pane fade" id="docs-tab">
        <div class="row g-3">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">Checklist Documenti Critici</div>
                    <div class="card-body">
                        @php $uploadedTypes = $shipment->documents->pluck('document_type')->toArray(); @endphp
                        @foreach(\App\Models\Shipment::CRITICAL_DOCS as $docType)
                        <div class="doc-checklist-item">
                            <span style="font-size:0.85rem">{{ \App\Models\Document::TYPE_LABELS[$docType] ?? $docType }}</span>
                            @if(in_array($docType, $uploadedTypes))
                            <div class="doc-check ok"><i class="bi bi-check"></i></div>
                            @else
                            <div class="doc-check missing"><i class="bi bi-x"></i></div>
                            @endif
                        </div>
                        @endforeach
                    </div>
                </div>
                <div class="card mt-3">
                    <div class="card-header">Carica Documento</div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('documents.store') }}" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="documentable_type" value="shipment">
                            <input type="hidden" name="documentable_id" value="{{ $shipment->id }}">
                            <div class="mb-2">
                                <select name="document_type" class="form-select form-select-sm">
                                    @foreach(\App\Models\Document::TYPE_LABELS as $k => $v)
                                        <option value="{{ $k }}">{{ $v }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-2"><input type="text" name="name" class="form-control form-control-sm" placeholder="Nome documento" required></div>
                            <div class="mb-2"><input type="file" name="file" class="form-control form-control-sm"></div>
                            <div class="mb-2">
                                <select name="status" class="form-select form-select-sm">
                                    <option value="received">Ricevuto</option>
                                    <option value="under_review">In Revisione</option>
                                    <option value="approved">Approvato</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary btn-sm w-100">Carica</button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Documenti Caricati ({{ $shipment->documents->count() }})</div>
                    <div class="card-body p-0">
                        @forelse($shipment->documents as $doc)
                        <div class="d-flex align-items-center justify-content-between p-2 border-bottom">
                            <div>
                                <div style="font-size:0.85rem;font-weight:600">{{ $doc->name }}</div>
                                <div style="font-size:0.73rem;color:#94a3b8">{{ $doc->type_label }} · v{{ $doc->version }} · {{ $doc->uploader->name ?? 'Sconosciuto' }}</div>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge-status status-{{ $doc->status }}" style="font-size:0.65rem">{{ ucfirst(str_replace('_',' ',$doc->status)) }}</span>
                                @if($doc->file_path)
                                <a href="{{ route('documents.download', $doc) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-download"></i></a>
                                @endif
                                <form method="POST" action="{{ route('documents.destroy', $doc) }}" onsubmit="return confirm('Eliminare?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                </form>
                            </div>
                        </div>
                        @empty
                        <div class="p-3 text-center text-muted">Nessun documento caricato.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- PAGAMENTI -->
    <div class="tab-pane fade" id="pay-tab">
        <div class="d-flex justify-content-between mb-3">
            <h6>Pagamenti</h6>
            <a href="{{ route('payments.create') }}?contract_id={{ $shipment->contract_id }}" class="btn btn-sm btn-primary">+ Nuovo Pagamento</a>
        </div>
        <div class="table-responsive">
            <table class="table table-supply">
                <thead><tr><th>Contratto</th><th class="text-end">Importo Dovuto</th><th class="text-end">Pagato</th><th>Scadenza</th><th>Data Pagamento</th><th>Riferimento</th><th>Stato</th></tr></thead>
                <tbody>
                    @forelse($shipment->payments as $p)
                    <tr>
                        <td>{{ $p->contract->contract_number ?? '—' }}</td>
                        <td class="text-end fw-bold">{{ number_format($p->amount_due, 2) }} {{ $p->currency }}</td>
                        <td class="text-end">{{ number_format($p->amount_paid, 2) }}</td>
                        <td>{{ $p->due_date?->format('d/m/Y') }}</td>
                        <td>{{ $p->payment_date?->format('d/m/Y') ?? '—' }}</td>
                        <td>{{ $p->bank_reference ?? '—' }}</td>
                        <td><span class="badge-status status-{{ $p->status }}">{{ $p->status_label }}</span></td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="text-center text-muted py-3">Nessun pagamento.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- RECLAMI -->
    <div class="tab-pane fade" id="claims-tab2">
        <div class="d-flex justify-content-between mb-3">
            <h6>Reclami</h6>
            <a href="{{ route('claims.create') }}?shipment_id={{ $shipment->id }}" class="btn btn-sm btn-warning">+ Nuovo Reclamo</a>
        </div>
        @forelse($shipment->claims as $claim)
        <div class="card mb-2">
            <div class="card-body d-flex justify-content-between align-items-center py-2 px-3">
                <div>
                    <span class="badge bg-light text-dark border me-2">{{ $claim->type_label }}</span>
                    <span style="font-size:0.875rem">{{ Str::limit($claim->reason, 80) }}</span>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span class="badge-status status-{{ $claim->status }}">{{ $claim->status_label }}</span>
                    <strong style="font-size:0.875rem">{{ number_format($claim->amount, 0) }} {{ $claim->currency }}</strong>
                </div>
            </div>
        </div>
        @empty
        <p class="text-center text-muted py-3">Nessun reclamo per questa spedizione.</p>
        @endforelse
    </div>

    <!-- COMUNICAZIONI -->
    <div class="tab-pane fade" id="comms-tab2">
        <div class="d-flex justify-content-between mb-3">
            <h6>Comunicazioni</h6>
            <a href="{{ route('communications.create') }}?shipment_id={{ $shipment->id }}" class="btn btn-sm btn-outline-secondary">+ Nuovo Task</a>
        </div>
        @forelse($shipment->communicationTasks as $task)
        <div class="comm-card priority-{{ $task->priority }}">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <a href="{{ route('communications.show', $task) }}" class="fw-bold text-decoration-none">{{ $task->subject }}</a>
                    <div class="mt-1">
                        <span class="badge bg-light text-dark border me-1" style="font-size:0.7rem">{{ $task->category_label }}</span>
                        <span class="badge-status priority-{{ $task->priority }}" style="font-size:0.68rem">{{ $task->priority_label }}</span>
                    </div>
                </div>
                <span class="badge-status status-{{ $task->status }}" style="font-size:0.7rem">{{ $task->status_label }}</span>
            </div>
        </div>
        @empty
        <p class="text-center text-muted py-3">Nessuna comunicazione.</p>
        @endforelse
    </div>

    <!-- ATTIVITÀ -->
    <div class="tab-pane fade" id="activity-tab2">
        <div class="timeline mt-2">
            @forelse($recentActivity as $log)
            <div class="timeline-item {{ $log->description }}">
                <div style="font-size:0.85rem;font-weight:600">
                    <span class="badge-status status-{{ $log->description === 'created' ? 'confirmed' : 'partially_shipped' }}" style="font-size:0.65rem">{{ strtoupper($log->description) }}</span>
                    {{ $log->log_name }} #{{ $log->subject_id }}
                </div>
                <div style="font-size:0.75rem;color:#94a3b8">{{ $log->created_at?->diffForHumans() }}</div>
            </div>
            @empty
            <p class="text-muted">Nessuna attività registrata.</p>
            @endforelse
        </div>
    </div>
</div>

@endsection
