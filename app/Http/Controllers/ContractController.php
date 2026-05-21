<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use App\Models\Supplier;
use App\Models\Product;
use Illuminate\Http\Request;

class ContractController extends Controller
{
    public function index(Request $request)
    {
        $query = Contract::with(['supplier', 'product'])
            ->withCount(['shipments', 'claims']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('supplier_id')) {
            $query->where('supplier_id', $request->supplier_id);
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('contract_number', 'like', '%' . $request->search . '%')
                  ->orWhereHas('supplier', fn($s) => $s->where('name', 'like', '%' . $request->search . '%'));
            });
        }

        $contracts = $query->latest()->paginate(20);
        $suppliers = Supplier::orderBy('name')->pluck('name', 'id');

        $statusCounts = Contract::selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');

        return view('contracts.index', compact('contracts', 'suppliers', 'statusCounts'));
    }

    public function create()
    {
        $suppliers = Supplier::orderBy('name')->get();
        $products = Product::orderBy('name')->get();
        return view('contracts.create', compact('suppliers', 'products'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'contract_number'          => 'required|string|unique:contracts,contract_number',
            'supplier_id'              => 'required|exists:suppliers,id',
            'product_id'               => 'nullable|exists:products,id',
            'type'                     => 'required|in:framework,single',
            'crop_season'              => 'nullable|string|max:50',
            'quantity_contracted'      => 'required|numeric|min:0',
            'unit_of_measure'          => 'required|string|max:20',
            'unit_price'               => 'required|numeric|min:0',
            'currency'                 => 'required|string|max:10',
            'incoterm'                 => 'nullable|string|max:20',
            'port_of_loading'          => 'nullable|string|max:100',
            'port_of_discharge'        => 'nullable|string|max:100',
            'contract_date'            => 'nullable|date',
            'shipment_window_start'    => 'nullable|date',
            'shipment_window_end'      => 'nullable|date',
            'payment_terms_description'=> 'nullable|string',
            'total_value'              => 'required|numeric|min:0',
            'notes'                    => 'nullable|string',
            'status'                   => 'required|in:draft,confirmed,partially_shipped,completed,cancelled',
        ]);

        $validated['created_by'] = auth()->id();

        $contract = Contract::create($validated);

        return redirect()->route('contracts.show', $contract)
            ->with('success', 'Contract ' . $contract->contract_number . ' created successfully.');
    }

    public function show(Contract $contract)
    {
        $contract->load([
            'supplier',
            'product',
            'creator',
            'shipments.supplier',
            'payments.supplier',
            'paymentTerms',
            'claims.supplier',
            'communicationTasks.assignedUser',
            'documents.uploader',
        ]);

        $recentActivity = \App\Models\ActivityLog::where(function ($q) use ($contract) {
            $q->where('subject_type', Contract::class)->where('subject_id', $contract->id);
        })->orWhere(function ($q) use ($contract) {
            $q->where('subject_type', \App\Models\Shipment::class)
              ->whereIn('subject_id', $contract->shipments->pluck('id'));
        })->latest()->take(20)->get();

        return view('contracts.show', compact('contract', 'recentActivity'));
    }

    public function edit(Contract $contract)
    {
        $suppliers = Supplier::orderBy('name')->get();
        $products = Product::orderBy('name')->get();
        return view('contracts.edit', compact('contract', 'suppliers', 'products'));
    }

    public function update(Request $request, Contract $contract)
    {
        $validated = $request->validate([
            'contract_number'          => 'required|string|unique:contracts,contract_number,' . $contract->id,
            'supplier_id'              => 'required|exists:suppliers,id',
            'product_id'               => 'nullable|exists:products,id',
            'type'                     => 'required|in:framework,single',
            'crop_season'              => 'nullable|string|max:50',
            'quantity_contracted'      => 'required|numeric|min:0',
            'unit_of_measure'          => 'required|string|max:20',
            'unit_price'               => 'required|numeric|min:0',
            'currency'                 => 'required|string|max:10',
            'incoterm'                 => 'nullable|string|max:20',
            'port_of_loading'          => 'nullable|string|max:100',
            'port_of_discharge'        => 'nullable|string|max:100',
            'contract_date'            => 'nullable|date',
            'shipment_window_start'    => 'nullable|date',
            'shipment_window_end'      => 'nullable|date',
            'payment_terms_description'=> 'nullable|string',
            'total_value'              => 'required|numeric|min:0',
            'notes'                    => 'nullable|string',
            'status'                   => 'required|in:draft,confirmed,partially_shipped,completed,cancelled',
        ]);

        $contract->update($validated);

        return redirect()->route('contracts.show', $contract)
            ->with('success', 'Contract updated successfully.');
    }

    public function destroy(Contract $contract)
    {
        $contract->delete();

        return redirect()->route('contracts.index')
            ->with('success', 'Contract deleted.');
    }
}
