@extends('layouts.app')
@section('title', 'Modifica Contratto')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header"><i class="bi bi-pencil-square me-2"></i>Modifica Contratto — {{ $contract->contract_number }}</div>
            <div class="card-body">
                <form method="POST" action="{{ route('contracts.update', $contract) }}">
                    @csrf @method('PUT')
                    @include('contracts._form', ['contract' => $contract])
                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Aggiorna</button>
                        <a href="{{ route('contracts.show', $contract) }}" class="btn btn-outline-secondary">Annulla</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
