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

    <div class="card">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="fas fa-info-circle"></i> Informações do Log #{{ $log->id }}</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h5 class="border-bottom pb-2">Informações Básicas</h5>
                    <table class="table table-borderless">
                        <tr>
                            <th width="30%">ID do Log:</th>
                            <td>#{{ $log->id }}</td>
                        </tr>
                        <tr>
                            <th>Data:</th>
                            <td>{{ $log->created_at->format('d/m/Y') }}</td>
                        </tr>
                        <tr>
                            <th>Hora:</th>
                            <td>{{ $log->created_at->format('H:i:s') }}</td>
                        </tr>
                        <tr>
                            <th>Usuário:</th>
                            <td>
                                @if($log->user)
                                    <div class="d-flex align-items-center">
                                        <div class="flex-grow-1">
                                            <div>{{ $log->user->name }}</div>
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
                            </td>
                        </tr>
                        <tr>
                            <th>Ação:</th>
                            <td>
                                <span class="badge bg-{{ $log->action_color }} p-2">
                                    <i class="fas fa-{{ $log->action_icon }} me-1"></i>
                                    {{ $log->action_name }}
                                </span>
                            </td>
                        </tr>
                        @if($log->description)
                        <tr>
                            <th>Descrição:</th>
                            <td>{{ $log->description }}</td>
                        </tr>
                        @endif
                    </table>
                </div>
                <div class="col-md-6">
                    <h5 class="border-bottom pb-2">Detalhes Técnicos</h5>
                    <table class="table table-borderless">
                        <tr>
                            <th width="30%">Modelo:</th>
                            <td>
                                {{ $log->model_name }}
                                <br>
                                <small class="text-muted">{{ $log->model_type }}</small>
                            </td>
                        </tr>
                        <tr>
                            <th>ID do Modelo:</th>
                            <td>
                                @if($log->model_id)
                                    <span class="badge bg-light text-dark">#{{ $log->model_id }}</span>
                                    @if($log->model_type === 'App\Models\User' && Route::has('admin.users.show'))
                                        <a href="{{ route('admin.users.show', $log->model_id) }}" class="btn btn-sm btn-outline-info ms-1" title="Ver este usuário">
                                            <i class="fas fa-external-link-alt"></i>
                                        </a>
                                    @endif
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Endereço IP:</th>
                            <td>
                                <code>{{ $log->ip_address }}</code>
                                @if(filter_var($log->ip_address, FILTER_VALIDATE_IP))
                                    <a href="https://www.ip-tracker.org/locator/ip-lookup.php?ip={{ $log->ip_address }}" 
                                       target="_blank" class="btn btn-sm btn-outline-secondary ms-1" title="Rastrear IP">
                                        <i class="fas fa-search"></i>
                                    </a>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>User Agent:</th>
                            <td>
                                <small class="user-agent-text">{{ $log->user_agent }}</small>
                                <button class="btn btn-sm btn-outline-secondary ms-1 copy-text" 
                                        data-text="{{ $log->user_agent }}" title="Copiar User Agent">
                                    <i class="fas fa-copy"></i>
                                </button>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            @if(!empty($log->formatted_changes))
            <div class="row mt-4">
                <div class="col-12">
                    <h5 class="border-bottom pb-2 mb-3">Alterações Realizadas</h5>
                    
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
                                                {!! $this->formatChangeValue($change['old']) !!}
                                            </div>
                                        @else
                                            <span class="text-muted fst-italic">N/A</span>
                                        @endif
                                    </td>
                                    
                                    {{-- Coluna Novo Valor --}}
                                    <td>
                                        @if(isset($change['new']))
                                            <div class="change-value-container">
                                                {!! $this->formatChangeValue($change['new']) !!}
                                            </div>
                                        @elseif(isset($change['value']))
                                            <div class="change-value-container">
                                                {!! $this->formatChangeValue($change['value']) !!}
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
            <div class="row mt-4">
                <div class="col-12">
                    <div class="alert alert-success">
                        <i class="fas fa-plus-circle me-2"></i>
                        <strong>Registro criado:</strong> Um novo registro do tipo "{{ $log->model_name }}" foi criado.
                    </div>
                </div>
            </div>
            @elseif($log->action === 'delete')
            <div class="row mt-4">
                <div class="col-12">
                    <div class="alert alert-danger">
                        <i class="fas fa-trash me-2"></i>
                        <strong>Registro excluído:</strong> O registro do tipo "{{ $log->model_name }}" foi excluído.
                    </div>
                </div>
            </div>
            @elseif($log->action === 'restore')
            <div class="row mt-4">
                <div class="col-12">
                    <div class="alert alert-warning">
                        <i class="fas fa-undo me-2"></i>
                        <strong>Registro restaurado:</strong> O registro do tipo "{{ $log->model_name }}" foi restaurado.
                    </div>
                </div>
            </div>
            @else
            <div class="row mt-4">
                <div class="col-12">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        Nenhuma alteração detalhada disponível para este log.
                    </div>
                </div>
            </div>
            @endif

            <!-- Raw Data para debug -->
            @if(config('app.debug') && $log->changes)
            <div class="row mt-4">
                <div class="col-12">
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
    font-family: monospace;
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
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Função para copiar texto
    function copyToClipboard(text) {
        navigator.clipboard.writeText(text).then(() => {
            // Mostrar feedback visual (opcional)
            console.log('Texto copiado: ', text);
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
@endsection

<?php
// Helper function para formatar valores na view
if (!function_exists('formatChangeValue')) {
    function formatChangeValue($value) {
        if (is_null($value)) {
            return '<span class="text-muted fst-italic">Nulo</span>';
        }
        
        if ($value === '') {
            return '<span class="text-muted fst-italic">Vazio</span>';
        }
        
        // Se for um array ou objeto JSON, formatar bonito
        if (is_array($value) || (is_string($value) && is_json($value))) {
            $json = is_string($value) ? json_decode($value, true) : $value;
            $formatted = json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            return '<pre class="mb-0 long-text">' . e($formatted) . '</pre>';
        }
        
        // Se for muito longo, usar textarea
        if (is_string($value) && strlen($value) > 150) {
            return '<textarea class="form-control" rows="4" readonly>' . e($value) . '</textarea>';
        }
        
        return e($value);
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