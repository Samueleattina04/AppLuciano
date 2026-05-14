@extends('layouts.app')
@section('title', $supplier->name)

@section('content')
<div class="d-flex justify-content-between align-items-start mb-4">
    <div>
        <h2 class="fw-bold mb-1">{{ $supplier->name }}</h2>
        <div class="text-muted">
            <i class="bi bi-geo-alt me-1"></i>{{ $supplier->country ?? 'N/D' }}
            @if($supplier->contact_email) &nbsp;·&nbsp; <i class="bi bi-envelope me-1"></i>{{ $supplier->contact_email }} @endif
            @if($supplier->contact_phone) &nbsp;·&nbsp; <i class="bi bi-telephone me-1"></i>{{ $supplier->contact_phone }} @endif
        </div>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('suppliers.edit', $supplier) }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-pencil me-1"></i>Modifica</a>
    </div>
</div>

<div class="card">
    <div class="card-header"><i class="bi bi-file-earmark-text me-2"></i>Contratti ({{ $supplier->contracts->count() }})</div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead><tr>
                    <th>N° Contratto</th><th>Data</th><th>Incoterms</th><th>Valuta</th>
                    <th class="text-end">Valore</th><th class="text-center">Container</th><th></th>
                </tr></thead>
                <tbody>
                    @forelse($supplier->contracts as $ct)
                    <tr>
                        <td class="fw-semibold">{{ $ct->contract_number }}</td>
                        <td>{{ $ct->contract_date->format('d/m/Y') }}</td>
                        <td><span class="badge bg-secondary">{{ $ct->incoterms }}</span></td>
                        <td>{{ $ct->currency }}</td>
                        <td class="text-end">{{ number_format($ct->total_value, 2) }}</td>
                        <td class="text-center"><span class="badge bg-info text-dark">{{ $ct->containers->count() }}</span></td>
                        <td><a href="{{ route('contracts.show', $ct) }}" class="btn btn-outline-primary btn-action"><i class="bi bi-eye"></i></a></td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="text-center text-muted py-3">Nessun contratto</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
