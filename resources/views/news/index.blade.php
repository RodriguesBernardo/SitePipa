@extends('layouts.app')

@section('title', 'Notícias')

@section('content')
<div class="container py-4">
    <!-- Hero Section -->
    <div class="hero-section bg-pipa-gradient rounded-4 p-5 mb-5 text-white shadow position-relative overflow-hidden">
        <div class="row align-items-center position-relative z-index-1">
            <div class="col-lg-8">
                <h1 class="display-5 fw-bold mb-3">Notícias e Atualizações</h1>
                <p class="lead mb-4">Fique por dentro das últimas novidades, eventos e informações importantes do PIPA IFRS</p>
            </div>
            <div class="col-lg-4 d-none d-lg-block">
                <img src="{{ asset('images/logo.png') }}" alt="Ilustração de notícias" class="img-fluid floating-animation" style="max-height: 250px;">
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
                        <form action="{{ route('news.index') }}" method="GET" class="search-form">
                            <div class="input-group">
                                <span class="input-group-text border-end-0"><i class="bi bi-search"></i></span>
                                <input type="text" name="search" class="form-control border-start-0 py-3" placeholder="Buscar notícias por título, conteúdo ou tags..." value="{{ request('search') }}">
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
                                <!-- dropdown de filtros -->
                                <ul class="dropdown-menu w-100 p-3" aria-labelledby="categoryDropdown">
                                    <li><h6 class="dropdown-header">Categorias</h6></li>
                                    <li>
                                        <a class="dropdown-item d-flex justify-content-between align-items-center py-2" 
                                        href="{{ route('news.index', ['filter' => 'featured']) }}">
                                            Destaques
                                            @if(request('filter') == 'featured')
                                                <i class="bi bi-check-lg text-success"></i>
                                            @endif
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item d-flex justify-content-between align-items-center py-2" 
                                        href="{{ route('news.index', ['filter' => 'recent']) }}">
                                            Mais recentes
                                            @if(request('filter') == 'recent')
                                                <i class="bi bi-check-lg text-success"></i>
                                            @endif
                                        </a>
                                    </li>
                                    <li><hr class="dropdown-divider my-2"></li>
                                    <li>
                                        <a class="dropdown-item py-2" href="{{ route('news.index') }}">
                                            <i class="bi bi-x-circle me-2"></i>Limpar filtros
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Active filters -->
                @if(request()->has('filter') || request()->has('search'))
                <div class="mt-3">
                    <div class="d-flex flex-wrap gap-2 align-items-center">
                        <small class="text-muted me-2">Filtros ativos:</small>
                        @if(request('filter') == 'featured')
                            <a href="{{ route('news.index', Arr::except(request()->query(), ['filter'])) }}" class="badge bg-pipa-red bg-opacity-10 text-pipa-red border border-pipa-red d-flex align-items-center">
                                Destaques
                                <i class="bi bi-x ms-2"></i>
                            </a>
                        @endif
                        @if(request('filter') == 'recent')
                            <a href="{{ route('news.index', Arr::except(request()->query(), ['filter'])) }}" class="badge bg-pipa-red bg-opacity-10 text-pipa-red border border-pipa-red d-flex align-items-center">
                                Mais recentes
                                <i class="bi bi-x ms-2"></i>
                            </a>
                        @endif
                        @if(request('search'))
                            <a href="{{ route('news.index', Arr::except(request()->query(), ['search'])) }}" class="badge bg-pipa-red bg-opacity-10 text-pipa-red border border-pipa-red d-flex align-items-center">
                                "{{ request('search') }}"
                                <i class="bi bi-x ms-2"></i>
                            </a>
                        @endif
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Featured News Section -->
    @if($featuredNews->count() > 0 && !request()->has('filter'))
    <section id="featured-news" class="mb-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="section-title mb-0 fw-bold">
                <i class="bi bi-star-fill text-warning me-2"></i>Destaques
            </h2>
        </div>
        
        <div class="row g-4">
            @foreach($featuredNews as $featuredItem)
            <div class="col-lg-6">
                <div class="card h-100 featured-news-card border-0 shadow-sm overflow-hidden transition-all">
                    <div class="row g-0 h-100">
                        <div class="col-md-6">
                            @if($featuredItem->featured_image)
                                <img src="{{ Storage::url($featuredItem->featured_image) }}" class="img-fluid h-100 w-100" style="object-fit: cover;" alt="{{ $featuredItem->title }}">
                            @elseif($featuredItem->cover_image)
                                <img src="{{ Storage::url($featuredItem->cover_image) }}" class="img-fluid h-100 w-100" style="object-fit: cover;" alt="{{ $featuredItem->title }}">
                            @else
                                <div class="bg-secondary h-100 d-flex align-items-center justify-content-center">
                                    <i class="bi bi-newspaper text-white" style="font-size: 3rem;"></i>
                                </div>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <div class="card-body d-flex flex-column h-100">
                                <div class="mb-3">
                                    <span class="badge bg-pipa-red mb-2">Destaque</span>
                                    <h3 class="card-title h4 fw-bold">{{ $featuredItem->title }}</h3>
                                    <p class="card-text text-muted">{!! Str::limit(strip_tags($featuredItem->excerpt), 150) !!}</p>
                                </div>
                                <div class="mt-auto">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <small class="text-muted">
                                            <i class="bi bi-calendar me-1"></i>
                                            {{ $item->published_at ? $item->published_at->format('d/m/Y') : 'Data não disponível' }}
                                        </small>
                                        <a href="{{ route('news.show', $featuredItem) }}" class="btn btn-sm btn-pipa-red stretched-link">
                                            Ler mais <i class="bi bi-arrow-right ms-1"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </section>
    @endif

    <!-- All News Section -->
    <section id="all-news">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="section-title mb-0 fw-bold">
                @if(request('filter') == 'featured')
                    Notícias em Destaque
                @elseif(request('filter') == 'recent')
                    Notícias Recentes
                @else
                    Todas as Notícias
                @endif
                <span class="text-muted fs-6">({{ $news->total() }} {{ $news->total() == 1 ? 'notícia' : 'notícias' }})</span>
            </h2>
            
            @if($news->count() > 0)
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
        
        @if($news->count() > 0)
            <!-- Grid View -->
            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4 news-grid-view">
                @foreach($news as $item)
                    <div class="col">
                        <div class="card h-100 news-card border-0 shadow-sm overflow-hidden transition-all">
                            <div class="position-relative">
                                @if($item->cover_image)
                                    <img src="{{ Storage::url($item->cover_image) }}" class="card-img-top" style="height: 200px; object-fit: cover;" alt="{{ $item->title }}">
                                @else
                                    <div class="card-img-top bg-secondary d-flex align-items-center justify-content-center" style="height: 200px;">
                                        <i class="bi bi-newspaper text-white" style="font-size: 3rem;"></i>
                                    </div>
                                @endif
                                @if($item->is_featured)
                                <div class="card-img-overlay-top d-flex justify-content-end p-3">
                                    <span class="badge bg-warning text-dark">
                                        <i class="bi bi-star-fill"></i> Destaque
                                    </span>
                                </div>
                                @endif
                            </div>
                            <div class="card-body">
                                <h3 class="card-title fw-bold mb-2">{{ $item->title }}</h3>
                                <p class="card-text text-muted mb-3">{!! Str::limit(strip_tags($item->excerpt), 100) !!}</p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <small class="text-muted">
                                            <i class="bi bi-calendar me-1"></i>
                                            {{ $item->published_at ? $item->published_at->format('d/m/Y') : 'Data não disponível' }}
                                        </small>
                                    </div>
                                    <a href="{{ route('news.show', $item) }}" class="btn btn-sm btn-pipa-red stretched-link">
                                        Ler mais <i class="bi bi-arrow-right"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            
            <!-- List View (Hidden by default) -->
            <div class="news-list-view d-none">
                <div class="card shadow-sm border-0 overflow-hidden">
                    <div class="list-group list-group-flush">
                        @foreach($news as $item)
                        <div class="list-group-item border-0 p-0">
                            <div class="d-md-flex align-items-center p-3 news-list-item">
                                <div class="flex-shrink-0 me-3 mb-3 mb-md-0" style="width: 200px;">
                                    @if($item->cover_image)
                                        <img src="{{ Storage::url($item->cover_image) }}" class="img-fluid rounded-3" alt="{{ $item->title }}" style="height: 120px; width: 100%; object-fit: cover;">
                                    @else
                                        <div class="d-flex align-items-center justify-content-center rounded-3 bg-secondary" style="height: 120px; width: 100%;">
                                            <i class="bi bi-newspaper text-white" style="font-size: 2rem;"></i>
                                        </div>
                                    @endif
                                </div>
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <h5 class="mb-1 fw-bold">{{ $item->title }}</h5>
                                            <div class="d-flex align-items-center mb-2">
                                                @if($item->is_featured)
                                                    <span class="badge bg-warning text-dark me-2">
                                                        <i class="bi bi-star-fill"></i> Destaque
                                                    </span>
                                                @endif
                                               <small class="text-muted">
                                                    <i class="bi bi-calendar me-1"></i>
                                                    {{ $item->published_at ? $item->published_at->format('d/m/Y') : 'Data não disponível' }}
                                                </small>
                                            </div>
                                        </div>
                                        <a href="{{ route('news.show', $item) }}" class="btn btn-sm btn-pipa-red ms-3">
                                            Ler mais <i class="bi bi-arrow-right"></i>
                                        </a>
                                    </div>
                                    <p class="mb-2">{!! Str::limit(strip_tags($item->excerpt), 200) !!}</p>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @else
            <div class="text-center py-5">
                <img src="{{ asset('images/logo.png') }}" alt="Nenhuma notícia encontrada" class="img-fluid mb-4" style="max-height: 200px;">
                <h3 class="fw-bold mb-3">Nenhuma notícia encontrada</h3>
                <p class="text-muted mb-4">Não encontramos notícias correspondentes à sua busca. Tente ajustar os filtros ou explore nossa coleção completa.</p>
                <a href="{{ route('news.index') }}" class="btn btn-pipa-red rounded-pill px-4">
                    <i class="bi bi-arrow-counterclockwise me-2"></i>Limpar filtros
                </a>
            </div>
        @endif
    </section>

    <!-- Pagination -->
    @if($news->count() > 0)
        <div class="row">
            <div class="col-12">
                <nav aria-label="Page navigation">
                    {{ $news->onEachSide(1)->links('pagination::bootstrap-5') }}
                </nav>
            </div>
        </div>
    @endif
