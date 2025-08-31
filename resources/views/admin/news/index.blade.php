@extends('layouts.app')

@section('title', 'Gerenciamento de Notícias')

@section('content')
<div class="container-fluid py-4">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-0"><i class="bi bi-newspaper me-2"></i> Notícias</h2>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.news.create') }}" class="btn btn-pipa-red">
                        <i class="bi bi-plus-circle me-1"></i> Nova Notícia
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase text-muted mb-2">Total de Notícias</h6>
                            <h3 class="mb-0">{{ $news->total() }}</h3>
                        </div>
                        <div class="bg-pipa-red bg-opacity-10 p-3 rounded">
                            <i class="bi bi-newspaper text-pipa-red" style="font-size: 1.5rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase text-muted mb-2">Em Destaque</h6>
                            <h3 class="mb-0">{{ $news->where('is_featured', true)->count() }}</h3>
                        </div>
                        <div class="bg-warning bg-opacity-10 p-3 rounded">
                            <i class="bi bi-star-fill text-warning" style="font-size: 1.5rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase text-muted mb-2">Agendadas</h6>
                            <h3 class="mb-0">{{ $news->where('published_at', '>', now())->count() }}</h3>
                        </div>
                        <div class="bg-info bg-opacity-10 p-3 rounded">
                            <i class="bi bi-clock text-info" style="font-size: 1.5rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Table -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header border-bottom py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">Lista de Notícias</h6>
                        <form class="d-flex" method="GET" action="{{ route('admin.news.index') }}">
                            <div class="input-group input-group-sm" style="width: 250px;">
                                <input type="text" name="search" class="form-control" placeholder="Buscar notícias..." 
                                       value="{{ request('search') }}">
                                <button class="btn btn-outline-secondary" type="submit">
                                    <i class="bi bi-search"></i>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th width="80" class="text-center">Capa</th>
                                    <th>Título</th>
                                    <th width="150">Status</th>
                                    <th width="150">Publicação</th>
                                    <th width="180" class="text-center">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($news as $item)
                                    <tr>
                                        <td class="text-center">
                                            @if($item->cover_image)
                                                <img src="{{ Storage::url($item->cover_image) }}" alt="{{ $item->title }}" 
                                                     class="img-thumbnail rounded" style="width: 60px; height: 60px; object-fit: cover;">
                                            @else
                                                <div class="bg-light d-flex align-items-center justify-content-center rounded" 
                                                     style="width: 60px; height: 60px;">
                                                    <i class="bi bi-newspaper text-muted"></i>
                                                </div>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="d-flex flex-column">
                                                <strong>{{ Str::limit($item->title, 50) }}</strong>
                                                <small class="text-muted">Criado em: {{ $item->created_at->format('d/m/Y') }}</small>
                                            </div>
                                        </td>
                                        <td>
                                            @if($item->published)
                                                @if($item->published_at && $item->published_at <= now())
                                                    <span class="badge bg-success bg-opacity-10 text-success border border-success">Publicado</span>
                                                @elseif($item->published_at)
                                                    <span class="badge bg-warning bg-opacity-10 text-warning border border-warning">Agendado</span>
                                                @else
                                                    <span class="badge bg-primary bg-opacity-10 text-primary border border-primary">Publicado</span>
                                                @endif
                                            @else
                                                <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary">Rascunho</span>
                                            @endif
                                            
                                            @if($item->is_featured)
                                                <span class="badge bg-info bg-opacity-10 text-info border border-info mt-1 d-block">Destaque</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($item->published_at)
                                                <div class="fw-bold">{{ $item->published_at->format('d/m/Y') }}</div>
                                            @else
                                                <span class="text-muted">Não agendado</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <div class="d-flex justify-content-center gap-2">
                                                <a href="{{ route('news.show', $item) }}" 
                                                   class="btn btn-sm btn-outline-primary" 
                                                   data-bs-toggle="tooltip" data-bs-title="Visualizar" target="_blank">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                
                                                <a href="{{ route('admin.news.edit', $item) }}" 
                                                   class="btn btn-sm btn-outline-warning" 
                                                   data-bs-toggle="tooltip" data-bs-title="Editar">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                
                                                <form action="{{ route('admin.news.destroy', $item) }}" method="POST" 
                                                    class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" 
                                                            data-bs-toggle="tooltip" data-bs-title="Excluir"
                                                            onclick="return confirm('Tem certeza que deseja excluir permanentemente esta notícia?')">
                                                        <i class="bi bi-trash-fill"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-4">
                                            <div class="d-flex flex-column align-items-center">
                                                <i class="bi bi-newspaper text-muted" style="font-size: 2.5rem;"></i>
                                                <p class="mt-3 mb-2 fw-bold">Nenhuma notícia encontrada</p>
                                                <p class="text-muted mb-3">Não encontramos notícias correspondentes à sua busca</p>
                                                <a href="{{ route('admin.news.create') }}" class="btn btn-pipa-red btn-sm">
                                                    <i class="bi bi-plus-circle me-1"></i> Criar Notícia
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($news->hasPages())
                <div class="card-footer border-top py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="text-muted">
                            Mostrando {{ $news->firstItem() }} a {{ $news->lastItem() }} de {{ $news->total() }} resultados
                        </div>
                        <div>
                            {{ $news->links() }}
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Enable tooltips
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });
</script>
@endsection

@section('styles')
<style>
    .card {
        border-radius: 0.75rem;
    }
    
    .table th {
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.5px;
        color: #6c757d;
    }
    
    .table td {
        vertical-align: middle;
    }
    
    .img-thumbnail {
        padding: 0.25rem;
        border-radius: 0.5rem;
    }
    
    .badge {
        font-weight: 500;
        padding: 0.35em 0.65em;
    }
    
    .btn-sm {
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
    }
</style>
@endsection