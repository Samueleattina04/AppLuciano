<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    protected $fillable = [
        'documentable_id', 'documentable_type', 'type',
        'name', 'file_path', 'original_filename', 'file_size', 'notes',
    ];

    const TYPE_LABELS = [
        'bill_of_lading'      => 'Polizza di Carico (BL)',
        'commercial_invoice'  => 'Commercial Invoice',
        'packing_list'        => 'Packing List',
        'certificate_of_origin' => 'Certificato di Origine',
        'other'               => 'Altro',
    ];

    public function documentable()
    {
        return $this->morphTo();
    }

    public function getTypeLabelAttribute(): string
    {
        return self::TYPE_LABELS[$this->type] ?? $this->type;
    }

    public function getFileSizeFormattedAttribute(): string
    {
        if (!$this->file_size) return '-';
        $kb = $this->file_size / 1024;
        if ($kb < 1024) return round($kb, 1) . ' KB';
        return round($kb / 1024, 1) . ' MB';
    }
}
