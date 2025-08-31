<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    protected $fillable = [
        'user_id',
        'action',
        'model_type',
        'model_id',
        'description',
        'changes',
        'ip_address',
        'user_agent'
    ];

    protected $casts = [
        'changes' => 'array',
        'created_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getActionNameAttribute()
    {
        return match($this->action) {
            'create' => 'Criar',
            'update' => 'Editar',
            'delete' => 'Excluir',
            'restore' => 'Restaurar',
            'download' => 'Download',
            'rate' => 'Avaliar',
            'login' => 'Login',
            'logout' => 'Logout',
            'register' => 'Registrar',
            default => $this->action
        };
    }

    public function getActionColorAttribute()
    {
        return match($this->action) {
            'create' => 'success',
            'update' => 'primary',
            'delete' => 'danger',
            'restore' => 'warning',
            'download' => 'info',
            'rate' => 'info',
            'login' => 'success',
            'logout' => 'secondary',
            'register' => 'primary',
            default => 'secondary'
        };
    }

    public function getModelNameAttribute()
    {
        if (!$this->model_type) return 'Sistema';
        
        return match($this->model_type) {
            'App\Models\Game' => 'Jogo',
            'App\Models\News' => 'Notícia',
            'App\Models\User' => 'Usuário',
            'App\Models\HelpContent' => 'Conteúdo de Ajuda',
            default => class_basename($this->model_type)
        };
    }
}