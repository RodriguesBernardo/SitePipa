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
            // Capturar todos os atributos para criação
            $attributes = $model->getAttributes();
            
            // Remover campos sensíveis
            unset($attributes['password'], $attributes['remember_token']);
            
            self::logActivity($model, 'create', $attributes, 'Registro criado');
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
            $formattedChanges = [];
            foreach ($changes as $field => $newValue) {
                $oldValue = $model->getOriginal($field);
                
                // Formatar mudanças para incluir old e new
                $formattedChanges[$field] = [
                    'old' => $oldValue,
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
            } else {
                $description = 'Registro excluído permanentemente';
            }
            
            self::logActivity($model, 'delete', null, $description);
        });

        // Só registrar o evento restored se o model tiver SoftDeletes
        if (method_exists(static::class, 'restored')) {
            static::restored(function ($model) {
                self::logActivity($model, 'restore', null, 'Registro restaurado da lixeira');
            });
        }

        // Registrar force delete
        if (method_exists(static::class, 'forceDeleted')) {
            static::forceDeleted(function ($model) {
                self::logActivity($model, 'force_delete', null, 'Registro excluído permanentemente (force delete)');
            });
        }

        // Registrar replicação (duplicação)
        if (method_exists(static::class, 'replicated')) {
            static::replicated(function ($model) {
                self::logActivity($model, 'replicate', $model->getAttributes(), 'Registro duplicado');
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
            'model_id' => $model->getKey(),
            'changes' => $changes,
            'description' => $description,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
        ]);
    }

    // Método para log manual de atividades específicas
    public static function logManualActivity($action, $changes = null, $description = null)
    {
        if (app()->runningInConsole() && !app()->runningUnitTests()) {
            return;
        }

        ActivityLog::create([
            'user_id' => Auth::check() ? Auth::id() : null,
            'action' => $action,
            'model_type' => static::class,
            'model_id' => null,
            'changes' => $changes,
            'description' => $description,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
        ]);
    }
}