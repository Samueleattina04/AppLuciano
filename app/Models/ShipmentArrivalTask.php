<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShipmentArrivalTask extends Model
{
    protected $fillable = [
        'shipment_id',
        'title',
        'description',
        'days_before_eta',
        'due_date',
        'completed',
        'completed_at',
        'completed_by',
        'sort_order',
    ];

    protected $casts = [
        'due_date'     => 'date',
        'completed'    => 'boolean',
        'completed_at' => 'datetime',
    ];

    public function shipment()
    {
        return $this->belongsTo(Shipment::class);
    }

    public function completedBy()
    {
        return $this->belongsTo(User::class, 'completed_by');
    }

    public function computeDueDate(): void
    {
        if ($this->days_before_eta && $this->shipment?->eta) {
            $this->due_date = $this->shipment->eta->subDays($this->days_before_eta);
        }
    }

    public function getDaysRemainingAttribute(): ?int
    {
        if (!$this->due_date) return null;
        return now()->startOfDay()->diffInDays($this->due_date->startOfDay(), false);
    }

    public static function defaultTasks(): array
    {
        return [
            ['title' => 'Richiedere polizza di carico al fornitore', 'days_before_eta' => 21, 'sort_order' => 1],
            ['title' => 'Inviare documenti allo spedizioniere doganale', 'days_before_eta' => 14, 'sort_order' => 2],
            ['title' => 'Confermare arrival notice con il vettore', 'days_before_eta' => 10, 'sort_order' => 3],
            ['title' => 'Richiedere delivery order (D/O)', 'days_before_eta' => 7, 'sort_order' => 4],
            ['title' => 'Notificare il magazzino dell\'arrivo', 'days_before_eta' => 7, 'sort_order' => 5],
            ['title' => 'Verificare disponibilità slot magazzino', 'days_before_eta' => 5, 'sort_order' => 6],
            ['title' => 'Avviare pratiche di sdoganamento', 'days_before_eta' => 5, 'sort_order' => 7],
            ['title' => 'Pianificare trasporto interno (camion)', 'days_before_eta' => 3, 'sort_order' => 8],
            ['title' => 'Pesatura e ispezione all\'arrivo', 'days_before_eta' => 0, 'sort_order' => 9],
        ];
    }
}
