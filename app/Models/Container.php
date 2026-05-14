<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Container extends Model
{
    protected $fillable = [
        'contract_id', 'container_number', 'status',
        'vessel_name', 'voyage_number', 'etd', 'eta',
        'port_of_loading', 'port_of_discharge', 'notes',
    ];

    protected $casts = [
        'etd' => 'date',
        'eta' => 'date',
    ];

    const STATUS_LABELS = [
        'in_production'   => 'In Produzione',
        'at_port'         => 'Al Porto',
        'in_transit'      => 'In Transito',
        'customs_cleared' => 'Sdoganato',
        'at_warehouse'    => 'Arrivato a Magazzino',
    ];

    const STATUS_COLORS = [
        'in_production'   => 'secondary',
        'at_port'         => 'warning',
        'in_transit'      => 'info',
        'customs_cleared' => 'primary',
        'at_warehouse'    => 'success',
    ];

    public function contract()
    {
        return $this->belongsTo(Contract::class);
    }

    public function documents()
    {
        return $this->morphMany(Document::class, 'documentable');
    }

    public function getMissingCriticalDocumentsAttribute(): array
    {
        $required = ['bill_of_lading', 'commercial_invoice', 'packing_list', 'certificate_of_origin'];
        $uploaded = $this->documents->pluck('type')->toArray();
        return array_diff($required, $uploaded);
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUS_LABELS[$this->status] ?? $this->status;
    }

    public function getStatusColorAttribute(): string
    {
        return self::STATUS_COLORS[$this->status] ?? 'secondary';
    }

    public function isAtSea(): bool
    {
        return in_array($this->status, ['at_port', 'in_transit']);
    }
}
