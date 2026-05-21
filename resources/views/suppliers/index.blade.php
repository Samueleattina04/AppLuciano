@extends('layouts.app')
@section('title', 'Fornitori — SupplyManager')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="page-title mb-0">Fornitori</h1>
    <a href="{{ route('suppliers.create') }}" class="btn btn-primary"><i class="bi bi-plus-circle me-1"></i>Nuovo Fornitore</a>
</div>
<div class="card mb-3">
    <div class="card-body py-2">
        <form method="GET" class="d-flex gap-2">
            <input type="text" name="search" value="{{ request('search') }}" class="form-control form-control-sm" placeholder="Cerca nome, paese..." style="max-width:300px">
            <button type="submit" class="btn btn-sm btn-primary">Cerca</button>
        </form>
    </div>
</div>
<div class="card">
    <div class="card-body p-0">
        <table class="table table-supply mb-0">
            <thead>
                <tr><th>Fornitore</th><th>Paese</th><th>Contatto</th><th class="text-center">Contratti</th><th class="text-center">Spedizioni</th><th class="text-end">Azioni</th></tr>
            </thead>
            <tbody>
                @forelse($suppliers as $s)
                <tr>
                    <td><a href="{{ route('suppliers.show', $s) }}" class="fw-bold text-decoration-none">{{ $s->name }}</a></td>
                    <td>{{ $s->country ?? '—' }}</td>
                    <td>
                        @if($s->contact_name)<div style="font-size:0.85rem">{{ $s->contact_name }}</div>@endif
                        @if($s->contact_email)<div class="text-muted-sm">{{ $s->contact_email }}</div>@endif
                    </td>
                    <td class="text-center">{{ $s->contracts_count }}</td>
                    <td class="text-center">{{ $s->shipments_count }}</td>
                    <td class="text-end">
                        <div class="btn-group btn-group-sm">
                            <a href="{{ route('suppliers.show', $s) }}" class="btn btn-outline-primary btn-sm"><i class="bi bi-eye"></i></a>
                            <a href="{{ route('suppliers.edit', $s) }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-pencil"></i></a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center py-4 text-muted">Nessun fornitore trovato.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-3">{{ $suppliers->withQueryString()->links() }}</div>
@endsection
