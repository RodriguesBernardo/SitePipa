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
                                    <span class="badge">#{{ $log->model_id }}</span>
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
                            <td>{{ $log->ip_address }}</td>
                        </tr>
                        <tr>
                            <th>User Agent:</th>
                            <td>
                                <small>{{ $log->user_agent }}</small>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            @if(!empty($log->formatted_changes))
            <div class="row mt-4">
                <div class="col-12">
                    <h5 class="border-bottom pb-2">Alterações Realizadas</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead class="">
                                <tr>
                                    <th width="25%">Campo</th>
                                    <th width="35%">Valor Anterior</th>
                                    <th width="35%">Novo Valor</th>
                                    <th width="5%">Tipo</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($log->formatted_changes as $change)
                                <tr>
                                    <td><strong>{{ $change['field'] }}</strong></td>
                                    <td>
                                        @if(isset($change['old']))
                                            <div class="p-2 rounded">
                                                @if(is_string($change['old']) && strlen($change['old']) > 100)
                                                    <textarea class="form-control" rows="3" readonly>{{ $change['old'] }}</textarea>
                                                @else
                                                    {{ $change['old'] }}
                                                @endif
                                            </div>
                                        @else
                                            <span class="text-muted fst-italic">N/A</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if(isset($change['new']))
                                            <div class="p-2 rounded">
                                                @if(is_string($change['new']) && strlen($change['new']) > 100)
                                                    <textarea class="form-control" rows="3" readonly>{{ $change['new'] }}</textarea>
                                                @else
                                                    {{ $change['new'] }}
                                                @endif
                                            </div>
                                        @else
                                            <span class="text-muted fst-italic">N/A</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if(isset($change['old']) && isset($change['new']))
                                            @if($change['old'] === $change['new'])
                                                <span class="badge bg-secondary">=</span>
                                            @else
                                                <span class="badge bg-warning">→</span>
                                            @endif
                                        @else
                                            <span class="badge bg-info">+</span>
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

            <!-- Debug information (pode remover depois de testar) -->
            @if(config('app.debug'))
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h6 class="mb-0">Informações de Debug</h6>
                        </div>
                        <div class="card-body">
                            <p><strong>Changes raw:</strong> {{ json_encode($log->changes) }}</p>
                            <p><strong>Formatted changes:</strong> {{ json_encode($log->formatted_changes) }}</p>
                            <p><strong>Action:</strong> {{ $log->action }}</p>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<style>
.long-text {
    max-height: 100px;
    overflow-y: auto;
    font-family: monospace;
    font-size: 0.9em;
    white-space: pre-wrap;
}
</style>
@endsection