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
            self::logActivity($model, 'create', null, 'Registro criado');
        });

        static::updated(function ($model) {
            $changes = $model->getChanges();
            
            // Remover campos que não queremos logar
            unset($changes['updated_at']);
            
            // Se não houver mudanças relevantes, não registrar o log
            if (empty($changes)) {
                return;
            }
            
            // Adicionar valores antigos para comparação
            $oldValues = [];
            foreach ($changes as $field => $newValue) {
                $oldValues[$field] = $model->getOriginal($field);
            }
            
            // Formatar mudanças para incluir old e new
            $formattedChanges = [];
            foreach ($changes as $field => $newValue) {
                $formattedChanges[$field] = [
                    'old' => $oldValues[$field],
                    'new' => $newValue
                ];
            }
            
            $description = count($changes) . ' campo(s) alterado(s)';
            self::logActivity($model, 'update', $formattedChanges, $description);
        });

        static::deleted(function ($model) {
            $description = 'Registro excluído';
            
            // Se for soft delete, adicionar informação
            if (method_exists($model, 'trashed') && $model->trashed()) {
                $description = 'Registro movido para lixeira (soft delete)';
            }
            
            self::logActivity($model, 'delete', null, $description);
        });

        // Só registrar o evento restored se o model tiver SoftDeletes
        if (method_exists(static::class, 'restored')) {
            static::restored(function ($model) {
                self::logActivity($model, 'restore', null, 'Registro restaurado da lixeira');
            });
        }
    }

    protected static function logActivity($model, $action, $changes = null, $description = null)
    {
        // Não registrar logs durante execução de comandos artisan ou seeds
        if (app()->runningInConsole() && !app()->runningUnitTests()) {
            return;
        }

        ActivityLog::create([
            'user_id' => Auth::check() ? Auth::id() : null,
            'action' => $action,
            'model_type' => get_class($model),
            'model_id' => $model->id,
            'changes' => $changes,
            'description' => $description,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
        ]);
    }
}