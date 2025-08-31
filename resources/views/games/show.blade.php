@extends('layouts.app')

@section('title', $game->title)

@section('content')
<div class="container py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-pipa-red"><i class="bi bi-house-door"></i></a></li>
            <li class="breadcrumb-item"><a href="{{ route('games.index') }}" class="text-pipa-red">Jogos</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ Str::limit($game->title, 30) }}</li>
        </ol>
    </nav>

    <div class="row mb-5">
        <!-- Game Cover and Actions -->
        <div class="col-lg-4 mb-4 mb-lg-0">
            <div class="card shadow-sm border-0 sticky-top" style="top: 20px;">
                <div class="position-relative">
                    @if($game->cover_image)
                        <img src="{{ Storage::url($game->cover_image) }}" class="card-img-top" alt="{{ $game->title }}" style="max-height: 400px; object-fit: contain; background-color: #f8f9fa;">
                    @else
                        <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 300px;">
                            <i class="bi bi-joystick text-muted" style="font-size: 3rem;"></i>
                        </div>
                    @endif
                    @if($game->is_featured)
                        <div class="position-absolute top-0 start-0 m-2">
                            <span class="badge bg-warning text-dark">
                                <i class="bi bi-star-fill"></i> Destaque
                            </span>
                        </div>
                    @endif
                </div>
                
                <div class="card-body">
                    <!-- Download Button -->
                    <div class="d-flex flex-column flex-md-row gap-3 mb-4">
                        <!-- Download Button -->
                        <a href="{{ route('games.download', $game) }}" 
                        class="btn btn-pipa-green btn-lg flex-grow-1 py-3"
                        onclick="trackDownload('{{ $game->title }}'); return true;">
                            <i class="bi bi-download me-2"></i> Baixar Jogo
                        </a>
                        
                        <!-- PDF Button -->
                        <a href="{{ route('games.pdf', $game) }}" 
                        class="btn btn-outline-pipa-red btn-lg flex-grow-1 py-3">
                            <i class="bi bi-file-earmark-pdf me-2"></i> Gerar PDF
                        </a>
                    </div>
                    
                    <!-- Quick Stats -->
                    <div class="list-group list-group-flush mb-4">
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <span><i class="bi bi-download text-pipa-red me-2"></i> Downloads</span>
                            <span class="badge bg-pipa-red rounded-pill">{{ $game->downloads_count }}</span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <span><i class="bi bi-calendar text-pipa-red me-2"></i> Publicação</span>
                            <span>{{ $game->created_at->format('d/m/Y') }}</span>
                        </div>
                    </div>
                    
                    <!-- Tags -->
                    @if($game->tags->count() > 0)
                        <div class="mb-3">
                            <h6 class="fw-bold mb-2"><i class="bi bi-tags text-pipa-red me-2"></i>Tags</h6>
                            <div class="d-flex flex-wrap gap-2">
                                @foreach($game->tags as $tag)
                                    <a href="{{ route('games.index', ['tag' => $tag->slug]) }}" 
                                    class="badge tag-badge">
                                        {{ $tag->name }}
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif
                    
                    <!-- Admin Actions -->
                    @can('update', $game)
                        <hr>
                        <div class="d-grid gap-2">
                            <a href="{{ route('admin.games.edit', $game) }}" class="btn btn-outline-warning">
                                <i class="bi bi-pencil me-2"></i> Editar Jogo
                            </a>
                        </div>
                    @endcan
                </div>
            </div>
        </div>
        
        <!-- Game Details -->
        <div class="col-lg-8">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body">
                    <h1 class="fw-bold mb-3">{{ $game->title }}</h1>
                    <p class="lead text-muted mb-4">{!! $game->short_description !!}</p>
                    
                    <!-- Description with tabs -->
                    <ul class="nav nav-tabs mb-4" id="descriptionTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="about-tab" data-bs-toggle="tab" data-bs-target="#about" type="button" role="tab">Regras</button>
                        </li>
                    </ul>
                    
                    <div class="tab-content" id="descriptionTabsContent">
                        <div class="tab-pane fade show active" id="about" role="tabpanel" aria-labelledby="about-tab">
                            <div class="game-description">
                                @if($game->long_description)
                                    {!! $game->long_description !!}
                                @else
                                    <div class="alert alert-info">Nenhuma descrição disponível.</div>
                                @endif
                            </div>
                        </div>
                        
                        @if($game->how_to_play)
                        <div class="tab-pane fade" id="how-to-play" role="tabpanel" aria-labelledby="how-to-play-tab">
                            <div class="game-how-to-play">
                                {!! $game->how_to_play !!}
                            </div>
                        </div>
                        @endif
                        
                        @if($game->educational_objectives)
                        <div class="tab-pane fade" id="educational" role="tabpanel" aria-labelledby="educational-tab">
                            <div class="game-educational-objectives">
                                {!! $game->educational_objectives !!}
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            
            <!-- Screenshots Gallery -->
            @if($game->screenshots->count() > 0)
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-body">
                        <h3 class="fw-bold mb-4"><i class="bi bi-images text-pipa-red me-2"></i>Capturas de Tela</h3>
                        <div class="row g-3">
                            @foreach($game->screenshots as $screenshot)
                                <div class="col-6 col-md-4 col-lg-3">
                                    <a href="{{ Storage::url($screenshot->path) }}" data-fancybox="gallery" data-caption="{{ $game->title }} - Captura {{ $loop->iteration }}">
                                        <img src="{{ Storage::url($screenshot->path) }}" class="img-fluid rounded-3 shadow-sm" alt="Captura de tela do jogo {{ $game->title }}" style="height: 120px; width: 100%; object-fit: cover;">
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
            
            <!-- Related Games -->
            @if(isset($relatedGames) && $relatedGames->count() > 0)
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-body">
                        <h3 class="fw-bold mb-4"><i class="bi bi-controller text-pipa-red me-2"></i>Jogos Relacionados</h3>
                        <div class="row g-3">
                            @foreach($relatedGames as $relatedGame)
                                <div class="col-6 col-md-4">
                                    <div class="card h-100 border-0 shadow-sm">
                                        <a href="{{ route('games.show', $relatedGame) }}">
                                            @if($relatedGame->cover_image)
                                                <img src="{{ Storage::url($relatedGame->cover_image) }}" class="card-img-top" alt="{{ $relatedGame->title }}" style="height: 120px; object-fit: cover;">
                                            @else
                                                <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 120px;">
                                                    <i class="bi bi-joystick text-muted"></i>
                                                </div>
                                            @endif
                                        </a>
                                        <div class="card-body">
                                            <h5 class="card-title">{{ Str::limit($relatedGame->title, 30) }}</h5>
                                            <p class="card-text text-muted small">{{ Str::limit($relatedGame->short_description, 60) }}</p>
                                        </div>
                                        <div class="card-footer bg-transparent">
                                            <a href="{{ route('games.show', $relatedGame) }}" class="btn btn-sm btn-pipa-red">Ver detalhes</a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<style>
    .game-description, .game-how-to-play, .game-educational-objectives {
        line-height: 1.8;
    }
    
    .game-description p,
    .game-how-to-play p,
    .game-educational-objectives p {
        margin-bottom: 1rem;
    }
    
    .game-description ul,
    .game-how-to-play ul,
    .game-educational-objectives ul {
        margin-bottom: 1rem;
        padding-left: 2rem;
    }
    
    .game-description li,
    .game-how-to-play li,
    .game-educational-objectives li {
        margin-bottom: 0.5rem;
    }
    
    .rating-stars {
        direction: rtl;
        unicode-bidi: bidi-override;
        font-size: 2rem;
        display: inline-block;
    }
    
    .rating-stars input {
        display: none;
    }
    
    .rating-stars label {
        color: #ddd;
        cursor: pointer;
        padding: 0 5px;
    }
    
    .rating-stars input:checked ~ label,
    .rating-stars label:hover,
    .rating-stars label:hover ~ label {
        color: #ffc107;
    }
    
    .rating-stars input:checked + label {
        color: #ffc107;
    }
    
    .nav-tabs .nav-link {
        color: #495057;
        font-weight: 500;
    }
    
    .nav-tabs .nav-link.active {
        color: #d62b1f;
        border-bottom: 3px solid #d62b1f;
        font-weight: 600;
    }
    
    .sticky-top {
        z-index: 10;
    }
    
    .card-img-overlay-top {
        position: absolute;
        top: 10px;
        right: 10px;
    }
    
    .tag-badge {
        background-color: var(--bs-primary-bg-subtle) !important;
        color: var(--bs-primary-text-emphasis) !important;
        border: 1px solid var(--bs-primary-border-subtle) !important;
        transition: all 0.2s;
    }
    
    .tag-badge:hover {
        background-color: var(--bs-primary) !important;
        color: white !important;
    }
    
    .card {
        background-color: var(--bs-card-bg) !important;
    }
    
    .list-group-item {
        background-color: var(--bs-body-bg) !important;
        color: var(--bs-body-color) !important;
        border-color: var(--bs-border-color) !important;
    }
    
    .bi {
        color: var(--bs-primary) !important;
    }
    
    .text-muted {
        opacity: 0.8 !important;
    }

    .btn-outline-pipa-red {
        color: #d62b1f;
        border-color: #d62b1f;
    }

    .btn-outline-pipa-red:hover {
        color: #fff;
        background-color: #d62b1f;
        border-color: #d62b1f;
    }

    .btn-pipa-green {
        background-color: #28a745;
        color: white;
        border: none;
        transition: all 0.3s;
    }
    
    .btn-pipa-green:hover {
        background-color: #218838;
        color: white;
    }
    
    .btn-outline-pipa-red {
        color: #d62b1f;
        border-color: #d62b1f;
        transition: all 0.3s;
    }
    
    .btn-outline-pipa-red:hover {
        color: white;
        background-color: #d62b1f;
    }
    
    .game-description {
        line-height: 1.8;
        font-size: 1.05rem;
    }
    
    .nav-tabs .nav-link {
        font-weight: 600;
        color: #495057;
        border: none;
        border-bottom: 3px solid transparent;
        padding: 0.75rem 1.25rem;
    }
    
    .nav-tabs .nav-link.active {
        color: #d62b1f;
        border-bottom: 3px solid #d62b1f;
        background-color: transparent;
    }
    
    .card {
        border-radius: 0.75rem;
        overflow: hidden;
    }
    
    .card-img-top {
        transition: transform 0.3s;
    }
    
    .card-img-top:hover {
        transform: scale(1.03);
    }
    
    @media (max-width: 767.98px) {
        .sticky-top {
            position: static !important;
        }
    }
</style>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fancyapps/ui@4.0/dist/fancybox.css">
<script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui@4.0/dist/fancybox.umd.js"></script>
<script>
    Fancybox.bind("[data-fancybox]", {});
    
    function trackDownload(gameTitle) {
        // Implemente aqui o rastreamento de downloads se necessário
        console.log('Download iniciado para: ' + gameTitle);
    }
</script>
@endsection