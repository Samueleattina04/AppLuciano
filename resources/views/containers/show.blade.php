@extends('layouts.app')
@section('title', 'Container ' . $container->container_number)

@section('content')
<div class="d-flex justify-content-between align-items-start mb-4">
    <div>
        <h2 class="fw-bold mb-1">{{ $container->container_number }}</h2>
        <div class="d-flex gap-2 align-items-center flex-wrap">
            <span class="badge bg-{{ $container->status_color }} fs-6">{{ $container->status_label }}</span>
            <span class="text-muted">
                <i class="bi bi-file-earmark-text me-1"></i>
                <a href="{{ route('contracts.show', $container->contract) }}" class="text-decoration-none">{{ $container->contract->contract_number }}</a>
            </span>
            <span class="text-muted"><i class="bi bi-building me-1"></i>{{ $container->contract->supplier->name }}</span>
        </div>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('containers.edit', $container) }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-pencil me-1"></i>Modifica</a>
        <form method="POST" action="{{ route('containers.destroy', $container) }}" onsubmit="return confirm('Eliminare il container?')">
            @csrf @method('DELETE')
            <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash me-1"></i>Elimina</button>
        </form>
    </div>
</div>

{{-- Missing docs alert --}}
@php $missing = $container->missing_critical_documents; @endphp
@if(count($missing) > 0)
<div class="alert alert-danger alert-sm d-flex align-items-center mb-4">
    <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i>
    <div>
        <strong>Documenti mancanti per lo sdoganamento:</strong>
        @foreach($missing as $doc)
            <span class="badge bg-danger ms-1">{{ \App\Models\Document::TYPE_LABELS[$doc] ?? $doc }}</span>
        @endforeach
    </div>
</div>
@endif

{{-- Tracking timeline --}}
<div class="card mb-4">
    <div class="card-header"><i class="bi bi-map me-2"></i>Dati Logistici</div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-3">
                <div class="small text-muted">Nave</div>
                <div class="fw-semibold">{{ $container->vessel_name ?? '—' }}</div>
            </div>
            <div class="col-md-3">
                <div class="small text-muted">N° Viaggio</div>
                <div class="fw-semibold">{{ $container->voyage_number ?? '—' }}</div>
            </div>
            <div class="col-md-3">
                <div class="small text-muted">Porto di Carico</div>
                <div class="fw-semibold">{{ $container->port_of_loading ?? '—' }}</div>
            </div>
            <div class="col-md-3">
                <div class="small text-muted">Porto di Scarico</div>
                <div class="fw-semibold">{{ $container->port_of_discharge ?? '—' }}</div>
            </div>
            <div class="col-md-3">
                <div class="small text-muted">ETD</div>
                <div class="fw-semibold">{{ $container->etd ? $container->etd->format('d/m/Y') : '—' }}</div>
            </div>
            <div class="col-md-3">
                <div class="small text-muted">ETA</div>
                <div class="fw-semibold">{{ $container->eta ? $container->eta->format('d/m/Y') : '—' }}</div>
            </div>
        </div>
    </div>
</div>

{{-- Status Progress --}}
<div class="card mb-4">
    <div class="card-header"><i class="bi bi-arrow-right-circle me-2"></i>Progressione Stato</div>
    <div class="card-body">
        <div class="d-flex gap-0 align-items-center justify-content-between">
            @php
                $steps = array_keys(\App\Models\Container::STATUS_LABELS);
                $currentIdx = array_search($container->status, $steps);
            @endphp
            @foreach(\App\Models\Container::STATUS_LABELS as $key => $label)
            @php $idx = array_search($key, $steps); @endphp
            <div class="text-center flex-fill">
                <div class="rounded-circle border border-2 mx-auto d-flex align-items-center justify-content-center mb-1
                    {{ $idx < $currentIdx ? 'bg-success border-success text-white' : ($idx == $currentIdx ? 'bg-primary border-primary text-white' : 'border-secondary text-muted') }}"
                    style="width:36px;height:36px;font-size:.8rem;font-weight:700;">
                    {{ $idx < $currentIdx ? '✓' : ($idx + 1) }}
                </div>
                <div style="font-size:.72rem;" class="{{ $idx == $currentIdx ? 'fw-bold text-primary' : 'text-muted' }}">{{ $label }}</div>
            </div>
            @if(!$loop->last)
                <div class="flex-shrink-0" style="width:24px;height:2px;background:{{ $idx < $currentIdx ? '#198754' : '#dee2e6' }};margin-bottom:20px;"></div>
            @endif
            @endforeach
        </div>
    </div>
</div>

{{-- Documents --}}
<div class="card">
    <div class="card-header"><i class="bi bi-folder2-open me-2"></i>Documenti Container</div>
    <div class="card-body">
        @include('partials._document_upload', ['model' => 'container', 'modelId' => $container->id])
        @include('partials._document_list', ['documents' => $container->documents])
    </div>
</div>

@endsection
