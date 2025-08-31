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
            'force_delete' => 'Exclusão Forçada',
            'replicate' => 'Duplicação',
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
            'force_delete' => 'danger',
            'replicate' => 'info',
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
            'force_delete' => 'trash-alt',
            'replicate' => 'copy',
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
            'App\Models\GameTag' => 'Tag de Jogo',
            'App\Models\GameScreenshot' => 'Screenshot de Jogo',
            'App\Models\GameDownload' => 'Download de Jogo',
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
            
            // Se for um array com old e new (mudanças em updates)
            if (is_array($change) && isset($change['old']) && isset($change['new'])) {
                $formatted[] = [
                    'field' => $fieldName,
                    'old' => $this->formatValueForDisplay($field, $change['old']),
                    'new' => $this->formatValueForDisplay($field, $change['new']),
                    'type' => 'change'
                ];
            } 
            // Se for um array com apenas valores (criações)
            elseif (is_array($change) && !isset($change['old']) && !isset($change['new'])) {
                $formatted[] = [
                    'field' => $fieldName,
                    'value' => $this->formatValueForDisplay($field, $change),
                    'type' => 'create'
                ];
            }
            // Outros casos
            else {
                $formatted[] = [
                    'field' => $fieldName,
                    'value' => $this->formatValueForDisplay($field, $change),
                    'type' => 'single'
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
            'Ssh' => 'SSH',
            'Ftp' => 'FTP',
            'Http' => 'HTTP',
            'Https' => 'HTTPS',
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
            'cover_image' => 'Imagem de Capa',
            'featured_image' => 'Imagem em Destaque',
            'file_path' => 'Arquivo',
            'price' => 'Preço',
            'score' => 'Pontuação',
            'is_active' => 'Ativo',
            'is_featured' => 'Destaque',
            'is_admin' => 'Administrador',
            'is_blocked' => 'Bloqueado',
            'published' => 'Publicado',
            'published_at' => 'Data de Publicação',
            'created_at' => 'Criado em',
            'updated_at' => 'Atualizado em',
            'deleted_at' => 'Excluído em',
            'email_verified_at' => 'E-mail verificado em',
            'remember_token' => 'Token de lembrete',
            'verification_code' => 'Código de Verificação',
            'verification_code_expires_at' => 'Expiração do Código',
            'permissions' => 'Permissões',
            'slug' => 'Slug',
            'excerpt' => 'Resumo',
            'short_description' => 'Descrição Curta',
            'long_description' => 'Descrição Longa',
            'how_to_play' => 'Como Jogar',
            'educational_objectives' => 'Objetivos Educacionais',
            'coordinators_content' => 'Conteúdo para Coordenadores',
            'interns_content' => 'Conteúdo para Estagiários',
            'machines_usage_content' => 'Conteúdo de Uso de Máquinas',
        ];
    }
    
    // Formatar valores específicos
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
        $dateFields = ['created_at', 'updated_at', 'deleted_at', 'published_at', 
                      'email_verified_at', 'verification_code_expires_at'];
        if (in_array($field, $dateFields)) {
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
        
        // Campos de permissões (array)
        if ($field === 'permissions' && is_array($value)) {
            return implode(', ', $value);
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

    public function getEnhancedChangesAttribute(): array
    {
        $changes = $this->changes;
        
        if (is_string($changes)) {
            $changes = json_decode($changes, true) ?? [];
        }
        
        if (empty($changes) || !is_array($changes)) {
            return [];
        }
        
        $enhanced = [];
        $fieldNames = $this->getFieldNames();
        
        foreach ($changes as $field => $change) {
            $fieldName = $fieldNames[$field] ?? $this->formatFieldName($field);
            
            // Para mudanças com old/new (updates)
            if (is_array($change) && isset($change['old']) && isset($change['new'])) {
                $enhanced[] = [
                    'field' => $fieldName,
                    'old' => $this->formatValueForDisplay($field, $change['old']),
                    'new' => $this->formatValueForDisplay($field, $change['new']),
                    'type' => 'change',
                    'raw_old' => $change['old'],
                    'raw_new' => $change['new']
                ];
            } 
            // Para criações (apenas valores)
            elseif (is_array($change) && !isset($change['old']) && !isset($change['new'])) {
                $enhanced[] = [
                    'field' => $fieldName,
                    'value' => $this->formatValueForDisplay($field, $change),
                    'type' => 'create',
                    'raw_value' => $change
                ];
            }
            // Outros casos
            else {
                $enhanced[] = [
                    'field' => $fieldName,
                    'value' => $this->formatValueForDisplay($field, $change),
                    'type' => 'single',
                    'raw_value' => $change
                ];
            }
        }
        
        return $enhanced;
    }
}