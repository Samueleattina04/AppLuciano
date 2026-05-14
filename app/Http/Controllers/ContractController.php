<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use App\Models\Supplier;
use Illuminate\Http\Request;

class ContractController extends Controller
{
    public function index(Request $request)
    {
        $query = Contract::with('supplier')->withCount('containers');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn($q) => $q->where('contract_number', 'like', "%$s%")
                ->orWhereHas('supplier', fn($sq) => $sq->where('name', 'like', "%$s%")));
        }

        $contracts = $query->orderByDesc('contract_date')->paginate(20)->withQueryString();
        return view('contracts.index', compact('contracts'));
    }

    public function create()
    {
        $suppliers = Supplier::orderBy('name')->get();
        return view('contracts.create', compact('suppliers'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'contract_number' => 'required|string|max:100|unique:contracts',
            'supplier_id'     => 'required|exists:suppliers,id',
            'contract_date'   => 'required|date',
            'incoterms'       => 'required|string|max:10',
            'currency'        => 'required|string|max:10',
            'total_value'     => 'required|numeric|min:0',
            'notes'           => 'nullable|string',
        ]);

        $contract = Contract::create($data);
        return redirect()->route('contracts.show', $contract)->with('success', 'Contratto creato con successo.');
    }

    public function show(Contract $contract)
    {
        $contract->load(['supplier', 'containers.documents', 'payments', 'documents']);
        return view('contracts.show', compact('contract'));
    }

    public function edit(Contract $contract)
    {
        $suppliers = Supplier::orderBy('name')->get();
        return view('contracts.edit', compact('contract', 'suppliers'));
    }

    public function update(Request $request, Contract $contract)
    {
        $data = $request->validate([
            'contract_number' => 'required|string|max:100|unique:contracts,contract_number,' . $contract->id,
            'supplier_id'     => 'required|exists:suppliers,id',
            'contract_date'   => 'required|date',
            'incoterms'       => 'required|string|max:10',
            'currency'        => 'required|string|max:10',
            'total_value'     => 'required|numeric|min:0',
            'notes'           => 'nullable|string',
        ]);

        $contract->update($data);
        return redirect()->route('contracts.show', $contract)->with('success', 'Contratto aggiornato.');
    }

    public function destroy(Contract $contract)
    {
        $contract->delete();
        return redirect()->route('contracts.index')->with('success', 'Contratto eliminato.');
    }
}
