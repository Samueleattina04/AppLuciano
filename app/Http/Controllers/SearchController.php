<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use App\Models\Shipment;
use App\Models\Supplier;
use App\Models\Document;
use App\Models\Payment;
use App\Models\CommunicationTask;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function global(Request $request)
    {
        $q = trim($request->get('q', ''));

        if (strlen($q) < 2) {
            return response()->json(['results' => []]);
        }

        $results = [];

        // Contracts
        $contracts = Contract::where('contract_number', 'like', "%{$q}%")
            ->with('supplier')
            ->take(5)->get();
        foreach ($contracts as $c) {
            $results['contracts'][] = [
                'id'    => $c->id,
                'label' => $c->contract_number . ' — ' . ($c->supplier->name ?? ''),
                'sub'   => $c->status_label,
                'url'   => route('contracts.show', $c->id),
                'icon'  => 'bi-file-earmark-text',
                'color' => '#3b82f6',
            ];
        }

        // Shipments
        $shipments = Shipment::where(function ($sq) use ($q) {
            $sq->where('shipment_code', 'like', "%{$q}%")
               ->orWhere('container_number', 'like', "%{$q}%")
               ->orWhere('bl_number', 'like', "%{$q}%")
               ->orWhere('vessel_name', 'like', "%{$q}%");
        })->with('supplier')->take(5)->get();
        foreach ($shipments as $s) {
            $results['shipments'][] = [
                'id'    => $s->id,
                'label' => $s->shipment_code . ' — ' . ($s->container_number ?? 'No container'),
                'sub'   => ($s->supplier->name ?? '') . ' · ' . $s->status_label,
                'url'   => route('shipments.show', $s->id),
                'icon'  => 'bi-box-seam',
                'color' => '#10b981',
            ];
        }

        // Suppliers
        $suppliers = Supplier::where('name', 'like', "%{$q}%")
            ->take(5)->get();
        foreach ($suppliers as $s) {
            $results['suppliers'][] = [
                'id'    => $s->id,
                'label' => $s->name,
                'sub'   => $s->country ?? '',
                'url'   => route('suppliers.show', $s->id),
                'icon'  => 'bi-building',
                'color' => '#8b5cf6',
            ];
        }

        // Payments
        $payments = Payment::where('bank_reference', 'like', "%{$q}%")
            ->with(['contract', 'supplier'])
            ->take(5)->get();
        foreach ($payments as $p) {
            $results['payments'][] = [
                'id'    => $p->id,
                'label' => 'Payment — ' . ($p->bank_reference ?? 'REF-' . $p->id),
                'sub'   => ($p->supplier->name ?? '') . ' · ' . number_format($p->amount_due, 2) . ' ' . $p->currency,
                'url'   => route('payments.index') . '?search=' . urlencode($q),
                'icon'  => 'bi-credit-card',
                'color' => '#f59e0b',
            ];
        }

        // Communications
        $comms = CommunicationTask::where('subject', 'like', "%{$q}%")
            ->take(5)->get();
        foreach ($comms as $c) {
            $results['communications'][] = [
                'id'    => $c->id,
                'label' => $c->subject,
                'sub'   => $c->category_label . ' · ' . $c->priority_label,
                'url'   => route('communications.show', $c->id),
                'icon'  => 'bi-envelope',
                'color' => '#ef4444',
            ];
        }

        // Flatten all for quick count
        $totalCount = collect($results)->flatten(1)->count();

        return response()->json([
            'results' => $results,
            'total'   => $totalCount,
            'query'   => $q,
        ]);
    }
}
