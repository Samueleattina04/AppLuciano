@extends('layouts.app')
@section('title', 'Contratto ' . $contract->contract_number)

@section('content')
{{-- Header --}}
<div class="d-flex justify-content-between align-items-start mb-4">
    <div>
        <h2 class="fw-bold mb-1">{{ $contract->contract_number }}</h2>
        <div class="text-muted">
            <i class="bi bi-building me-1"></i>{{ $contract->supplier->name }}
            &nbsp;·&nbsp; <span class="badge bg-secondary">{{ $contract->incoterms }}</span>
            &nbsp;·&nbsp; {{ $contract->contract_date->format('d/m/Y') }}
        </div>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('contracts.edit', $contract) }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-pencil me-1"></i>Modifica</a>
        <form method="POST" action="{{ route('contracts.destroy', $contract) }}" onsubmit="return confirm('Eliminare il contratto?')">
            @csrf @method('DELETE')
            <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash me-1"></i>Elimina</button>
        </form>
    </div>
</div>

{{-- Financial Summary --}}
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card text-center">
            <div class="card-body">
                <div class="text-muted small mb-1">Valore Totale</div>
                <div class="fs-4 fw-bold">{{ number_format($contract->total_value, 2) }} {{ $contract->currency }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-center">
            <div class="card-body">
                <div class="text-muted small mb-1">Pagato</div>
                <div class="fs-4 fw-bold text-success">{{ number_format($contract->paid_amount, 2) }} {{ $contract->currency }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-center">
            <div class="card-body">
                <div class="text-muted small mb-1">Saldo Rimanente</div>
                <div class="fs-4 fw-bold {{ $contract->remaining_balance > 0 ? 'text-danger' : 'text-success' }}">
                    {{ number_format($contract->remaining_balance, 2) }} {{ $contract->currency }}
                </div>
            </div>
        </div>
    </div>
</div>

@if($contract->total_value > 0)
<div class="mb-4">
    @php $pct = min(100, ($contract->paid_amount / $contract->total_value) * 100); @endphp
    <div class="d-flex justify-content-between small text-muted mb-1">
        <span>Avanzamento Pagamenti</span><span>{{ number_format($pct, 1) }}%</span>
    </div>
    <div class="progress" style="height:10px;">
        <div class="progress-bar bg-success" style="width:{{ $pct }}%"></div>
    </div>
</div>
@endif

<div class="row g-3">
    {{-- Containers --}}
    <div class="col-lg-7">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-box-seam me-2"></i>Container ({{ $contract->containers->count() }})</span>
                <a href="{{ route('containers.create') }}?contract_id={{ $contract->id }}" class="btn btn-sm btn-outline-primary">
                    <i class="bi bi-plus-lg me-1"></i>Aggiungi
                </a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead><tr><th>N° Container</th><th>Nave</th><th>ETA</th><th>Stato</th><th>Doc.</th><th></th></tr></thead>
                        <tbody>
                            @forelse($contract->containers as $c)
                            <tr>
                                <td class="fw-semibold">{{ $c->container_number }}</td>
                                <td>{{ $c->vessel_name ?? '—' }}</td>
                                <td>{{ $c->eta ? $c->eta->format('d/m/Y') : '—' }}</td>
                                <td><span class="badge bg-{{ $c->status_color }} badge-status">{{ $c->status_label }}</span></td>
                                <td>
                                    @if(count($c->missing_critical_documents) > 0)
                                        <span class="badge bg-danger badge-status" title="{{ implode(', ', $c->missing_critical_documents) }}">
                                            <i class="bi bi-exclamation-triangle-fill"></i> {{ count($c->missing_critical_documents) }} mancanti
                                        </span>
                                    @else
                                        <span class="badge bg-success badge-status"><i class="bi bi-check-lg"></i> OK</span>
                                    @endif
                                </td>
                                <td><a href="{{ route('containers.show', $c) }}" class="btn btn-outline-primary btn-action"><i class="bi bi-eye"></i></a></td>
                            </tr>
                            @empty
                            <tr><td colspan="6" class="text-center text-muted py-3">Nessun container associato</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Payments --}}
    <div class="col-lg-5">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-credit-card me-2"></i>Pagamenti</span>
                <a href="{{ route('payments.create') }}?contract_id={{ $contract->id }}" class="btn btn-sm btn-outline-primary">
                    <i class="bi bi-plus-lg me-1"></i>Aggiungi
                </a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead><tr><th>Descrizione</th><th class="text-end">Importo</th><th>Scadenza</th><th>Stato</th></tr></thead>
                        <tbody>
                            @forelse($contract->payments as $p)
                            <tr>
                                <td>{{ $p->description }}</td>
                                <td class="text-end fw-semibold">{{ number_format($p->amount, 2) }}</td>
                                <td>{{ $p->due_date->format('d/m/Y') }}</td>
                                <td>
                                    @if($p->status === 'paid')
                                        <span class="badge bg-success badge-status">Pagato</span>
                                    @elseif($p->status === 'overdue')
                                        <span class="badge bg-danger badge-status">Scaduto</span>
                                    @else
                                        <span class="badge bg-warning text-dark badge-status">In Attesa</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="4" class="text-center text-muted py-3">Nessun pagamento</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Documents --}}
<div class="card mt-3">
    <div class="card-header"><i class="bi bi-folder2-open me-2"></i>Documenti Contratto</div>
    <div class="card-body">
        @include('partials._document_upload', ['model' => 'contract', 'modelId' => $contract->id])
        @include('partials._document_list', ['documents' => $contract->documents])
    </div>
</div>

@endsection
