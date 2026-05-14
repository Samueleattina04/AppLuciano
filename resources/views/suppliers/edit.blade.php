@extends('layouts.app')
@section('title', 'Modifica Fornitore')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="card">
            <div class="card-header"><i class="bi bi-pencil-square me-2"></i>Modifica Fornitore</div>
            <div class="card-body">
                <form method="POST" action="{{ route('suppliers.update', $supplier) }}">
                    @csrf @method('PUT')
                    @include('suppliers._form', ['supplier' => $supplier])
                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Aggiorna</button>
                        <a href="{{ route('suppliers.show', $supplier) }}" class="btn btn-outline-secondary">Annulla</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
