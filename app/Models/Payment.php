<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'contract_id', 'description', 'amount', 'percentage',
        'due_date', 'paid_date', 'transaction_reference', 'status', 'notes',
    ];

    protected $casts = [
        'due_date'   => 'date',
        'paid_date'  => 'date',
        'amount'     => 'decimal:2',
        'percentage' => 'decimal:2',
    ];

    public function contract()
    {
        return $this->belongsTo(Contract::class);
    }

    public function isDueSoon(): bool
    {
        return $this->status === 'pending'
            && $this->due_date
            && $this->due_date->diffInDays(now(), false) >= -7
            && $this->due_date->isFuture();
    }

    public function scopeDueSoon($query)
    {
        return $query->where('status', 'pending')
            ->whereBetween('due_date', [now()->toDateString(), now()->addDays(7)->toDateString()]);
    }

    public function scopeOverdue($query)
    {
        return $query->where('status', 'pending')->where('due_date', '<', now()->toDateString());
    }
}
