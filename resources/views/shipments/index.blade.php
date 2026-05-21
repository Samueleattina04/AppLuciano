@extends('layouts.app')
@section('title', 'Spedizioni — SupplyManager')
@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="page-title mb-0">Spedizioni</h1>
        <small class="text-muted">{{ $shipments->total() }} spedizioni trovate</small>
    </div>
    <a href="{{ route('shipments.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle me-1"></i>Nuova Spedizione
    </a>
</div>

<!-- TAB STATO -->
<ul class="nav nav-tabs mb-0" style="flex-wrap:nowrap;overflow-x:auto">
    <li class="nav-item" style="white-space:nowrap">
        <a class="nav-link {{ !request('status') ? 'active' : '' }}" href="{{ route('shipments.index', request()->except('status','page')) }}">
            Tutte <span class="badge bg-secondary ms-1">{{ $shipments->total() }}</span>
        </a>
    </li>
    @foreach(\App\Models\Shipment::STATUS_LABELS as $key => $label)
    <li class="nav-item" style="white-space:nowrap">
        <a class="nav-link {{ request('status') === $key ? 'active' : '' }}" href="{{ route('shipments.index', array_merge(request()->all(), ['status' => $key, 'page' => 1])) }}">
            {{ $label }}
            @if($cnt = ($statusCounts[$key] ?? 0))
            <span class="badge bg-secondary ms-1">{{ $cnt }}</span>
            @endif
        </a>
    </li>
    @endforeach
</ul>

<!-- RICERCA -->
<div class="card mb-0" style="border-top:none;border-radius:0 0 8px 8px">
    <div class="card-body py-2">
        <form method="GET" class="d-flex gap-2">
            @if(request('status')) <input type="hidden" name="status" value="{{ request('status') }}"> @endif
            <input type="text" name="search" value="{{ request('search') }}" class="form-control form-control-sm" placeholder="Cerca spedizione, container, BL, nave..." style="max-width:400px">
            <button type="submit" class="btn btn-sm btn-primary">Cerca</button>
            @if(request('search') || request('status'))<a href="{{ route('shipments.index') }}" class="btn btn-sm btn-outline-secondary">Azzera</a>@endif
        </form>
    </div>
</div>

<!-- TABELLA -->
<div class="card mt-3">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-supply mb-0">
                <thead>
                    <tr>
                        <th>N° Spedizione</th>
                        <th>Container</th>
                        <th>Fornitore</th>
                        <th>Contratto</th>
                        <th>Nave / Viaggio</th>
                        <th>ETD</th>
                        <th>ETA</th>
                        <th class="text-center">Docs</th>
                        <th class="text-center">Stato</th>
                        <th class="text-end">Azioni</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($shipments as $s)
                    <tr>
                        <td>
                            <a href="{{ route('shipments.show', $s) }}" class="fw-bold text-decoration-none">{{ $s->shipment_code }}</a>
                            @if($s->bl_number)
                            <div class="text-muted-sm">BL: {{ $s->bl_number }}</div>
                            @endif
                        </td>
                        <td>
                            <code style="font-size:0.8rem">{{ $s->container_number ?? '—' }}</code>
                        </td>
                        <td>{{ $s->supplier->name ?? '—' }}</td>
                        <td>
                            @if($s->contract)
                            <a href="{{ route('contracts.show', $s->contract) }}" class="text-decoration-none" style="font-size:0.8rem">{{ $s->contract->contract_number }}</a>
                            @else —
                            @endif
                        </td>
                        <td>
                            <div style="font-size:0.8rem">{{ $s->vessel_name ?? '—' }}</div>
                            @if($s->voyage_number)<div class="text-muted-sm">Voy: {{ $s->voyage_number }}</div>@endif
                        </td>
                        <td>
                            <span style="font-size:0.85rem">{{ $s->etd?->format('d/m/Y') ?? '—' }}</span>
                        </td>
                        <td>
                            @if($s->eta)
                            @php
                                $daysToEta = now()->startOfDay()->diffInDays($s->eta->startOfDay(), false);
                                $etaClass = $daysToEta < 0 ? 'text-danger' : ($daysToEta <= 7 ? 'text-warning fw-bold' : '');
                            @endphp
                            <span class="{{ $etaClass }}" style="font-size:0.85rem">{{ $s->eta->format('d/m/Y') }}</span>
                            @if($daysToEta >= 0 && $daysToEta <= 15)
                            <div class="text-muted-sm">{{ $daysToEta === 0 ? 'Oggi!' : "tra {$daysToEta}g" }}</div>
                            @endif
                            @else —
                            @endif
                        </td>
                        <td class="text-center">
                            @php $missing = count($s->missing_critical_documents); @endphp
                            @if($missing > 0)
                            <span class="badge bg-danger" title="{{ implode(', ', $s->missing_critical_documents) }}">
                                <i class="bi bi-exclamation-triangle"></i> {{ $missing }}
                            </span>
                            @else
                            <i class="bi bi-check-circle-fill text-success"></i>
                            @endif
                        </td>
                        <td class="text-center">
                            <span class="badge-status status-{{ $s->status }}">{{ $s->status_label }}</span>
                        </td>
                        <td class="text-end">
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('shipments.show', $s) }}" class="btn btn-outline-primary btn-sm"><i class="bi bi-eye"></i></a>
                                <a href="{{ route('shipments.edit', $s) }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-pencil"></i></a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="10" class="text-center py-4 text-muted">Nessuna spedizione trovata.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="mt-3">{{ $shipments->withQueryString()->links() }}</div>
@endsection
