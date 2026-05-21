<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\LogsActivity;

class Payment extends Model
{
    use HasFactory, LogsActivity;

    const STATUS_LABELS = [
        'pending'        => 'Pending',
        'due_soon'       => 'Due Soon',
        'overdue'        => 'Overdue',
        'paid'           => 'Paid',
        'partially_paid' => 'Partially Paid',
    ];

    protected $fillable = [
        'contract_id',
        'shipment_id',
        'payment_term_id',
        'supplier_id',
        'amount_due',
        'amount_paid',
        'currency',
        'due_date',
        'payment_date',
        'bank_reference',
        'status',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'due_date'     => 'date',
        'payment_date' => 'date',
        'amount_due'   => 'decimal:2',
        'amount_paid'  => 'decimal:2',
    ];

    public function contract()
    {
        return $this->belongsTo(Contract::class);
    }

    public function shipment()
    {
        return $this->belongsTo(Shipment::class);
    }

    public function paymentTerm()
    {
        return $this->belongsTo(PaymentTerm::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeDueSoon($query)
    {
        return $query->whereNotIn('status', ['paid'])
            ->whereBetween('due_date', [now()->toDateString(), now()->addDays(7)->toDateString()]);
    }

    public function scopeOverdue($query)
    {
        return $query->whereNotIn('status', ['paid'])
            ->where('due_date', '<', now()->toDateString());
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUS_LABELS[$this->status] ?? $this->status;
    }
}
