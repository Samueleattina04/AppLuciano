@extends('layouts.app')
@section('title', 'Modifica Container')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-9">
        <div class="card">
            <div class="card-header"><i class="bi bi-pencil-square me-2"></i>Modifica Container — {{ $container->container_number }}</div>
            <div class="card-body">
                <form method="POST" action="{{ route('containers.update', $container) }}">
                    @csrf @method('PUT')
                    @include('containers._form', ['container' => $container])
                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Aggiorna</button>
                        <a href="{{ route('containers.show', $container) }}" class="btn btn-outline-secondary">Annulla</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
