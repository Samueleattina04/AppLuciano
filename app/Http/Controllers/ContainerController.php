<?php

namespace App\Http\Controllers;

use App\Models\Container;
use App\Models\Contract;
use Illuminate\Http\Request;

class ContainerController extends Controller
{
    public function index(Request $request)
    {
        $query = Container::with('contract.supplier');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn($q) => $q->where('container_number', 'like', "%$s%")
                ->orWhere('vessel_name', 'like', "%$s%")
                ->orWhereHas('contract', fn($cq) => $cq->where('contract_number', 'like', "%$s%")));
        }

        $containers = $query->orderByDesc('updated_at')->paginate(25)->withQueryString();
        $statusCounts = Container::selectRaw('status, count(*) as total')->groupBy('status')->pluck('total', 'status');

        return view('containers.index', compact('containers', 'statusCounts'));
    }

    public function create()
    {
        $contracts = Contract::with('supplier')->orderByDesc('contract_date')->get();
        return view('containers.create', compact('contracts'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'contract_id'       => 'required|exists:contracts,id',
            'container_number'  => 'required|string|max:50|unique:containers',
            'status'            => 'required|in:in_production,at_port,in_transit,customs_cleared,at_warehouse',
            'vessel_name'       => 'nullable|string|max:100',
            'voyage_number'     => 'nullable|string|max:50',
            'etd'               => 'nullable|date',
            'eta'               => 'nullable|date|after_or_equal:etd',
            'port_of_loading'   => 'nullable|string|max:100',
            'port_of_discharge' => 'nullable|string|max:100',
            'notes'             => 'nullable|string',
        ]);

        $container = Container::create($data);
        return redirect()->route('containers.show', $container)->with('success', 'Container creato.');
    }

    public function show(Container $container)
    {
        $container->load(['contract.supplier', 'contract.payments', 'documents']);
        return view('containers.show', compact('container'));
    }

    public function edit(Container $container)
    {
        $contracts = Contract::with('supplier')->orderByDesc('contract_date')->get();
        return view('containers.edit', compact('container', 'contracts'));
    }

    public function update(Request $request, Container $container)
    {
        $data = $request->validate([
            'contract_id'       => 'required|exists:contracts,id',
            'container_number'  => 'required|string|max:50|unique:containers,container_number,' . $container->id,
            'status'            => 'required|in:in_production,at_port,in_transit,customs_cleared,at_warehouse',
            'vessel_name'       => 'nullable|string|max:100',
            'voyage_number'     => 'nullable|string|max:50',
            'etd'               => 'nullable|date',
            'eta'               => 'nullable|date|after_or_equal:etd',
            'port_of_loading'   => 'nullable|string|max:100',
            'port_of_discharge' => 'nullable|string|max:100',
            'notes'             => 'nullable|string',
        ]);

        $container->update($data);
        return redirect()->route('containers.show', $container)->with('success', 'Container aggiornato.');
    }

    public function destroy(Container $container)
    {
        $container->delete();
        return redirect()->route('containers.index')->with('success', 'Container eliminato.');
    }
}
