@extends('layouts.app')

@section('title', 'Logs de Atividade')
@section('content')
<div class="container-fluid">
    
    <!-- Filtros -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-filter"></i> Filtros</h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.logs.filter') }}">
                <div class="row">
                    <div class="col-md-3">
                        <label class="form-label">Usuário</label>
                        <select name="user_id" class="form-select">
                            <option value="">Todos os usuários</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }} ({{ $user->email }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Ação</label>
                        <select name="action" class="form-select">
                            <option value="">Todas as ações</option>
                            <option value="create" {{ request('action') == 'create' ? 'selected' : '' }}>Criação</option>
                            <option value="update" {{ request('action') == 'update' ? 'selected' : '' }}>Edição</option>
                            <option value="delete" {{ request('action') == 'delete' ? 'selected' : '' }}>Exclusão</option>
                            <option value="restore" {{ request('action') == 'restore' ? 'selected' : '' }}>Restauração</option>
                            <option value="download" {{ request('action') == 'download' ? 'selected' : '' }}>Download</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Modelo</label>
                        <select name="model_type" class="form-select">
                            <option value="">Todos os modelos</option>
                            <option value="App\Models\Game" {{ request('model_type') == 'App\Models\Game' ? 'selected' : '' }}>Jogo</option>
                            <option value="App\Models\News" {{ request('model_type') == 'App\Models\News' ? 'selected' : '' }}>Notícia</option>
                            <option value="App\Models\User" {{ request('model_type') == 'App\Models\User' ? 'selected' : '' }}>Usuário</option>
                            <option value="App\Models\HelpContent" {{ request('model_type') == 'App\Models\HelpContent' ? 'selected' : '' }}>Conteúdo de Ajuda</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Data Início</label>
                        <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Data Fim</label>
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
                        
                        @if($logs->count() > 0)
                        <span class="ms-2 text-muted">
                            {{ $logs->total() }} registro(s) encontrado(s)
                        </span>
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabela de logs -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-history"></i> Histórico de Atividades</h5>
        </div>
        <div class="card-body">
            @if($logs->total() > 0)
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>Data/Hora</th>
                            <th>Usuário</th>
                            <th>Ação</th>
                            <th>Modelo</th>
                            <th>ID</th>
                            <th>Detalhes</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($logs as $log)
                        <tr>
                            <td>
                                <span class="fw-bold">{{ $log->created_at->format('d/m/Y') }}</span>
                                <br>
                                <small class="text-muted">{{ $log->created_at->format('H:i:s') }}</small>
                            </td>
                            <td>
                                @if($log->user)
                                    <div>{{ $log->user->name }}</div>
                                    <small class="text-muted">{{ $log->user->email }}</small>
                                @else
                                    <span class="text-muted fst-italic">Usuário não encontrado</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-{{ $log->action_color }} p-2">
                                    <i class="fas fa-{{ $log->action_icon }} me-1"></i>
                                    {{ $log->action_name }}
                                </span>
                            </td>
                            <td>{{ $log->model_name }}</td>
                            <td>
                                @if($log->model_id)
                                    <span class="badge bg-light text-dark">#{{ $log->model_id }}</span>
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </td>
                            <td>
                                @if($log->changes && $log->action === 'update')
                                    <small>
                                        {{ count($log->changes) }} campo(s) alterado(s)
                                    </small>
                                @elseif($log->action === 'create')
                                    <small class="text-success">Registro criado</small>
                                @elseif($log->action === 'delete')
                                    <small class="text-danger">Registro excluído</small>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('admin.logs.show', $log) }}" class="btn btn-sm btn-info" title="Detalhes">
                                    <i class="fas fa-eye"></i> Ver detalhes
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
            <div class="alert alert-info text-center py-4">
                <i class="fas fa-info-circle fa-2x mb-3"></i>
                <h4>Nenhum log de atividade encontrado</h4>
                <p class="mb-0">Tente ajustar os filtros para ver mais resultados.</p>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection