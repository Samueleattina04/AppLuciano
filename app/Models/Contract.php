<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\LogsActivity;

class Contract extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    const STATUS_LABELS = [
        'draft'             => 'Draft',
        'confirmed'         => 'Confirmed',
        'partially_shipped' => 'Partially Shipped',
        'completed'         => 'Completed',
        'cancelled'         => 'Cancelled',
    ];

    protected $fillable = [
        'contract_number',
        'supplier_id',
        'product_id',
        'type',
        'crop_season',
        'quantity_contracted',
        'unit_of_measure',
        'unit_price',
        'currency',
        'incoterm',
        'port_of_loading',
        'port_of_discharge',
        'contract_date',
        'shipment_window_start',
        'shipment_window_end',
        'payment_terms_description',
        'total_value',
        'notes',
        'status',
        'created_by',
    ];

    protected $casts = [
        'contract_date'         => 'date',
        'shipment_window_start' => 'date',
        'shipment_window_end'   => 'date',
        'total_value'           => 'decimal:2',
        'unit_price'            => 'decimal:4',
        'quantity_contracted'   => 'decimal:3',
    ];

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

    public function shipments()
    {
        return $this->hasMany(Shipment::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function paymentTerms()
    {
        return $this->hasMany(PaymentTerm::class)->orderBy('order');
    }

    public function claims()
    {
        return $this->hasMany(Claim::class);
    }

    public function communicationTasks()
    {
        return $this->hasMany(CommunicationTask::class);
    }

    public function documents()
    {
        return $this->morphMany(Document::class, 'documentable');
    }

    public function getPaidAmountAttribute(): float
    {
        return (float) $this->payments()->where('status', 'paid')->sum('amount_paid');
    }

    public function getRemainingBalanceAttribute(): float
    {
        return (float) $this->total_value - $this->paid_amount;
    }

    public function getShippedQuantityAttribute(): float
    {
        return (float) $this->shipments()->whereNotIn('status', ['cancelled'])->sum('quantity_shipped');
    }

    public function getRemainingQuantityAttribute(): float
    {
        return (float) $this->quantity_contracted - $this->shipped_quantity;
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUS_LABELS[$this->status] ?? $this->status;
    }
}
