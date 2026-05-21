<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\LogsActivity;

class Claim extends Model
{
    use HasFactory, LogsActivity;

    const TYPE_LABELS = [
        'quality'        => 'Qualità',
        'weight_shortage'=> 'Calo Peso',
        'packaging'      => 'Imballaggio',
        'price'          => 'Prezzo',
        'other'          => 'Altro',
    ];

    const STATUS_LABELS = [
        'open'         => 'Aperto',
        'under_review' => 'In Revisione',
        'accepted'     => 'Accettato',
        'rejected'     => 'Respinto',
        'deducted'     => 'Dedotto',
        'closed'       => 'Chiuso',
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
