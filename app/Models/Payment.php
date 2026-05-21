<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\LogsActivity;

class Payment extends Model
{
    use HasFactory, LogsActivity;

    const TYPE_LABELS = [
        'advance'          => 'Acconto',
        'shipment_payment' => 'Pagamento Spedizione',
    ];

    const TYPE_COLORS = [
        'advance'          => 'info',
        'shipment_payment' => 'primary',
    ];

    const STATUS_LABELS = [
        'pending'        => 'In Sospeso',
        'due_soon'       => 'In Scadenza',
        'overdue'        => 'Scaduto',
        'paid'           => 'Pagato',
        'partially_paid' => 'Parzialmente Pagato',
    ];

    protected $fillable = [
        'contract_id',
        'payment_type',
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

    public function getTypeLabelAttribute(): string
    {
        return self::TYPE_LABELS[$this->payment_type] ?? $this->payment_type;
    }

    public function getIsAdvanceAttribute(): bool
    {
        return $this->payment_type === 'advance';
    }
}
