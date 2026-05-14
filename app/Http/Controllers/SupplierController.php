<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function index()
    {
        $suppliers = Supplier::withCount('contracts')->orderBy('name')->paginate(20);
        return view('suppliers.index', compact('suppliers'));
    }

    public function create()
    {
        return view('suppliers.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'          => 'required|string|max:255',
            'country'       => 'nullable|string|max:100',
            'contact_email' => 'nullable|email|max:255',
            'contact_phone' => 'nullable|string|max:50',
            'notes'         => 'nullable|string',
        ]);

        Supplier::create($data);
        return redirect()->route('suppliers.index')->with('success', 'Fornitore creato con successo.');
    }

    public function show(Supplier $supplier)
    {
        $supplier->load(['contracts.containers', 'contracts.payments']);
        return view('suppliers.show', compact('supplier'));
    }

    public function edit(Supplier $supplier)
    {
        return view('suppliers.edit', compact('supplier'));
    }

    public function update(Request $request, Supplier $supplier)
    {
        $data = $request->validate([
            'name'          => 'required|string|max:255',
            'country'       => 'nullable|string|max:100',
            'contact_email' => 'nullable|email|max:255',
            'contact_phone' => 'nullable|string|max:50',
            'notes'         => 'nullable|string',
        ]);

        $supplier->update($data);
        return redirect()->route('suppliers.show', $supplier)->with('success', 'Fornitore aggiornato.');
    }

    public function destroy(Supplier $supplier)
    {
        $supplier->delete();
        return redirect()->route('suppliers.index')->with('success', 'Fornitore eliminato.');
    }
}
