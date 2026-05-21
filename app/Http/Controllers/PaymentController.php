<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Contract;
use App\Models\Shipment;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $query = Payment::with(['contract', 'supplier', 'shipment']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        } elseif ($request->tab === 'overdue') {
            $query->overdue();
        } elseif ($request->tab === 'due_soon') {
            $query->dueSoon();
        }

        if ($request->filled('payment_type')) {
            $query->where('payment_type', $request->payment_type);
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('bank_reference', 'like', '%' . $request->search . '%')
                  ->orWhereHas('contract', fn($c) => $c->where('contract_number', 'like', '%' . $request->search . '%'))
                  ->orWhereHas('supplier', fn($s) => $s->where('name', 'like', '%' . $request->search . '%'));
            });
        }

        $payments = $query->orderBy('due_date')->paginate(25);

        $statusCounts = [
            'overdue'  => Payment::overdue()->count(),
            'due_soon' => Payment::dueSoon()->count(),
            'pending'  => Payment::where('status', 'pending')->count(),
            'paid'     => Payment::where('status', 'paid')->count(),
        ];

        $advanceTotal   = Payment::where('payment_type', 'advance')->whereNotIn('status', ['paid'])->sum('amount_due');
        $advancePaid    = Payment::where('payment_type', 'advance')->where('status', 'paid')->sum('amount_paid');

        return view('payments.index', compact('payments', 'statusCounts', 'advanceTotal', 'advancePaid'));
    }

    public function create(Request $request)
    {
        $contracts = Contract::with(['supplier', 'shipments' => fn($q) => $q->whereNotIn('status', ['closed'])])
            ->orderBy('contract_number')->get();

        $contractsJson = $contracts->map(fn($c) => [
            'id'           => $c->id,
            'supplier_id'  => $c->supplier_id,
            'supplier_name'=> $c->supplier->name ?? '',
            'currency'     => $c->currency,
            'shipments'    => $c->shipments->map(fn($s) => [
                'id'   => $s->id,
                'code' => $s->shipment_code . ($s->container_number ? ' — ' . $s->container_number : ''),
            ])->values(),
        ])->keyBy('id');

        $selectedContract = $request->filled('contract_id')
            ? $contracts->firstWhere('id', $request->contract_id)
            : null;

        return view('payments.create', compact('contracts', 'contractsJson', 'selectedContract'));
    }

    public function store(Request $request)
    {
        $isAdvance = $request->payment_type === 'advance';

        $validated = $request->validate([
            'contract_id'    => 'required|exists:contracts,id',
            'payment_type'   => 'required|in:advance,shipment_payment',
            'shipment_id'    => $isAdvance ? 'nullable' : 'nullable|exists:shipments,id',
            'amount_due'     => 'required|numeric|min:0',
            'amount_paid'    => 'nullable|numeric|min:0',
            'currency'       => 'required|string|max:10',
            'due_date'       => 'required|date',
            'payment_date'   => 'nullable|date',
            'bank_reference' => 'nullable|string|max:255',
            'status'         => 'required|in:pending,due_soon,overdue,paid,partially_paid',
            'notes'          => 'nullable|string',
        ]);

        // Fornitore sempre dal contratto, mai dal form
        $contract = Contract::find($validated['contract_id']);
        $validated['supplier_id'] = $contract->supplier_id;

        if ($isAdvance) {
            $validated['shipment_id'] = null;
        }

        $validated['created_by'] = auth()->id();
        $validated['amount_paid'] = $validated['amount_paid'] ?? 0;

        Payment::create($validated);

        return redirect()->route('payments.index')->with('success', 'Pagamento creato con successo.');
    }

    public function edit(Payment $payment)
    {
        $contracts = Contract::with(['supplier', 'shipments' => fn($q) => $q->whereNotIn('status', ['closed'])])
            ->orderBy('contract_number')->get();

        $contractsJson = $contracts->map(fn($c) => [
            'id'           => $c->id,
            'supplier_id'  => $c->supplier_id,
            'supplier_name'=> $c->supplier->name ?? '',
            'currency'     => $c->currency,
            'shipments'    => $c->shipments->map(fn($s) => [
                'id'   => $s->id,
                'code' => $s->shipment_code . ($s->container_number ? ' — ' . $s->container_number : ''),
            ])->values(),
        ])->keyBy('id');

        return view('payments.edit', compact('payment', 'contracts', 'contractsJson'));
    }

    public function update(Request $request, Payment $payment)
    {
        $isAdvance = $request->payment_type === 'advance';

        $validated = $request->validate([
            'contract_id'    => 'required|exists:contracts,id',
            'payment_type'   => 'required|in:advance,shipment_payment',
            'shipment_id'    => $isAdvance ? 'nullable' : 'nullable|exists:shipments,id',
            'amount_due'     => 'required|numeric|min:0',
            'amount_paid'    => 'nullable|numeric|min:0',
            'currency'       => 'required|string|max:10',
            'due_date'       => 'required|date',
            'payment_date'   => 'nullable|date',
            'bank_reference' => 'nullable|string|max:255',
            'status'         => 'required|in:pending,due_soon,overdue,paid,partially_paid',
            'notes'          => 'nullable|string',
        ]);

        $contract = Contract::find($validated['contract_id']);
        $validated['supplier_id'] = $contract->supplier_id;

        if ($isAdvance) {
            $validated['shipment_id'] = null;
        }

        $payment->update($validated);

        return redirect()->route('payments.index')->with('success', 'Pagamento aggiornato con successo.');
    }

    public function show(Payment $payment)
    {
        return redirect()->route('payments.index');
    }

    public function destroy(Payment $payment)
    {
        $payment->delete();
        return redirect()->route('payments.index')->with('success', 'Pagamento eliminato.');
    }

    public function markPaid(Request $request, Payment $payment)
    {
        $payment->update([
            'status'         => 'paid',
            'amount_paid'    => $payment->amount_due,
            'payment_date'   => $request->payment_date ?? now()->toDateString(),
            'bank_reference' => $request->bank_reference ?? $payment->bank_reference,
        ]);

        return back()->with('success', 'Pagamento segnato come pagato.');
    }
}
