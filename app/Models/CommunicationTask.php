<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\LogsActivity;

class CommunicationTask extends Model
{
    use HasFactory, LogsActivity;

    const CATEGORY_LABELS = [
        'document_approval'  => 'Approvazione Documenti',
        'cad_bank_request'   => 'Richiesta CAD/Banca',
        'payment_followup'   => 'Follow-up Pagamento',
        'supplier_request'   => 'Richiesta Fornitore',
        'forwarder_request'  => 'Richiesta Spedizioniere',
        'internal_note'      => 'Nota Interna',
    ];

    const PRIORITY_LABELS = [
        'low'    => 'Bassa',
        'normal' => 'Normale',
        'high'   => 'Alta',
        'urgent' => 'Urgente',
    ];

    const STATUS_LABELS = [
        'to_review'        => 'Da Rivedere',
        'waiting_internal' => 'In Attesa Interna',
        'waiting_supplier' => 'In Attesa Fornitore',
        'ready_to_reply'   => 'Pronto per Risposta',
        'replied'          => 'Risposto',
        'closed'           => 'Chiuso',
    ];

    protected $fillable = [
        'subject',
        'sender',
        'recipient',
        'outlook_thread_link',
        'outlook_message_id',
        'outlook_conversation_id',
        'category',
        'priority',
        'due_date',
        'reminder_date',
        'status',
        'notes',
        'contract_id',
        'shipment_id',
        'assigned_to',
        'created_by',
    ];

    protected $casts = [
        'due_date'      => 'date',
        'reminder_date' => 'date',
    ];

    public function contract()
    {
        return $this->belongsTo(Contract::class);
    }

    public function shipment()
    {
        return $this->belongsTo(Shipment::class);
    }

    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Scopes
    public function scopeOpen($query)
    {
        return $query->whereNotIn('status', ['replied', 'closed']);
    }

    public function scopeUrgent($query)
    {
        return $query->whereIn('priority', ['high', 'urgent']);
    }

    public function scopeDueSoon($query, $days = 3)
    {
        return $query->whereNotNull('due_date')
            ->where('due_date', '<=', now()->addDays($days)->toDateString())
            ->whereNotIn('status', ['replied', 'closed']);
    }

    public function getCategoryLabelAttribute(): string
    {
        return self::CATEGORY_LABELS[$this->category] ?? $this->category;
    }

    public function getPriorityLabelAttribute(): string
    {
        return self::PRIORITY_LABELS[$this->priority] ?? $this->priority;
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUS_LABELS[$this->status] ?? $this->status;
    }
}
