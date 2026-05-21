<?php

namespace App\Traits;

use App\Models\ActivityLog;

trait LogsActivity
{
    public static function bootLogsActivity(): void
    {
        static::created(fn($model) => static::logActivity('created', $model, []));
        static::updated(fn($model) => static::logActivity('updated', $model, $model->getDirty()));
        static::deleted(fn($model) => static::logActivity('deleted', $model, []));
    }

    protected static function logActivity(string $description, $model, array $changed): void
    {
        try {
            ActivityLog::create([
                'log_name'     => class_basename($model),
                'description'  => $description,
                'subject_type' => get_class($model),
                'subject_id'   => $model->getKey(),
                'causer_type'  => auth()->check() ? get_class(auth()->user()) : null,
                'causer_id'    => auth()->id(),
                'properties'   => count($changed) ? json_encode($changed) : null,
            ]);
        } catch (\Throwable $e) {
            // Silently fail to avoid breaking normal operations
        }
    }
}
