<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\LogsActivity;

class Claim extends Model
{
    use HasFactory, LogsActivity;

    const TYPE_LABELS = [
        'quality'        => 'Quality',
        'weight_shortage'=> 'Weight Shortage',
        'packaging'      => 'Packaging',
        'price'          => 'Price',
        'other'          => 'Other',
    ];

    const STATUS_LABELS = [
        'open'         => 'Open',
        'under_review' => 'Under Review',
        'accepted'     => 'Accepted',
        'rejected'     => 'Rejected',
        'deducted'     => 'Deducted',
        'closed'       => 'Closed',
    ];

    protected $fillable = [
        'contract_id',
        'shipment_id',
        'supplier_id',
        'claim_type',
        'amount',
        'currency',
        'reason',
        'status',
        'resolved_date',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'resolved_date' => 'date',
        'amount'        => 'decimal:2',
    ];

    public function contract()
    {
        return $this->belongsTo(Contract::class);
    }

    public function shipment()
    {
        return $this->belongsTo(Shipment::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getTypeLabelAttribute(): string
    {
        return self::TYPE_LABELS[$this->claim_type] ?? $this->claim_type;
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUS_LABELS[$this->status] ?? $this->status;
    }
}
