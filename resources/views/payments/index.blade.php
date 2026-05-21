@extends('layouts.app')
@section('title', 'Pagamenti — SupplyManager')
@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="page-title mb-0">Pagamenti</h1>
        <small class="text-muted">{{ $payments->total() }} pagamenti trovati</small>
    </div>
    <a href="{{ route('payments.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle me-1"></i>Nuovo Pagamento
    </a>
</div>

<!-- TAB FILTRI -->
<ul class="nav nav-tabs mb-3">
    <li class="nav-item"><a class="nav-link {{ !request('tab') && !request('status') ? 'active' : '' }}" href="{{ route('payments.index') }}">Tutti</a></li>
    <li class="nav-item">
        <a class="nav-link {{ request('tab') === 'overdue' ? 'active' : '' }}" href="{{ route('payments.index') }}?tab=overdue">
            <span class="text-danger">Scaduti</span>
            @if($statusCounts['overdue'] > 0)<span class="badge bg-danger ms-1">{{ $statusCounts['overdue'] }}</span>@endif
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ request('tab') === 'due_soon' ? 'active' : '' }}" href="{{ route('payments.index') }}?tab=due_soon">
            In Scadenza <span class="badge bg-warning text-dark ms-1">{{ $statusCounts['due_soon'] }}</span>
        </a>
    </li>
    <li class="nav-item"><a class="nav-link {{ request('status') === 'pending' ? 'active' : '' }}" href="{{ route('payments.index') }}?status=pending">In Sospeso</a></li>
    <li class="nav-item"><a class="nav-link {{ request('status') === 'paid' ? 'active' : '' }}" href="{{ route('payments.index') }}?status=paid">Pagati <span class="badge bg-success ms-1">{{ $statusCounts['paid'] }}</span></a></li>
</ul>

<!-- RICERCA -->
<div class="card mb-3">
    <div class="card-body py-2">
        <form method="GET" class="d-flex gap-2">
            @if(request('tab')) <input type="hidden" name="tab" value="{{ request('tab') }}"> @endif
            @if(request('status')) <input type="hidden" name="status" value="{{ request('status') }}"> @endif
            <input type="text" name="search" value="{{ request('search') }}" class="form-control form-control-sm" placeholder="Cerca contratto, fornitore, riferimento..." style="max-width:400px">
            <button type="submit" class="btn btn-sm btn-primary">Cerca</button>
        </form>
    </div>
</div>

<!-- TABELLA -->
<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-supply mb-0">
                <thead>
                    <tr>
                        <th>Contratto</th>
                        <th>Fornitore</th>
                        <th>Spedizione</th>
                        <th class="text-end">Importo Dovuto</th>
                        <th class="text-end">Pagato</th>
                        <th>Scadenza</th>
                        <th>Data Pagamento</th>
                        <th>Riferimento</th>
                        <th class="text-center">Stato</th>
                        <th class="text-end">Azioni</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payments as $p)
                    <tr class="{{ $p->status === 'overdue' ? 'row-overdue' : '' }}">
                        <td>
                            @if($p->contract)
                            <a href="{{ route('contracts.show', $p->contract) }}" class="text-decoration-none fw-bold" style="font-size:0.85rem">{{ $p->contract->contract_number }}</a>
                            @else —
                            @endif
                        </td>
                        <td style="font-size:0.85rem">{{ $p->supplier->name ?? '—' }}</td>
                        <td>
                            @if($p->shipment)
                            <a href="{{ route('shipments.show', $p->shipment) }}" style="font-size:0.8rem">{{ $p->shipment->shipment_code }}</a>
                            @else <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <strong>{{ number_format($p->amount_due, 2) }}</strong>
                            <div class="text-muted-sm">{{ $p->currency }}</div>
                        </td>
                        <td class="text-end">{{ number_format($p->amount_paid, 2) }}</td>
                        <td>
                            @if($p->due_date)
                            @php $daysLeft = now()->startOfDay()->diffInDays($p->due_date->startOfDay(), false); @endphp
                            <span class="{{ $daysLeft < 0 ? 'text-danger fw-bold' : ($daysLeft <= 7 ? 'text-warning fw-bold' : '') }}">
                                {{ $p->due_date->format('d/m/Y') }}
                            </span>
                            @if($daysLeft < 0 && $p->status !== 'paid')
                            <div class="text-muted-sm text-danger">{{ abs($daysLeft) }}g scaduto</div>
                            @elseif($daysLeft >= 0 && $daysLeft <= 7 && $p->status !== 'paid')
                            <div class="text-muted-sm">tra {{ $daysLeft }}g</div>
                            @endif
                            @else —
                            @endif
                        </td>
                        <td>{{ $p->payment_date?->format('d/m/Y') ?? '—' }}</td>
                        <td style="font-size:0.8rem">{{ $p->bank_reference ?? '—' }}</td>
                        <td class="text-center">
                            <span class="badge-status status-{{ $p->status }}">{{ $p->status_label }}</span>
                        </td>
                        <td class="text-end">
                            <div class="d-flex gap-1 justify-content-end">
                                @if($p->status !== 'paid')
                                <form method="POST" action="{{ route('payments.mark-paid', $p) }}">
                                    @csrf
                                    <button type="submit" class="btn btn-xs btn-sm btn-success" title="Segna come Pagato">
                                        <i class="bi bi-check-circle"></i>
                                    </button>
                                </form>
                                @endif
                                <a href="{{ route('payments.edit', $p) }}" class="btn btn-xs btn-sm btn-outline-secondary">
                                    <i class="bi bi-pencil"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="10" class="text-center py-4 text-muted">Nessun pagamento trovato.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="mt-3">{{ $payments->withQueryString()->links() }}</div>

@endsection
