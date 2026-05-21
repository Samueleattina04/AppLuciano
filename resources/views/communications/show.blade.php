@extends('layouts.app')
@section('title', 'Communication Task — SupplyManager')
@section('content')
<div class="d-flex align-items-center gap-2 mb-4">
    <a href="{{ route('communications.index') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left"></i></a>
    <h1 class="page-title mb-0">{{ Str::limit($communication->subject, 50) }}</h1>
    <span class="badge-status priority-{{ $communication->priority }}">{{ $communication->priority_label }}</span>
    <span class="badge-status status-{{ $communication->status }}">{{ $communication->status_label }}</span>
    <div class="ms-auto d-flex gap-2">
        @if(!in_array($communication->status, ['replied','closed']))
        <form method="POST" action="{{ route('communications.mark-replied', $communication) }}">
            @csrf
            <button type="submit" class="btn btn-sm btn-success"><i class="bi bi-check2-circle me-1"></i>Mark Replied</button>
        </form>
        @endif
        <a href="{{ route('communications.edit', $communication) }}" class="btn btn-sm btn-outline-primary">Edit</a>
    </div>
</div>
<div class="row g-3">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">Details</div>
            <div class="card-body">
                <table class="table table-sm table-borderless">
                    <tr><td class="text-muted" style="width:30%">Subject</td><td><strong>{{ $communication->subject }}</strong></td></tr>
                    <tr><td class="text-muted">Category</td><td>{{ $communication->category_label }}</td></tr>
                    <tr><td class="text-muted">Priority</td><td><span class="badge-status priority-{{ $communication->priority }}">{{ $communication->priority_label }}</span></td></tr>
                    <tr><td class="text-muted">Status</td><td><span class="badge-status status-{{ $communication->status }}">{{ $communication->status_label }}</span></td></tr>
                    <tr><td class="text-muted">Sender</td><td>{{ $communication->sender ?? '—' }}</td></tr>
                    <tr><td class="text-muted">Recipient</td><td>{{ $communication->recipient ?? '—' }}</td></tr>
                    <tr><td class="text-muted">Due Date</td><td>{{ $communication->due_date?->format('d/m/Y') ?? '—' }}</td></tr>
                    <tr><td class="text-muted">Reminder</td><td>{{ $communication->reminder_date?->format('d/m/Y') ?? '—' }}</td></tr>
                    <tr><td class="text-muted">Assigned To</td><td>{{ $communication->assignedUser?->name ?? '—' }}</td></tr>
                    <tr><td class="text-muted">Contract</td><td>@if($communication->contract)<a href="{{ route('contracts.show', $communication->contract) }}">{{ $communication->contract->contract_number }}</a>@else —@endif</td></tr>
                    <tr><td class="text-muted">Shipment</td><td>@if($communication->shipment)<a href="{{ route('shipments.show', $communication->shipment) }}">{{ $communication->shipment->shipment_code }}</a>@else —@endif</td></tr>
                    @if($communication->outlook_thread_link)
                    <tr><td class="text-muted">Outlook</td><td><a href="{{ $communication->outlook_thread_link }}" target="_blank" class="btn btn-xs btn-sm btn-outline-primary"><i class="bi bi-envelope me-1"></i>Open in Outlook</a></td></tr>
                    @endif
                </table>
                @if($communication->notes)
                <hr>
                <p class="mb-0" style="font-size:0.875rem">{{ $communication->notes }}</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
