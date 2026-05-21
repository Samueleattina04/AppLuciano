@extends('layouts.app')
@section('title', 'Activity Log — SupplyManager')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="page-title mb-0">Activity Log</h1>
</div>

<!-- FILTERS -->
<div class="card mb-3">
    <div class="card-body py-2">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-3">
                <select name="log_name" class="form-select form-select-sm">
                    <option value="">All Modules</option>
                    @foreach($logNames as $name)
                        <option value="{{ $name }}" {{ request('log_name') == $name ? 'selected' : '' }}>{{ $name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="description" class="form-select form-select-sm">
                    <option value="">All Actions</option>
                    <option value="created" {{ request('description') == 'created' ? 'selected' : '' }}>Created</option>
                    <option value="updated" {{ request('description') == 'updated' ? 'selected' : '' }}>Updated</option>
                    <option value="deleted" {{ request('description') == 'deleted' ? 'selected' : '' }}>Deleted</option>
                </select>
            </div>
            <div class="col-md-2"><input type="date" name="date_from" class="form-control form-control-sm" value="{{ request('date_from') }}" placeholder="From"></div>
            <div class="col-md-2"><input type="date" name="date_to" class="form-control form-control-sm" value="{{ request('date_to') }}" placeholder="To"></div>
            <div class="col-auto">
                <button type="submit" class="btn btn-sm btn-primary">Filter</button>
                <a href="{{ route('activity.index') }}" class="btn btn-sm btn-outline-secondary">Clear</a>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body" style="max-height:75vh;overflow-y:auto">
        <div class="timeline">
            @forelse($logs as $log)
            <div class="timeline-item {{ $log->description }}">
                <div class="d-flex align-items-center gap-2 mb-1">
                    <span class="badge-status status-{{ $log->description === 'created' ? 'confirmed' : ($log->description === 'deleted' ? 'cancelled' : 'partially_shipped') }}" style="font-size:0.65rem">{{ strtoupper($log->description) }}</span>
                    <strong style="font-size:0.875rem">{{ $log->log_name }}</strong>
                    <span class="text-muted" style="font-size:0.8rem">#{{ $log->subject_id }}</span>
                    @if($log->causer_name)
                    <span class="text-muted" style="font-size:0.8rem">by {{ $log->causer_name }}</span>
                    @endif
                </div>
                <div style="font-size:0.75rem;color:#94a3b8">{{ $log->created_at?->format('d/m/Y H:i') }}</div>
                @if($log->properties && is_array($log->properties) && count($log->properties) > 0)
                <div style="font-size:0.75rem;color:#64748b;margin-top:2px">
                    Changed: {{ collect($log->properties)->keys()->implode(', ') }}
                </div>
                @endif
            </div>
            @empty
            <p class="text-muted text-center py-5">No activity recorded yet.</p>
            @endforelse
        </div>
    </div>
</div>
<div class="mt-3">{{ $logs->withQueryString()->links() }}</div>
@endsection
