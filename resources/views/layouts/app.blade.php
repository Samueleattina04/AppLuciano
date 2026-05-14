<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Supply Manager') — Supply Manager</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        :root {
            --sidebar-width: 240px;
            --sidebar-bg: #0f172a;
            --sidebar-active: #1e293b;
            --accent: #3b82f6;
        }
        body { background: #f1f5f9; font-family: 'Segoe UI', system-ui, sans-serif; }
        #sidebar {
            position: fixed; top: 0; left: 0; height: 100vh;
            width: var(--sidebar-width); background: var(--sidebar-bg);
            z-index: 1000; overflow-y: auto;
        }
        #sidebar .brand {
            padding: 1.25rem 1.5rem; border-bottom: 1px solid #1e293b;
            font-weight: 700; font-size: 1rem; color: #e2e8f0; letter-spacing: .5px;
        }
        #sidebar .brand span { color: var(--accent); }
        #sidebar .nav-label {
            padding: .5rem 1.5rem .25rem; font-size: .68rem; font-weight: 600;
            text-transform: uppercase; letter-spacing: 1px; color: #475569;
        }
        #sidebar .nav-link {
            display: flex; align-items: center; gap: .6rem;
            padding: .55rem 1.5rem; color: #94a3b8; font-size: .875rem;
            border-left: 3px solid transparent; transition: all .15s;
        }
        #sidebar .nav-link:hover { color: #e2e8f0; background: #1e293b; }
        #sidebar .nav-link.active { color: #fff; background: var(--sidebar-active); border-left-color: var(--accent); }
        #sidebar .nav-link .bi { font-size: 1rem; }
        #main {
            margin-left: var(--sidebar-width);
            min-height: 100vh; display: flex; flex-direction: column;
        }
        #topbar {
            background: #fff; border-bottom: 1px solid #e2e8f0;
            padding: .75rem 1.75rem; display: flex; align-items: center;
            justify-content: space-between; position: sticky; top: 0; z-index: 100;
        }
        #topbar .page-title { font-size: 1.1rem; font-weight: 600; color: #0f172a; margin: 0; }
        #content { padding: 1.75rem; flex: 1; }
        .card { border: none; box-shadow: 0 1px 3px rgba(0,0,0,.06); border-radius: .75rem; }
        .card-header { background: #fff; border-bottom: 1px solid #f1f5f9; font-weight: 600; padding: 1rem 1.25rem; }
        .kpi-card { border-radius: .75rem; overflow: hidden; }
        .kpi-card .kpi-icon { width: 3rem; height: 3rem; border-radius: .5rem; display: flex; align-items: center; justify-content: center; font-size: 1.4rem; }
        .badge-status { font-size: .72rem; padding: .35em .65em; border-radius: 999px; font-weight: 600; }
        .table th { font-size: .78rem; font-weight: 600; text-transform: uppercase; letter-spacing: .5px; color: #64748b; border-bottom: 2px solid #e2e8f0; }
        .table td { vertical-align: middle; color: #1e293b; }
        .table-hover tbody tr:hover { background: #f8fafc; }
        .alert-sm { padding: .5rem .9rem; font-size: .83rem; }
        .btn-action { padding: .3rem .6rem; font-size: .8rem; }
        .section-divider { border-top: 2px solid #e2e8f0; margin: 1.5rem 0; }
    </style>
    @stack('styles')
</head>
<body>

<div id="sidebar">
    <div class="brand">
        <i class="bi bi-boxes me-1"></i>Supply<span>Manager</span>
    </div>
    <nav class="mt-2">
        <div class="nav-label">Principale</div>
        <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <i class="bi bi-grid-1x2-fill"></i> Dashboard
        </a>

        <div class="nav-label mt-2">Acquisti</div>
        <a href="{{ route('contracts.index') }}" class="nav-link {{ request()->routeIs('contracts.*') ? 'active' : '' }}">
            <i class="bi bi-file-earmark-text-fill"></i> Contratti
        </a>
        <a href="{{ route('suppliers.index') }}" class="nav-link {{ request()->routeIs('suppliers.*') ? 'active' : '' }}">
            <i class="bi bi-building-fill"></i> Fornitori
        </a>

        <div class="nav-label mt-2">Logistica</div>
        <a href="{{ route('containers.index') }}" class="nav-link {{ request()->routeIs('containers.*') ? 'active' : '' }}">
            <i class="bi bi-box-seam-fill"></i> Container
        </a>

        <div class="nav-label mt-2">Finanza</div>
        <a href="{{ route('payments.index') }}" class="nav-link {{ request()->routeIs('payments.*') ? 'active' : '' }}">
            <i class="bi bi-credit-card-2-front-fill"></i> Pagamenti
        </a>
    </nav>
</div>

<div id="main">
    <div id="topbar">
        <h1 class="page-title">@yield('title', 'Dashboard')</h1>
        <div class="d-flex gap-2">
            <a href="{{ route('contracts.create') }}" class="btn btn-sm btn-primary">
                <i class="bi bi-plus-lg me-1"></i>Nuovo Contratto
            </a>
            <a href="{{ route('containers.create') }}" class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-box-seam me-1"></i>Nuovo Container
            </a>
        </div>
    </div>

    <div id="content">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible alert-sm fade show mb-3" role="alert">
                <i class="bi bi-check-circle-fill me-1"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible alert-sm fade show mb-3" role="alert">
                <i class="bi bi-exclamation-circle-fill me-1"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @yield('content')
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>
