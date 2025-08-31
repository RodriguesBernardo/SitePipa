@extends('layouts.app')

@section('title', 'Logs')
@section('content')
<div class="container-fluid">
    <h1>Logs de Atividade</h1>
    
    <!-- Filtros -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.logs.filter') }}">
                <div class="row">
                    <div class="col-md-3">
                        <label>Usuário</label>
                        <select name="user_id" class="form-select">
                            <option value="">Todos os usuários</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label>Ação</label>
                        <select name="action" class="form-select">
                            <option value="">Todas as ações</option>
                            <option value="create" {{ request('action') == 'create' ? 'selected' : '' }}>Criar</option>
                            <option value="update" {{ request('action') == 'update' ? 'selected' : '' }}>Editar</option>
                            <option value="delete" {{ request('action') == 'delete' ? 'selected' : '' }}>Excluir</option>
                            <option value="restore" {{ request('action') == 'restore' ? 'selected' : '' }}>Restaurar</option>
                            <option value="download" {{ request('action') == 'download' ? 'selected' : '' }}>Download</option>
                            <option value="rate" {{ request('action') == 'rate' ? 'selected' : '' }}>Avaliar</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label>Modelo</label>
                        <select name="model_type" class="form-select">
                            <option value="">Todos os modelos</option>
                            <option value="App\Models\Game" {{ request('model_type') == 'App\Models\Game' ? 'selected' : '' }}>Jogo</option>
                            <option value="App\Models\News" {{ request('model_type') == 'App\Models\News' ? 'selected' : '' }}>Notícia</option>
                            <option value="App\Models\User" {{ request('model_type') == 'App\Models\User' ? 'selected' : '' }}>Usuário</option>
                            <option value="App\Models\HelpContent" {{ request('model_type') == 'App\Models\HelpContent' ? 'selected' : '' }}>Ajuda</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label>Data Início</label>
                        <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                    </div>
                    <div class="col-md-2">
                        <label>Data Fim</label>
                        <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-filter"></i> Filtrar
                        </button>
                        <a href="{{ route('admin.logs.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Limpar
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabela de logs -->
    <div class="card">
        <div class="card-body">
            @if($logs->total() > 0)
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Data/Hora</th>
                            <th>Usuário</th>
                            <th>Ação</th>
                            <th>Modelo</th>
                            <th>ID</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($logs as $log)
                        <tr>
                            <td>{{ $log->created_at->format('d/m/Y H:i') }}</td>
                            <td>
                                @if($log->user)
                                    {{ $log->user->name }}
                                @else
                                    <span class="text-muted">Usuário não encontrado</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-{{ $log->action_color }}">
                                    {{ $log->action_name }}
                                </span>
                            </td>
                            <td>{{ $log->model_name }}</td>
                            <td>{{ $log->model_id ?? 'N/A' }}</td>
                            <td>
                                <a href="{{ route('admin.logs.show', $log) }}" class="btn btn-sm btn-info" title="Detalhes">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-between align-items-center mt-3">
                <div>Mostrando {{ $logs->firstItem() }} a {{ $logs->lastItem() }} de {{ $logs->total() }} registros</div>
                <div>{{ $logs->links() }}</div>
            </div>
            @else
            <div class="alert alert-info text-center">
                <i class="fas fa-info-circle"></i> Nenhum log de atividade encontrado.
            </div>
            @endif
        </div>
    </div>
</div>
@endsection