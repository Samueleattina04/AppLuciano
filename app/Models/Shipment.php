<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\LogsActivity;

class Shipment extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    const STATUS_LABELS = [
        'in_production'       => 'In Production',
        'ready_to_ship'       => 'Ready to Ship',
        'at_port'             => 'At Port',
        'in_transit'          => 'In Transit',
        'arrived_pod'         => 'Arrived POD',
        'customs_clearance'   => 'Customs Clearance',
        'delivered_warehouse' => 'Delivered to Warehouse',
        'closed'              => 'Closed',
    ];

    const STATUS_COLORS = [
        'in_production'       => 'secondary',
        'ready_to_ship'       => 'primary',
        'at_port'             => 'warning',
        'in_transit'          => 'info',
        'arrived_pod'         => 'success',
        'customs_clearance'   => 'purple',
        'delivered_warehouse' => 'success',
        'closed'              => 'dark',
    ];

    const STATUS_ORDER = [
        'in_production'       => 1,
        'ready_to_ship'       => 2,
        'at_port'             => 3,
        'in_transit'          => 4,
        'arrived_pod'         => 5,
        'customs_clearance'   => 6,
        'delivered_warehouse' => 7,
        'closed'              => 8,
    ];

    const CRITICAL_DOCS = [
        'bill_of_lading',
        'commercial_invoice',
        'packing_list',
        'certificate_of_origin',
    ];

    protected $fillable = [
        'shipment_code',
        'contract_id',
        'supplier_id',
        'product_id',
        'container_number',
        'seal_number',
        'quantity_shipped',
        'vessel_name',
        'voyage_number',
        'carrier',
        'forwarder',
        'bl_number',
        'port_of_loading',
        'port_of_discharge',
        'etd',
        'eta',
        'actual_arrival_date',
        'warehouse_arrival_date',
        'status',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'etd'                   => 'date',
        'eta'                   => 'date',
        'actual_arrival_date'   => 'date',
        'warehouse_arrival_date'=> 'date',
        'quantity_shipped'      => 'decimal:3',
    ];

    public function contract()
    {
        return $this->belongsTo(Contract::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function documents()
    {
        return $this->morphMany(Document::class, 'documentable');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function claims()
    {
        return $this->hasMany(Claim::class);
    }

    public function communicationTasks()
    {
        return $this->hasMany(CommunicationTask::class);
    }

    // Scopes
    public function scopeAtSea($query)
    {
        return $query->whereIn('status', ['at_port', 'in_transit']);
    }

    public function scopeUpcoming($query, $days = 7)
    {
        return $query->whereBetween('eta', [now()->toDateString(), now()->addDays($days)->toDateString()]);
    }

    public function scopeOverdue($query)
    {
        return $query->where('eta', '<', now()->toDateString())
            ->whereNotIn('status', ['arrived_pod', 'customs_clearance', 'delivered_warehouse', 'closed']);
    }

    // Accessors
    public function getMissingCriticalDocumentsAttribute(): array
    {
        $uploaded = $this->documents->pluck('document_type')->toArray();
        return array_values(array_diff(self::CRITICAL_DOCS, $uploaded));
    }

    public function getDocumentStatusAttribute(): string
    {
        return count($this->missing_critical_documents) === 0 ? 'complete' : 'incomplete';
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUS_LABELS[$this->status] ?? $this->status;
    }

    public function getStatusOrderAttribute(): int
    {
        return self::STATUS_ORDER[$this->status] ?? 0;
    }
}
