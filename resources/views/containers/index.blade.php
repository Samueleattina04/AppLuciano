@extends('layouts.app')
@section('title', 'Tracking Container')

@section('content')
{{-- Status filter tabs --}}
<div class="d-flex gap-2 mb-3 flex-wrap">
    <a href="{{ route('containers.index', request()->except('status')) }}"
       class="btn btn-sm {{ !request('status') ? 'btn-dark' : 'btn-outline-secondary' }}">
        Tutti <span class="badge bg-white text-dark ms-1">{{ $statusCounts->sum() }}</span>
    </a>
    @php
        $tabs = [
            'in_production'   => ['label' => 'In Produzione',   'color' => 'secondary'],
            'at_port'         => ['label' => 'Al Porto',         'color' => 'warning'],
            'in_transit'      => ['label' => 'In Transito',      'color' => 'info'],
            'customs_cleared' => ['label' => 'Sdoganato',        'color' => 'primary'],
            'at_warehouse'    => ['label' => 'A Magazzino',      'color' => 'success'],
        ];
    @endphp
    @foreach($tabs as $key => $meta)
    <a href="{{ route('containers.index', array_merge(request()->except('status'), ['status' => $key])) }}"
       class="btn btn-sm {{ request('status') === $key ? 'btn-'.$meta['color'] : 'btn-outline-'.$meta['color'] }}">
        {{ $meta['label'] }} <span class="badge bg-white text-dark ms-1">{{ $statusCounts[$key] ?? 0 }}</span>
    </a>
    @endforeach
</div>

<div class="card mb-3">
    <div class="card-body py-2">
        <form method="GET" action="{{ route('containers.index') }}" class="d-flex gap-2">
            @if(request('status'))<input type="hidden" name="status" value="{{ request('status') }}">@endif
            <input type="text" name="search" class="form-control form-control-sm" placeholder="Cerca container, nave, contratto..." value="{{ request('search') }}">
            <button class="btn btn-sm btn-outline-secondary"><i class="bi bi-search"></i></button>
            @if(request('search'))
                <a href="{{ route('containers.index', request()->except('search')) }}" class="btn btn-sm btn-outline-danger"><i class="bi bi-x-lg"></i></a>
            @endif
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead><tr>
                    <th>N° Container</th>
                    <th>Contratto</th>
                    <th>Fornitore</th>
                    <th>Nave / Viaggio</th>
                    <th>ETD</th>
                    <th>ETA</th>
                    <th>Documenti</th>
                    <th>Stato</th>
                    <th></th>
                </tr></thead>
                <tbody>
                    @forelse($containers as $c)
                    <tr>
                        <td class="fw-semibold">{{ $c->container_number }}</td>
                        <td>
                            <a href="{{ route('contracts.show', $c->contract) }}" class="text-decoration-none">
                                {{ $c->contract->contract_number }}
                            </a>
                        </td>
                        <td>{{ $c->contract->supplier->name }}</td>
                        <td>
                            @if($c->vessel_name)
                                {{ $c->vessel_name }}
                                @if($c->voyage_number)<small class="text-muted"> / {{ $c->voyage_number }}</small>@endif
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td>{{ $c->etd ? $c->etd->format('d/m/Y') : '—' }}</td>
                        <td>{{ $c->eta ? $c->eta->format('d/m/Y') : '—' }}</td>
                        <td>
                            @php $missing = $c->missing_critical_documents; @endphp
                            @if(count($missing) > 0)
                                <span class="badge bg-danger badge-status" title="{{ implode(', ', $missing) }}">
                                    <i class="bi bi-exclamation-triangle-fill"></i> {{ count($missing) }} mancanti
                                </span>
                            @else
                                <span class="badge bg-success badge-status"><i class="bi bi-check-lg"></i> Completi</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-{{ $c->status_color }} badge-status">{{ $c->status_label }}</span>
                        </td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="{{ route('containers.show', $c) }}" class="btn btn-outline-primary btn-action"><i class="bi bi-eye"></i></a>
                                <a href="{{ route('containers.edit', $c) }}" class="btn btn-outline-secondary btn-action"><i class="bi bi-pencil"></i></a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="9" class="text-center text-muted py-4">Nessun container trovato</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($containers->hasPages())
    <div class="card-footer bg-white">{{ $containers->links() }}</div>
    @endif
</div>
@endsection
