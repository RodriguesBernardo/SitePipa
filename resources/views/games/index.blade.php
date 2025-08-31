@extends('layouts.app')

@section('title', 'Jogos')

@section('content')
<div class="container py-4">
    <!-- Hero Section Improved -->
    <div class="hero-section bg-pipa-gradient rounded-4 p-5 mb-5 text-white shadow position-relative overflow-hidden">
        <div class="row align-items-center position-relative z-index-1">
            <div class="col-lg-8">
                <h1 class="display-5 fw-bold mb-3">Repositório de Jogos Educacionais</h1>
                <p class="lead mb-4">Explore nossa coleção de jogos desenvolvidos para transformar o aprendizado em uma experiência divertida e engajadora</p>
            </div>
            <div class="col-lg-4 d-none d-lg-block">
                <img src="{{ asset('images/logo.png') }}" alt="Ilustração de controle de jogos" class="img-fluid floating-animation" style="max-height: 250px;">
            </div>
        </div>
        <!-- Decorative elements -->
        <div class="position-absolute top-0 end-0 w-100 h-100 overflow-hidden">
            <div class="hero-dots"></div>
        </div>
    </div>

    <!-- Search and Filters Section -->
    <div class="search-filter-section mb-5">
        <div class="card shadow-sm border-0">
            <div class="card-body p-4 position-relative">
                <div class="row g-3">
                    <div class="col-md-8">
                        <form action="{{ route('games.index') }}" method="GET" class="search-form">
                            <div class="input-group">
                                <span class="input-group-text border-end-0"><i class="bi bi-search"></i></span>
                                <input type="text" name="search" class="form-control border-start-0 py-3" placeholder="Buscar jogos por título, categoria, tag ou descrição..." value="{{ request('search') }}">
                                <button type="submit" class="btn btn-pipa-red px-4">Buscar</button>
                            </div>
                        </form>
                    </div>
                    <div class="col-md-4">
                        <div class="d-flex gap-2 h-100">
                            <div class="dropdown flex-grow-1">
                                <button class="btn btn-outline-secondary dropdown-toggle w-100 h-100 py-2 d-flex justify-content-between align-items-center" 
                                        type="button" 
                                        id="categoryDropdown" 
                                        data-bs-toggle="dropdown" 
                                        data-bs-boundary="viewport"
                                        aria-expanded="false">
                                    <span><i class="bi bi-funnel-fill me-2"></i>Filtrar</span>
                                </button>
                                <ul class="dropdown-menu w-100 p-3" aria-labelledby="categoryDropdown">
                                    <li><h6 class="dropdown-header">Categorias</h6></li>
                                    @foreach($tags as $tag)
                                        <li>
                                            <a class="dropdown-item d-flex justify-content-between align-items-center py-2" 
                                               href="{{ route('games.index', ['tag' => $tag->slug]) }}">
                                                {{ $tag->name }}
                                                <span class="badge bg-pipa-red">{{ $tag->games_count }}</span>
                                            </a>
                                        </li>
                                    @endforeach
                                    <li><hr class="dropdown-divider my-2"></li>
                                    <li>
                                        <a class="dropdown-item py-2" href="{{ route('games.index') }}">
                                            <i class="bi bi-x-circle me-2"></i>Limpar filtros
                                        </a>
                                    </li>
                                </ul>
                            </div>
                            <div class="dropdown">
                                <button class="btn btn-outline-secondary dropdown-toggle h-100 py-2 d-flex align-items-center" 
                                        type="button" 
                                        id="sortDropdown" 
                                        data-bs-toggle="dropdown" 
                                        data-bs-boundary="viewport"
                                        aria-expanded="false">
                                    <i class="bi bi-arrow-down-up me-2"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end p-3" aria-labelledby="sortDropdown">
                                    <li><h6 class="dropdown-header">Ordenar por</h6></li>
                                    <li>
                                        <a class="dropdown-item py-2" href="{{ route('games.index', ['sort' => 'newest']) }}">
                                            <i class="bi bi-arrow-up me-2"></i>Mais recentes
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item py-2" href="{{ route('games.index', ['sort' => 'oldest']) }}">
                                            <i class="bi bi-arrow-down me-2"></i>Mais antigos
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item py-2" href="{{ route('games.index', ['sort' => 'downloads']) }}">
                                            <i class="bi bi-download me-2"></i>Mais baixados
                                        </a>
                                    </li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <a class="dropdown-item py-2" href="{{ route('games.index', ['filter' => 'featured']) }}">
                                            <i class="bi bi-star-fill me-2"></i>Destaques
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Active filters -->
                @if(request()->has('tag') || request()->has('search') || request()->has('sort') || request()->has('filter'))
                <div class="mt-3">
                    <div class="d-flex flex-wrap gap-2 align-items-center">
                        <small class="text-muted me-2">Filtros ativos:</small>
                        @if(request('tag') && $selectedTag = $tags->firstWhere('slug', request('tag')))
                            <a href="{{ route('games.index', Arr::except(request()->query(), ['tag'])) }}" class="badge bg-pipa-red bg-opacity-10 text-pipa-red border border-pipa-red d-flex align-items-center">
                                {{ $selectedTag->name }}
                                <i class="bi bi-x ms-2"></i>
                            </a>
                        @endif
                        @if(request('search'))
                            <a href="{{ route('games.index', Arr::except(request()->query(), ['search'])) }}" class="badge bg-pipa-red bg-opacity-10 text-pipa-red border border-pipa-red d-flex align-items-center">
                                "{{ request('search') }}"
                                <i class="bi bi-x ms-2"></i>
                            </a>
                        @endif
                        @if(request('sort') == 'newest')
                            <a href="{{ route('games.index', Arr::except(request()->query(), ['sort'])) }}" class="badge bg-pipa-red bg-opacity-10 text-pipa-red border border-pipa-red d-flex align-items-center">
                                Mais recentes
                                <i class="bi bi-x ms-2"></i>
                            </a>
                        @endif
                        @if(request('sort') == 'oldest')
                            <a href="{{ route('games.index', Arr::except(request()->query(), ['sort'])) }}" class="badge bg-pipa-red bg-opacity-10 text-pipa-red border border-pipa-red d-flex align-items-center">
                                Mais antigos
                                <i class="bi bi-x ms-2"></i>
                            </a>
                        @endif
                        @if(request('sort') == 'downloads')
                            <a href="{{ route('games.index', Arr::except(request()->query(), ['sort'])) }}" class="badge bg-pipa-red bg-opacity-10 text-pipa-red border border-pipa-red d-flex align-items-center">
                                Mais baixados
                                <i class="bi bi-x ms-2"></i>
                            </a>
                        @endif
                        @if(request('filter') == 'featured')
                            <a href="{{ route('games.index', Arr::except(request()->query(), ['filter'])) }}" class="badge bg-pipa-red bg-opacity-10 text-pipa-red border border-pipa-red d-flex align-items-center">
                                Destaques
                                <i class="bi bi-x ms-2"></i>
                            </a>
                        @endif
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Featured Games Section -->
    @if($featuredGames->count() > 0 && !request()->has('filter'))
    <section id="featured-games" class="mb-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="section-title mb-0 fw-bold">
                <i class="bi bi-star-fill text-warning me-2"></i>Jogos em Destaque
            </h2>
            <a href="{{ route('games.index', ['filter' => 'featured']) }}" class="btn btn-sm btn-outline-secondary">
                Ver todos <i class="bi bi-arrow-right"></i>
            </a>
        </div>
        
        <div class="row g-4">
            @foreach($featuredGames as $featuredGame)
            <div class="col-lg-4">
                <div class="card h-100 featured-game-card border-0 shadow-sm overflow-hidden transition-all">
                    <div class="position-relative">
                        @if($featuredGame->cover_image)
                            <img src="{{ Storage::url($featuredGame->cover_image) }}" class="card-img-top" alt="{{ $featuredGame->title }}" style="height: 200px; object-fit: cover;">
                        @else
                            <div class="card-img-top d-flex align-items-center justify-content-center" style="height: 200px;">
                                <i class="bi bi-joystick text-muted" style="font-size: 3rem;"></i>
                            </div>
                        @endif
                        <div class="card-img-overlay-top d-flex justify-content-end p-3">
                            <span class="badge bg-warning text-dark">
                                <i class="bi bi-star-fill"></i> Destaque
                            </span>
                        </div>
                    </div>
                    <div class="card-body">
                        <h3 class="card-title fw-bold mb-2">{{ $featuredGame->title }}</h3>
                        <p class="mb-2">{!! Str::limit(strip_tags($featuredGame->short_description), 100) !!}</p>
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <small class="text-muted">
                                    <i class="bi bi-download me-1"></i> {{ $featuredGame->downloads_count }} downloads
                                </small>
                            </div>
                            <a href="{{ route('games.show', $featuredGame) }}" class="btn btn-sm btn-pipa-red stretched-link">
                                Detalhes <i class="bi bi-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </section>
    @endif

    <!-- All Games Section -->
    <section id="all-games">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="section-title mb-0 fw-bold">
                @if(request('filter') == 'featured')
                    Jogos em Destaque
                @elseif(request('tag') && $selectedTag = $tags->firstWhere('slug', request('tag')))
                    {{ $selectedTag->name }}
                @else
                    Todos os Jogos
                @endif
                <span class="text-muted fs-6">({{ $games->total() }} {{ $games->total() == 1 ? 'jogo' : 'jogos' }})</span>
            </h2>
            
            @if($games->count() > 0)
            <div class="d-none d-md-block">
                <div class="btn-group" role="group">
                    <button type="button" class="btn btn-outline-secondary active grid-view-btn" data-view="grid">
                        <i class="bi bi-grid-3x3-gap"></i>
                    </button>
                    <button type="button" class="btn btn-outline-secondary list-view-btn" data-view="list">
                        <i class="bi bi-list-ul"></i>
                    </button>
                </div>
            </div>
            @endif
        </div>
        
        @if($games->count() > 0)
            <!-- Grid View -->
            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4 games-grid-view">
                @foreach($games as $game)
                    <div class="col">
                        <div class="card h-100 game-card border-0 shadow-sm overflow-hidden transition-all">
                            <div class="position-relative">
                                @if($game->cover_image)
                                    <img src="{{ Storage::url($game->cover_image) }}" class="card-img-top" alt="{{ $game->title }}" style="height: 200px; object-fit: cover;">
                                @else
                                    <div class="card-img-top d-flex align-items-center justify-content-center" style="height: 200px;">
                                        <i class="bi bi-joystick text-muted" style="font-size: 3rem;"></i>
                                    </div>
                                @endif
                                @if($game->is_featured)
                                <div class="card-img-overlay-top d-flex justify-content-end p-3">
                                    <span class="badge bg-warning text-dark">
                                        <i class="bi bi-star-fill"></i> Destaque
                                    </span>
                                </div>
                                @endif
                            </div>
                            <div class="card-body">
                                <h3 class="card-title fw-bold mb-2">{{ $game->title }}</h3>
                                <p class="mb-2">{!! Str::limit(strip_tags($game->short_description), 100) !!}</p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <small class="text-muted">
                                            <i class="bi bi-download me-1"></i> {{ $game->downloads_count }} downloads
                                        </small>
                                    </div>
                                    <a href="{{ route('games.show', $game) }}" class="btn btn-sm btn-pipa-red stretched-link">
                                        Detalhes <i class="bi bi-arrow-right"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            
            <!-- List View (Hidden by default) -->
            <div class="games-list-view d-none">
                <div class="card shadow-sm border-0 overflow-hidden">
                    <div class="list-group list-group-flush">
                        @foreach($games as $game)
                        <div class="list-group-item border-0 p-0">
                            <div class="d-md-flex align-items-center p-3 game-list-item">
                                <div class="flex-shrink-0 me-3 mb-3 mb-md-0" style="width: 200px;">
                                    @if($game->cover_image)
                                        <img src="{{ Storage::url($game->cover_image) }}" class="img-fluid rounded-3" alt="{{ $game->title }}" style="height: 120px; width: 100%; object-fit: cover;">
                                    @else
                                        <div class="d-flex align-items-center justify-content-center rounded-3" style="height: 120px; width: 100%;">
                                            <i class="bi bi-joystick text-muted" style="font-size: 2rem;"></i>
                                        </div>
                                    @endif
                                </div>
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <h5 class="mb-1 fw-bold">{{ $game->title }}</h5>
                                            <div class="d-flex align-items-center mb-2">
                                                @if($game->is_featured)
                                                    <span class="badge bg-warning text-dark me-2">
                                                        <i class="bi bi-star-fill"></i> Destaque
                                                    </span>
                                                @endif
                                                <small class="text-muted me-2"><i class="bi bi-download"></i> {{ $game->downloads_count }} downloads</small>
                                                <small class="text-muted"><i class="bi bi-calendar"></i> {{ $game->created_at->format('d/m/Y') }}</small>
                                            </div>
                                        </div>
                                        <a href="{{ route('games.show', $game) }}" class="btn btn-sm btn-pipa-red ms-3">
                                            Detalhes <i class="bi bi-arrow-right"></i>
                                        </a>
                                    </div>
                                    <p class="mb-2">{!! Str::limit(strip_tags($game->short_description), 200) !!}</p>
                                    <div class="d-flex flex-wrap gap-1">
                                        @foreach($game->tags as $tag)
                                            <span class="badge border">{{ $tag->name }}</span>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @else
            <div class="text-center py-5">
                <img src="{{ asset('images/logo.png') }}" alt="Nenhum jogo encontrado" class="img-fluid mb-4" style="max-height: 200px;">
                <h3 class="fw-bold mb-3">Nenhum jogo encontrado</h3>
                <p class="text-muted mb-4">Não encontramos jogos correspondentes à sua busca. Tente ajustar os filtros ou explore nossa coleção completa.</p>
                <a href="{{ route('games.index') }}" class="btn btn-pipa-red rounded-pill px-4">
                    <i class="bi bi-arrow-counterclockwise me-2"></i>Limpar filtros
                </a>
            </div>
        @endif
    </section>

    <!-- Pagination -->
    @if($games->count() > 0)
        <div class="row">
            <div class="col-12">
                <nav aria-label="Page navigation">
                    {{ $games->onEachSide(1)->links('pagination::bootstrap-5') }}
                </nav>
            </div>
        </div>
    @endif
