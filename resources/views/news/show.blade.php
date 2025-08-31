@extends('layouts.app')

@section('title', $news->title)

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Início</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('news.index') }}">Notícias</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ Str::limit($news->title, 30) }}</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-12">
            <h1 class="fw-bold">{{ $news->title }}</h1>
            <p class="text-muted">
                <i class="bi bi-calendar me-1"></i> Publicado em {{ $news->published_at->format('d/m/Y') }} 
                <i class="bi bi-person ms-3 me-1"></i> por {{ $news->user->name }}
            </p>
            
            @auth
                @if(auth()->user()->is_admin)
                    <div class="mb-4">
                        <a href="{{ route('admin.news.edit', $news) }}" class="btn btn-outline-warning me-2">
                            <i class="bi bi-pencil me-1"></i> Editar
                        </a>
                    </div>
                @endif
            @endauth
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-12">
            @if($news->cover_image)
                <img src="{{ Storage::url($news->cover_image) }}" 
                     class="img-fluid rounded mb-4 w-100 shadow-sm" 
                     style="max-height: 500px; object-fit: cover;" 
                     alt="{{ $news->title }}">
            @else
                <div class="bg-secondary rounded mb-4 d-flex align-items-center justify-content-center" style="height: 300px;">
                    <i class="bi bi-newspaper text-white" style="font-size: 3rem;"></i>
                </div>
            @endif
        </div>
    </div>

    <div class="row">
        <div class="col-12 col-lg-8 mx-auto">
            <div class="news-content mb-5">
                {!! $news->content !!}
            </div>
            
            @if($news->featured_image)
                <div class="text-center mt-5">
                    <img src="{{ Storage::url($news->featured_image) }}" 
                         class="img-fluid rounded shadow-sm" 
                         style="max-height: 400px; object-fit: contain;" 
                         alt="Imagem destacada">
                </div>
            @endif
        </div>
    </div>
</div>

<style>
    .news-content {
        font-size: 1.1rem;
        line-height: 1.8;
    }
    
    .news-content img {
        max-width: 100%;
        height: auto;
        border-radius: 8px;
        margin: 1rem 0;
    }
</style>
@endsection