</div>

<style>
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
    
    .featured-news-card, .news-card {
        transition: all 0.3s ease;
        border-radius: 0.75rem !important;
    }
    
    .featured-news-card:hover, .news-card:hover {
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
    
    .floating-animation {
        animation: floating 3s ease-in-out infinite;
    }
    
    @keyframes floating {
        0% { transform: translateY(0px); }
        50% { transform: translateY(-15px); }
        100% { transform: translateY(0px); }
    }
    
    .news-list-item {
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
    
    .card-img-overlay-top {
        position: absolute;
        top: 0;
        right: 0;
        left: 0;
        padding: 1rem;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // View toggle functionality
        const gridViewBtn = document.querySelector('.grid-view-btn');
        const listViewBtn = document.querySelector('.list-view-btn');
        const gridView = document.querySelector('.news-grid-view');
        const listView = document.querySelector('.news-list-view');
        
        if(gridViewBtn && listViewBtn) {
            gridViewBtn.addEventListener('click', function() {
                gridView.classList.remove('d-none');
                listView.classList.add('d-none');
                gridViewBtn.classList.add('active');
                listViewBtn.classList.remove('active');
                localStorage.setItem('newsView', 'grid');
            });
            
            listViewBtn.addEventListener('click', function() {
                gridView.classList.add('d-none');
                listView.classList.remove('d-none');
                gridViewBtn.classList.remove('active');
                listViewBtn.classList.add('active');
                localStorage.setItem('newsView', 'list');
            });
            
            // Load saved view preference
            const savedView = localStorage.getItem('newsView');
            if(savedView === 'list') {
                listViewBtn.click();
            }
        }
    });
</script>
@endsection