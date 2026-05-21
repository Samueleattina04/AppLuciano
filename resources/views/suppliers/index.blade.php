@extends('layouts.app')
@section('title', 'Suppliers — SupplyManager')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="page-title mb-0">Suppliers</h1>
    <a href="{{ route('suppliers.create') }}" class="btn btn-primary"><i class="bi bi-plus-circle me-1"></i>New Supplier</a>
</div>
<div class="card mb-3">
    <div class="card-body py-2">
        <form method="GET" class="d-flex gap-2">
            <input type="text" name="search" value="{{ request('search') }}" class="form-control form-control-sm" placeholder="Search name, country..." style="max-width:300px">
            <button type="submit" class="btn btn-sm btn-primary">Search</button>
        </form>
    </div>
</div>
<div class="card">
    <div class="card-body p-0">
        <table class="table table-supply mb-0">
            <thead>
                <tr><th>Supplier</th><th>Country</th><th>Contact</th><th class="text-center">Contracts</th><th class="text-center">Shipments</th><th class="text-end">Actions</th></tr>
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
                <tr><td colspan="6" class="text-center py-4 text-muted">No suppliers found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-3">{{ $suppliers->withQueryString()->links() }}</div>
@endsection
