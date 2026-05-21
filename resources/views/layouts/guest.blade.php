<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'SupplyManager') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        body { background: #0f172a; font-family: 'Segoe UI', system-ui, sans-serif; min-height: 100vh; display: flex; align-items: center; justify-content: center; }
        .auth-card { width: 100%; max-width: 420px; }
        .auth-logo { text-align: center; margin-bottom: 2rem; color: #fff; font-size: 1.5rem; font-weight: 700; }
        .auth-logo i { color: #3b82f6; }
        .auth-logo small { display: block; color: rgba(255,255,255,.4); font-size: .75rem; font-weight: 400; margin-top: .25rem; }
    </style>
</head>
<body>
    <div class="auth-card">
        <div class="auth-logo">
            <i class="bi bi-boxes me-2"></i>SupplyManager
            <small>Supply Chain Operations</small>
        </div>
        <div class="card border-0 shadow-lg">
            <div class="card-body p-4">
                {{ $slot }}
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
