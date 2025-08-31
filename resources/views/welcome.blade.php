@extends('layouts.app')

@section('title', 'Início')

@section('content')
<div class="container-fluid px-4 py-5">
    <!-- Hero Section -->
    <div class="row mb-5 hero-section" style="background: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('{{ asset('images/pipa.jpg') }}') no-repeat center center; background-size: cover;">
        <div class="col-12 text-center py-5 text-white">
            <h1 class="display-4 fw-bold mb-3 animate__animated animate__fadeInDown">Bem-vindo ao site do PIPA IFRS</h1>
        </div>
    </div>

    <!-- Featured Games -->
    @if($featuredGames->count() > 0)
        <section id="featured-games" class="mb-5 py-4">
            <div class="row mb-4">
                <div class="col-12">
                    <div class="d-flex justify-content-between align-items-center">
                        <h2 class="fw-bold mb-0">
                            <i class="bi bi-stars text-warning me-2"></i>Jogos em Destaque
                        </h2>
                        <a href="{{ route('games.index') }}" class="btn btn-sm btn-outline-secondary">
                            Ver todos <i class="bi bi-chevron-right"></i>
                        </a>
                    </div>
                    <hr class="mt-2">
                </div>
            </div>
            
            <div class="row g-4">
                @foreach($featuredGames as $game)
                    <div class="col-xl-3 col-lg-4 col-md-6">
                        <div class="card h-100 game-card shadow-sm border-0 overflow-hidden">
                            <div class="position-relative">
                                <img src="{{ Storage::url($game->cover_image) }}" class="card-img-top" alt="{{ $game->title }}" style="height: 200px; object-fit: cover;">
                                <div class="card-img-overlay-top d-flex justify-content-end">
                                    <span class="badge bg-pipa-red">{{ $game->category->name ?? 'Geral' }}</span>
                                </div>
                            </div>
                            <div class="card-body">
                                <h5 class="card-title fw-bold">{{ $game->title }}</h5>
                                <div class="d-flex align-items-center mb-2">
                                </div>
                                <p class="card-text text-muted">{!! Str::limit($game->short_description, 100) !!}</p>
                            </div>
                            <div class="card-footer bg-transparent border-top-0 d-flex justify-content-between align-items-center">
                                <small class="text-muted">
                                    <i class="bi bi-download me-1"></i> {{ $game->downloads_count }}
                                </small>
                                <a href="{{ route('games.show', $game) }}" class="btn btn-sm btn-pipa-red stretched-link">
                                    Detalhes <i class="bi bi-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>
    @endif

    <!-- Featured News Carousel -->
    @if($featuredNews->count() > 0)
        <section id="featured-news" class="mb-5 py-4">
            <div class="row mb-4">
                <div class="col-12">
                    <div class="d-flex justify-content-between align-items-center">
                        <h2 class="fw-bold mb-0">
                            <i class="bi bi-star-fill text-warning me-2"></i>Notícias em Destaque
                        </h2>
                        <a href="{{ route('news.index') }}" class="btn btn-sm btn-outline-secondary">
                            Ver todas <i class="bi bi-chevron-right"></i>
                        </a>
                    </div>
                    <hr class="mt-2">
                </div>
            </div>
            
            <div id="newsCarousel" class="carousel slide" data-bs-ride="carousel">
                <div class="carousel-inner">
                    @foreach($featuredNews as $index => $item)
                        <div class="carousel-item {{ $index === 0 ? 'active' : '' }}" data-bs-interval="5000">
                            <div class="row justify-content-center">
                                <div class="col-lg-8">
                                    <div class="card news-featured-card border-0 shadow-sm">
                                        <div class="row g-0">
                                            <div class="col-md-5">
                                                @if($item->featured_image)
                                                    <img src="{{ Storage::url($item->featured_image) }}" class="img-fluid rounded-start h-100" alt="{{ $item->title }}" style="object-fit: cover; min-height: 300px;">
                                                @elseif($item->cover_image)
                                                    <img src="{{ Storage::url($item->cover_image) }}" class="img-fluid rounded-start h-100" alt="{{ $item->title }}" style="object-fit: cover; min-height: 300px;">
                                                @else
                                                    <div class="bg-secondary h-100 d-flex align-items-center justify-content-center">
                                                        <i class="bi bi-newspaper text-white" style="font-size: 3rem;"></i>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="col-md-7">
                                                <div class="card-body h-100 d-flex flex-column">
                                                    <div class="mb-2">
                                                        <small class="text-muted">
                                                            <i class="bi bi-calendar me-1"></i> {{ $item->created_at->format('d/m/Y') }}
                                                        </small>
                                                        <span class="badge bg-info ms-2">{{ $item->category }}</span>
                                                    </div>
                                                    <h3 class="card-title fw-bold">{{ $item->title }}</h3>
                                                    <p class="card-text">{{ Str::limit(strip_tags($item->excerpt), 200) }}</p>
                                                    <div class="mt-auto">
                                                        <a href="{{ route('news.show', $item) }}" class="btn btn-pipa-red">
                                                            Ler notícia completa <i class="bi bi-arrow-right"></i>
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <button class="carousel-control-prev" type="button" data-bs-target="#newsCarousel" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Anterior</span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#newsCarousel" data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Próximo</span>
                </button>
            </div>
        </section>
    @endif

    <!-- Latest News -->
    @if($news->count() > 0)
        <section id="latest-news" class="mb-5 py-4">
            <div class="row mb-4">
                <div class="col-12">
                    <div class="d-flex justify-content-between align-items-center">
                        <h2 class="fw-bold mb-0">
                            <i class="bi bi-newspaper text-primary me-2"></i>Últimas Notícias
                        </h2>
                        <a href="{{ route('news.index') }}" class="btn btn-sm btn-outline-secondary">
                            Ver todas <i class="bi bi-chevron-right"></i>
                        </a>
                    </div>
                    <hr class="mt-2">
                </div>
            </div>
            
            <div class="row g-4">
                @foreach($news as $item)
                    <div class="col-lg-4 col-md-6">
                        <div class="card h-100 news-card shadow-sm border-0 overflow-hidden">
                            @if($item->cover_image)
                                <img src="{{ Storage::url($item->cover_image) }}" class="card-img-top" alt="{{ $item->title }}" style="height: 200px; object-fit: cover;">
                            @else
                                <div class="card-img-top bg-secondary" style="height: 200px; display: flex; align-items: center; justify-content: center;">
                                    <i class="bi bi-newspaper text-white" style="font-size: 3rem;"></i>
                                </div>
                            @endif
                            <div class="card-body">
                                <div class="d-flex justify-content-between mb-2">
                                    <small class="text-muted">
                                        <i class="bi bi-calendar me-1"></i> {{ $item->created_at->format('d/m/Y') }}
                                    </small>
                                    <span class="badge bg-info">{{ $item->category }}</span>
                                </div>
                                <h5 class="card-title fw-bold">{{ $item->title }}</h5>
                                <p class="card-text text-muted">{{ Str::limit(strip_tags($item->excerpt), 120) }}</p>
                            </div>
                            <div class="card-footer bg-transparent border-top-0">
                                <a href="{{ route('news.show', $item) }}" class="btn btn-sm btn-outline-primary stretched-link">
                                    Ler notícia <i class="bi bi-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>
    @endif

    <!-- Call to Action -->
    <div class="row mb-5">
        <div class="col-12 text-center py-4">
            <h2 class="fw-bold mb-4">Pronto para começar?</h2>
            <a href="{{ route('games.index') }}" class="btn btn-pipa-red btn-lg px-5 py-3">
                <i class="bi bi-joystick me-2"></i>Explorar Todos os Jogos
            </a>
        </div>
    </div>
