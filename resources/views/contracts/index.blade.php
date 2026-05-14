@extends('layouts.app')
@section('title', 'Contratti')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <span class="text-muted small">{{ $contracts->total() }} contratti trovati</span>
    </div>
    <a href="{{ route('contracts.create') }}" class="btn btn-primary btn-sm">
        <i class="bi bi-plus-lg me-1"></i>Nuovo Contratto
    </a>
</div>

<div class="card mb-3">
    <div class="card-body py-2">
        <form method="GET" action="{{ route('contracts.index') }}" class="d-flex gap-2">
            <input type="text" name="search" class="form-control form-control-sm" placeholder="Cerca per n° contratto o fornitore..." value="{{ request('search') }}">
            <button class="btn btn-sm btn-outline-secondary"><i class="bi bi-search"></i></button>
            @if(request('search'))
                <a href="{{ route('contracts.index') }}" class="btn btn-sm btn-outline-danger"><i class="bi bi-x-lg"></i></a>
            @endif
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead><tr>
                    <th>N° Contratto</th>
                    <th>Fornitore</th>
                    <th>Data</th>
                    <th>Incoterms</th>
                    <th>Valuta</th>
                    <th class="text-end">Valore</th>
                    <th class="text-end">Saldo</th>
                    <th class="text-center">Container</th>
                    <th></th>
                </tr></thead>
                <tbody>
                    @forelse($contracts as $contract)
                    <tr>
                        <td class="fw-semibold">{{ $contract->contract_number }}</td>
                        <td>{{ $contract->supplier->name }}</td>
                        <td>{{ $contract->contract_date->format('d/m/Y') }}</td>
                        <td><span class="badge bg-secondary">{{ $contract->incoterms }}</span></td>
                        <td>{{ $contract->currency }}</td>
                        <td class="text-end fw-semibold">{{ number_format($contract->total_value, 2) }}</td>
                        <td class="text-end {{ $contract->remaining_balance > 0 ? 'text-danger' : 'text-success' }}">
                            {{ number_format($contract->remaining_balance, 2) }}
                        </td>
                        <td class="text-center">
                            <span class="badge bg-info text-dark">{{ $contract->containers_count }}</span>
                        </td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="{{ route('contracts.show', $contract) }}" class="btn btn-outline-primary btn-action"><i class="bi bi-eye"></i></a>
                                <a href="{{ route('contracts.edit', $contract) }}" class="btn btn-outline-secondary btn-action"><i class="bi bi-pencil"></i></a>
                                <form method="POST" action="{{ route('contracts.destroy', $contract) }}" onsubmit="return confirm('Eliminare questo contratto?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-outline-danger btn-action"><i class="bi bi-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="9" class="text-center text-muted py-4">Nessun contratto trovato</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($contracts->hasPages())
    <div class="card-footer bg-white">
        {{ $contracts->links() }}
    </div>
    @endif
</div>
@endsection
