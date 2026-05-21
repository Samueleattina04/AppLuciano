@extends('layouts.app')
@section('title', 'New Communication Task — SupplyManager')
@section('content')
<div class="d-flex align-items-center gap-2 mb-4">
    <a href="{{ route('communications.index') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left"></i></a>
    <h1 class="page-title mb-0">New Communication Task</h1>
</div>
<form method="POST" action="{{ route('communications.store') }}">
    @csrf
    <div class="row g-3">
        <div class="col-md-8">
            <div class="card mb-3">
                <div class="card-header">Task Details</div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-12"><label class="form-label fw-semibold">Subject *</label><input type="text" name="subject" class="form-control" value="{{ old('subject') }}" required></div>
                        <div class="col-md-6"><label class="form-label fw-semibold">Sender</label><input type="text" name="sender" class="form-control" value="{{ old('sender') }}"></div>
                        <div class="col-md-6"><label class="form-label fw-semibold">Recipient</label><input type="text" name="recipient" class="form-control" value="{{ old('recipient') }}"></div>
                        <div class="col-md-4"><label class="form-label fw-semibold">Category</label>
                            <select name="category" class="form-select">
                                @foreach(\App\Models\CommunicationTask::CATEGORY_LABELS as $k => $v)<option value="{{ $k }}" {{ old('category') == $k ? 'selected' : '' }}>{{ $v }}</option>@endforeach
                            </select>
                        </div>
                        <div class="col-md-4"><label class="form-label fw-semibold">Priority</label>
                            <select name="priority" class="form-select">
                                @foreach(\App\Models\CommunicationTask::PRIORITY_LABELS as $k => $v)<option value="{{ $k }}" {{ old('priority','normal') == $k ? 'selected' : '' }}>{{ $v }}</option>@endforeach
                            </select>
                        </div>
                        <div class="col-md-4"><label class="form-label fw-semibold">Status</label>
                            <select name="status" class="form-select">
                                @foreach(\App\Models\CommunicationTask::STATUS_LABELS as $k => $v)<option value="{{ $k }}" {{ old('status','to_review') == $k ? 'selected' : '' }}>{{ $v }}</option>@endforeach
                            </select>
                        </div>
                        <div class="col-md-4"><label class="form-label fw-semibold">Due Date</label><input type="date" name="due_date" class="form-control" value="{{ old('due_date') }}"></div>
                        <div class="col-md-4"><label class="form-label fw-semibold">Reminder Date</label><input type="date" name="reminder_date" class="form-control" value="{{ old('reminder_date') }}"></div>
                        <div class="col-md-4"><label class="form-label fw-semibold">Assigned To</label>
                            <select name="assigned_to" class="form-select">
                                <option value="">Unassigned</option>
                                @foreach($users as $u)<option value="{{ $u->id }}" {{ old('assigned_to') == $u->id ? 'selected' : '' }}>{{ $u->name }}</option>@endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card mb-3">
                <div class="card-header">Links</div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6"><label class="form-label fw-semibold">Contract</label>
                            <select name="contract_id" class="form-select">
                                <option value="">None</option>
                                @foreach($contracts as $c)<option value="{{ $c->id }}" {{ old('contract_id', request('contract_id')) == $c->id ? 'selected' : '' }}>{{ $c->contract_number }}</option>@endforeach
                            </select>
                        </div>
                        <div class="col-md-6"><label class="form-label fw-semibold">Shipment</label>
                            <select name="shipment_id" class="form-select">
                                <option value="">None</option>
                                @foreach($shipments as $s)<option value="{{ $s->id }}" {{ old('shipment_id', request('shipment_id')) == $s->id ? 'selected' : '' }}>{{ $s->shipment_code }}</option>@endforeach
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Outlook Thread Link</label>
                            <div class="input-group">
                                <input type="url" name="outlook_thread_link" class="form-control" value="{{ old('outlook_thread_link') }}" placeholder="https://outlook.office.com/...">
                                <button type="button" class="btn btn-outline-secondary" onclick="if(document.querySelector('[name=outlook_thread_link]').value) window.open(document.querySelector('[name=outlook_thread_link]').value)">
                                    <i class="bi bi-box-arrow-up-right"></i> Open
                                </button>
                            </div>
                        </div>
                        <div class="col-12"><label class="form-label fw-semibold">Notes</label><textarea name="notes" class="form-control" rows="4">{{ old('notes') }}</textarea></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <button type="submit" class="btn btn-primary w-100 mb-2">Create Task</button>
                    <a href="{{ route('communications.index') }}" class="btn btn-outline-secondary w-100">Cancel</a>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection
