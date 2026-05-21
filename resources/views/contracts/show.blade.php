@extends('layouts.app')
@section('title', $contract->contract_number . ' — SupplyManager')

@section('content')

<div class="d-flex align-items-center gap-2 mb-3">
    <a href="{{ route('contracts.index') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left"></i></a>
    <div>
        <h1 class="page-title mb-0">{{ $contract->contract_number }}</h1>
        <small class="text-muted">{{ $contract->supplier->name ?? '' }} · {{ $contract->product->name ?? '' }}</small>
    </div>
    <div class="ms-auto d-flex gap-2">
        <a href="{{ route('shipments.create') }}?contract_id={{ $contract->id }}" class="btn btn-sm btn-success">
            <i class="bi bi-plus me-1"></i>Nuova Spedizione
        </a>
        <a href="{{ route('contracts.edit', $contract) }}" class="btn btn-sm btn-outline-primary">
            <i class="bi bi-pencil me-1"></i>Modifica
        </a>
        <span class="badge-status status-{{ $contract->status }} d-flex align-items-center px-3">{{ $contract->status_label }}</span>
    </div>
</div>

<!-- KPI -->
<div class="row g-3 mb-4">
    <div class="col-md-2">
        <div class="card text-center py-3">
            <div style="font-size:1.4rem;font-weight:700;color:#0f172a">{{ number_format($contract->total_value, 0) }}</div>
            <div style="font-size:0.75rem;color:#64748b">{{ $contract->currency }} Valore</div>
            @if($contract->exchange_rate_to_eur != 1)
            <div style="font-size:0.8rem;color:#10b981;font-weight:600">≈ € {{ number_format($contract->total_value_eur, 0) }}</div>
            @endif
        </div>
    </div>
    <div class="col-md-2">
        <div class="card text-center py-3">
            <div style="font-size:1.4rem;font-weight:700;color:#10b981">{{ number_format($contract->paid_amount, 0) }}</div>
            <div style="font-size:0.75rem;color:#64748b">{{ $contract->currency }} Pagato</div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card text-center py-3">
            @php $balance = $contract->remaining_balance; @endphp
            <div style="font-size:1.4rem;font-weight:700;color:{{ $balance > 0 ? '#f59e0b' : '#10b981' }}">{{ number_format(abs($balance), 0) }}</div>
            <div style="font-size:0.75rem;color:#64748b">{{ $contract->currency }} Residuo</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center py-3">
            @php $pct = $contract->quantity_contracted > 0 ? min(100, round(($contract->shipped_quantity / $contract->quantity_contracted) * 100)) : 0; @endphp
            <div style="font-size:1.4rem;font-weight:700;color:#3b82f6">{{ $pct }}%</div>
            <div class="progress mt-1 mx-3" style="height:4px"><div class="progress-bar bg-primary" style="width:{{ $pct }}%"></div></div>
            <div style="font-size:0.75rem;color:#64748b">
                {{ number_format($contract->shipped_quantity, 0) }} / {{ number_format($contract->quantity_contracted, 0) }} {{ $contract->unit_of_measure }}
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center py-3">
            <div style="font-size:1.4rem;font-weight:700;color:#6366f1">{{ number_format($contract->total_kg, 0) }}</div>
            <div style="font-size:0.75rem;color:#64748b">kg Totali Contratto</div>
            @if($contract->kg_per_unit != 1)
            <div style="font-size:0.75rem;color:#94a3b8">{{ number_format($contract->shipped_kg, 0) }} kg spediti</div>
            @endif
        </div>
    </div>
</div>

<!-- TAB -->
<ul class="nav nav-tabs mb-3" id="contractTabs">
    <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#overview">Riepilogo</a></li>
    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#shipments-tab">Spedizioni <span class="badge bg-secondary">{{ $contract->shipments->count() }}</span></a></li>
    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#documents-tab">Documenti <span class="badge bg-secondary">{{ $contract->documents->count() }}</span></a></li>
    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#payments-tab">Pagamenti <span class="badge bg-secondary">{{ $contract->payments->count() }}</span></a></li>
    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#claims-tab">Reclami <span class="badge bg-secondary">{{ $contract->claims->count() }}</span></a></li>
    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#comms-tab">Comunicazioni <span class="badge bg-secondary">{{ $contract->communicationTasks->count() }}</span></a></li>
    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#activity-tab">Attività</a></li>
