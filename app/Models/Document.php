<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Document extends Model
{
    use HasFactory;

    const TYPE_LABELS = [
        'bill_of_lading'            => 'Polizza di Carico',
        'commercial_invoice'        => 'Fattura Commerciale',
        'packing_list'              => 'Packing List',
        'certificate_of_origin'     => 'Certificato di Origine',
        'phytosanitary_certificate' => 'Certificato Fitosanitario',
        'insurance_certificate'     => 'Certificato Assicurativo',
        'quality_certificate'       => 'Certificato di Qualità',
        'other'                     => 'Altro',
    ];

    const CRITICAL_DOCS = [
        'bill_of_lading',
        'commercial_invoice',
        'packing_list',
        'certificate_of_origin',
    ];

    protected $fillable = [
        'documentable_type',
        'documentable_id',
        'document_type',
        'name',
        'file_path',
        'version',
        'status',
        'notes',
        'uploaded_by',
    ];

    public function documentable()
    {
        return $this->morphTo();
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function getTypeLabelAttribute(): string
    {
        return self::TYPE_LABELS[$this->document_type] ?? $this->document_type;
    }
}
