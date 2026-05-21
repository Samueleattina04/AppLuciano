@extends('layouts.app')
@section('title', 'Claim — SupplyManager')
@section('content')
<div class="d-flex align-items-center gap-2 mb-4">
    <a href="{{ route('claims.index') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left"></i></a>
    <h1 class="page-title mb-0">Claim #{{ $claim->id }}</h1>
    <span class="badge-status status-{{ $claim->status }}">{{ $claim->status_label }}</span>
    <div class="ms-auto">
        <a href="{{ route('claims.edit', $claim) }}" class="btn btn-sm btn-outline-primary">Edit</a>
    </div>
</div>
<div class="row g-3">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">Claim Details</div>
            <div class="card-body">
                <table class="table table-sm table-borderless">
                    <tr><td class="text-muted">Type</td><td><span class="badge bg-light text-dark border">{{ $claim->type_label }}</span></td></tr>
                    <tr><td class="text-muted">Contract</td><td>@if($claim->contract)<a href="{{ route('contracts.show', $claim->contract) }}">{{ $claim->contract->contract_number }}</a>@else —@endif</td></tr>
                    <tr><td class="text-muted">Shipment</td><td>@if($claim->shipment)<a href="{{ route('shipments.show', $claim->shipment) }}">{{ $claim->shipment->shipment_code }}</a>@else —@endif</td></tr>
                    <tr><td class="text-muted">Supplier</td><td>{{ $claim->supplier->name ?? '—' }}</td></tr>
                    <tr><td class="text-muted">Amount</td><td><strong>{{ number_format($claim->amount, 2) }} {{ $claim->currency }}</strong></td></tr>
                    <tr><td class="text-muted">Status</td><td><span class="badge-status status-{{ $claim->status }}">{{ $claim->status_label }}</span></td></tr>
                    <tr><td class="text-muted">Resolved</td><td>{{ $claim->resolved_date?->format('d/m/Y') ?? '—' }}</td></tr>
                    <tr><td class="text-muted">Created</td><td>{{ $claim->created_at?->format('d/m/Y') }}</td></tr>
                </table>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">Reason</div>
            <div class="card-body"><p>{{ $claim->reason }}</p></div>
        </div>
        @if($claim->notes)
        <div class="card mt-3">
            <div class="card-header">Notes</div>
            <div class="card-body"><p class="mb-0">{{ $claim->notes }}</p></div>
        </div>
        @endif
    </div>
</div>
@endsection
