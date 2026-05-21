@extends('layouts.app')
@section('title', 'Communications — SupplyManager')
@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="page-title mb-0">Inbox / Follow-up</h1>
        <small class="text-muted">{{ $tasks->total() }} tasks found</small>
    </div>
    <a href="{{ route('communications.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle me-1"></i>New Task
    </a>
</div>

<!-- TABS -->
<ul class="nav nav-tabs mb-3">
    <li class="nav-item"><a class="nav-link {{ !request('tab') && !request('status') ? 'active' : '' }}" href="{{ route('communications.index') }}">All</a></li>
    <li class="nav-item">
        <a class="nav-link {{ request('tab') === 'open' ? 'active' : '' }}" href="{{ route('communications.index') }}?tab=open">
            Open <span class="badge bg-secondary ms-1">{{ $statusCounts['open'] }}</span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ request('tab') === 'urgent' ? 'active' : '' }}" href="{{ route('communications.index') }}?tab=urgent">
            <span class="text-danger">Urgent/High</span> <span class="badge bg-danger ms-1">{{ $statusCounts['urgent'] }}</span>
        </a>
    </li>
    <li class="nav-item"><a class="nav-link {{ request('status') === 'to_review' ? 'active' : '' }}" href="{{ route('communications.index') }}?status=to_review">To Review</a></li>
    <li class="nav-item"><a class="nav-link {{ request('status') === 'replied' ? 'active' : '' }}" href="{{ route('communications.index') }}?status=replied">Replied</a></li>
    <li class="nav-item"><a class="nav-link {{ request('status') === 'closed' ? 'active' : '' }}" href="{{ route('communications.index') }}?status=closed">Closed</a></li>
</ul>

<!-- FILTERS -->
<div class="card mb-3">
    <div class="card-body py-2">
        <form method="GET" class="row g-2 align-items-end">
            @if(request('tab'))<input type="hidden" name="tab" value="{{ request('tab') }}">@endif
            @if(request('status'))<input type="hidden" name="status" value="{{ request('status') }}">@endif
            <div class="col-md-3">
                <input type="text" name="search" value="{{ request('search') }}" class="form-control form-control-sm" placeholder="Search subject...">
            </div>
            <div class="col-md-3">
                <select name="category" class="form-select form-select-sm">
                    <option value="">All Categories</option>
                    @foreach(\App\Models\CommunicationTask::CATEGORY_LABELS as $k => $v)
                        <option value="{{ $k }}" {{ request('category') == $k ? 'selected' : '' }}>{{ $v }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <select name="assigned_to" class="form-select form-select-sm">
                    <option value="">All Assignees</option>
                    @foreach($users as $u)
                        <option value="{{ $u->id }}" {{ request('assigned_to') == $u->id ? 'selected' : '' }}>{{ $u->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-sm btn-primary">Filter</button>
                <a href="{{ route('communications.index') }}" class="btn btn-sm btn-outline-secondary">Clear</a>
            </div>
        </form>
    </div>
</div>

<!-- TASK CARDS -->
<div class="row g-2">
    @forelse($tasks as $task)
    <div class="col-12">
        <div class="comm-card priority-{{ $task->priority }}">
            <div class="d-flex align-items-start gap-3">
                <div class="flex-grow-1">
                    <div class="d-flex align-items-center gap-2 flex-wrap mb-1">
                        <a href="{{ route('communications.show', $task) }}" class="fw-bold text-decoration-none" style="font-size:0.95rem">{{ $task->subject }}</a>
                        <span class="badge-status priority-{{ $task->priority }}" style="font-size:0.68rem">{{ $task->priority_label }}</span>
                        <span class="badge bg-light text-dark border" style="font-size:0.7rem">{{ $task->category_label }}</span>
                    </div>
                    <div class="d-flex align-items-center gap-3 flex-wrap" style="font-size:0.8rem;color:#64748b">
                        @if($task->contract)
                        <span><i class="bi bi-file-earmark-text me-1"></i><a href="{{ route('contracts.show', $task->contract) }}" style="font-size:0.8rem">{{ $task->contract->contract_number }}</a></span>
                        @endif
                        @if($task->shipment)
                        <span><i class="bi bi-box-seam me-1"></i><a href="{{ route('shipments.show', $task->shipment) }}" style="font-size:0.8rem">{{ $task->shipment->shipment_code }}</a></span>
                        @endif
                        @if($task->due_date)
                        @php $daysLeft = now()->diffInDays($task->due_date, false); @endphp
                        <span class="{{ $daysLeft < 0 ? 'text-danger fw-bold' : '' }}">
                            <i class="bi bi-calendar me-1"></i>Due: {{ $task->due_date->format('d/m/Y') }}
                            @if($daysLeft < 0)({{ abs($daysLeft) }}d late)@elseif($daysLeft <= 3)(in {{ $daysLeft }}d)@endif
                        </span>
                        @endif
                        @if($task->assignedUser)
                        <span><i class="bi bi-person me-1"></i>{{ $task->assignedUser->name }}</span>
                        @endif
                        <span class="text-muted">{{ $task->created_at?->diffForHumans() }}</span>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2 flex-shrink-0">
                    <span class="badge-status status-{{ $task->status }}" style="font-size:0.7rem">{{ $task->status_label }}</span>
                    @if(!in_array($task->status, ['replied','closed']))
                    <form method="POST" action="{{ route('communications.mark-replied', $task) }}">
                        @csrf
                        <button type="submit" class="btn btn-xs btn-sm btn-success" title="Mark Replied">
                            <i class="bi bi-check2-circle"></i>
                        </button>
                    </form>
                    @endif
                    <a href="{{ route('communications.edit', $task) }}" class="btn btn-xs btn-sm btn-outline-secondary">
                        <i class="bi bi-pencil"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12">
        <div class="card">
            <div class="card-body text-center py-5 text-muted">
                <i class="bi bi-inbox" style="font-size:2rem;display:block;margin-bottom:0.5rem"></i>
                No communication tasks found.
            </div>
        </div>
    </div>
    @endforelse
</div>

<div class="mt-3">{{ $tasks->withQueryString()->links() }}</div>
@endsection
