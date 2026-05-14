@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')

{{-- KPI Row --}}
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card kpi-card h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="kpi-icon bg-primary bg-opacity-10 text-primary">
                    <i class="bi bi-water"></i>
                </div>
                <div>
                    <div class="text-muted small">Container in Mare</div>
                    <div class="fs-2 fw-bold text-primary">{{ $containersAtSea }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card kpi-card h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="kpi-icon bg-success bg-opacity-10 text-success">
                    <i class="bi bi-box-seam-fill"></i>
                </div>
                <div>
                    <div class="text-muted small">Container Totali</div>
                    <div class="fs-2 fw-bold text-success">{{ $containersTotal }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card kpi-card h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="kpi-icon bg-warning bg-opacity-10 text-warning">
                    <i class="bi bi-file-earmark-text-fill"></i>
                </div>
                <div>
                    <div class="text-muted small">Contratti Attivi</div>
                    <div class="fs-2 fw-bold text-warning">{{ $contractsActive }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card kpi-card h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="kpi-icon bg-danger bg-opacity-10 text-danger">
                    <i class="bi bi-clock-history"></i>
                </div>
                <div>
                    <div class="text-muted small">Pagamenti Scadenti (7gg)</div>
                    <div class="fs-2 fw-bold text-danger">{{ $paymentsDueSoon->count() }}</div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    {{-- Container Status Breakdown --}}
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-header"><i class="bi bi-bar-chart-fill me-2 text-primary"></i>Stato Container</div>
            <div class="card-body p-0">
                @php
                    $statuses = [
                        'in_production'   => ['label' => 'In Produzione',   'color' => 'secondary'],
                        'at_port'         => ['label' => 'Al Porto',         'color' => 'warning'],
                        'in_transit'      => ['label' => 'In Transito',      'color' => 'info'],
                        'customs_cleared' => ['label' => 'Sdoganato',        'color' => 'primary'],
                        'at_warehouse'    => ['label' => 'A Magazzino',      'color' => 'success'],
                    ];
                @endphp
                <ul class="list-group list-group-flush">
                    @foreach($statuses as $key => $meta)
                    <li class="list-group-item d-flex justify-content-between align-items-center py-2">
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-{{ $meta['color'] }} badge-status">{{ $meta['label'] }}</span>
                        </div>
                        <span class="fw-bold">{{ $containersByStatus[$key] ?? 0 }}</span>
                    </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>

    {{-- Payments Due Soon --}}
    <div class="col-md-8">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-exclamation-circle-fill me-2 text-warning"></i>Pagamenti in Scadenza (7 giorni)</span>
                <a href="{{ route('payments.index', ['status' => 'pending']) }}" class="btn btn-sm btn-outline-secondary">Tutti</a>
            </div>
            <div class="card-body p-0">
                @if($paymentsDueSoon->isEmpty() && $paymentsOverdue->isEmpty())
                    <div class="p-4 text-center text-muted"><i class="bi bi-check-circle-fill text-success fs-3 d-block mb-2"></i>Nessun pagamento urgente</div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead><tr>
                                <th>Contratto</th><th>Fornitore</th><th>Descrizione</th><th>Importo</th><th>Scadenza</th><th>Stato</th>
                            </tr></thead>
                            <tbody>
                                @foreach($paymentsOverdue as $p)
                                <tr class="table-danger">
                                    <td><a href="{{ route('contracts.show', $p->contract) }}" class="text-decoration-none fw-semibold">{{ $p->contract->contract_number }}</a></td>
                                    <td>{{ $p->contract->supplier->name }}</td>
                                    <td>{{ $p->description }}</td>
                                    <td class="fw-semibold">{{ number_format($p->amount, 2) }} {{ $p->contract->currency }}</td>
                                    <td class="text-danger fw-bold">{{ $p->due_date->format('d/m/Y') }}</td>
                                    <td><span class="badge bg-danger badge-status">SCADUTO</span></td>
                                </tr>
                                @endforeach
                                @foreach($paymentsDueSoon as $p)
                                <tr>
                                    <td><a href="{{ route('contracts.show', $p->contract) }}" class="text-decoration-none fw-semibold">{{ $p->contract->contract_number }}</a></td>
                                    <td>{{ $p->contract->supplier->name }}</td>
                                    <td>{{ $p->description }}</td>
                                    <td class="fw-semibold">{{ number_format($p->amount, 2) }} {{ $p->contract->currency }}</td>
                                    <td>{{ $p->due_date->format('d/m/Y') }}</td>
                                    <td><span class="badge bg-warning text-dark badge-status">In Scadenza</span></td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Recent Containers --}}
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-box-seam me-2 text-info"></i>Container Recenti</span>
        <a href="{{ route('containers.index') }}" class="btn btn-sm btn-outline-secondary">Vedi tutti</a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead><tr>
                    <th>Container</th><th>Contratto</th><th>Fornitore</th><th>Nave</th><th>ETA</th><th>Stato</th><th></th>
                </tr></thead>
                <tbody>
                    @forelse($recentContainers as $c)
                    <tr>
                        <td class="fw-semibold">{{ $c->container_number }}</td>
                        <td><a href="{{ route('contracts.show', $c->contract) }}" class="text-decoration-none">{{ $c->contract->contract_number }}</a></td>
                        <td>{{ $c->contract->supplier->name }}</td>
                        <td>{{ $c->vessel_name ?? '—' }}</td>
                        <td>{{ $c->eta ? $c->eta->format('d/m/Y') : '—' }}</td>
                        <td><span class="badge bg-{{ $c->status_color }} badge-status">{{ $c->status_label }}</span></td>
                        <td><a href="{{ route('containers.show', $c) }}" class="btn btn-sm btn-outline-primary btn-action"><i class="bi bi-eye"></i></a></td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="text-center text-muted py-4">Nessun container trovato</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection
