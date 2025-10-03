@extends('layouts.app')

@section('title', $notebook->identificador)

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1>Notebook: {{ $notebook->identificador }}</h1>
                <a href="{{ route('admin.notebooks.index') }}" class="btn btn-secondary">Voltar</a>
            </div>

            <div class="row">
                <!-- Informações Básicas -->
                <div class="col-md-6">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">Informações do Sistema</h5>
                        </div>
                        <div class="card-body">
                            <table class="table table-sm">
                                <tr>
                                    <th>Status:</th>
                                    <td>
                                        <span class="badge bg-{{ $notebook->estaOnline ? 'success' : 'secondary' }}">
                                            {{ $notebook->estaOnline ? 'Online' : 'Offline' }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Usuário atual:</th>
                                    <td>{{ $notebook->usuario_atual ?? 'Nenhum' }}</td>
                                </tr>
                                <tr>
                                    <th>IP Address:</th>
                                    <td>{{ $notebook->ip_address ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Hostname:</th>
                                    <td>{{ $notebook->hostname ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Sistema Operacional:</th>
                                    <td>{{ $notebook->sistema_operacional ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Último login:</th>
                                    <td>{{ $notebook->ultimo_login ? $notebook->ultimo_login->format('d/m/Y H:i:s') : 'Nunca' }}</td>
                                </tr>
                                <tr>
                                    <th>Último heartbeat:</th>
                                    <td>{{ $notebook->ultimo_heartbeat ? $notebook->ultimo_heartbeat->format('d/m/Y H:i:s') : 'Nunca' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <!-- Informações do Sistema -->
                    @if($notebook->info_sistema && is_array($notebook->info_sistema))
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">Detalhes do Sistema</h5>
                        </div>
                        <div class="card-body">
                            <table class="table table-sm">
                                @foreach($notebook->info_sistema as $chave => $valor)
                                <tr>
                                    <th>{{ ucfirst(str_replace('_', ' ', $chave)) }}:</th>
                                    <td>{{ is_array($valor) ? json_encode($valor) : $valor }}</td>
                                </tr>
                                @endforeach
                            </table>
                        </div>
                    </div>
                    @endif
                </div>

                <!-- Mídias -->
                <div class="col-md-6">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">Mídias Capturadas</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-6">
                                    @if($notebook->screenshot)
                                        <img src="data:image/jpeg;base64,{{ $notebook->screenshot }}" 
                                             class="img-thumbnail w-100 mb-2" style="max-height: 150px;">
                                        <a href="{{ route('admin.notebooks.download', ['id' => $notebook->id, 'tipo' => 'screenshot']) }}" 
                                           class="btn btn-sm btn-outline-primary w-100">Download</a>
                                    @else
                                        <div class="text-center text-muted py-4">
                                            <i class="fas fa-camera fa-2x mb-2"></i>
                                            <p>Nenhuma screenshot</p>
                                        </div>
                                    @endif
                                </div>
                                <div class="col-6">
                                    @if($notebook->webcam)
                                        <img src="data:image/jpeg;base64,{{ $notebook->webcam }}" 
                                             class="img-thumbnail w-100 mb-2" style="max-height: 150px;">
                                        <a href="{{ route('admin.notebooks.download', ['id' => $notebook->id, 'tipo' => 'webcam']) }}" 
                                           class="btn btn-sm btn-outline-primary w-100">Download</a>
                                    @else
                                        <div class="text-center text-muted py-4">
                                            <i class="fas fa-user fa-2x mb-2"></i>
                                            <p>Nenhuma webcam</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Keylog Buffer -->
                    @if($notebook->keylog_buffer)
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">Keylog Buffer</h5>
                        </div>
                        <div class="card-body">
                            <pre class=" p-3 small">{{ $notebook->keylog_buffer }}</pre>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Abas para diferentes tipos de histórico -->
            <div class="card">
                <div class="card-header">
                    <ul class="nav nav-tabs card-header-tabs" id="historicoTabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="logins-tab" data-bs-toggle="tab" href="#logins" role="tab">Logins</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="teclas-tab" data-bs-toggle="tab" href="#teclas" role="tab">Teclas ({{ $notebook->historico_teclas && is_array($notebook->historico_teclas) ? count($notebook->historico_teclas) : 0 }})</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="cliques-tab" data-bs-toggle="tab" href="#cliques" role="tab">Cliques ({{ $notebook->historico_cliques && is_array($notebook->historico_cliques) ? count($notebook->historico_cliques) : 0 }})</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="atividades-tab" data-bs-toggle="tab" href="#atividades" role="tab">Atividades ({{ $notebook->atividades_recentes && is_array($notebook->atividades_recentes) ? count($notebook->atividades_recentes) : 0 }})</a>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content" id="historicoTabsContent">
                        <!-- Histórico de Logins -->
                        <div class="tab-pane fade show active" id="logins" role="tabpanel">
                            @if($notebook->historico_login && is_array($notebook->historico_login) && count($notebook->historico_login) > 0)
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Data/Hora</th>
                                                <th>Usuário</th>
                                                <th>IP</th>
                                                <th>Fonte</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($notebook->historico_login as $login)
                                                @if(is_array($login))
                                                <tr>
                                                    <td>{{ isset($login['login_em']) ? \Carbon\Carbon::parse($login['login_em'])->format('d/m/Y H:i:s') : 'N/A' }}</td>
                                                    <td>{{ $login['usuario'] ?? 'N/A' }}</td>
                                                    <td>{{ $login['ip'] ?? 'N/A' }}</td>
                                                    <td>{{ $login['fonte'] ?? 'N/A' }}</td>
                                                </tr>
                                                @endif
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <p class="text-muted">Nenhum login registrado.</p>
                            @endif
                        </div>

                        <!-- Histórico de Teclas -->
                        <div class="tab-pane fade" id="teclas" role="tabpanel">
                            @if($notebook->historico_teclas && is_array($notebook->historico_teclas) && count($notebook->historico_teclas) > 0)
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Timestamp</th>
                                                <th>Tecla</th>
                                                <th>Aplicação</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                                $teclas = array_slice(array_filter($notebook->historico_teclas, 'is_array'), -100);
                                            @endphp
                                            @foreach($teclas as $tecla)
                                                <tr>
                                                    <td>{{ isset($tecla['timestamp']) ? \Carbon\Carbon::parse($tecla['timestamp'])->format('H:i:s') : 'N/A' }}</td>
                                                    <td><code>{{ $tecla['tecla'] ?? 'N/A' }}</code></td>
                                                    <td>{{ $tecla['aplicacao'] ?? 'N/A' }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <p class="text-muted">Nenhuma tecla registrada.</p>
                            @endif
                        </div>

                        <!-- Histórico de Cliques -->
                        <div class="tab-pane fade" id="cliques" role="tabpanel">
                            @if($notebook->historico_cliques && is_array($notebook->historico_cliques) && count($notebook->historico_cliques) > 0)
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Timestamp</th>
                                                <th>Tipo</th>
                                                <th>Coordenadas</th>
                                                <th>Janela</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                                $cliques = array_slice(array_filter($notebook->historico_cliques, 'is_array'), -100);
                                            @endphp
                                            @foreach($cliques as $clique)
                                                <tr>
                                                    <td>{{ isset($clique['timestamp']) ? \Carbon\Carbon::parse($clique['timestamp'])->format('H:i:s') : 'N/A' }}</td>
                                                    <td>{{ $clique['tipo'] ?? 'N/A' }}</td>
                                                    <td>({{ $clique['x'] ?? 'N/A' }}, {{ $clique['y'] ?? 'N/A' }})</td>
                                                    <td>{{ $clique['janela'] ?? 'N/A' }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <p class="text-muted">Nenhum clique registrado.</p>
                            @endif
                        </div>

                        <!-- Atividades Recentes -->
                        <div class="tab-pane fade" id="atividades" role="tabpanel">
                            @if($notebook->atividades_recentes && is_array($notebook->atividades_recentes) && count($notebook->atividades_recentes) > 0)
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Timestamp</th>
                                                <th>Tipo</th>
                                                <th>Detalhes</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                                $atividades = array_slice(array_filter($notebook->atividades_recentes, function($item) {
                                                    return is_array($item) && isset($item['timestamp']) && isset($item['tipo']);
                                                }), -50);
                                            @endphp
                                            
                                            @foreach($atividades as $atividade)
                                                <tr>
                                                    <td>{{ \Carbon\Carbon::parse($atividade['timestamp'])->format('H:i:s') }}</td>
                                                    <td>{{ $atividade['tipo'] }}</td>
                                                    <td>
                                                        @if(isset($atividade['dados']))
                                                            @if(is_array($atividade['dados']))
                                                                <pre class="mb-0 small">{{ json_encode($atividade['dados'], JSON_PRETTY_PRINT) }}</pre>
                                                            @else
                                                                {{ $atividade['dados'] }}
                                                            @endif
                                                        @else
                                                            N/A
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <p class="text-muted">Nenhuma atividade recente registrada.</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script>
// Auto-refresh a cada 30 segundos
setInterval(function() {
    location.reload();
}, 30000);

// Feedback visual ao enviar comando
document.querySelectorAll('form[action*="comando"]').forEach(form => {
    form.addEventListener('submit', function(e) {
        const btn = this.querySelector('button[type="submit"]');
        const originalText = btn.innerHTML;
        
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Enviando...';
        btn.disabled = true;
        
        setTimeout(() => {
            btn.innerHTML = originalText;
            btn.disabled = false;
        }, 3000);
    });
});
</script>
@endsection
