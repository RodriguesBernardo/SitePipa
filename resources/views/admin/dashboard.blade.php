@extends('layouts.app')

@section('title', 'Painel Administrativo')

@section('content')
<div class="container-fluid py-4 admin-dashboard">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <p class="text-muted mb-0">Bem-vindo, {{ auth()->user()->name }}! Gerencie o conteúdo do sistema.</p>
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
        <div class="col-xl-3 col-md-6">
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
        <div class="col-xl-3 col-md-6">
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
        <div class="col-xl-3 col-md-6">
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
                            <span class="badge bg-warning bg-opacity-10-warning ms-1">{{ $blockedUsersCount }} bloqueados</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif
        
        @if($user->is_admin || $user->hasPermission('view_calendar'))
        <div class="col-xl-3 col-md-6">
            <div class="card stat-card h-100 border-0 shadow-sm card-hover">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="d-flex align-items-center">
                            <div class="bg-purple bg-opacity-10 p-3 rounded-3 me-3">
                                <i class="bi bi-calendar-event fs-4 text-purple"></i>
                            </div>
                            <div>
                                <h6 class="card-title mb-1 text-muted">Próximos Eventos</h6>
                                <h2 class="fw-bold mb-0">{{ $upcomingEvents->count() }}</h2>
                            </div>
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
            <!-- Próximos Eventos Section -->
            @if(($user->is_admin || $user->hasPermission('view_calendar')) && $upcomingEvents->count() > 0)
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header py-3 border-bottom d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold">
                        <i class="bi bi-calendar-event me-2 text-purple"></i>Próximos Eventos
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @foreach($upcomingEvents as $event)
                        <div class="list-group-item border-0 p-4">
                            <div class="d-flex align-items-start">
                                @if($event->color)
                                <div class="me-3" style="width: 8px; height: 40px; background-color: {{ $event->color }}; border-radius: 4px;"></div>
                                @else
                                <div class="me-3" style="width: 8px; height: 40px; background-color: #6c757d; border-radius: 4px;"></div>
                                @endif
                                <div class="flex-grow-1">
                                    <div class="d-flex w-100 justify-content-between align-items-start mb-2">
                                        <h6 class="mb-0 fw-bold">{{ $event->title }}</h6>
                                        <span class="badge bg-{{ $event->visibility === 'public' ? 'success' : 'secondary' }}">
                                            {{ $event->visibility === 'public' ? 'Público' : 'Privado' }}
                                        </span>
                                    </div>
                                    <p class="mb-2 text-muted small">{{ Str::limit($event->description, 100) }}</p>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <small class="text-muted">
                                            <i class="bi bi-clock me-1"></i>
                                            {{ \Carbon\Carbon::parse($event->start_date)->format('d/m/Y H:i') }}
                                        </small>
                                        <small class="text-muted">
                                            <i class="bi bi-person me-1"></i>
                                            Criado por: {{ $event->user->name }}
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @elseif($user->is_admin || $user->hasPermission('view_calendar'))
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header py-3 border-bottom">
                    <h5 class="mb-0 fw-bold">
                        <i class="bi bi-calendar-event me-2 text-purple"></i>Próximos Eventos
                    </h5>
                </div>
            </div>
            @endif
            
            <!-- Quick Actions -->
            <div class="row">
                @if($user->is_admin || $user->hasPermission('create_games'))
                <div class="col-md-6 mb-4">
                    <div class="card border-0 shadow-sm h-100 card-hover">
                        <div class="card-body text-center p-4">
                            <div class="bg-primary bg-opacity-10 p-3 rounded-circle d-inline-block mb-3">
                                <i class="bi bi-joystick fs-2 text-primary"></i>
                            </div>
                            <h5 class="card-title">Gerenciar Jogos</h5>
                            <p class="card-text text-muted">Adicione, edite ou remova jogos do catálogo.</p>
                            <a href="{{ route('admin.games.index') }}" class="btn btn-primary stretched-link">
                                <i class="bi bi-gear me-1"></i>Gerenciar Jogos
                            </a>
                        </div>
                    </div>
                </div>
                @endif
                
                @if($user->is_admin || $user->hasPermission('create_news'))
                <div class="col-md-6 mb-4">
                    <div class="card border-0 shadow-sm h-100 card-hover">
                        <div class="card-body text-center p-4">
                            <div class="bg-info bg-opacity-10 p-3 rounded-circle d-inline-block mb-3">
                                <i class="bi bi-newspaper fs-2 text-info"></i>
                            </div>
                            <h5 class="card-title">Gerenciar Notícias</h5>
                            <p class="card-text text-muted">Crie e publique notícias para os usuários.</p>
                            <a href="{{ route('admin.news.index') }}" class="btn btn-info text-white stretched-link">
                                <i class="bi bi-gear me-1"></i>Gerenciar Notícias
                            </a>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
        
        <!-- Right Column -->
        <div class="col-lg-4">
            @if($user->is_admin || $user->hasPermission('edit_help'))
            <!-- Help Card with Highlight -->
            <div class="card border-0 shadow-sm mb-4 bg-warning bg-opacity-5 border-warning">
                <div class="card-header bg-transparent border-warning py-3">
                    <h5 class="mb-0 fw-bold">
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
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-archive me-2 text-secondary"></i>
                            <span>Jogos excluídos</span>
                        </div>
                        <span class="fw-bold">{{ $deletedGamesCount }}</span>
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
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-shield-check me-2 text-success"></i>
                            <span>Administradores</span>
                        </div>
                        <span class="fw-bold">{{ $adminUsersCount }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-person-x me-2 text-danger"></i>
                            <span>Usuários bloqueados</span>
                        </div>
                        <span class="fw-bold">{{ $blockedUsersCount }}</span>
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
    border-bottom: 1px solid #eee;
}

.list-group-item:last-child {
    border-bottom: none;
}

.list-group-item:hover {
    background-color: #f8f9fa;
}

.badge {
    font-size: 0.7rem;
    padding: 0.35em 0.65em;
}

.bg-opacity-10 {
    --bs-bg-opacity: 0.1;
}

.bg-purple {
    background-color: #6f42c1 !important;
}

.text-purple {
    color: #6f42c1 !important;
}

.btn-purple {
    background-color: #6f42c1;
    border-color: #6f42c1;
    color: white;
}

.btn-purple:hover {
    background-color: #5a359c;
    border-color: #5a359c;
    color: white;
}

.btn-outline-purple {
    color: #6f42c1;
    border-color: #6f42c1;
}

.btn-outline-purple:hover {
    background-color: #6f42c1;
    border-color: #6f42c1;
    color: white;
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