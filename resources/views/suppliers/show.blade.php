@extends('layouts.app')
@section('title', 'Supplier — SupplyManager')
@section('content')
<div class="d-flex align-items-center gap-2 mb-4">
    <a href="{{ route('suppliers.index') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left"></i></a>
    <h1 class="page-title mb-0">{{ $supplier->name }}</h1>
    <span class="badge bg-light text-dark border">{{ $supplier->country ?? '' }}</span>
    <div class="ms-auto">
        <a href="{{ route('suppliers.edit', $supplier) }}" class="btn btn-sm btn-outline-primary">Edit</a>
    </div>
</div>
<div class="row g-3">
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">Contact Info</div>
            <div class="card-body">
                <table class="table table-sm table-borderless">
                    <tr><td class="text-muted">Name</td><td>{{ $supplier->contact_name ?? '—' }}</td></tr>
                    <tr><td class="text-muted">Email</td><td>{{ $supplier->contact_email ?? '—' }}</td></tr>
                    <tr><td class="text-muted">Phone</td><td>{{ $supplier->contact_phone ?? '—' }}</td></tr>
                    <tr><td class="text-muted">Country</td><td>{{ $supplier->country ?? '—' }}</td></tr>
                </table>
                @if($supplier->notes)<hr><p class="mb-0" style="font-size:0.875rem">{{ $supplier->notes }}</p>@endif
            </div>
        </div>
    </div>
    <div class="col-md-8">
        <div class="card mb-3">
            <div class="card-header">Contracts ({{ $supplier->contracts->count() }})</div>
            <div class="card-body p-0">
                @forelse($supplier->contracts->take(5) as $c)
                <div class="d-flex align-items-center justify-content-between p-2 border-bottom">
                    <a href="{{ route('contracts.show', $c) }}" class="fw-bold text-decoration-none">{{ $c->contract_number }}</a>
                    <div class="d-flex gap-2 align-items-center">
                        <span style="font-size:0.8rem">{{ number_format($c->total_value,0) }} {{ $c->currency }}</span>
                        <span class="badge-status status-{{ $c->status }}">{{ $c->status_label }}</span>
                    </div>
                </div>
                @empty
                <div class="p-3 text-center text-muted">No contracts.</div>
                @endforelse
            </div>
        </div>
        <div class="card">
            <div class="card-header">Recent Shipments ({{ $supplier->shipments->count() }})</div>
            <div class="card-body p-0">
                @forelse($supplier->shipments->take(5) as $s)
                <div class="d-flex align-items-center justify-content-between p-2 border-bottom">
                    <div>
                        <a href="{{ route('shipments.show', $s) }}" class="fw-bold text-decoration-none">{{ $s->shipment_code }}</a>
                        <div class="text-muted-sm">{{ $s->container_number ?? '' }}</div>
                    </div>
                    <span class="badge-status status-{{ $s->status }}">{{ $s->status_label }}</span>
                </div>
                @empty
                <div class="p-3 text-center text-muted">No shipments.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
