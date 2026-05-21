<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    protected $fillable = [
        'log_name',
        'description',
        'subject_type',
        'subject_id',
        'causer_type',
        'causer_id',
        'properties',
    ];

    protected $casts = [
        'properties' => 'array',
    ];

    public function subject()
    {
        return $this->morphTo();
    }

    public function causer()
    {
        return $this->morphTo();
    }

    public function getCauserNameAttribute(): string
    {
        if ($this->causer_type && $this->causer_id) {
            try {
                $causer = $this->causer;
                return $causer ? ($causer->name ?? 'Unknown') : 'System';
            } catch (\Throwable $e) {
                return 'System';
            }
        }
        return 'System';
    }

    public function getSubjectLabelAttribute(): string
    {
        return $this->log_name . ' #' . $this->subject_id;
    }
}