</ul>

<div class="tab-content">
    <!-- RIEPILOGO -->
    <div class="tab-pane fade show active" id="overview">
        <div class="row g-3">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">Dettagli Contratto</div>
                    <div class="card-body">
                        <table class="table table-sm table-borderless mb-0">
                            <tr><td class="text-muted" style="width:40%">Fornitore</td><td><strong>{{ $contract->supplier->name ?? '—' }}</strong></td></tr>
                            <tr><td class="text-muted">Prodotto</td><td>{{ $contract->product->name ?? '—' }}</td></tr>
                            <tr><td class="text-muted">Tipo</td><td>{{ ucfirst($contract->type) }}</td></tr>
                            <tr><td class="text-muted">Stagione</td><td>{{ $contract->crop_season ?? '—' }}</td></tr>
                            <tr><td class="text-muted">Data Contratto</td><td>{{ $contract->contract_date?->format('d/m/Y') ?? '—' }}</td></tr>
                            <tr><td class="text-muted">Incoterm</td><td><strong>{{ $contract->incoterm ?? '—' }}</strong></td></tr>
                            <tr><td class="text-muted">Porto Imbarco</td><td>{{ $contract->port_of_loading ?? '—' }}</td></tr>
                            <tr><td class="text-muted">Porto Scarico</td><td>{{ $contract->port_of_discharge ?? '—' }}</td></tr>
                            <tr><td class="text-muted">Finestra Spedizione</td><td>{{ $contract->shipment_window_start?->format('d/m/Y') }} → {{ $contract->shipment_window_end?->format('d/m/Y') }}</td></tr>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">Condizioni Commerciali</div>
                    <div class="card-body">
                        <table class="table table-sm table-borderless mb-0">
                            <tr><td class="text-muted" style="width:40%">Quantità</td><td><strong>{{ number_format($contract->quantity_contracted, 0) }} {{ $contract->unit_of_measure }}</strong>
                                @if($contract->kg_per_unit != 1)<span class="text-muted ms-1">({{ number_format($contract->total_kg, 0) }} kg)</span>@endif</td></tr>
                            <tr><td class="text-muted">Prezzo Unitario</td><td>{{ number_format($contract->unit_price, 4) }} {{ $contract->currency }}
                                @if($contract->exchange_rate_to_eur != 1)<span class="text-muted ms-1">≈ € {{ number_format($contract->unit_price_eur, 4) }}</span>@endif</td></tr>
                            <tr><td class="text-muted">Valore Totale</td><td><strong>{{ number_format($contract->total_value, 2) }} {{ $contract->currency }}</strong>
                                @if($contract->exchange_rate_to_eur != 1)<span class="text-success ms-1">≈ € {{ number_format($contract->total_value_eur, 2) }}</span>@endif</td></tr>
                            <tr><td class="text-muted">Cambio → EUR</td><td>1 {{ $contract->currency }} = € {{ $contract->exchange_rate_to_eur }}</td></tr>
                            <tr><td class="text-muted">Qtà Spedita</td><td>{{ number_format($contract->shipped_quantity, 0) }} {{ $contract->unit_of_measure }}
                                @if($contract->kg_per_unit != 1)<span class="text-muted ms-1">({{ number_format($contract->shipped_kg, 0) }} kg)</span>@endif</td></tr>
                            <tr><td class="text-muted">Qtà Residua</td><td>{{ number_format($contract->remaining_quantity, 0) }} {{ $contract->unit_of_measure }}</td></tr>
                            <tr><td class="text-muted">Importo Pagato</td><td>{{ number_format($contract->paid_amount, 2) }} {{ $contract->currency }}</td></tr>
                            <tr><td class="text-muted">Saldo</td><td><strong>{{ number_format($contract->remaining_balance, 2) }} {{ $contract->currency }}</strong></td></tr>
                        </table>
                        @if($contract->payment_terms_description)
                        <hr>
                        <small class="text-muted d-block mb-1">Termini di Pagamento:</small>
                        <p class="mb-0" style="font-size:0.875rem">{{ $contract->payment_terms_description }}</p>
                        @endif
                    </div>
                </div>
                @if($contract->notes)
                <div class="card mt-3">
                    <div class="card-header">Note</div>
                    <div class="card-body"><p class="mb-0" style="font-size:0.875rem">{{ $contract->notes }}</p></div>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- TAB SPEDIZIONI -->
    <div class="tab-pane fade" id="shipments-tab">
        <div class="d-flex justify-content-between mb-3">
            <h6 class="mb-0">Spedizioni</h6>
            <a href="{{ route('shipments.create') }}?contract_id={{ $contract->id }}" class="btn btn-sm btn-success">+ Nuova Spedizione</a>
        </div>
        <div class="table-responsive">
            <table class="table table-supply">
                <thead><tr><th>N° Spedizione</th><th>Container</th><th>Nave</th><th>ETD</th><th>ETA</th><th>Qtà</th><th>Stato</th><th></th></tr></thead>
                <tbody>
                    @forelse($contract->shipments as $s)
                    <tr>
                        <td><a href="{{ route('shipments.show', $s) }}" class="fw-bold">{{ $s->shipment_code }}</a></td>
                        <td>{{ $s->container_number ?? '—' }}</td>
                        <td>{{ $s->vessel_name ?? '—' }}</td>
                        <td>{{ $s->etd?->format('d/m/Y') ?? '—' }}</td>
                        <td>{{ $s->eta?->format('d/m/Y') ?? '—' }}</td>
                        <td>{{ number_format($s->quantity_shipped, 0) }} {{ $contract->unit_of_measure }}</td>
                        <td><span class="badge-status status-{{ $s->status }}">{{ $s->status_label }}</span></td>
                        <td><a href="{{ route('shipments.show', $s) }}" class="btn btn-xs btn-outline-primary btn-sm">Vedi</a></td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="text-center text-muted py-3">Nessuna spedizione ancora.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- TAB DOCUMENTI -->
    <div class="tab-pane fade" id="documents-tab">
        <div class="row g-3">
            <div class="col-md-5">
                <div class="card">
                    <div class="card-header">Carica Documento</div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('documents.store') }}" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="documentable_type" value="contract">
                            <input type="hidden" name="documentable_id" value="{{ $contract->id }}">
                            <div class="mb-2">
                                <label class="form-label form-label-sm fw-semibold">Tipo Documento</label>
                                <select name="document_type" class="form-select form-select-sm">
                                    @foreach(\App\Models\Document::TYPE_LABELS as $k => $v)
                                        <option value="{{ $k }}">{{ $v }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-2">
                                <label class="form-label form-label-sm fw-semibold">Nome Documento</label>
                                <input type="text" name="name" class="form-control form-control-sm" required>
                            </div>
                            <div class="mb-2">
                                <label class="form-label form-label-sm fw-semibold">File</label>
                                <input type="file" name="file" class="form-control form-control-sm">
                            </div>
                            <div class="mb-3">
                                <label class="form-label form-label-sm fw-semibold">Stato</label>
                                <select name="status" class="form-select form-select-sm">
                                    <option value="received">Ricevuto</option>
                                    <option value="under_review">In Revisione</option>
                                    <option value="approved">Approvato</option>
                                    <option value="missing">Mancante</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary btn-sm w-100">Carica</button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-md-7">
                <div class="card">
                    <div class="card-header">Documenti ({{ $contract->documents->count() }})</div>
                    <div class="card-body p-0">
                        @forelse($contract->documents as $doc)
                        <div class="d-flex align-items-center justify-content-between p-2 border-bottom">
                            <div>
                                <div style="font-size:0.85rem;font-weight:600">{{ $doc->name }}</div>
                                <div style="font-size:0.73rem;color:#94a3b8">{{ $doc->type_label }} · v{{ $doc->version }}</div>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge-status status-{{ $doc->status }}" style="font-size:0.65rem">{{ ucfirst(str_replace('_', ' ', $doc->status)) }}</span>
                                @if($doc->file_path)
                                <a href="{{ route('documents.download', $doc) }}" class="btn btn-xs btn-outline-primary btn-sm"><i class="bi bi-download"></i></a>
                                @endif
                                <form method="POST" action="{{ route('documents.destroy', $doc) }}" onsubmit="return confirm('Eliminare?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-xs btn-outline-danger btn-sm"><i class="bi bi-trash"></i></button>
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

    <!-- TAB PAGAMENTI -->
    <div class="tab-pane fade" id="payments-tab">
        <div class="d-flex justify-content-between mb-3">
            <h6>Pagamenti</h6>
            <a href="{{ route('payments.create') }}?contract_id={{ $contract->id }}" class="btn btn-sm btn-primary">+ Nuovo Pagamento</a>
        </div>
        <div class="table-responsive">
            <table class="table table-supply">
                <thead><tr><th>Descrizione</th><th class="text-end">Importo Dovuto</th><th class="text-end">Importo Pagato</th><th>Scadenza</th><th>Data Pagamento</th><th>Riferimento</th><th>Stato</th></tr></thead>
                <tbody>
                    @forelse($contract->payments as $p)
                    <tr class="{{ $p->status === 'overdue' ? 'row-overdue' : '' }}">
                        <td>{{ $p->notes ?? 'Pagamento' }}</td>
                        <td class="text-end fw-bold">{{ number_format($p->amount_due, 2) }} {{ $p->currency }}</td>
                        <td class="text-end">{{ number_format($p->amount_paid, 2) }}</td>
                        <td>{{ $p->due_date?->format('d/m/Y') }}</td>
                        <td>{{ $p->payment_date?->format('d/m/Y') ?? '—' }}</td>
                        <td>{{ $p->bank_reference ?? '—' }}</td>
                        <td><span class="badge-status status-{{ $p->status }}">{{ $p->status_label }}</span></td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="text-center text-muted py-3">Nessun pagamento registrato.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- TAB RECLAMI -->
    <div class="tab-pane fade" id="claims-tab">
        <div class="d-flex justify-content-between mb-3">
            <h6>Reclami</h6>
            <a href="{{ route('claims.create') }}?contract_id={{ $contract->id }}" class="btn btn-sm btn-warning">+ Nuovo Reclamo</a>
        </div>
        <div class="table-responsive">
            <table class="table table-supply">
                <thead><tr><th>Tipo</th><th>Motivo</th><th class="text-end">Importo</th><th>Stato</th><th>Data</th></tr></thead>
                <tbody>
                    @forelse($contract->claims as $claim)
                    <tr>
                        <td><span class="badge bg-light text-dark border">{{ $claim->type_label }}</span></td>
                        <td>{{ Str::limit($claim->reason, 60) }}</td>
                        <td class="text-end fw-bold">{{ number_format($claim->amount, 2) }} {{ $claim->currency }}</td>
                        <td><span class="badge-status status-{{ $claim->status }}">{{ $claim->status_label }}</span></td>
                        <td>{{ $claim->created_at?->format('d/m/Y') }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="text-center text-muted py-3">Nessun reclamo.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- TAB COMUNICAZIONI -->
    <div class="tab-pane fade" id="comms-tab">
        <div class="d-flex justify-content-between mb-3">
            <h6>Comunicazioni</h6>
            <a href="{{ route('communications.create') }}?contract_id={{ $contract->id }}" class="btn btn-sm btn-outline-secondary">+ Nuovo Task</a>
        </div>
        @forelse($contract->communicationTasks as $task)
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
        <p class="text-muted text-center py-3">Nessuna comunicazione.</p>
        @endforelse
    </div>

    <!-- TAB ATTIVITÀ -->
    <div class="tab-pane fade" id="activity-tab">
        <div class="timeline mt-2">
            @forelse($recentActivity as $log)
            <div class="timeline-item {{ $log->description }}">
                <div style="font-size:0.85rem;font-weight:600">
                    <span class="badge-status status-{{ $log->description === 'created' ? 'confirmed' : ($log->description === 'deleted' ? 'cancelled' : 'partially_shipped') }}" style="font-size:0.65rem">{{ strtoupper($log->description) }}</span>
                    {{ $log->log_name }} #{{ $log->subject_id }}
                </div>
                <div style="font-size:0.75rem;color:#94a3b8">{{ $log->created_at?->diffForHumans() }}</div>
                @if($log->properties)
                <div style="font-size:0.75rem;color:#64748b;margin-top:2px">
                    {{ collect($log->properties)->keys()->implode(', ') }} modificati
                </div>
                @endif
            </div>
            @empty
            <p class="text-muted">Nessuna attività registrata.</p>
            @endforelse
        </div>
    </div>
</div>

@endsection
