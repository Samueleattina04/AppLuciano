@extends('layouts.app')
@section('title', 'Contracts — SupplyManager')
@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="page-title mb-0">Contracts</h1>
        <small class="text-muted">{{ $contracts->total() }} contracts found</small>
    </div>
    <a href="{{ route('contracts.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle me-1"></i>New Contract
    </a>
</div>

<!-- FILTERS -->
<div class="card mb-4">
    <div class="card-body py-2">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-4">
                <input type="text" name="search" value="{{ request('search') }}" class="form-control form-control-sm" placeholder="Search contract #, supplier...">
            </div>
            <div class="col-md-3">
                <select name="supplier_id" class="form-select form-select-sm">
                    <option value="">All Suppliers</option>
                    @foreach($suppliers as $id => $name)
                        <option value="{{ $id }}" {{ request('supplier_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-sm btn-primary">Filter</button>
                <a href="{{ route('contracts.index') }}" class="btn btn-sm btn-outline-secondary">Clear</a>
            </div>
        </form>
    </div>
</div>

<!-- STATUS TABS -->
<ul class="nav nav-tabs mb-3">
    <li class="nav-item">
        <a class="nav-link {{ !request('status') ? 'active' : '' }}" href="{{ route('contracts.index', request()->except('status', 'page')) }}">
            All <span class="badge bg-secondary ms-1">{{ $contracts->total() }}</span>
        </a>
    </li>
    @foreach(['draft' => 'Draft', 'confirmed' => 'Confirmed', 'partially_shipped' => 'Partially Shipped', 'completed' => 'Completed', 'cancelled' => 'Cancelled'] as $key => $label)
    <li class="nav-item">
        <a class="nav-link {{ request('status') === $key ? 'active' : '' }}" href="{{ route('contracts.index', array_merge(request()->all(), ['status' => $key, 'page' => 1])) }}">
            {{ $label }} <span class="badge bg-secondary ms-1">{{ $statusCounts[$key] ?? 0 }}</span>
        </a>
    </li>
    @endforeach
</ul>

<!-- TABLE -->
<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-supply mb-0">
                <thead>
                    <tr>
                        <th>Contract #</th>
                        <th>Supplier</th>
                        <th>Product</th>
                        <th>Incoterm / Ports</th>
                        <th class="text-end">Value</th>
                        <th class="text-center">Qty Progress</th>
                        <th class="text-center">Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($contracts as $contract)
                    <tr>
                        <td>
                            <a href="{{ route('contracts.show', $contract) }}" class="fw-bold text-decoration-none">{{ $contract->contract_number }}</a>
                            <div class="text-muted-sm">{{ $contract->contract_date?->format('d/m/Y') }}</div>
                        </td>
                        <td>
                            <div style="font-size:0.875rem">{{ $contract->supplier->name ?? '—' }}</div>
                            <div class="text-muted-sm">{{ $contract->supplier->country ?? '' }}</div>
                        </td>
                        <td>{{ $contract->product->name ?? '—' }}</td>
                        <td>
                            <span class="badge bg-light text-dark border" style="font-size:0.7rem">{{ $contract->incoterm ?? '—' }}</span>
                            <div class="text-muted-sm">{{ $contract->port_of_loading ?? '' }} → {{ $contract->port_of_discharge ?? '' }}</div>
                        </td>
                        <td class="text-end">
                            <div class="fw-bold">{{ number_format($contract->total_value, 0) }} {{ $contract->currency }}</div>
                            <div class="text-muted-sm">{{ number_format($contract->unit_price, 2) }}/{{ $contract->unit_of_measure }}</div>
                        </td>
                        <td class="text-center" style="min-width:130px">
                            @php
                                $pct = $contract->quantity_contracted > 0
                                    ? min(100, round(($contract->shipped_quantity / $contract->quantity_contracted) * 100))
                                    : 0;
                            @endphp
                            <div class="progress" style="height:6px;margin-bottom:3px">
                                <div class="progress-bar bg-success" style="width:{{ $pct }}%"></div>
                            </div>
                            <div class="text-muted-sm">{{ number_format($contract->shipped_quantity,0) }} / {{ number_format($contract->quantity_contracted,0) }} {{ $contract->unit_of_measure }}</div>
                        </td>
                        <td class="text-center">
                            <span class="badge-status status-{{ $contract->status }}">{{ $contract->status_label }}</span>
                        </td>
                        <td class="text-end">
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('contracts.show', $contract) }}" class="btn btn-outline-primary btn-sm" title="View">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('contracts.edit', $contract) }}" class="btn btn-outline-secondary btn-sm" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form method="POST" action="{{ route('contracts.destroy', $contract) }}" onsubmit="return confirm('Delete this contract?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger btn-sm" title="Delete">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-4 text-muted">No contracts found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="mt-3">{{ $contracts->withQueryString()->links() }}</div>

@endsection
