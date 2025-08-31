@extends('layouts.app')

@section('title', 'Painel Administrativo')

@section('content')
	

<div class="container-fluid py-4 admin-dashboard">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1 fw-bold">Painel Administrativo</h1>
            <p class="text-muted mb-0">Bem-vindo, {{ auth()->user()->name }}! Gerencie todo o conteúdo do sistema.</p>
        </div>
        
        @if($user->is_admin || $user->permissions)
        <div class="d-flex">
            <div class="dropdown me-2">
                <button class="btn btn-outline-primary dropdown-toggle" type="button" id="quickActionsDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-lightning-charge me-1"></i>Ações Rápidas
                </button>
                <ul class="dropdown-menu" aria-labelledby="quickActionsDropdown">
                    @if($user->is_admin || $user->hasPermission('create_games'))
                    <li><a class="dropdown-item" href="{{ route('admin.games.create') }}"><i class="bi bi-joystick me-2"></i>Adicionar Jogo</a></li>
                    @endif
                    
                    @if($user->is_admin || $user->hasPermission('create_news'))
                    <li><a class="dropdown-item" href="{{ route('admin.news.create') }}"><i class="bi bi-newspaper me-2"></i>Adicionar Notícia</a></li>
                    @endif
                    
                    @if($user->is_admin)
                    <li><a class="dropdown-item" href="{{ route('admin.users.create') }}"><i class="bi bi-person-plus me-2"></i>Adicionar Usuário</a></li>
                    @endif
                    
                    @if($user->is_admin || $user->hasPermission('edit_help'))
                    <li><a class="dropdown-item" href="{{ route('admin.help.edit') }}"><i class="bi bi-pencil me-2"></i>Editar Ajuda</a></li>
                    @endif
                </ul>
            </div>
        </div>
        @endif
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4 g-4">
        @if($user->is_admin || $user->hasPermission('edit_games') || $user->hasPermission('create_games'))
        <div class="col-xl-4 col-md-6">
            <div class="card stat-card h-100 border-0 shadow-sm card-hover">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="d-flex align-items-center">
                            <div class="bg-primary bg-opacity-10 p-3 rounded-3 me-3">
                                <i class="bi bi-joystick fs-4 text-primary"></i>
                            </div>
                            <div>
                                <h6 class="card-title mb-1 text-muted">Total de Jogos</h6>
                                <h2 class="fw-bold mb-0">{{ $gamesCount }}</h2>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mt-4">
                        <a href="{{ route('admin.games.index') }}" class="btn btn-sm btn-primary stretched-link">
                            <i class="bi bi-gear me-1"></i>Gerenciar
                        </a>
                        <div class="text-end">
                            <span class="badge bg-primary bg-opacity-10 text-primary">{{ $activeGamesCount }} ativos</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif
        
        @if($user->is_admin || $user->hasPermission('edit_news') || $user->hasPermission('create_news'))
        <div class="col-xl-4 col-md-6">
            <div class="card stat-card h-100 border-0 shadow-sm card-hover">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="d-flex align-items-center">
                            <div class="bg-info bg-opacity-10 p-3 rounded-3 me-3">
                                <i class="bi bi-newspaper fs-4 text-info"></i>
                            </div>
                            <div>
                                <h6 class="card-title mb-1 text-muted">Total de Notícias</h6>
                                <h2 class="fw-bold mb-0">{{ $newsCount }}</h2>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mt-4">
                        <a href="{{ route('admin.news.index') }}" class="btn btn-sm btn-info text-white stretched-link">
                            <i class="bi bi-gear me-1"></i>Gerenciar
                        </a>
                        <div class="text-end">
                            <span class="badge bg-info bg-opacity-10 text-info">{{ $recentNewsCount }} recentes</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif
        
        @if($user->is_admin)
        <div class="col-xl-4 col-md-6">
            <div class="card stat-card h-100 border-0 shadow-sm card-hover">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="d-flex align-items-center">
                            <div class="bg-success bg-opacity-10 p-3 rounded-3 me-3">
                                <i class="bi bi-people fs-4 text-success"></i>
                            </div>
                            <div>
                                <h6 class="card-title mb-1 text-muted">Total de Usuários</h6>
                                <h2 class="fw-bold mb-0">{{ $usersCount }}</h2>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mt-4">
                        <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-success stretched-link">
                            <i class="bi bi-gear me-1"></i>Gerenciar
                        </a>
                        <div class="text-end">
                            <span class="badge bg-success bg-opacity-10 text-success">{{ $adminUsersCount }} administradores</span>
                            <span class="badge bg-opacity-10 text-warning ms-1">{{ $blockedUsersCount }} bloqueados</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>

    <!-- Main Content Area -->
    <div class="row g-4">
        <!-- Left Column -->
        <div class="col-lg-8">
            <!-- Recent Activity -->
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header py-3 border-bottom">
                    <h5 class="mb-0 fw-bold">
                        <i class="bi bi-clock-history me-2 text-primary"></i>Atividade Recente
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @php
                            // Combinar todas as atividades em uma única coleção
                            $allActivities = collect();
                            
                            // Adicionar jogos se tiver permissão
                            if($user->is_admin || $user->hasPermission('edit_games') || $user->hasPermission('create_games')) {
                                foreach($recentGames as $game) {
                                    $allActivities->push([
                                        'type' => 'game',
                                        'item' => $game,
                                        'updated_at' => $game->updated_at
                                    ]);
                                }
                            }
                            
                            // Adicionar notícias se tiver permissão
                            if($user->is_admin || $user->hasPermission('edit_news') || $user->hasPermission('create_news')) {
                                foreach($recentNews as $news) {
                                    $allActivities->push([
                                        'type' => 'news',
                                        'item' => $news,
                                        'updated_at' => $news->updated_at
                                    ]);
                                }
                            }
                            
                            // Adicionar usuários se for admin
                            if($user->is_admin) {
                                foreach($recentUsers as $userItem) {
                                    $allActivities->push([
                                        'type' => 'user',
                                        'item' => $userItem,
                                        'updated_at' => $userItem->updated_at
                                    ]);
                                }
                            }
                            
                            // Ordenar por data de atualização (mais recente primeiro)
                            $allActivities = $allActivities->sortByDesc('updated_at')->take(10);
                        @endphp
                        
                        @if($allActivities->count() > 0)
                            @foreach($allActivities as $activity)
                                @if($activity['type'] == 'game')
                                    @php $game = $activity['item']; @endphp
                                    <div class="list-group-item border-0 py-3">
                                        <div class="d-flex align-items-center">
                                            <div class="bg-primary bg-opacity-10 p-2 rounded-3 me-3">
                                                <i class="bi bi-joystick text-primary"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-0">Jogo {{ $game->trashed() ? 'desativado' : ($game->created_at->diffInHours() < 24 ? 'adicionado' : 'atualizado') }}</h6>
                                                <p class="text-muted mb-0 small">{{ $game->title }}</p>
                                            </div>
                                            <div class="text-muted small">{{ $game->updated_at->diffForHumans(now(), true) }} atrás</div>
                                        </div>
                                    </div>
                                @elseif($activity['type'] == 'news')
                                    @php $news = $activity['item']; @endphp
                                    <div class="list-group-item border-0 py-3">
                                        <div class="d-flex align-items-center">
                                            <div class="bg-info bg-opacity-10 p-2 rounded-3 me-3">
                                                <i class="bi bi-newspaper text-info"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-0">Notícia {{ $news->created_at->diffInHours() < 24 ? 'publicada' : 'atualizada' }}</h6>
                                                <p class="text-muted mb-0 small">{{ $news->title }}</p>
                                            </div>
                                            <div class="text-muted small">{{ $news->updated_at->diffForHumans(now(), true) }} atrás</div>
                                        </div>
                                    </div>
                                @elseif($activity['type'] == 'user')
                                    @php $userItem = $activity['item']; @endphp
                                    <div class="list-group-item border-0 py-3">
                                        <div class="d-flex align-items-center">
                                            <div class="bg-success bg-opacity-10 p-2 rounded-3 me-3">
                                                <i class="bi bi-person-plus text-success"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-0">Usuário {{ $userItem->created_at->diffInHours() < 24 ? 'registrado' : 'atualizado' }}</h6>
                                                <p class="text-muted mb-0 small">{{ $userItem->name }} ({{ $userItem->email }})</p>
                                            </div>
                                            <div class="text-muted small">{{ $userItem->updated_at->diffForHumans(now(), true) }} atrás</div>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        @else
                            <div class="list-group-item border-0 py-3 text-center text-muted">
                                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                Nenhuma atividade recente
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Right Column -->
        <div class="col-lg-4">
            @if($user->is_admin || $user->hasPermission('edit_help'))
            <!-- Help Card with Highlight -->
            <div class="card border-0 shadow-sm mb-4 bg-opacity-5 border-warning">
                <div class="card-header bg-transparent border-warning py-3">
                    <h5 class="mb-0 fw-bold text-warning">
                        <i class="bi bi-question-circle me-2"></i>Conteúdo de Ajuda
                    </h5>
                </div>
                <div class="card-body">
                    <p class="card-text">Atualize o conteúdo da página de ajuda para coordenadores, estagiários e uso de máquinas.</p>
                    <a href="{{ route('admin.help.edit') }}" class="btn btn-warning btn-sm w-100">
                        <i class="bi bi-pencil me-1"></i>Editar Conteúdo de Ajuda
                    </a>
                </div>
            </div>
            @endif
            
            <!-- Quick Stats -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header py-3 border-bottom">
                    <h5 class="mb-0 fw-bold">
                        <i class="bi bi-graph-up me-2 text-success"></i>Estatísticas Rápidas
                    </h5>
                </div>
                <div class="card-body">
                    @if($user->is_admin || $user->hasPermission('edit_games') || $user->hasPermission('create_games'))
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-joystick me-2 text-primary"></i>
                            <span>Jogos ativos</span>
                        </div>
                        <span class="fw-bold">{{ $activeGamesCount }}</span>
                    </div>
                    @endif
                    
                    @if($user->is_admin || $user->hasPermission('edit_news') || $user->hasPermission('create_news'))
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-calendar-week me-2 text-info"></i>
                            <span>Notícias esta semana</span>
                        </div>
                        <span class="fw-bold">{{ $recentNewsCount }}</span>
                    </div>
                    @endif
                    
                    @if($user->is_admin)
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-shield-check me-2 text-success"></i>
                            <span>Administradores</span>
                        </div>
                        <span class="fw-bold">{{ $adminUsersCount }}</span>
                    </div>
                    @endif
                </div>
            </div>
            
            <!-- System Status -->
            <div class="card border-0 shadow-sm">
                <div class="card-header py-3 border-bottom">
                    <h5 class="mb-0 fw-bold">
                        <i class="bi bi-hdd-stack me-2 text-secondary"></i>Status do Sistema
                    </h5>
                </div>
                <div class="card-body">
                    @if($user->is_admin || $user->hasPermission('edit_games') || $user->hasPermission('create_games'))
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span>Jogos cadastrados</span>
                        <span class="fw-bold">{{ $gamesCount }}</span>
                    </div>
                    @endif
                    
                    @if($user->is_admin || $user->hasPermission('edit_news') || $user->hasPermission('create_news'))
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span>Notícias publicadas</span>
                        <span class="fw-bold">{{ $newsCount }}</span>
                    </div>
                    @endif
                    
                    @if($user->is_admin)
                    <div class="d-flex justify-content-between align-items-center">
                        <span>Usuários registrados</span>
                        <span class="fw-bold">{{ $usersCount }}</span>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.admin-dashboard {
}

