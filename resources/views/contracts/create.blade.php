@extends('layouts.app')
@section('title', 'Nuovo Contratto')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header"><i class="bi bi-file-earmark-plus me-2"></i>Nuovo Contratto d'Acquisto</div>
            <div class="card-body">
                <form method="POST" action="{{ route('contracts.store') }}">
                    @csrf
                    @include('contracts._form', ['contract' => null])
                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Salva Contratto</button>
                        <a href="{{ route('contracts.index') }}" class="btn btn-outline-secondary">Annulla</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
