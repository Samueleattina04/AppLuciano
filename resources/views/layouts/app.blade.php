<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name', 'SupplyManager'))</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
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
</body>
</html>
