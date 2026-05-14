@extends('layouts.app')
@section('title', 'Pagamenti')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <div class="d-flex gap-2">
        <a href="{{ route('payments.index') }}" class="btn btn-sm {{ !request('status') ? 'btn-dark' : 'btn-outline-secondary' }}">Tutti</a>
        <a href="{{ route('payments.index', ['status' => 'pending']) }}" class="btn btn-sm {{ request('status') === 'pending' ? 'btn-warning text-dark' : 'btn-outline-warning' }}">In Attesa</a>
        <a href="{{ route('payments.index', ['status' => 'overdue']) }}" class="btn btn-sm {{ request('status') === 'overdue' ? 'btn-danger' : 'btn-outline-danger' }}">Scaduti</a>
        <a href="{{ route('payments.index', ['status' => 'paid']) }}" class="btn btn-sm {{ request('status') === 'paid' ? 'btn-success' : 'btn-outline-success' }}">Pagati</a>
    </div>
    <a href="{{ route('payments.create') }}" class="btn btn-primary btn-sm">
        <i class="bi bi-plus-lg me-1"></i>Nuovo Pagamento
    </a>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead><tr>
                    <th>Contratto</th>
                    <th>Fornitore</th>
                    <th>Descrizione</th>
                    <th class="text-end">Importo</th>
                    <th class="text-end">%</th>
                    <th>Scadenza</th>
                    <th>Pagato il</th>
                    <th>Riferimento</th>
                    <th>Stato</th>
                    <th></th>
                </tr></thead>
                <tbody>
                    @forelse($payments as $p)
                    <tr class="{{ $p->status === 'overdue' ? 'table-danger' : '' }}">
                        <td><a href="{{ route('contracts.show', $p->contract) }}" class="text-decoration-none fw-semibold">{{ $p->contract->contract_number }}</a></td>
                        <td>{{ $p->contract->supplier->name }}</td>
                        <td>{{ $p->description }}</td>
                        <td class="text-end fw-semibold">{{ number_format($p->amount, 2) }} {{ $p->contract->currency }}</td>
                        <td class="text-end">{{ $p->percentage ? $p->percentage . '%' : '—' }}</td>
                        <td>{{ $p->due_date->format('d/m/Y') }}</td>
                        <td>{{ $p->paid_date ? $p->paid_date->format('d/m/Y') : '—' }}</td>
                        <td>{{ $p->transaction_reference ?? '—' }}</td>
                        <td>
                            @if($p->status === 'paid')
                                <span class="badge bg-success badge-status">Pagato</span>
                            @elseif($p->status === 'overdue')
                                <span class="badge bg-danger badge-status">Scaduto</span>
                            @else
                                <span class="badge bg-warning text-dark badge-status">In Attesa</span>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex gap-1">
                                @if($p->status !== 'paid')
                                <button type="button" class="btn btn-outline-success btn-action" data-bs-toggle="modal" data-bs-target="#markPaidModal{{ $p->id }}">
                                    <i class="bi bi-check2"></i>
                                </button>
                                @endif
                                <a href="{{ route('payments.edit', $p) }}" class="btn btn-outline-secondary btn-action"><i class="bi bi-pencil"></i></a>
                                <form method="POST" action="{{ route('payments.destroy', $p) }}" onsubmit="return confirm('Eliminare pagamento?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-outline-danger btn-action"><i class="bi bi-trash"></i></button>
                                </form>
                            </div>

                            @if($p->status !== 'paid')
                            <div class="modal fade" id="markPaidModal{{ $p->id }}" tabindex="-1">
                                <div class="modal-dialog modal-sm">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Segna come Pagato</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <form method="POST" action="{{ route('payments.mark-paid', $p) }}">
                                            @csrf
                                            <div class="modal-body">
                                                <div class="mb-2">
                                                    <label class="form-label small fw-semibold">Data Pagamento</label>
                                                    <input type="date" name="paid_date" class="form-control form-control-sm" value="{{ now()->format('Y-m-d') }}" required>
                                                </div>
                                                <div>
                                                    <label class="form-label small fw-semibold">Riferimento Transazione</label>
                                                    <input type="text" name="transaction_reference" class="form-control form-control-sm" placeholder="es. WIRE-20240523">
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Annulla</button>
                                                <button type="submit" class="btn btn-sm btn-success">Conferma</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="10" class="text-center text-muted py-4">Nessun pagamento trovato</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($payments->hasPages())
    <div class="card-footer bg-white">{{ $payments->links() }}</div>
    @endif
</div>
@endsection
