<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name', 'SupplyManager'))</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
:root{--sidebar-bg:#0f172a;--sidebar-hover:#1e293b;--sidebar-active:#1d4ed8;--accent:#3b82f6;--danger:#ef4444;--warning:#f59e0b;--success:#10b981;--surface:#f8fafc;--sidebar-width:260px}
body{background:var(--surface);font-family:'Segoe UI',system-ui,-apple-system,sans-serif;font-size:.9rem}
#sidebar{width:var(--sidebar-width);min-height:100vh;background:var(--sidebar-bg);position:fixed;top:0;left:0;z-index:1000;overflow-y:auto;transition:transform .3s ease}
#sidebar .sidebar-brand{padding:1.25rem 1.5rem;border-bottom:1px solid rgba(255,255,255,.08)}
#sidebar .sidebar-brand span{color:#fff;font-size:1.1rem;font-weight:700;letter-spacing:.5px}
#sidebar .sidebar-brand small{color:rgba(255,255,255,.4);font-size:.7rem;display:block;margin-top:2px}
#sidebar .sidebar-section{padding:1rem 1rem .25rem;font-size:.65rem;font-weight:700;text-transform:uppercase;letter-spacing:1.2px;color:rgba(255,255,255,.3);margin:0}
#sidebar .nav-link{color:rgba(255,255,255,.65);padding:.6rem 1.5rem;display:flex;align-items:center;gap:.75rem;border-radius:0;transition:all .15s;font-size:.875rem;text-decoration:none}
#sidebar .nav-link:hover{color:#fff;background:var(--sidebar-hover)}
#sidebar .nav-link.active{color:#fff;background:var(--sidebar-active);font-weight:600}
#sidebar .nav-link i{width:18px;text-align:center;font-size:.875rem}
#topbar{height:60px;background:#fff;border-bottom:1px solid #e2e8f0;position:fixed;top:0;left:var(--sidebar-width);right:0;z-index:999;display:flex;align-items:center;padding:0 1.5rem;gap:1rem}
#main-content{margin-left:var(--sidebar-width);margin-top:60px;padding:1.5rem;min-height:calc(100vh - 60px)}
.kpi-card{border:none;border-radius:12px;padding:1.25rem;color:#fff;position:relative;overflow:hidden}
.kpi-card .kpi-value{font-size:2rem;font-weight:700;line-height:1}
.kpi-card .kpi-label{font-size:.8rem;opacity:.85;margin-top:.35rem}
.kpi-card .kpi-icon{position:absolute;right:1rem;top:50%;transform:translateY(-50%);font-size:2.5rem;opacity:.2}
.kpi-blue{background:linear-gradient(135deg,#3b82f6,#1d4ed8)}.kpi-orange{background:linear-gradient(135deg,#f59e0b,#d97706)}.kpi-red{background:linear-gradient(135deg,#ef4444,#dc2626)}.kpi-purple{background:linear-gradient(135deg,#8b5cf6,#7c3aed)}.kpi-green{background:linear-gradient(135deg,#10b981,#059669)}
.badge-status{font-size:.7rem;padding:.3em .7em;border-radius:50px;font-weight:600;text-transform:uppercase;letter-spacing:.5px}
.status-in_production{background:#e2e8f0;color:#475569}.status-ready_to_ship{background:#dbeafe;color:#1e40af}.status-at_port{background:#fef9c3;color:#92400e}.status-in_transit{background:#dbeafe;color:#1e40af}.status-arrived_pod{background:#d1fae5;color:#065f46}.status-customs_clearance{background:#ede9fe;color:#5b21b6}.status-delivered_warehouse{background:#d1fae5;color:#065f46}.status-closed{background:#f1f5f9;color:#64748b}
.status-draft{background:#f1f5f9;color:#64748b}.status-confirmed{background:#d1fae5;color:#065f46}.status-partially_shipped{background:#dbeafe;color:#1e40af}.status-completed{background:#d1fae5;color:#065f46}.status-cancelled{background:#fee2e2;color:#991b1b}
.status-pending{background:#f1f5f9;color:#64748b}.status-due_soon{background:#fef9c3;color:#92400e}.status-overdue{background:#fee2e2;color:#991b1b}.status-paid{background:#d1fae5;color:#065f46}.status-partially_paid{background:#dbeafe;color:#1e40af}
.status-open{background:#fee2e2;color:#991b1b}.status-under_review{background:#fef9c3;color:#92400e}.status-accepted{background:#d1fae5;color:#065f46}.status-rejected{background:#f1f5f9;color:#64748b}.status-deducted{background:#ede9fe;color:#5b21b6}
.priority-low{background:#f1f5f9;color:#64748b}.priority-normal{background:#dbeafe;color:#1e40af}.priority-high{background:#fef9c3;color:#92400e}.priority-urgent{background:#fee2e2;color:#991b1b}
.table-supply th{font-size:.75rem;text-transform:uppercase;letter-spacing:.5px;font-weight:600;color:#64748b;border-bottom:2px solid #e2e8f0;background:#f8fafc;white-space:nowrap}
.table-supply td{vertical-align:middle;font-size:.875rem;border-bottom:1px solid #f1f5f9}
.table-supply tbody tr:hover{background:#f8fafc}
.table-supply .row-overdue{background:#fff5f5}.table-supply .row-overdue:hover{background:#fee2e2}
.timeline{position:relative;padding-left:2rem}.timeline::before{content:'';position:absolute;left:.5rem;top:0;bottom:0;width:2px;background:#e2e8f0}
.timeline-item{position:relative;padding-bottom:1.25rem}.timeline-item::before{content:'';position:absolute;left:-1.625rem;top:.25rem;width:10px;height:10px;border-radius:50%;background:var(--accent);border:2px solid #fff;box-shadow:0 0 0 2px var(--accent)}
.timeline-item.created::before{background:#10b981;box-shadow:0 0 0 2px #10b981}.timeline-item.updated::before{background:#3b82f6;box-shadow:0 0 0 2px #3b82f6}.timeline-item.deleted::before{background:#ef4444;box-shadow:0 0 0 2px #ef4444}
.shipment-progress{display:flex;align-items:center;gap:0;margin:1.5rem 0}
.progress-step{flex:1;text-align:center;position:relative}.progress-step::before{content:'';position:absolute;top:14px;left:50%;right:-50%;height:2px;background:#e2e8f0;z-index:0}.progress-step:last-child::before{display:none}
.step-circle{width:30px;height:30px;border-radius:50%;background:#e2e8f0;display:inline-flex;align-items:center;justify-content:center;font-size:.7rem;font-weight:700;position:relative;z-index:1;color:#64748b}
.progress-step.completed .step-circle{background:#10b981;color:#fff}.progress-step.active .step-circle{background:var(--accent);color:#fff;box-shadow:0 0 0 4px rgba(59,130,246,.2)}.progress-step.completed::before{background:#10b981}
.step-label{font-size:.65rem;color:#64748b;margin-top:.35rem;display:block}
#globalSearchWrapper{position:relative;flex:1;max-width:500px}
#globalSearch{border-radius:8px;border:1px solid #e2e8f0;padding:.5rem 1rem .5rem 2.5rem;font-size:.875rem;background:#f8fafc;width:100%}
#globalSearch:focus{background:#fff;border-color:var(--accent);box-shadow:0 0 0 3px rgba(59,130,246,.15);outline:none}
#searchDropdown{position:absolute;top:100%;left:0;right:0;background:#fff;border:1px solid #e2e8f0;border-radius:8px;box-shadow:0 8px 32px rgba(0,0,0,.12);z-index:9999;max-height:450px;overflow-y:auto;display:none}
#searchDropdown.show{display:block}
.search-group-title{padding:.5rem 1rem .25rem;font-size:.7rem;text-transform:uppercase;letter-spacing:.8px;font-weight:700;color:#94a3b8;background:#f8fafc;border-bottom:1px solid #f1f5f9}
.search-result-item{padding:.6rem 1rem;cursor:pointer;border-bottom:1px solid #f8fafc;display:flex;align-items:center;gap:.75rem;text-decoration:none;color:inherit;transition:background .1s}
.search-result-item:hover,.search-result-item.highlighted{background:#f0f9ff;color:#1e40af;text-decoration:none}
.search-result-item .result-icon{width:28px;height:28px;border-radius:6px;display:flex;align-items:center;justify-content:center;font-size:.75rem;flex-shrink:0}
.search-empty{padding:2rem;text-align:center;color:#94a3b8;font-size:.875rem}
.card{border:1px solid #e2e8f0;border-radius:12px;box-shadow:0 1px 3px rgba(0,0,0,.04)}
.card-header{background:#fff;border-bottom:1px solid #f1f5f9;font-weight:600;font-size:.875rem}
.doc-checklist-item{display:flex;align-items:center;justify-content:space-between;padding:.6rem 0;border-bottom:1px solid #f1f5f9}
.doc-check{width:20px;height:20px;border-radius:50%;display:inline-flex;align-items:center;justify-content:center;font-size:.7rem}
.doc-check.ok{background:#d1fae5;color:#065f46}.doc-check.missing{background:#fee2e2;color:#991b1b}
.comm-card{border-left:3px solid #e2e8f0;border-radius:0 8px 8px 0;padding:.875rem 1rem;margin-bottom:.75rem;background:#fff;border-top:1px solid #f1f5f9;border-right:1px solid #f1f5f9;border-bottom:1px solid #f1f5f9}
.comm-card.priority-urgent{border-left-color:#ef4444}.comm-card.priority-high{border-left-color:#f59e0b}.comm-card.priority-normal{border-left-color:#3b82f6}
.nav-tabs .nav-link{color:#64748b;font-size:.875rem;font-weight:500;border:none;border-bottom:2px solid transparent;padding:.75rem 1.25rem}
.nav-tabs .nav-link.active{color:var(--accent);border-bottom-color:var(--accent);background:none}
.nav-tabs .nav-link:hover{color:#334155;border-bottom-color:#e2e8f0}
.page-title{font-size:1.25rem;font-weight:700;color:#0f172a}
.text-muted-sm{font-size:.75rem;color:#94a3b8}
.alert-bar{border-radius:8px;padding:.75rem 1rem;margin-bottom:.75rem;display:flex;align-items:center;gap:.75rem;font-size:.875rem}
.kbd{background:#f1f5f9;border:1px solid #e2e8f0;border-radius:4px;padding:.1em .4em;font-size:.7rem;color:#64748b}
.search-hint{font-size:.75rem;color:#94a3b8;display:flex;align-items:center;gap:.5rem}
    </style>
    @stack('styles')
</head>
<body>

<!-- SIDEBAR -->
<nav id="sidebar">
    <div class="sidebar-brand">
        <span><i class="bi bi-boxes me-2"></i>SupplyManager</span>
        <small>Supply Chain Operations</small>
    </div>

    <p class="sidebar-section">Main</p>
    <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
        <i class="bi bi-speedometer2"></i> Dashboard
    </a>

    <p class="sidebar-section">Operations</p>
    <a href="{{ route('contracts.index') }}" class="nav-link {{ request()->routeIs('contracts.*') ? 'active' : '' }}">
        <i class="bi bi-file-earmark-text"></i> Contracts
    </a>
    <a href="{{ route('shipments.index') }}" class="nav-link {{ request()->routeIs('shipments.*') ? 'active' : '' }}">
        <i class="bi bi-box-seam"></i> Shipments
    </a>

    <p class="sidebar-section">Finance</p>
    <a href="{{ route('payments.index') }}" class="nav-link {{ request()->routeIs('payments.*') ? 'active' : '' }}">
        <i class="bi bi-credit-card"></i> Payments
        @php $overdueCount = \App\Models\Payment::overdue()->count(); @endphp
        @if($overdueCount > 0)
            <span class="badge bg-danger ms-auto" style="font-size:0.65rem">{{ $overdueCount }}</span>
        @endif
    </a>
    <a href="{{ route('claims.index') }}" class="nav-link {{ request()->routeIs('claims.*') ? 'active' : '' }}">
        <i class="bi bi-exclamation-triangle"></i> Claims
    </a>

    <p class="sidebar-section">Communications</p>
    <a href="{{ route('communications.index') }}" class="nav-link {{ request()->routeIs('communications.*') ? 'active' : '' }}">
        <i class="bi bi-envelope-open"></i> Inbox / Follow-up
        @php $urgentComms = \App\Models\CommunicationTask::open()->urgent()->count(); @endphp
        @if($urgentComms > 0)
            <span class="badge bg-danger ms-auto" style="font-size:0.65rem">{{ $urgentComms }}</span>
        @endif
    </a>

    <p class="sidebar-section">Data</p>
    <a href="{{ route('suppliers.index') }}" class="nav-link {{ request()->routeIs('suppliers.*') ? 'active' : '' }}">
        <i class="bi bi-building"></i> Suppliers
    </a>
    <a href="{{ route('activity.index') }}" class="nav-link {{ request()->routeIs('activity.*') ? 'active' : '' }}">
        <i class="bi bi-clock-history"></i> Activity Log
    </a>

    <div style="height: 2rem;"></div>
</nav>

<!-- TOPBAR -->
<div id="topbar">
    <div id="globalSearchWrapper">
        <i class="bi bi-search" style="position:absolute;left:0.75rem;top:50%;transform:translateY(-50%);color:#94a3b8;font-size:0.875rem;z-index:1"></i>
        <input type="text" id="globalSearch" placeholder="Search contracts, shipments, suppliers... (Ctrl+K)" autocomplete="off">
        <div id="searchDropdown"></div>
    </div>

    <div class="ms-auto d-flex align-items-center gap-3">
        @php $overdueTotal = \App\Models\Payment::overdue()->count(); @endphp
        @if($overdueTotal > 0)
        <a href="{{ route('payments.index') }}?tab=overdue" class="btn btn-sm btn-danger d-flex align-items-center gap-1" style="font-size:0.75rem">
            <i class="bi bi-exclamation-circle"></i>
            {{ $overdueTotal }} Overdue
        </a>
        @endif

        <div class="dropdown">
            <button class="btn btn-sm btn-light dropdown-toggle d-flex align-items-center gap-2" data-bs-toggle="dropdown">
                <i class="bi bi-person-circle"></i>
                {{ auth()->user()->name }}
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
                <li>
                    <a class="dropdown-item" href="{{ route('profile.edit') }}">
                        <i class="bi bi-person me-2"></i>Profile
                    </a>
                </li>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="dropdown-item text-danger">
                            <i class="bi bi-box-arrow-right me-2"></i>Logout
                        </button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
</div>

<!-- MAIN CONTENT -->
<div id="main-content">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-3" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
            <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @yield('content')
</div>

<!-- Global Search Script -->
<script>
(function() {
    const searchInput = document.getElementById('globalSearch');
    const searchDropdown = document.getElementById('searchDropdown');
    let debounceTimer = null;
    let currentHighlight = -1;

    if (!searchInput) return;

    document.addEventListener('keydown', function(e) {
        if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
            e.preventDefault();
            searchInput.focus();
            searchInput.select();
        }
    });

    searchInput.addEventListener('input', function() {
        clearTimeout(debounceTimer);
        const q = this.value.trim();
        if (q.length < 2) { closeDropdown(); return; }
        debounceTimer = setTimeout(() => performSearch(q), 300);
    });

    searchInput.addEventListener('keydown', function(e) {
        const items = searchDropdown.querySelectorAll('.search-result-item');
        if (e.key === 'ArrowDown') {
            e.preventDefault();
            currentHighlight = Math.min(currentHighlight + 1, items.length - 1);
            updateHighlight(items);
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            currentHighlight = Math.max(currentHighlight - 1, -1);
            updateHighlight(items);
        } else if (e.key === 'Enter' && currentHighlight >= 0 && items[currentHighlight]) {
            e.preventDefault();
            window.location.href = items[currentHighlight].href;
        } else if (e.key === 'Escape') {
            closeDropdown(); searchInput.blur();
        }
    });

    document.addEventListener('click', function(e) {
        if (!e.target.closest('#globalSearchWrapper')) closeDropdown();
    });

    function performSearch(q) {
        fetch('/search?q=' + encodeURIComponent(q), { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(r => r.json()).then(data => renderResults(data, q)).catch(() => closeDropdown());
    }

    function renderResults(data, q) {
        const results = data.results || {};
        const total = data.total || 0;
        if (total === 0) {
            searchDropdown.innerHTML = '<div class="search-empty"><i class="bi bi-search" style="font-size:1.5rem;display:block;margin-bottom:0.5rem"></i>No results for "<strong>' + escHtml(q) + '</strong>"</div>';
            searchDropdown.classList.add('show'); return;
        }
        const groupLabels = {
            contracts: { label: 'Contracts', icon: 'bi-file-earmark-text' },
            shipments: { label: 'Shipments', icon: 'bi-box-seam' },
            suppliers: { label: 'Suppliers', icon: 'bi-building' },
            payments:  { label: 'Payments', icon: 'bi-credit-card' },
            communications: { label: 'Communications', icon: 'bi-envelope' },
        };
        let html = '';
        for (const [key, items] of Object.entries(results)) {
            if (!items || items.length === 0) continue;
            const grp = groupLabels[key] || { label: key, icon: 'bi-circle' };
            html += '<div class="search-group-title"><i class="' + grp.icon + ' me-1"></i>' + grp.label + '</div>';
            items.forEach(item => {
                html += '<a href="' + escHtml(item.url) + '" class="search-result-item">'
                    + '<div class="result-icon" style="background:' + (item.color||'#f1f5f9') + '22;color:' + (item.color||'#64748b') + '"><i class="' + (item.icon||'bi-circle') + '"></i></div>'
                    + '<div><div style="font-size:0.875rem;font-weight:500">' + escHtml(item.label) + '</div>'
                    + '<div style="font-size:0.75rem;color:#94a3b8">' + escHtml(item.sub||'') + '</div></div></a>';
            });
        }
        searchDropdown.innerHTML = html;
        searchDropdown.classList.add('show');
        currentHighlight = -1;
    }

    function updateHighlight(items) {
        items.forEach((item, i) => item.classList.toggle('highlighted', i === currentHighlight));
    }

    function closeDropdown() {
        searchDropdown.classList.remove('show');
        searchDropdown.innerHTML = '';
        currentHighlight = -1;
    }

    function escHtml(str) {
        const d = document.createElement('div');
        d.appendChild(document.createTextNode(str));
        return d.innerHTML;
    }
})();
</script>

@stack('scripts')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
