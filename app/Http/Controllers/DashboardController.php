<?php

namespace App\Http\Controllers;

use App\Models\Shipment;
use App\Models\Payment;
use App\Models\CommunicationTask;
use App\Models\Claim;
use App\Models\Contract;
use App\Models\ActivityLog;

class DashboardController extends Controller
{
    public function index()
    {
        $containersAtSea = Shipment::whereIn('status', ['at_port', 'in_transit'])->count();

        $etaNext7 = Shipment::whereNotNull('eta')
            ->whereBetween('eta', [now()->toDateString(), now()->addDays(7)->toDateString()])
            ->whereNotIn('status', ['arrived_pod', 'customs_clearance', 'delivered_warehouse', 'closed'])
            ->with(['supplier', 'contract'])
            ->get();

        $etaNext15 = Shipment::whereNotNull('eta')
            ->whereBetween('eta', [now()->toDateString(), now()->addDays(15)->toDateString()])
            ->whereNotIn('status', ['arrived_pod', 'customs_clearance', 'delivered_warehouse', 'closed'])
            ->with(['supplier', 'contract'])
            ->get();

        $paymentsOverdue = Payment::overdue()
            ->with(['contract', 'supplier'])
            ->orderBy('due_date')
            ->get();

        $paymentsDueSoon = Payment::dueSoon()
            ->with(['contract', 'supplier'])
            ->orderBy('due_date')
            ->get();

        // Shipments with missing critical docs
        $shipmentsWithMissingDocs = Shipment::whereIn('status', ['in_transit', 'at_port', 'arrived_pod', 'customs_clearance'])
            ->with('documents')
            ->get()
            ->filter(fn($s) => count($s->missing_critical_documents) > 0);

        $missingDocumentsCount = $shipmentsWithMissingDocs->count();

        $openCommunications = CommunicationTask::open()->urgent()->count();

        $openClaims = Claim::whereNotIn('status', ['closed', 'rejected'])->count();

        $contractsActiveValue = Contract::whereIn('status', ['confirmed', 'partially_shipped'])
            ->sum('total_value');

        $recentActivity = ActivityLog::latest()->take(10)->get();

        $shipmentStatusBreakdown = Shipment::selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        $openClaimsList = Claim::whereNotIn('status', ['closed', 'rejected'])
            ->with(['contract', 'supplier'])
            ->latest()
            ->take(5)
            ->get();

        return view('dashboard', compact(
            'containersAtSea',
            'etaNext7',
            'etaNext15',
            'paymentsOverdue',
            'paymentsDueSoon',
            'missingDocumentsCount',
            'openCommunications',
            'openClaims',
            'contractsActiveValue',
            'recentActivity',
            'shipmentStatusBreakdown',
            'openClaimsList'
        ));
    }
}
