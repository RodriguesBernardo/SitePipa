@extends('layouts.app')

@section('title', 'Visualização de Logs')
@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-6">
            <h1>Detalhes do Log</h1>
        </div>
        <div class="col-md-6 text-end">
            <a href="{{ route('admin.logs.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Voltar para a lista
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h5>Informações Básicas</h5>
                    <table class="table table-bordered">
                        <tr>
                            <th width="30%">ID:</th>
                            <td>{{ $log->id }}</td>
                        </tr>
                        <tr>
                            <th>Data/Hora:</th>
                            <td>{{ $log->created_at->format('d/m/Y H:i:s') }}</td>
                        </tr>
                        <tr>
                            <th>Usuário:</th>
                            <td>
                                @if($log->user)
                                    {{ $log->user->name }} ({{ $log->user->email }})
                                @else
                                    Usuário não encontrado
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Ação:</th>
                            <td>
                                <span class="badge bg-{{ $log->action_color }}">
                                    {{ $log->action_name }}
                                </span>
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <h5>Detalhes da Ação</h5>
                    <table class="table table-bordered">
                        <tr>
                            <th width="30%">Modelo:</th>
                            <td>{{ $log->model_name }}</td>
                        </tr>
                        <tr>
                            <th>ID do Modelo:</th>
                            <td>{{ $log->model_id ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>IP:</th>
                            <td>{{ $log->ip_address }}</td>
                        </tr>
                        <tr>
                            <th>User Agent:</th>
                            <td>{{ $log->user_agent }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            @if($log->description)
            <div class="row mt-4">
                <div class="col-12">
                    <h5>Descrição</h5>
                    <div class="alert alert-info">
                        {{ $log->description }}
                    </div>
                </div>
            </div>
            @endif

            @if($log->changes && is_array($log->changes))
            <div class="row mt-4">
                <div class="col-12">
                    <h5>Alterações Realizadas</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Campo</th>
                                    <th>Valor Anterior</th>
                                    <th>Novo Valor</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($log->changes as $field => $change)
                                    @if(is_array($change) && isset($change['old']) && isset($change['new']))
                                    <tr>
                                        <td width="30%"><strong>{{ $field }}</strong></td>
                                        <td width="35%">
                                            @if(is_array($change['old']))
                                                <pre>{{ json_encode($change['old'], JSON_PRETTY_PRINT) }}</pre>
                                            @else
                                                {{ $change['old'] ?? 'N/A' }}
                                            @endif
                                        </td>
                                        <td width="35%">
                                            @if(is_array($change['new']))
                                                <pre>{{ json_encode($change['new'], JSON_PRETTY_PRINT) }}</pre>
                                            @else
                                                {{ $change['new'] ?? 'N/A' }}
                                            @endif
                                        </td>
                                    </tr>
                                    @else
                                    <tr>
                                        <td width="30%"><strong>{{ $field }}</strong></td>
                                        <td colspan="2">
                                            <pre>{{ json_encode($change, JSON_PRETTY_PRINT) }}</pre>
                                        </td>
                                    </tr>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection