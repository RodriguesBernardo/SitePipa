<?php

namespace App\Traits;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

trait Loggable
{
    protected static function bootLoggable()
    {
        static::created(function ($model) {
            self::logActivity($model, 'create');
        });

        static::updated(function ($model) {
            $changes = $model->getChanges();
            // Remover campos que não queremos logar
            unset($changes['updated_at']);
            
            self::logActivity($model, 'update', $changes);
        });

        static::deleted(function ($model) {
            self::logActivity($model, 'delete');
        });

        // Só registrar o evento restored se o model tiver o método restored
        // (ou seja, se usar SoftDeletes)
        if (method_exists(static::class, 'restored')) {
            static::restored(function ($model) {
                self::logActivity($model, 'restore');
            });
        }
    }

    protected static function logActivity($model, $action, $changes = null)
    {
        if (Auth::check()) {
            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => $action,
                'model_type' => get_class($model),
                'model_id' => $model->id,
                'changes' => $changes,
                'ip_address' => Request::ip(),
                'user_agent' => Request::userAgent(),
            ]);
        }
    }
}