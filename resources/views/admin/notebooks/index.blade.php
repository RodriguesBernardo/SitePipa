@extends('layouts.app')


@section('title', 'Controle de Notebooks')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1>Gerenciar Notebooks</h1>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#adicionarNotebook">
                    Adicionar Notebook
                </button>
            </div>
            <div class="row">
                @foreach($notebooks as $notebook)
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h5 class="card-title">{{ $notebook->identificador }}</h5>
                                <span class="badge bg-{{ $notebook->estaOnline ? 'success' : 'secondary' }}">
                                    {{ $notebook->estaOnline ? 'Online' : 'Offline' }}
                                </span>
                            </div>
                            
                            <p class="card-text">
                                <small class="text-muted">
                                    <strong>Usuário:</strong> {{ $notebook->usuario_atual ?? 'Nenhum' }}<br>
                                    <strong>IP:</strong> {{ $notebook->ip_address ?? 'N/A' }}<br>
                                    <strong>Hostname:</strong> {{ $notebook->hostname ?? 'N/A' }}<br>
                                    <strong>Último login:</strong> {{ $notebook->ultimo_login ? $notebook->ultimo_login->format('d/m/Y H:i') : 'Nunca' }}<br>
                                    <strong>Último heartbeat:</strong> {{ $notebook->ultimo_heartbeat ? $notebook->ultimo_heartbeat->format('d/m/Y H:i') : 'Nunca' }}
                                </small>
                            </p>
                            
                            <div class="btn-group w-100">
                                <a href="{{ route('admin.notebooks.show', $notebook->id) }}" class="btn btn-outline-primary btn-sm">
                                    Detalhes
                                </a>
                                <button class="btn btn-outline-success btn-sm" data-bs-toggle="modal" data-bs-target="#comandoModal{{ $notebook->id }}">
                                    Comando
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal Comando -->
                <div class="modal fade" id="comandoModal{{ $notebook->id }}" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <form action="{{ route('admin.notebooks.comando', $notebook->id) }}" method="POST">
                                @csrf
                                <div class="modal-header">
                                    <h5 class="modal-title">Enviar Comando - {{ $notebook->identificador }}</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label class="form-label">Ação:</label>
                                        <select name="acao" class="form-select" required>
                                            <option value="screenshot">Capturar Tela</option>
                                            <option value="webcam">Capturar Webcam</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                    <button type="submit" class="btn btn-primary">Enviar Comando</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<!-- Modal Adicionar Notebook -->
<div class="modal fade" id="adicionarNotebook" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.notebooks.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Adicionar Novo Notebook</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Identificador único:</label>
                        <input type="text" name="identificador" class="form-control" required 
                               placeholder="Ex: pipa-notebook-01">
                        <div class="form-text">Este ID deve ser único e será usado no script Python</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Adicionar</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection