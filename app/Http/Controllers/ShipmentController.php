<?php

namespace App\Http\Controllers;

use App\Models\Shipment;
use App\Models\Contract;
use App\Models\Supplier;
use App\Models\Product;
use Illuminate\Http\Request;

class ShipmentController extends Controller
{
    public function index(Request $request)
    {
        $query = Shipment::with(['supplier', 'contract', 'documents']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('shipment_code', 'like', '%' . $request->search . '%')
                  ->orWhere('container_number', 'like', '%' . $request->search . '%')
                  ->orWhere('bl_number', 'like', '%' . $request->search . '%')
                  ->orWhere('vessel_name', 'like', '%' . $request->search . '%')
                  ->orWhereHas('supplier', fn($s) => $s->where('name', 'like', '%' . $request->search . '%'));
            });
        }

        $shipments = $query->latest()->paginate(25);

        $statusCounts = Shipment::selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');

        return view('shipments.index', compact('shipments', 'statusCounts'));
    }

    public function create(Request $request)
    {
        $contracts = Contract::with('supplier')->whereIn('status', ['confirmed', 'partially_shipped'])->get();
        $suppliers = Supplier::orderBy('name')->get();
        $products = Product::orderBy('name')->get();
        $selectedContract = $request->filled('contract_id')
            ? Contract::with('supplier')->find($request->contract_id)
            : null;

        return view('shipments.create', compact('contracts', 'suppliers', 'products', 'selectedContract'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'contract_id'         => 'required|exists:contracts,id',
            'supplier_id'         => 'required|exists:suppliers,id',
            'product_id'          => 'nullable|exists:products,id',
            'container_number'    => 'nullable|string|max:50',
            'seal_number'         => 'nullable|string|max:50',
            'quantity_shipped'    => 'required|numeric|min:0',
            'vessel_name'         => 'nullable|string|max:100',
            'voyage_number'       => 'nullable|string|max:50',
            'carrier'             => 'nullable|string|max:100',
            'forwarder'           => 'nullable|string|max:100',
            'bl_number'           => 'nullable|string|max:100',
            'port_of_loading'     => 'nullable|string|max:100',
            'port_of_discharge'   => 'nullable|string|max:100',
            'etd'                 => 'nullable|date',
            'eta'                 => 'nullable|date',
            'actual_arrival_date' => 'nullable|date',
            'warehouse_arrival_date' => 'nullable|date',
            'status'              => 'required|in:in_production,ready_to_ship,at_port,in_transit,arrived_pod,customs_clearance,delivered_warehouse,closed',
            'notes'               => 'nullable|string',
        ]);

        // Auto-generate shipment code
        $year = now()->year;
        $count = Shipment::whereYear('created_at', $year)->count() + 1;
        $validated['shipment_code'] = 'SHP-' . $year . '-' . str_pad($count, 3, '0', STR_PAD_LEFT);
        $validated['created_by'] = auth()->id();

        $shipment = Shipment::create($validated);

        // Update contract status if needed
        $contract = Contract::find($validated['contract_id']);
        if ($contract && $contract->status === 'confirmed') {
            $contract->update(['status' => 'partially_shipped']);
        }

        return redirect()->route('shipments.show', $shipment)
            ->with('success', 'Spedizione ' . $shipment->shipment_code . ' creata con successo.');
    }

    public function show(Shipment $shipment)
    {
        $shipment->load([
            'contract',
            'supplier',
            'product',
            'creator',
            'documents.uploader',
            'payments.contract',
            'claims',
            'communicationTasks.assignedUser',
        ]);

        $recentActivity = \App\Models\ActivityLog::where('subject_type', Shipment::class)
            ->where('subject_id', $shipment->id)
            ->latest()
            ->take(20)
            ->get();

        return view('shipments.show', compact('shipment', 'recentActivity'));
    }

    public function edit(Shipment $shipment)
    {
        $contracts = Contract::with('supplier')->get();
        $suppliers = Supplier::orderBy('name')->get();
        $products = Product::orderBy('name')->get();
        return view('shipments.edit', compact('shipment', 'contracts', 'suppliers', 'products'));
    }

    public function update(Request $request, Shipment $shipment)
    {
        $validated = $request->validate([
            'contract_id'         => 'required|exists:contracts,id',
            'supplier_id'         => 'required|exists:suppliers,id',
            'product_id'          => 'nullable|exists:products,id',
            'container_number'    => 'nullable|string|max:50',
            'seal_number'         => 'nullable|string|max:50',
            'quantity_shipped'    => 'required|numeric|min:0',
            'vessel_name'         => 'nullable|string|max:100',
            'voyage_number'       => 'nullable|string|max:50',
            'carrier'             => 'nullable|string|max:100',
            'forwarder'           => 'nullable|string|max:100',
            'bl_number'           => 'nullable|string|max:100',
            'port_of_loading'     => 'nullable|string|max:100',
            'port_of_discharge'   => 'nullable|string|max:100',
            'etd'                 => 'nullable|date',
            'eta'                 => 'nullable|date',
            'actual_arrival_date' => 'nullable|date',
            'warehouse_arrival_date' => 'nullable|date',
            'status'              => 'required|in:in_production,ready_to_ship,at_port,in_transit,arrived_pod,customs_clearance,delivered_warehouse,closed',
            'notes'               => 'nullable|string',
        ]);

        $shipment->update($validated);

        return redirect()->route('shipments.show', $shipment)
            ->with('success', 'Spedizione aggiornata con successo.');
    }

    public function destroy(Shipment $shipment)
    {
        $shipment->delete();
        return redirect()->route('shipments.index')->with('success', 'Spedizione eliminata.');
    }

    public function updateStatus(Request $request, Shipment $shipment)
    {
        $validated = $request->validate([
            'status' => 'required|in:in_production,ready_to_ship,at_port,in_transit,arrived_pod,customs_clearance,delivered_warehouse,closed',
        ]);

        $shipment->update($validated);

        return back()->with('success', 'Stato aggiornato: ' . Shipment::STATUS_LABELS[$validated['status']]);
    }
}
