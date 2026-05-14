@extends('layouts.app')
@section('title', 'Fornitori')

@section('content')
<div class="d-flex justify-content-end mb-3">
    <a href="{{ route('suppliers.create') }}" class="btn btn-primary btn-sm">
        <i class="bi bi-plus-lg me-1"></i>Nuovo Fornitore
    </a>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead><tr>
                    <th>Nome</th><th>Paese</th><th>Email</th><th>Telefono</th><th class="text-center">Contratti</th><th></th>
                </tr></thead>
                <tbody>
                    @forelse($suppliers as $s)
                    <tr>
                        <td class="fw-semibold">{{ $s->name }}</td>
                        <td>{{ $s->country ?? '—' }}</td>
                        <td>{{ $s->contact_email ?? '—' }}</td>
                        <td>{{ $s->contact_phone ?? '—' }}</td>
                        <td class="text-center"><span class="badge bg-info text-dark">{{ $s->contracts_count }}</span></td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="{{ route('suppliers.show', $s) }}" class="btn btn-outline-primary btn-action"><i class="bi bi-eye"></i></a>
                                <a href="{{ route('suppliers.edit', $s) }}" class="btn btn-outline-secondary btn-action"><i class="bi bi-pencil"></i></a>
                                <form method="POST" action="{{ route('suppliers.destroy', $s) }}" onsubmit="return confirm('Eliminare questo fornitore?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-outline-danger btn-action"><i class="bi bi-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center text-muted py-4">Nessun fornitore</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($suppliers->hasPages())
    <div class="card-footer bg-white">{{ $suppliers->links() }}</div>
    @endif
</div>
@endsection
