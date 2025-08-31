<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityLog extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'action',
        'model_type',
        'model_id',
        'changes',
        'ip_address',
        'user_agent',
        'description'
    ];

    protected $casts = [
        'changes' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
    
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    
    // Obter o nome da ação em português
    public function getActionNameAttribute(): string
    {
        return match($this->action) {
            'create' => 'Criação',
            'update' => 'Edição',
            'delete' => 'Exclusão',
            'restore' => 'Restauração',
            'download' => 'Download',
            'rate' => 'Avaliação',
            'login' => 'Login',
            'logout' => 'Logout',
            'password_reset' => 'Redefinição de Senha',
            default => ucfirst($this->action),
        };
    }
    
    // Obter cor para badge baseado na ação
    public function getActionColorAttribute(): string
    {
        return match($this->action) {
            'create' => 'success',
            'update' => 'primary',
            'delete' => 'danger',
            'restore' => 'warning',
            'download' => 'info',
            'rate' => 'secondary',
            'login' => 'dark',
            'logout' => 'dark',
            'password_reset' => 'info',
            default => 'dark',
        };
    }
    
    // Obter ícone para cada ação
    public function getActionIconAttribute(): string
    {
        return match($this->action) {
            'create' => 'plus-circle',
            'update' => 'edit',
            'delete' => 'trash',
            'restore' => 'undo',
            'download' => 'download',
            'rate' => 'star',
            'login' => 'sign-in-alt',
            'logout' => 'sign-out-alt',
            'password_reset' => 'key',
            default => 'history',
        };
    }
    
    // Obter nome do modelo de forma legível
    public function getModelNameAttribute(): string
    {
        $models = [
            'App\Models\Game' => 'Jogo',
            'App\Models\News' => 'Notícia',
            'App\Models\User' => 'Usuário',
            'App\Models\HelpContent' => 'Conteúdo de Ajuda',
        ];
        
        return $models[$this->model_type] ?? class_basename($this->model_type);
    }
    
    public function getFormattedChangesAttribute(): array
    {
        $changes = $this->changes;
        
        // Se changes é uma string JSON, decodificar
        if (is_string($changes)) {
            $changes = json_decode($changes, true) ?? [];
        }
        
        if (empty($changes) || !is_array($changes)) {
            return [];
        }
        
        $formatted = [];
        $fieldNames = $this->getFieldNames();
        
        foreach ($changes as $field => $change) {
            $fieldName = $fieldNames[$field] ?? $this->formatFieldName($field);
            
            // Se for um array com old e new
            if (is_array($change) && isset($change['old']) && isset($change['new'])) {
                $formatted[] = [
                    'field' => $fieldName,
                    'old' => $this->formatValueForDisplay($field, $change['old']),
                    'new' => $this->formatValueForDisplay($field, $change['new'])
                ];
            } 
            // Outros casos
            else {
                $formatted[] = [
                    'field' => $fieldName,
                    'value' => $this->formatValueForDisplay($field, $change)
                ];
            }
        }
        
        return $formatted;
    }
    
    // Formatar nomes de campos de forma mais inteligente
    protected function formatFieldName(string $field): string
    {
        // Converter snake_case para texto legível
        $field = str_replace('_', ' ', $field);
        $field = ucwords($field);
        
        // Traduções específicas
        $translations = [
            'Id' => 'ID',
            'Url' => 'URL',
            'Ip' => 'IP',
            'Api' => 'API',
            'Pdf' => 'PDF',
            'Html' => 'HTML',
            'Json' => 'JSON',
        ];
        
        foreach ($translations as $search => $replace) {
            $field = str_replace($search, $replace, $field);
        }
        
        return $field;
    }
    
    // Traduzir nomes dos campos
    protected function getFieldNames(): array
    {
        return [
            'title' => 'Título',
            'name' => 'Nome',
            'email' => 'E-mail',
            'description' => 'Descrição',
            'content' => 'Conteúdo',
            'password' => 'Senha',
            'status' => 'Status',
            'type' => 'Tipo',
            'category_id' => 'Categoria',
            'image' => 'Imagem',
            'price' => 'Preço',
            'score' => 'Pontuação',
            'is_active' => 'Ativo',
            'is_featured' => 'Destaque',
            'is_admin' => 'Administrador',
            'is_blocked' => 'Bloqueado',
            'published_at' => 'Data de Publicação',
            'created_at' => 'Criado em',
            'updated_at' => 'Atualizado em',
            'deleted_at' => 'Excluído em',
            'email_verified_at' => 'E-mail verificado em',
            'remember_token' => 'Token de lembrete',
        ];
    }
    
    // Formatar valores específicos
    protected function formatValue(string $field, $value)
    {
        if (is_null($value)) {
            return '<span class="text-muted fst-italic">Nulo</span>';
        }
        
        if ($value === '') {
            return '<span class="text-muted fst-italic">Vazio</span>';
        }
        
        // Campos booleanos
        if (in_array($field, ['is_active', 'status', 'published', 'is_featured', 'is_admin', 'is_blocked'])) {
            return $value ? '<span class="text-success">Sim</span>' : '<span class="text-danger">Não</span>';
        }
        
        // Campos de data
        if (in_array($field, ['created_at', 'updated_at', 'deleted_at', 'published_at', 'email_verified_at'])) {
            try {
                return \Carbon\Carbon::parse($value)->format('d/m/Y H:i:s');
            } catch (\Exception $e) {
                return $value;
            }
        }
        
        // Campos de senha (não mostrar o valor real)
        if ($field === 'password') {
            return '<span class="text-muted">••••••••</span>';
        }
        
        // Campos de token (não mostrar o valor completo)
        if ($field === 'remember_token') {
            return '<span class="text-muted">' . substr($value, 0, 10) . '...</span>';
        }
        
        // Se for um array, converter para JSON formatado
        if (is_array($value)) {
            $json = json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            return '<pre class="mb-0" style="white-space: pre-wrap;">' . e($json) . '</pre>';
        }
        
        // Se for um JSON string, tentar formatar
        if (is_string($value) && $this->isJson($value)) {
            $json = json_encode(json_decode($value, true), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            return '<pre class="mb-0" style="white-space: pre-wrap;">' . e($json) . '</pre>';
        }
        
        // Texto muito longo
        if (is_string($value) && strlen($value) > 100) {
            return '<div class="long-text">' . e($value) . '</div>';
        }
        
        return e($value);
    }
    
    // Verificar se é JSON
    protected function isJson($string): bool
    {
        if (!is_string($string)) {
            return false;
        }
        
        json_decode($string);
        return json_last_error() === JSON_ERROR_NONE;
    }
    
    // Escopo para filtrar logs por usuário
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }
    
    // Escopo para filtrar logs por modelo
    public function scopeForModel($query, $modelType, $modelId = null)
    {
        $query = $query->where('model_type', $modelType);
        
        if ($modelId) {
            $query = $query->where('model_id', $modelId);
        }
        
        return $query;
    }
    
    // Escopo para filtrar logs por ação
    public function scopeForAction($query, $action)
    {
        return $query->where('action', $action);
    }
    
    // Escopo para filtrar logs por período
    public function scopeForPeriod($query, $startDate, $endDate = null)
    {
        $query = $query->whereDate('created_at', '>=', $startDate);
        
        if ($endDate) {
            $query = $query->whereDate('created_at', '<=', $endDate);
        }
        
        return $query;
    }

    protected function formatValueForDisplay(string $field, $value)
    {
        if (is_null($value)) {
            return 'Nulo';
        }
        
        if ($value === '') {
            return 'Vazio';
        }
        
        // Campos booleanos
        if (in_array($field, ['is_active', 'status', 'published', 'is_featured', 'is_admin', 'is_blocked'])) {
            return $value ? 'Sim' : 'Não';
        }
        
        // Campos de data
        if (in_array($field, ['created_at', 'updated_at', 'deleted_at', 'published_at', 'email_verified_at'])) {
            try {
                return \Carbon\Carbon::parse($value)->format('d/m/Y H:i:s');
            } catch (\Exception $e) {
                return $value;
            }
        }
        
        // Campos de senha (não mostrar o valor real)
        if ($field === 'password') {
            return '••••••••';
        }
        
        // Campos de token (não mostrar o valor completo)
        if ($field === 'remember_token') {
            return substr($value, 0, 10) . '...';
        }
        
        // Se for um array, converter para JSON formatado
        if (is_array($value)) {
            return json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        }
        
        // Se for um JSON string, tentar formatar
        if (is_string($value) && $this->isJson($value)) {
            $decoded = json_decode($value, true);
            return json_encode($decoded, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        }
        
        return $value;
    }
}