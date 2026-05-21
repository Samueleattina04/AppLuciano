<?php

namespace App\Http\Controllers;

use App\Models\Shipment;
use App\Models\ShipmentArrivalTask;
use Illuminate\Http\Request;

class ShipmentArrivalTaskController extends Controller
{
    public function store(Request $request, Shipment $shipment)
    {
        $validated = $request->validate([
            'title'          => 'required|string|max:255',
            'description'    => 'nullable|string',
            'days_before_eta'=> 'nullable|integer|min:0',
            'due_date'       => 'nullable|date',
            'sort_order'     => 'nullable|integer',
        ]);

        $task = new ShipmentArrivalTask($validated);
        $task->shipment_id = $shipment->id;

        if (!isset($validated['due_date']) && isset($validated['days_before_eta']) && $shipment->eta) {
            $task->due_date = $shipment->eta->subDays($validated['days_before_eta']);
        }

        $task->sort_order = $validated['sort_order'] ?? (ShipmentArrivalTask::where('shipment_id', $shipment->id)->max('sort_order') + 1);
        $task->save();

        return back()->with('success', 'Task aggiunto.');
    }

    public function toggle(Shipment $shipment, ShipmentArrivalTask $task)
    {
        if ($task->completed) {
            $task->update([
                'completed'    => false,
                'completed_at' => null,
                'completed_by' => null,
            ]);
        } else {
            $task->update([
                'completed'    => true,
                'completed_at' => now(),
                'completed_by' => auth()->id(),
            ]);
        }

        return back()->with('success', $task->completed ? 'Task completato.' : 'Task riaperto.');
    }

    public function addDefaults(Shipment $shipment)
    {
        $existing = $shipment->arrivalTasks()->pluck('title')->toArray();
        $added = 0;

        foreach (ShipmentArrivalTask::defaultTasks() as $defaults) {
            if (in_array($defaults['title'], $existing)) continue;

            $task = new ShipmentArrivalTask($defaults);
            $task->shipment_id = $shipment->id;

            if ($shipment->eta && isset($defaults['days_before_eta'])) {
                $task->due_date = $shipment->eta->subDays($defaults['days_before_eta']);
            }

            $task->save();
            $added++;
        }

        return back()->with('success', "Aggiunti {$added} task predefiniti.");
    }

    public function destroy(Shipment $shipment, ShipmentArrivalTask $task)
    {
        $task->delete();
        return back()->with('success', 'Task eliminato.');
    }
}
