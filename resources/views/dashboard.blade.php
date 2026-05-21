@extends('layouts.app')
@section('title', 'Dashboard — SupplyManager')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="page-title mb-0">Dashboard</h1>
        <small class="text-muted">{{ now()->format('l, d F Y') }}</small>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('contracts.create') }}" class="btn btn-sm btn-primary">
            <i class="bi bi-plus me-1"></i>New Contract
        </a>
        <a href="{{ route('shipments.create') }}" class="btn btn-sm btn-success">
            <i class="bi bi-plus me-1"></i>New Shipment
        </a>
        <a href="{{ route('communications.create') }}" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-plus me-1"></i>New Follow-up
        </a>
    </div>
</div>

<!-- KPI ROW -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="kpi-card kpi-blue">
            <div class="kpi-value">{{ $containersAtSea }}</div>
            <div class="kpi-label">Containers at Sea</div>
            <i class="bi bi-ship kpi-icon"></i>
        </div>
    </div>
    <div class="col-md-3">
        <div class="kpi-card kpi-orange">
            <div class="kpi-value">{{ $etaNext7->count() }}</div>
            <div class="kpi-label">ETA Next 7 Days</div>
            <i class="bi bi-calendar-event kpi-icon"></i>
        </div>
    </div>
    <div class="col-md-3">
        <div class="kpi-card kpi-red">
            <div class="kpi-value">{{ $paymentsOverdue->count() }}</div>
            <div class="kpi-label">Payments Overdue</div>
            <i class="bi bi-exclamation-circle kpi-icon"></i>
        </div>
    </div>
    <div class="col-md-3">
        <div class="kpi-card kpi-purple">
            <div class="kpi-value">{{ $openCommunications }}</div>
            <div class="kpi-label">Urgent Follow-ups</div>
            <i class="bi bi-envelope-exclamation kpi-icon"></i>
        </div>
    </div>
</div>

<!-- ALERT ROW -->
@if($paymentsOverdue->count() > 0 || $paymentsDueSoon->count() > 0 || $etaNext7->count() > 0)
<div class="row g-3 mb-4">
    @if($paymentsOverdue->count() > 0)
    <div class="col-md-4">
        <div class="card border-danger">
            <div class="card-header text-danger d-flex align-items-center gap-2">
                <i class="bi bi-exclamation-circle-fill"></i> Overdue Payments
                <span class="badge bg-danger ms-auto">{{ $paymentsOverdue->count() }}</span>
            </div>
            <div class="card-body p-0">
                @foreach($paymentsOverdue->take(4) as $p)
                <div class="p-2 border-bottom d-flex justify-content-between align-items-center">
                    <div>
                        <div style="font-size:0.8rem;font-weight:600">{{ $p->contract->contract_number ?? 'N/A' }}</div>
                        <div style="font-size:0.73rem;color:#94a3b8">{{ $p->supplier->name ?? '' }} · Due {{ $p->due_date?->format('d/m/Y') }}</div>
                    </div>
                    <span class="badge-status status-overdue">{{ number_format($p->amount_due,0) }} {{ $p->currency }}</span>
                </div>
                @endforeach
                @if($paymentsOverdue->count() > 4)
                <div class="p-2 text-center"><a href="{{ route('payments.index') }}?tab=overdue" class="text-danger" style="font-size:0.8rem">View all {{ $paymentsOverdue->count() }} overdue &rarr;</a></div>
                @endif
            </div>
        </div>
    </div>
    @endif

    @if($paymentsDueSoon->count() > 0)
    <div class="col-md-4">
        <div class="card border-warning">
            <div class="card-header text-warning d-flex align-items-center gap-2">
                <i class="bi bi-clock-history"></i> Due This Week
                <span class="badge bg-warning text-dark ms-auto">{{ $paymentsDueSoon->count() }}</span>
            </div>
            <div class="card-body p-0">
                @foreach($paymentsDueSoon->take(4) as $p)
                <div class="p-2 border-bottom d-flex justify-content-between align-items-center">
                    <div>
                        <div style="font-size:0.8rem;font-weight:600">{{ $p->contract->contract_number ?? 'N/A' }}</div>
                        <div style="font-size:0.73rem;color:#94a3b8">{{ $p->supplier->name ?? '' }} · Due {{ $p->due_date?->format('d/m/Y') }}</div>
                    </div>
                    <span class="badge-status status-due_soon">{{ number_format($p->amount_due,0) }} {{ $p->currency }}</span>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    @if($etaNext7->count() > 0)
    <div class="col-md-4">
        <div class="card border-primary">
            <div class="card-header text-primary d-flex align-items-center gap-2">
                <i class="bi bi-geo-alt"></i> Arrivals Next 7 Days
                <span class="badge bg-primary ms-auto">{{ $etaNext7->count() }}</span>
            </div>
            <div class="card-body p-0">
                @foreach($etaNext7->take(4) as $s)
                <div class="p-2 border-bottom d-flex justify-content-between align-items-center">
                    <div>
                        <a href="{{ route('shipments.show', $s) }}" style="font-size:0.8rem;font-weight:600">{{ $s->shipment_code }}</a>
                        <div style="font-size:0.73rem;color:#94a3b8">{{ $s->supplier->name ?? '' }} · ETA {{ $s->eta?->format('d/m/Y') }}</div>
                    </div>
                    <span class="badge-status status-{{ $s->status }}">{{ $s->status_label }}</span>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif
</div>
@endif

