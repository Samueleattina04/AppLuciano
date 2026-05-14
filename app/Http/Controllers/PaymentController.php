<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use App\Models\Payment;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $query = Payment::with('contract.supplier');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $payments = $query->orderBy('due_date')->paginate(25)->withQueryString();
        return view('payments.index', compact('payments'));
    }

    public function create()
    {
        $contracts = Contract::with('supplier')->orderByDesc('contract_date')->get();
        return view('payments.create', compact('contracts'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'contract_id'           => 'required|exists:contracts,id',
            'description'           => 'required|string|max:255',
            'amount'                => 'required|numeric|min:0',
            'percentage'            => 'nullable|numeric|min:0|max:100',
            'due_date'              => 'required|date',
            'paid_date'             => 'nullable|date',
            'transaction_reference' => 'nullable|string|max:100',
            'status'                => 'required|in:pending,paid,overdue',
            'notes'                 => 'nullable|string',
        ]);

        Payment::create($data);
        return redirect()->route('payments.index')->with('success', 'Pagamento registrato.');
    }

    public function show(Payment $payment)
    {
        $payment->load('contract.supplier');
        return view('payments.show', compact('payment'));
    }

    public function edit(Payment $payment)
    {
        $contracts = Contract::with('supplier')->orderByDesc('contract_date')->get();
        return view('payments.edit', compact('payment', 'contracts'));
    }

    public function update(Request $request, Payment $payment)
    {
        $data = $request->validate([
            'contract_id'           => 'required|exists:contracts,id',
            'description'           => 'required|string|max:255',
            'amount'                => 'required|numeric|min:0',
            'percentage'            => 'nullable|numeric|min:0|max:100',
            'due_date'              => 'required|date',
            'paid_date'             => 'nullable|date',
            'transaction_reference' => 'nullable|string|max:100',
            'status'                => 'required|in:pending,paid,overdue',
            'notes'                 => 'nullable|string',
        ]);

        $payment->update($data);
        return redirect()->route('payments.index')->with('success', 'Pagamento aggiornato.');
    }

    public function destroy(Payment $payment)
    {
        $payment->delete();
        return redirect()->route('payments.index')->with('success', 'Pagamento eliminato.');
    }

    public function markPaid(Request $request, Payment $payment)
    {
        $request->validate([
            'paid_date'             => 'required|date',
            'transaction_reference' => 'nullable|string|max:100',
        ]);

        $payment->update([
            'status'                => 'paid',
            'paid_date'             => $request->paid_date,
            'transaction_reference' => $request->transaction_reference,
        ]);

        return back()->with('success', 'Pagamento segnato come pagato.');
    }
}