</div>

<style>

    .game-card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        border-radius: 0.75rem !important;
    }
    
    .game-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1) !important;
    }
    
    .news-card {
        transition: transform 0.3s ease;
        border-radius: 0.75rem !important;
    }
    
    .news-card:hover {
        transform: translateY(-3px);
    }
    
    .news-featured-card {
        border-radius: 0.75rem !important;
        min-height: 300px;
    }
    
    .bg-pipa-red {
        background-color: var(--pipa-red) !important;
    }
    
    .text-pipa-red {
        color: var(--pipa-red) !important;
    }
    
    .text-pipa-green {
        color: var(--pipa-green) !important;
    }
    
    hr {
        opacity: 0.1;
    }
    
    .carousel-control-prev,
    .carousel-control-next {
        width: 5%;
    }
    
    @media (max-width: 768px) {
        .hero-section {
            padding: 2rem 0 !important;
        }
        
        .display-4 {
            font-size: 2.5rem;
        }
        
        .news-featured-card .col-md-5 {
            height: 200px;
        }
    }

    .hero-section {
    border-radius: 1rem;
    margin-top: 1rem;
    position: relative;
    overflow: hidden;
    min-height: 400px; /* Ajuste conforme necessário */
    display: flex;
    align-items: center;
    }

    .hero-section::before {
        content: "";
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.4); /* Overlay adicional se necessário */
        z-index: 0;
    }

    .hero-section > * {
        position: relative;
        z-index: 1;
    }
    </style>

<!-- Animate.css -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">

<script>
    // Adiciona animação quando os elementos entram na viewport
    document.addEventListener('DOMContentLoaded', function() {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animate__animated', 'animate__fadeInUp');
                    observer.unobserve(entry.target);
                }
            });
        }, {
            threshold: 0.1
        });
        
        document.querySelectorAll('#featured-games .game-card, #latest-news .news-card').forEach(card => {
            observer.observe(card);
        });
        
        // Inicia o carrossel automaticamente
        const newsCarousel = new bootstrap.Carousel(document.getElementById('newsCarousel'), {
            interval: 5000,
            ride: 'carousel'
        });
    });
</script>
@endsection