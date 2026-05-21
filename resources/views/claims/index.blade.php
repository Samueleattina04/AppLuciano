@extends('layouts.app')
@section('title', 'Reclami — SupplyManager')
@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="page-title mb-0">Reclami</h1>
    <a href="{{ route('claims.create') }}" class="btn btn-warning"><i class="bi bi-plus-circle me-1"></i>Nuovo Reclamo</a>
</div>

<!-- FILTRI -->
<div class="card mb-3">
    <div class="card-body py-2">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-3">
                <select name="status" class="form-select form-select-sm">
                    <option value="">Tutti gli Stati</option>
                    @foreach(\App\Models\Claim::STATUS_LABELS as $k => $v)
                        <option value="{{ $k }}" {{ request('status') == $k ? 'selected' : '' }}>{{ $v }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <select name="claim_type" class="form-select form-select-sm">
                    <option value="">Tutti i Tipi</option>
                    @foreach(\App\Models\Claim::TYPE_LABELS as $k => $v)
                        <option value="{{ $k }}" {{ request('claim_type') == $k ? 'selected' : '' }}>{{ $v }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-sm btn-primary">Filtra</button>
                <a href="{{ route('claims.index') }}" class="btn btn-sm btn-outline-secondary">Azzera</a>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-supply mb-0">
                <thead>
                    <tr>
                        <th>Tipo</th>
                        <th>Contratto</th>
                        <th>Spedizione</th>
                        <th>Fornitore</th>
                        <th>Motivo</th>
                        <th class="text-end">Importo</th>
                        <th class="text-center">Stato</th>
                        <th>Data</th>
                        <th class="text-end">Azioni</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($claims as $claim)
                    <tr>
                        <td><span class="badge bg-light text-dark border">{{ $claim->type_label }}</span></td>
                        <td>@if($claim->contract)<a href="{{ route('contracts.show', $claim->contract) }}" style="font-size:0.85rem">{{ $claim->contract->contract_number }}</a>@else —@endif</td>
                        <td>@if($claim->shipment)<a href="{{ route('shipments.show', $claim->shipment) }}" style="font-size:0.85rem">{{ $claim->shipment->shipment_code }}</a>@else —@endif</td>
                        <td style="font-size:0.85rem">{{ $claim->supplier->name ?? '—' }}</td>
                        <td style="font-size:0.85rem">{{ Str::limit($claim->reason, 50) }}</td>
                        <td class="text-end fw-bold">{{ number_format($claim->amount, 2) }} {{ $claim->currency }}</td>
                        <td class="text-center"><span class="badge-status status-{{ $claim->status }}">{{ $claim->status_label }}</span></td>
                        <td style="font-size:0.8rem">{{ $claim->created_at?->format('d/m/Y') }}</td>
                        <td class="text-end">
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('claims.show', $claim) }}" class="btn btn-outline-primary btn-sm"><i class="bi bi-eye"></i></a>
                                <a href="{{ route('claims.edit', $claim) }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-pencil"></i></a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="9" class="text-center py-4 text-muted">Nessun reclamo trovato.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
<div class="mt-3">{{ $claims->withQueryString()->links() }}</div>
@endsection