.card {
    border-radius: 0.75rem;
    transition: all 0.3s ease;
}

.card-hover:hover {
    transform: translateY(-5px);
    box-shadow: 0 1rem 3rem rgba(0, 0, 0, 0.1) !important;
}

.icon-wrapper {
    transition: all 0.3s ease;
}

.stat-card:hover .icon-wrapper {
    transform: scale(1.1);
}

.list-group-item {
    transition: all 0.2s ease;
}

.list-group-item:hover {
}

.badge {
    font-size: 0.7rem;
    padding: 0.35em 0.65em;
}

.bg-opacity-10 {
    --bs-bg-opacity: 0.1;
}
</style>

<!-- Adicionando biblioteca para traduzir as datas -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/locale/pt-br.min.js"></script>
<script>
    // Configurar moment.js para português
    moment.locale('pt-br');
    
    // Função para converter todas as datas para português
    document.addEventListener('DOMContentLoaded', function() {
        const timeElements = document.querySelectorAll('.text-muted.small:last-child');
        
        timeElements.forEach(element => {
            const text = element.textContent.trim();
            
            // Traduções manuais para português
            let translatedText = text
                .replace('seconds', 'segundos')
                .replace('second', 'segundo')
                .replace('minutes', 'minutos')
                .replace('minute', 'minuto')
                .replace('hours', 'horas')
                .replace('hour', 'hora')
                .replace('days', 'dias')
                .replace('day', 'dia')
                .replace('weeks', 'semanas')
                .replace('week', 'semana')
                .replace('months', 'meses')
                .replace('month', 'mês')
                .replace('years', 'anos')
                .replace('year', 'ano')
                .replace('ago', 'atrás')
                .replace('from now', 'a partir de agora');
                
            element.textContent = translatedText;
        });
    });
</script>
@endsection