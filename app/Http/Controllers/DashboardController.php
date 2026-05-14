<?php

namespace App\Http\Controllers;

use App\Models\Container;
use App\Models\Contract;
use App\Models\Payment;

class DashboardController extends Controller
{
    public function index()
    {
        $containersAtSea = Container::whereIn('status', ['at_port', 'in_transit'])->count();
        $containersTotal = Container::count();
        $contractsActive = Contract::whereHas('containers', fn($q) => $q->whereNotIn('status', ['at_warehouse']))->count();

        $paymentsDueSoon = Payment::dueSoon()->with('contract.supplier')->get();
        $paymentsOverdue = Payment::overdue()->with('contract.supplier')->get();

        $recentContainers = Container::with('contract.supplier')
            ->orderByDesc('updated_at')
            ->limit(8)
            ->get();

        $containersByStatus = Container::selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        return view('dashboard', compact(
            'containersAtSea',
            'containersTotal',
            'contractsActive',
            'paymentsDueSoon',
            'paymentsOverdue',
            'recentContainers',
            'containersByStatus'
        ));
    }
}
