<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PaymentTerm extends Model
{
    use HasFactory;

    protected $fillable = [
        'contract_id',
        'description',
        'percentage',
        'amount',
        'currency',
        'due_trigger',
        'due_days_offset',
        'custom_due_date',
        'order',
    ];

    protected $casts = [
        'custom_due_date' => 'date',
        'percentage'      => 'decimal:2',
        'amount'          => 'decimal:2',
    ];

    public function contract()
    {
        return $this->belongsTo(Contract::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}
