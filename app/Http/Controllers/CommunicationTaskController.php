<?php

namespace App\Http\Controllers;

use App\Models\CommunicationTask;
use App\Models\Contract;
use App\Models\Shipment;
use App\Models\User;
use Illuminate\Http\Request;

class CommunicationTaskController extends Controller
{
    public function index(Request $request)
    {
        $query = CommunicationTask::with(['contract', 'shipment', 'assignedUser', 'creator']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->tab === 'urgent') {
            $query->urgent();
        } elseif ($request->tab === 'open') {
            $query->open();
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('assigned_to')) {
            $query->where('assigned_to', $request->assigned_to);
        }

        if ($request->filled('search')) {
            $query->where('subject', 'like', '%' . $request->search . '%');
        }

        $tasks = $query->orderByRaw("CASE priority WHEN 'urgent' THEN 1 WHEN 'high' THEN 2 WHEN 'normal' THEN 3 ELSE 4 END")
            ->orderBy('due_date')
            ->paginate(20);

        $users = User::orderBy('name')->get();

        $statusCounts = [
            'open'    => CommunicationTask::open()->count(),
            'urgent'  => CommunicationTask::urgent()->count(),
            'replied' => CommunicationTask::where('status', 'replied')->count(),
            'closed'  => CommunicationTask::where('status', 'closed')->count(),
        ];

        return view('communications.index', compact('tasks', 'users', 'statusCounts'));
    }

    public function create(Request $request)
    {
        $contracts = Contract::with('supplier')->orderBy('contract_number')->get();
        $shipments = Shipment::with(['contract.supplier'])->orderBy('shipment_code')->get();
        $users     = User::orderBy('name')->get();

        // Risolve contesto da query string
        $ctx = $this->resolveContext($request, $contracts, $shipments);

        return view('communications.create', array_merge(
            compact('contracts', 'shipments', 'users'),
            $ctx
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'subject'                   => 'required|string|max:255',
            'sender'                    => 'nullable|string|max:255',
            'recipient'                 => 'nullable|string|max:255',
            'outlook_thread_link'       => 'nullable|url|max:1000',
            'outlook_message_id'        => 'nullable|string|max:255',
            'outlook_conversation_id'   => 'nullable|string|max:255',
            'category'                  => 'required|in:document_approval,cad_bank_request,payment_followup,supplier_request,forwarder_request,internal_note',
            'priority'                  => 'required|in:low,normal,high,urgent',
            'due_date'                  => 'nullable|date',
            'reminder_date'             => 'nullable|date',
            'status'                    => 'required|in:to_review,waiting_internal,waiting_supplier,ready_to_reply,replied,closed',
            'notes'                     => 'nullable|string',
            'contract_id'               => 'nullable|exists:contracts,id',
            'shipment_id'               => 'nullable|exists:shipments,id',
            'assigned_to'               => 'nullable|exists:users,id',
        ]);

        $validated['created_by'] = auth()->id();

        $task = CommunicationTask::create($validated);

        return redirect()->route('communications.show', $task)
            ->with('success', 'Task di comunicazione creato con successo.');
    }

    public function show(CommunicationTask $communication)
    {
        $communication->load(['contract', 'shipment', 'assignedUser', 'creator']);
        return view('communications.show', compact('communication'));
    }

    public function edit(CommunicationTask $communication)
    {
        $contracts = Contract::with('supplier')->orderBy('contract_number')->get();
        $shipments = Shipment::with('contract')->orderBy('shipment_code')->get();
        $users = User::orderBy('name')->get();
        return view('communications.edit', compact('communication', 'contracts', 'shipments', 'users'));
    }

    public function update(Request $request, CommunicationTask $communication)
    {
        $validated = $request->validate([
            'subject'                   => 'required|string|max:255',
            'sender'                    => 'nullable|string|max:255',
            'recipient'                 => 'nullable|string|max:255',
            'outlook_thread_link'       => 'nullable|max:1000',
            'category'                  => 'required|in:document_approval,cad_bank_request,payment_followup,supplier_request,forwarder_request,internal_note',
            'priority'                  => 'required|in:low,normal,high,urgent',
            'due_date'                  => 'nullable|date',
            'reminder_date'             => 'nullable|date',
            'status'                    => 'required|in:to_review,waiting_internal,waiting_supplier,ready_to_reply,replied,closed',
            'notes'                     => 'nullable|string',
            'contract_id'               => 'nullable|exists:contracts,id',
            'shipment_id'               => 'nullable|exists:shipments,id',
            'assigned_to'               => 'nullable|exists:users,id',
        ]);

        $communication->update($validated);

        return redirect()->route('communications.show', $communication)
            ->with('success', 'Task aggiornato con successo.');
    }

    public function destroy(CommunicationTask $communication)
    {
        $communication->delete();
        return redirect()->route('communications.index')->with('success', 'Task eliminato.');
    }

    public function markReplied(CommunicationTask $communication)
    {
        $communication->update(['status' => 'replied']);
        return back()->with('success', 'Task segnato come risposto.');
    }

    // ─── Risolve i valori di precompilazione dal contesto (URL params) ────────
    private function resolveContext(Request $request, $contracts, $shipments): array
    {
        $selectedContract = null;
        $selectedShipment = null;
        $defaultSubject   = old('subject', '');
        $defaultSender    = old('sender', '');
        $defaultRecipient = old('recipient', '');
        $defaultCategory  = old('category', 'supplier_request');

        if ($request->filled('shipment_id')) {
            $selectedShipment = $shipments->firstWhere('id', $request->shipment_id);
            if ($selectedShipment) {
                if (!$selectedContract && $selectedShipment->contract) {
                    $selectedContract = $selectedShipment->contract;
                }
                if (!$defaultSubject) {
                    $label = $selectedShipment->shipment_code;
                    if ($selectedShipment->container_number) $label .= ' ' . $selectedShipment->container_number;
                    $defaultSubject = 'Follow-up spedizione ' . $label;
                }
            }
        }

        if (!$selectedContract && $request->filled('contract_id')) {
            $selectedContract = $contracts->firstWhere('id', $request->contract_id);
            if ($selectedContract && !$defaultSubject) {
                $defaultSubject = 'Follow-up contratto ' . $selectedContract->contract_number;
            }
        }

        // Auto-fill destinatario dai contatti del fornitore
        $supplier = $selectedShipment?->contract?->supplier
            ?? $selectedContract?->supplier
            ?? null;

        if ($supplier && !$defaultRecipient) {
            if ($supplier->contact_email) {
                $defaultRecipient = $supplier->contact_name
                    ? $supplier->contact_name . ' <' . $supplier->contact_email . '>'
                    : $supplier->contact_email;
            }
        }

        // Categoria di default da contesto
        if ($request->filled('category')) {
            $defaultCategory = $request->category;
        }

        return compact(
            'selectedContract', 'selectedShipment',
            'defaultSubject', 'defaultSender', 'defaultRecipient', 'defaultCategory'
        );
    }
}
