<?php

namespace App\Http\Controllers;

use App\Models\Claim;
use App\Models\Contract;
use App\Models\Supplier;
use App\Models\Shipment;
use Illuminate\Http\Request;

class ClaimController extends Controller
{
    public function index(Request $request)
    {
        $query = Claim::with(['contract', 'supplier', 'shipment']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('claim_type')) {
            $query->where('claim_type', $request->claim_type);
        }

        $claims = $query->latest()->paginate(20);

        return view('claims.index', compact('claims'));
    }

    public function create(Request $request)
    {
        $contracts = Contract::with('supplier')->orderBy('contract_number')->get();
        $suppliers = Supplier::orderBy('name')->get();
        $shipments = Shipment::with('contract')->orderBy('shipment_code')->get();

        return view('claims.create', compact('contracts', 'suppliers', 'shipments'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'contract_id'  => 'required|exists:contracts,id',
            'shipment_id'  => 'nullable|exists:shipments,id',
            'supplier_id'  => 'required|exists:suppliers,id',
            'claim_type'   => 'required|in:quality,weight_shortage,packaging,price,other',
            'amount'       => 'required|numeric|min:0',
            'currency'     => 'required|string|max:10',
            'reason'       => 'required|string',
            'status'       => 'required|in:open,under_review,accepted,rejected,deducted,closed',
            'resolved_date'=> 'nullable|date',
            'notes'        => 'nullable|string',
        ]);

        $validated['created_by'] = auth()->id();

        Claim::create($validated);

        return redirect()->route('claims.index')->with('success', 'Reclamo creato con successo.');
    }

    public function show(Claim $claim)
    {
        $claim->load(['contract', 'supplier', 'shipment', 'creator']);
        return view('claims.show', compact('claim'));
    }

    public function edit(Claim $claim)
    {
        $contracts = Contract::with('supplier')->orderBy('contract_number')->get();
        $suppliers = Supplier::orderBy('name')->get();
        $shipments = Shipment::with('contract')->orderBy('shipment_code')->get();
        return view('claims.edit', compact('claim', 'contracts', 'suppliers', 'shipments'));
    }

    public function update(Request $request, Claim $claim)
    {
        $validated = $request->validate([
            'contract_id'  => 'required|exists:contracts,id',
            'shipment_id'  => 'nullable|exists:shipments,id',
            'supplier_id'  => 'required|exists:suppliers,id',
            'claim_type'   => 'required|in:quality,weight_shortage,packaging,price,other',
            'amount'       => 'required|numeric|min:0',
            'currency'     => 'required|string|max:10',
            'reason'       => 'required|string',
            'status'       => 'required|in:open,under_review,accepted,rejected,deducted,closed',
            'resolved_date'=> 'nullable|date',
            'notes'        => 'nullable|string',
        ]);

        $claim->update($validated);

        return redirect()->route('claims.index')->with('success', 'Reclamo aggiornato con successo.');
    }

    public function destroy(Claim $claim)
    {
        $claim->delete();
        return redirect()->route('claims.index')->with('success', 'Reclamo eliminato.');
    }
}