<!-- MIDDLE ROW -->
<div class="row g-3 mb-4">
    <div class="col-md-5">
        <div class="card h-100">
            <div class="card-header d-flex align-items-center gap-2">
                <i class="bi bi-bar-chart-fill text-primary"></i> Shipment Status Breakdown
            </div>
            <div class="card-body p-0">
                @foreach(\App\Models\Shipment::STATUS_LABELS as $key => $label)
                    @php $cnt = $shipmentStatusBreakdown[$key] ?? 0; @endphp
                    @if($cnt > 0 || in_array($key, ['in_transit', 'at_port', 'arrived_pod']))
                    <a href="{{ route('shipments.index') }}?status={{ $key }}" class="d-flex align-items-center justify-content-between px-3 py-2 border-bottom text-decoration-none text-dark">
                        <span class="badge-status status-{{ $key }}">{{ $label }}</span>
                        <strong style="font-size:1rem">{{ $cnt }}</strong>
                    </a>
                    @endif
                @endforeach
            </div>
        </div>
    </div>

    <div class="col-md-7">
        <div class="card h-100">
            <div class="card-header d-flex align-items-center gap-2">
                <i class="bi bi-exclamation-triangle-fill text-warning"></i> Open Claims
                <a href="{{ route('claims.create') }}" class="btn btn-sm btn-outline-warning ms-auto" style="font-size:0.75rem">+ New</a>
            </div>
            <div class="card-body p-0">
                @forelse($openClaimsList as $claim)
                <div class="p-2 border-bottom d-flex align-items-center justify-content-between">
                    <div>
                        <div style="font-size:0.85rem;font-weight:600">{{ $claim->contract->contract_number ?? 'N/A' }}</div>
                        <div style="font-size:0.75rem;color:#64748b">{{ $claim->type_label }} · {{ $claim->supplier->name ?? '' }}</div>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge-status status-{{ $claim->status }}">{{ $claim->status_label }}</span>
                        <span style="font-size:0.8rem;font-weight:600">{{ number_format($claim->amount,0) }} {{ $claim->currency }}</span>
                    </div>
                </div>
                @empty
                <div class="p-3 text-center text-muted" style="font-size:0.875rem">
                    <i class="bi bi-check-circle text-success me-1"></i>No open claims
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<!-- BOTTOM ROW -->
<div class="row g-3">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header d-flex align-items-center gap-2">
                <i class="bi bi-clock-history text-secondary"></i> Recent Activity
            </div>
            <div class="card-body" style="max-height:320px;overflow-y:auto">
                <div class="timeline">
                    @forelse($recentActivity as $log)
                    <div class="timeline-item {{ $log->description }}">
                        <div style="font-size:0.82rem;font-weight:600">
                            <span class="badge-status status-{{ $log->description === 'created' ? 'confirmed' : ($log->description === 'deleted' ? 'cancelled' : 'partially_shipped') }}" style="font-size:0.65rem">{{ strtoupper($log->description) }}</span>
                            {{ $log->log_name }} #{{ $log->subject_id }}
                        </div>
                        <div style="font-size:0.73rem;color:#94a3b8">{{ $log->created_at?->diffForHumans() }} · {{ $log->causer_name ?? 'System' }}</div>
                    </div>
                    @empty
                    <p class="text-muted" style="font-size:0.875rem">No activity recorded yet.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card">
            <div class="card-header d-flex align-items-center gap-2">
                <i class="bi bi-lightning-charge-fill text-warning"></i> Quick Actions
            </div>
            <div class="card-body">
                <div class="row g-2">
                    <div class="col-6">
                        <a href="{{ route('contracts.create') }}" class="btn btn-outline-primary w-100 text-start d-flex align-items-center gap-2">
                            <i class="bi bi-file-earmark-plus"></i>
                            <div>
                                <div style="font-size:0.8rem;font-weight:600">New Contract</div>
                                <div style="font-size:0.7rem;color:#64748b">Register a purchase contract</div>
                            </div>
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="{{ route('shipments.create') }}" class="btn btn-outline-success w-100 text-start d-flex align-items-center gap-2">
                            <i class="bi bi-box-seam"></i>
                            <div>
                                <div style="font-size:0.8rem;font-weight:600">New Shipment</div>
                                <div style="font-size:0.7rem;color:#64748b">Track a new container</div>
                            </div>
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="{{ route('payments.create') }}" class="btn btn-outline-warning w-100 text-start d-flex align-items-center gap-2">
                            <i class="bi bi-credit-card"></i>
                            <div>
                                <div style="font-size:0.8rem;font-weight:600">New Payment</div>
                                <div style="font-size:0.7rem;color:#64748b">Record a payment</div>
                            </div>
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="{{ route('communications.create') }}" class="btn btn-outline-secondary w-100 text-start d-flex align-items-center gap-2">
                            <i class="bi bi-envelope-plus"></i>
                            <div>
                                <div style="font-size:0.8rem;font-weight:600">New Follow-up</div>
                                <div style="font-size:0.7rem;color:#64748b">Log a communication</div>
                            </div>
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="{{ route('claims.create') }}" class="btn btn-outline-danger w-100 text-start d-flex align-items-center gap-2">
                            <i class="bi bi-exclamation-triangle"></i>
                            <div>
                                <div style="font-size:0.8rem;font-weight:600">New Claim</div>
                                <div style="font-size:0.7rem;color:#64748b">Open a claim</div>
                            </div>
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="{{ route('suppliers.create') }}" class="btn btn-outline-info w-100 text-start d-flex align-items-center gap-2">
                            <i class="bi bi-building-add"></i>
                            <div>
                                <div style="font-size:0.8rem;font-weight:600">New Supplier</div>
                                <div style="font-size:0.7rem;color:#64748b">Add a supplier</div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
