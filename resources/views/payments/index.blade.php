@extends('layouts.app')
@section('title', 'Pagamenti — SupplyManager')
@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h1 class="page-title mb-0">Pagamenti</h1>
        <small class="text-muted">{{ $payments->total() }} pagamenti trovati</small>
    </div>
    <a href="{{ route('payments.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle me-1"></i>Nuovo Pagamento
    </a>
</div>

{{-- Riepilogo acconti --}}
@if($advanceTotal > 0 || $advancePaid > 0)
<div class="row g-2 mb-3">
    <div class="col-auto">
        <div class="alert alert-info py-2 px-3 mb-0" style="font-size:0.85rem">
            <i class="bi bi-cash-coin me-1"></i>
            <strong>Acconti in corso:</strong>
            {{ number_format($advanceTotal, 2) }} ancora da saldare —
            <strong>{{ number_format($advancePaid, 2) }}</strong> già pagati
            <a href="{{ route('payments.index') }}?payment_type=advance" class="ms-2 text-info">Vedi acconti →</a>
        </div>
    </div>
</div>
@endif

<!-- TAB FILTRI -->
<ul class="nav nav-tabs mb-3">
    <li class="nav-item"><a class="nav-link {{ !request('tab') && !request('status') && !request('payment_type') ? 'active' : '' }}" href="{{ route('payments.index') }}">Tutti</a></li>
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
    <li class="nav-item"><a class="nav-link {{ request('payment_type') === 'advance' ? 'active' : '' }}" href="{{ route('payments.index') }}?payment_type=advance">
        <i class="bi bi-cash-coin me-1 text-info"></i>Acconti
    </a></li>
    <li class="nav-item"><a class="nav-link {{ request('payment_type') === 'shipment_payment' ? 'active' : '' }}" href="{{ route('payments.index') }}?payment_type=shipment_payment">
        <i class="bi bi-box-seam me-1 text-primary"></i>Spedizioni
    </a></li>
</ul>

<!-- RICERCA -->
<div class="card mb-3">
    <div class="card-body py-2">
        <form method="GET" class="d-flex gap-2 flex-wrap">
            @if(request('tab')) <input type="hidden" name="tab" value="{{ request('tab') }}"> @endif
            @if(request('status')) <input type="hidden" name="status" value="{{ request('status') }}"> @endif
            @if(request('payment_type')) <input type="hidden" name="payment_type" value="{{ request('payment_type') }}"> @endif
            <input type="text" name="search" value="{{ request('search') }}" class="form-control form-control-sm"
                placeholder="Cerca contratto, fornitore, riferimento..." style="max-width:400px">
            <button type="submit" class="btn btn-sm btn-primary">Cerca</button>
            @if(request('search'))<a href="{{ route('payments.index') }}" class="btn btn-sm btn-outline-secondary">Azzera</a>@endif
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
                        <th>Tipo</th>
                        <th>Contratto</th>
                        <th>Fornitore</th>
                        <th>Spedizione</th>
                        <th class="text-end">Importo Dovuto</th>
                        <th class="text-end">Pagato</th>
                        <th>Scadenza</th>
                        <th>Data Pagamento</th>
                        <th>Rif. Bancario</th>
                        <th>Doc. Contabile</th>
                        <th class="text-center">Stato</th>
                        <th class="text-end">Azioni</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payments as $p)
                    <tr class="{{ $p->status === 'overdue' ? 'row-overdue' : '' }}">
                        <td>
                            @if($p->payment_type === 'advance')
                                <span class="badge bg-info text-dark" title="Acconto contratto"><i class="bi bi-cash-coin me-1"></i>Acconto</span>
                            @else
                                <span class="badge bg-primary bg-opacity-10 text-primary" style="font-size:0.72rem"><i class="bi bi-box-seam me-1"></i>Sped.</span>
                            @endif
                        </td>
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
                            @elseif($p->is_advance)
                            <span class="text-muted" style="font-size:0.75rem">— contratto</span>
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
                        <td style="font-size:0.8rem">
                            @if($p->doc_ref_number)
                                <span class="badge bg-light text-dark border" style="font-size:0.72rem">
                                    {{ \App\Models\Payment::DOC_REF_TYPES[$p->doc_ref_type] ?? $p->doc_ref_type }}
                                </span>
                                <div class="fw-semibold" style="font-size:0.8rem">{{ $p->doc_ref_number }}</div>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
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
                    <tr><td colspan="12" class="text-center py-4 text-muted">Nessun pagamento trovato.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="mt-3">{{ $payments->withQueryString()->links() }}</div>

@endsection
