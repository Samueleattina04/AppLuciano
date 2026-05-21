<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Document extends Model
{
    use HasFactory;

    const TYPE_LABELS = [
        'bill_of_lading'            => 'Bill of Lading',
        'commercial_invoice'        => 'Commercial Invoice',
        'packing_list'              => 'Packing List',
        'certificate_of_origin'     => 'Certificate of Origin',
        'phytosanitary_certificate' => 'Phytosanitary Certificate',
        'insurance_certificate'     => 'Insurance Certificate',
        'quality_certificate'       => 'Quality Certificate',
        'other'                     => 'Other',
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
