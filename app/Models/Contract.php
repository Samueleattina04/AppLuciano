<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contract extends Model
{
    protected $fillable = [
        'contract_number', 'supplier_id', 'contract_date',
        'incoterms', 'currency', 'total_value', 'notes',
    ];

    protected $casts = [
        'contract_date' => 'date',
        'total_value' => 'decimal:2',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function containers()
    {
        return $this->hasMany(Container::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function documents()
    {
        return $this->morphMany(Document::class, 'documentable');
    }

    public function getPaidAmountAttribute(): float
    {
        return (float) $this->payments()->where('status', 'paid')->sum('amount');
    }

    public function getRemainingBalanceAttribute(): float
    {
        return (float) $this->total_value - $this->paid_amount;
    }
}