</div>

<style>
    .featured-game-card {
        border-left: 4px solid #ffc107 !important;
    }
    
    .card-img-overlay-top {
        position: absolute;
        top: 0;
        right: 0;
        left: 0;
        padding: 1rem;
    }
    
    .hero-section {
        background: linear-gradient(135deg, #666666 0%, #333333 50%, #000000 100%);
        color: white; 
        overflow: hidden;
        border-radius: 0.75rem;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
    }

    
    .hero-dots {
        position: absolute;
        top: 0;
        right: 0;
        width: 100%;
        height: 100%;
        background-image: radial-gradient(rgba(255,255,255,0.15) 1px, transparent 1px);
        background-size: 15px 15px;
        opacity: 0.5;
    }
    
    .game-card {
        transition: all 0.3s ease;
        border-radius: 0.75rem !important;
    }
    
    .game-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 12px 24px rgba(0, 0, 0, 0.15) !important;
    }
    
    .section-title {
        position: relative;
        padding-bottom: 10px;
    }
    
    .section-title::after {
        content: '';
        position: absolute;
        left: 0;
        bottom: 0;
        width: 60px;
        height: 4px;
        background: #d62b1f;
        border-radius: 2px;
    }
    
    .empty-state {
        background-color: #f8f9fa;
        border-radius: 0.75rem;
    }
    
    .floating-animation {
        animation: floating 3s ease-in-out infinite;
    }
    
    @keyframes floating {
        0% { transform: translateY(0px); }
        50% { transform: translateY(-15px); }
        100% { transform: translateY(0px); }
    }
    
    .game-list-item {
        transition: all 0.2s ease;
    }

    /* Dropdown Fixes */
    .search-filter-section {
        position: relative;
        z-index: 1000;
    }

    .search-filter-section .card {
        overflow: visible !important;
    }

    .search-filter-section .dropdown-menu {
        max-height: 400px;
        overflow-y: auto;
        border-radius: 0.75rem;
        border: 1px solid rgba(0,0,0,0.1);
        box-shadow: 0 6px 12px rgba(0,0,0,0.1);
        transform: translateY(8px) !important;
        position: absolute !important;
        will-change: transform;
    }

    .search-filter-section .dropdown-menu-end {
        right: 0 !important;
        left: auto !important;
    }

    .search-filter-section .dropdown-item {
        border-radius: 0.5rem;
        transition: all 0.2s ease;
        padding: 0.5rem 1rem;
    }

    .search-filter-section .dropdown-item:hover {
        background-color: #f8f9fa;
        color: #d62b1f;
    }

    .search-filter-section .dropdown-toggle::after {
        margin-left: 0.5rem;
    }

    .search-filter-section .dropdown-divider {
        opacity: 0.5;
    }
    
    .transition-all {
        transition: all 0.3s ease;
    }
    
    .z-index-1 {
        z-index: 1;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // View toggle functionality
        const gridViewBtn = document.querySelector('.grid-view-btn');
        const listViewBtn = document.querySelector('.list-view-btn');
        const gridView = document.querySelector('.games-grid-view');
        const listView = document.querySelector('.games-list-view');
        
        if(gridViewBtn && listViewBtn) {
            gridViewBtn.addEventListener('click', function() {
                gridView.classList.remove('d-none');
                listView.classList.add('d-none');
                gridViewBtn.classList.add('active');
                listViewBtn.classList.remove('active');
                localStorage.setItem('gamesView', 'grid');
            });
            
            listViewBtn.addEventListener('click', function() {
                gridView.classList.add('d-none');
                listView.classList.remove('d-none');
                gridViewBtn.classList.remove('active');
                listViewBtn.classList.add('active');
                localStorage.setItem('gamesView', 'list');
            });
            
            // Load saved view preference
            const savedView = localStorage.getItem('gamesView');
            if(savedView === 'list') {
                listViewBtn.click();
            }
        }
    });
</script>
@endsection