@extends('layouts.app')
@section('title', 'Edit Payment — SupplyManager')
@section('content')

<div class="d-flex align-items-center gap-2 mb-4">
    <a href="{{ route('payments.index') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left"></i></a>
    <h1 class="page-title mb-0">Edit Payment</h1>
</div>

<form method="POST" action="{{ route('payments.update', $payment) }}">
    @csrf @method('PUT')
    <div class="row g-3">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Payment Details</div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Contract</label>
                            <select name="contract_id" class="form-select" required>
                                @foreach($contracts as $c)
                                    <option value="{{ $c->id }}" {{ old('contract_id', $payment->contract_id) == $c->id ? 'selected' : '' }}>{{ $c->contract_number }} — {{ $c->supplier->name ?? '' }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Supplier</label>
                            <select name="supplier_id" class="form-select" required>
                                @foreach($suppliers as $s)
                                    <option value="{{ $s->id }}" {{ old('supplier_id', $payment->supplier_id) == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3"><label class="form-label fw-semibold">Amount Due</label><input type="number" name="amount_due" step="0.01" class="form-control" value="{{ old('amount_due', $payment->amount_due) }}" required></div>
                        <div class="col-md-3"><label class="form-label fw-semibold">Amount Paid</label><input type="number" name="amount_paid" step="0.01" class="form-control" value="{{ old('amount_paid', $payment->amount_paid) }}"></div>
                        <div class="col-md-2"><label class="form-label fw-semibold">Currency</label>
                            <select name="currency" class="form-select">
                                @foreach(['USD', 'EUR', 'GBP'] as $c)<option value="{{ $c }}" {{ old('currency', $payment->currency) == $c ? 'selected' : '' }}>{{ $c }}</option>@endforeach
                            </select>
                        </div>
                        <div class="col-md-2"><label class="form-label fw-semibold">Status</label>
                            <select name="status" class="form-select">
                                @foreach(\App\Models\Payment::STATUS_LABELS as $k => $v)<option value="{{ $k }}" {{ old('status', $payment->status) == $k ? 'selected' : '' }}>{{ $v }}</option>@endforeach
                            </select>
                        </div>
                        <div class="col-md-4"><label class="form-label fw-semibold">Due Date</label><input type="date" name="due_date" class="form-control" value="{{ old('due_date', $payment->due_date?->format('Y-m-d')) }}" required></div>
                        <div class="col-md-4"><label class="form-label fw-semibold">Payment Date</label><input type="date" name="payment_date" class="form-control" value="{{ old('payment_date', $payment->payment_date?->format('Y-m-d')) }}"></div>
                        <div class="col-md-4"><label class="form-label fw-semibold">Bank Reference</label><input type="text" name="bank_reference" class="form-control" value="{{ old('bank_reference', $payment->bank_reference) }}"></div>
                        <div class="col-12"><label class="form-label fw-semibold">Notes</label><textarea name="notes" class="form-control" rows="3">{{ old('notes', $payment->notes) }}</textarea></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <button type="submit" class="btn btn-primary w-100 mb-2">Update Payment</button>
                    <a href="{{ route('payments.index') }}" class="btn btn-outline-secondary w-100">Cancel</a>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection
