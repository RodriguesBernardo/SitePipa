@extends('layouts.app')

@section('title', 'Detalhes do Log')
@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-6">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.logs.index') }}">Logs de Atividade</a></li>
                    <li class="breadcrumb-item active">Detalhes do Log</li>
                </ol>
            </nav>
            <h1>Detalhes do Log</h1>
        </div>
        <div class="col-md-6 text-end">
            <a href="{{ route('admin.logs.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Voltar para a lista
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Card de Informações Principais -->
        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-info-circle"></i> Informações do Log</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="flex-grow-1">
                            <h6 class="card-title text-muted">ID do Log</h6>
                            <h4>#{{ $log->id }}</h4>
                        </div>
                        <div class="flex-shrink-0">
                            <span class="badge bg-{{ $log->action_color }} p-2 fs-6">
                                <i class="fas fa-{{ $log->action_icon }} me-1"></i>
                                {{ $log->action_name }}
                            </span>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <h6 class="text-muted">Data e Hora</h6>
                        <p class="mb-0">
                            <i class="fas fa-calendar me-2"></i>{{ $log->created_at->format('d/m/Y') }}
                            <i class="fas fa-clock ms-3 me-2"></i>{{ $log->created_at->format('H:i:s') }}
                        </p>
                    </div>
                    
                    <div class="mb-3">
                        <h6 class="text-muted">Usuário Responsável</h6>
                        @if($log->user)
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <p class="mb-0">{{ $log->user->name }}</p>
                                    <small class="text-muted">{{ $log->user->email }}</small>
                                </div>
                                @if(Route::has('admin.users.show'))
                                    <a href="{{ route('admin.users.show', $log->user) }}" class="btn btn-sm btn-outline-primary ms-2" title="Ver usuário">
                                        <i class="fas fa-user"></i>
                                    </a>
                                @endif
                            </div>
                        @else
                            <span class="text-muted fst-italic">Usuário não encontrado</span>
                        @endif
                    </div>
                    
                    @if($log->description)
                    <div class="mb-3">
                        <h6 class="text-muted">Descrição</h6>
                        <p class="mb-0">{{ $log->description }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Card do Recurso Afetado -->
            <div class="card mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-database"></i> Recurso Afetado</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <h6 class="text-muted">Tipo</h6>
                        <p class="mb-0">
                            <span class="badge">{{ $log->model_name }}</span>
                            <small class="text-muted d-block mt-1">{{ $log->model_type }}</small>
                        </p>
                    </div>
                    
                    <div class="mb-3">
                        <h6 class="text-muted">ID do Recurso</h6>
                        <p class="mb-0">
                            @if($log->model_id)
                                <span class="badge">#{{ $log->model_id }}</span>
                                @if($log->model_type === 'App\Models\User' && Route::has('admin.users.show'))
                                    <a href="{{ route('admin.users.show', $log->model_id) }}" class="btn btn-sm btn-outline-info ms-1" title="Ver este usuário">
                                        <i class="fas fa-external-link-alt"></i>
                                    </a>
                                @endif
                            @else
                                <span class="text-muted">N/A</span>
                            @endif
                        </p>
                    </div>
                    
                    <!-- Mostrar detalhes do usuário editado quando aplicável -->
                    @if($log->action === 'update' && $log->model_type === 'App\Models\User' && $log->model_id)
                        @php
                            // Buscar o usuário editado diretamente pelo ID
                            $editedUser = \App\Models\User::find($log->model_id);
                        @endphp
                        @if($editedUser)
                        <div class="mt-3 p-3 rounded">
                            <h6 class="text-muted">Usuário Editado</h6>
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <p class="mb-0 fw-bold">{{ $editedUser->name }}</p>
                                    <small class="text-muted">{{ $editedUser->email }}</small>
                                    <small class="d-block text-muted">ID: #{{ $editedUser->id }}</small>
                                </div>
                                @if(Route::has('admin.users.show'))
                                    <a href="{{ route('admin.users.show', $editedUser) }}" class="btn btn-sm btn-outline-info ms-2" title="Ver usuário editado">
                                        <i class="fas fa-external-link-alt"></i>
                                    </a>
                                @endif
                            </div>
                        </div>
                        @else
                        <div class="mt-3 p-3 rounded">
                            <h6 class="text-muted">Usuário Editado</h6>
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <p class="mb-0 fw-bold text-muted">Usuário #{{ $log->model_id }}</p>
                                    <small class="text-muted">Este usuário pode ter sido excluído</small>
                                </div>
                            </div>
                        </div>
                        @endif
                    @endif
                </div>
            </div>
        </div>

        <!-- Card de Detalhes Técnicos -->
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0"><i class="fas fa-cogs"></i> Detalhes Técnicos</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <h6 class="text-muted">Endereço IP</h6>
                                <p class="mb-0">
                                    <code>{{ $log->ip_address }}</code>
                                    @if(filter_var($log->ip_address, FILTER_VALIDATE_IP))
                                        <a href="https://www.ip-tracker.org/locator/ip-lookup.php?ip={{ $log->ip_address }}" 
                                           target="_blank" class="btn btn-sm btn-outline-secondary ms-1" title="Rastrear IP">
                                            <i class="fas fa-search"></i>
                                        </a>
                                    @endif
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <h6 class="text-muted">User Agent</h6>
                                <div class="d-flex align-items-center">
                                    <small class="user-agent-text flex-grow-1">{{ $log->user_agent }}</small>
                                    <button class="btn btn-sm btn-outline-secondary ms-1 copy-text" 
                                            data-text="{{ $log->user_agent }}" title="Copiar User Agent">
                                        <i class="fas fa-copy"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card de Alterações -->
            @if(!empty($log->formatted_changes))
            <div class="card mb-4">
                <div class="card-header bg-{{ $log->action === 'create' ? 'success' : ($log->action === 'update' ? 'warning' : 'danger') }} text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-{{ $log->action === 'create' ? 'plus' : ($log->action === 'update' ? 'edit' : 'trash') }}"></i>
                        Alterações Realizadas
                    </h5>
                </div>
                <div class="card-body">
                    @if($log->action === 'create')
                    <div class="alert alert-info mb-4">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Registro criado:</strong> Os seguintes campos foram preenchidos durante a criação.
                    </div>
                    @endif
                    
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead class="table-dark">
                                <tr>
                                    <th width="20%">Campo</th>
                                    <th width="35%">Valor Anterior</th>
                                    <th width="35%">Novo Valor</th>
                                    <th width="10%">Tipo</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($log->formatted_changes as $change)
                                <tr>
                                    <td><strong>{{ $change['field'] }}</strong></td>
                                    
                                    {{-- Coluna Valor Anterior --}}
                                    <td>
                                        @if(isset($change['old']))
                                            <div class="change-value-container">
                                                {!! formatChangeValueForUser($change['old']) !!}
                                            </div>
                                        @else
                                            <span class="text-muted fst-italic">N/A</span>
                                        @endif
                                    </td>
                                    
                                    {{-- Coluna Novo Valor --}}
                                    <td>
                                        @if(isset($change['new']))
                                            <div class="change-value-container">
                                                {!! formatChangeValueForUser($change['new']) !!}
                                            </div>
                                        @elseif(isset($change['value']))
                                            <div class="change-value-container">
                                                {!! formatChangeValueForUser($change['value']) !!}
                                            </div>
                                        @else
                                            <span class="text-muted fst-italic">N/A</span>
                                        @endif
                                    </td>
                                    
                                    {{-- Coluna Tipo --}}
                                    <td class="text-center">
                                        @if(isset($change['type']) && $change['type'] === 'create')
                                            <span class="badge bg-success" title="Campo criado">C</span>
                                        @elseif(isset($change['old']) && isset($change['new']))
                                            @if($change['old'] === $change['new'])
                                                <span class="badge bg-secondary" title="Sem alteração">=</span>
                                            @else
                                                <span class="badge bg-warning" title="Campo alterado">→</span>
                                            @endif
                                        @else
                                            <span class="badge bg-info" title="Valor único">+</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @elseif($log->action === 'create')
            <div class="card mb-4">
                <div class="card-body">
                    <div class="alert alert-success">
                        <i class="fas fa-plus-circle me-2"></i>
                        <strong>Registro criado:</strong> Um novo registro do tipo "{{ $log->model_name }}" foi criado.
                    </div>
                </div>
            </div>
            @elseif($log->action === 'delete')
            <div class="card mb-4">
                <div class="card-body">
                    <div class="alert alert-danger">
                        <i class="fas fa-trash me-2"></i>
                        <strong>Registro excluído:</strong> O registro do tipo "{{ $log->model_name }}" foi excluído.
                    </div>
                </div>
            </div>
            @elseif($log->action === 'restore')
            <div class="card mb-4">
                <div class="card-body">
                    <div class="alert alert-warning">
                        <i class="fas fa-undo me-2"></i>
                        <strong>Registro restaurado:</strong> O registro do tipo "{{ $log->model_name }}" foi restaurado.
                    </div>
                </div>
            </div>
            @else
            <div class="card mb-4">
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        Nenhuma alteração detalhada disponível para este log.
                    </div>
                </div>
            </div>
            @endif

            <!-- Dados Brutos para Debug -->
            @if(config('app.debug') && $log->changes)
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-bug me-2"></i>Dados Brutos (Debug)
                        <button class="btn btn-sm btn-outline-secondary float-end copy-raw" 
                                data-raw="{{ json_encode($log->changes, JSON_PRETTY_PRINT) }}">
                            <i class="fas fa-copy"></i> Copiar
                        </button>
                    </h6>
                </div>
                <div class="card-body">
                    <pre class="mb-0 raw-data">{{ json_encode($log->changes, JSON_PRETTY_PRINT) }}</pre>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<style>
.change-value-container {
    max-height: 200px;
    overflow-y: auto;
    padding: 8px;
    background-color: #f8f9fa;
    border-radius: 4px;
    font-size: 0.9em;
    white-space: pre-wrap;
    word-break: break-all;
}

.user-agent-text {
    font-family: monospace;
    font-size: 0.85em;
}

.raw-data {
    max-height: 300px;
    overflow-y: auto;
    font-size: 0.8em;
}

.long-text {
    white-space: pre-wrap;
    word-break: break-word;
}

.copy-text:hover, .copy-raw:hover {
    cursor: pointer;
}

.value-boolean-true {
    color: #198754;
    font-weight: bold;
}

.value-boolean-false {
    color: #dc3545;
    font-weight: bold;
}

.value-null {
    color: #6c757d;
    font-style: italic;
}

.value-empty {
    color: #6c757d;
    font-style: italic;
}

.value-html {
    border: 1px solid #dee2e6;
    border-radius: 4px;
    padding: 8px;
    background-color: #f8f9fa;
}

.value-html-preview {
    max-height: 100px;
    overflow: hidden;
    position: relative;
}

.value-html-preview::after {
    content: "";
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    height: 20px;
    background: linear-gradient(transparent, #f8f9fa);
}

.value-image-path {
    background-color: #f8f9fa;
    padding: 4px 8px;
    border-radius: 4px;
    font-family: monospace;
    font-size: 0.85em;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Função para copiar texto
    function copyToClipboard(text) {
        navigator.clipboard.writeText(text).then(() => {
            // Mostrar feedback visual (opcional)
            const toast = new bootstrap.Toast(document.getElementById('copyToast'));
            toast.show();
        }).catch(err => {
            console.error('Erro ao copiar texto: ', err);
        });
    }
    
    // Adicionar event listeners para botões de copiar
    document.querySelectorAll('.copy-text').forEach(button => {
        button.addEventListener('click', function() {
            const text = this.getAttribute('data-text');
            copyToClipboard(text);
        });
    });
    
    document.querySelectorAll('.copy-raw').forEach(button => {
        button.addEventListener('click', function() {
            const rawData = this.getAttribute('data-raw');
            copyToClipboard(rawData);
        });
    });
});
</script>

<!-- Toast para feedback de cópia -->
<div class="toast-container position-fixed bottom-0 end-0 p-3">
    <div id="copyToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="toast-header">
            <i class="fas fa-check-circle text-success me-2"></i>
            <strong class="me-auto">Sucesso</strong>
            <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
        <div class="toast-body">
            Texto copiado para a área de transferência!
        </div>
    </div>
</div>
@endsection

<?php
// Helper function para formatar valores de forma amigável para usuários
if (!function_exists('formatChangeValueForUser')) {
    function formatChangeValueForUser($value) {
        if (is_null($value)) {
            return '<span class="value-null">Nulo</span>';
        }
        
        if ($value === '') {
            return '<span class="value-empty">Vazio</span>';
        }
        
        // Valores booleanos
        if (is_bool($value)) {
            return $value 
                ? '<span class="value-boolean-true"><i class="fas fa-check-circle me-1"></i>Sim</span>' 
                : '<span class="value-boolean-false"><i class="fas fa-times-circle me-1"></i>Não</span>';
        }
        
        // Se for um array ou objeto JSON, formatar de forma legível
        if (is_array($value) || (is_string($value) && is_json($value))) {
            $json = is_string($value) ? json_decode($value, true) : $value;
            
            // Se for um array simples com poucos itens
            if (count($json) <= 3 && isSimpleArray($json)) {
                $formatted = implode(', ', array_map(function($item) {
                    return e(is_array($item) ? json_encode($item) : $item);
                }, $json));
                return $formatted;
            }
            
            // Para arrays/objetos complexos, mostrar como JSON formatado
            $formatted = json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            return '<pre class="mb-0 long-text">' . e($formatted) . '</pre>';
        }
        
        // Verificar se é HTML
        if (is_string($value) && preg_match('/<[^>]*>/', $value)) {
            return '<div class="value-html">
                <div class="value-html-preview">' . strip_tags(substr($value, 0, 200)) . '...</div>
                <div class="value-html-full" style="display: none;">' . e($value) . '</div>
                <button class="btn btn-sm btn-outline-secondary mt-2 toggle-html">Expandir</button>
            </div>';
        }
        
        // Verificar se é um caminho de imagem
        if (is_string($value) && preg_match('/\.(jpg|jpeg|png|gif|bmp|webp|svg)$/i', $value)) {
            return '<div class="value-image-path">' . e($value) . '</div>';
        }
        
        // Se for muito longo, usar textarea
        if (is_string($value) && strlen($value) > 150) {
            return '<textarea class="form-control" rows="4" readonly>' . e($value) . '</textarea>';
        }
        
        return e($value);
    }
}

// Verificar se é um array simples (não associativo e com valores simples)
if (!function_exists('isSimpleArray')) {
    function isSimpleArray($array) {
        if (!is_array($array)) return false;
        
        // Verificar se é um array associativo
        if (count(array_filter(array_keys($array), 'is_string'))) {
            return false;
        }
        
        // Verificar se todos os valores são escalares
        foreach ($array as $value) {
            if (is_array($value) || is_object($value)) {
                return false;
            }
        }
        
        return true;
    }
}

if (!function_exists('is_json')) {
    function is_json($string) {
        if (!is_string($string)) return false;
        json_decode($string);
        return json_last_error() === JSON_ERROR_NONE;
    }
}
?>