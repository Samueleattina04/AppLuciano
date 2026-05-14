@extends('layouts.app')
@section('title', 'Nuovo Fornitore')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="card">
            <div class="card-header"><i class="bi bi-building-add me-2"></i>Nuovo Fornitore</div>
            <div class="card-body">
                <form method="POST" action="{{ route('suppliers.store') }}">
                    @csrf
                    @include('suppliers._form', ['supplier' => null])
                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Salva</button>
                        <a href="{{ route('suppliers.index') }}" class="btn btn-outline-secondary">Annulla</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
