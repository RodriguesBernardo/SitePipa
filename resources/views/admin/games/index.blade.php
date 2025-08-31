@extends('layouts.app')

@section('title', 'Gerenciar Jogos')

@section('content')
<div class="container-fluid py-4">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.games.create') }}" class="btn btn-pipa-green">
                        <i class="bi bi-plus-circle me-1"></i> Adicionar Jogo
                    </a>
                    <div class="dropdown">
                        <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="filterDropdown" 
                                data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-funnel"></i> Filtrar
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="filterDropdown">
                            <li><h6 class="dropdown-header">Destaques</h6></li>
                            <li>
                                <a class="dropdown-item" href="{{ route('admin.games.index', ['featured' => '1']) }}">
                                    <i class="bi bi-star-fill text-warning me-2"></i> Em destaque
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="{{ route('admin.games.index', ['featured' => '0']) }}">
                                    <i class="bi bi-star text-secondary me-2"></i> Sem destaque
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item" href="{{ route('admin.games.index') }}">
                                    <i class="bi bi-x-circle me-2"></i> Limpar filtros
                                </a>
                            </li>
                        </ul>
                    </div>
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
                            <h6 class="text-uppercase text-muted mb-2">Total de Jogos</h6>
                            <h3 class="mb-0">{{ $stats['total'] }}</h3>
                        </div>
                        <div class="bg-pipa-red bg-opacity-10 p-3 rounded">
                            <i class="bi bi-joystick text-pipa-red" style="font-size: 1.5rem;"></i>
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
                            <h6 class="text-uppercase text-muted mb-2">Jogos em Destaque</h6>
                            <h3 class="mb-0">{{ $stats['featured'] }}</h3>
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
                            <h6 class="text-uppercase text-muted mb-2">Total de Downloads</h6>
                            <h3 class="mb-0">{{ $stats['downloads'] }}</h3>
                        </div>
                        <div class="bg-success bg-opacity-10 p-3 rounded">
                            <i class="bi bi-download text-success" style="font-size: 1.5rem;"></i>
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
                        <h6 class="mb-0">Lista de Jogos</h6>
                        <form class="d-flex" method="GET" action="{{ route('admin.games.index') }}">
                            <div class="input-group input-group-sm" style="width: 250px;">
                                <input type="text" name="search" class="form-control" placeholder="Buscar jogos..." 
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
                                    <th width="80" class="text-center">Imagem</th>
                                    <th>Título</th>
                                    <th>Tags</th>
                                    <th width="120" class="text-center">Downloads</th>
                                    <th width="180" class="text-center">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($games as $game)
                                    <tr>
                                        <td class="text-center">
                                            @if($game->cover_image)
                                                <img src="{{ Storage::url($game->cover_image) }}" alt="{{ $game->title }}" 
                                                     class="img-thumbnail rounded" style="width: 60px; height: 60px; object-fit: cover;">
                                            @else
                                                <div class="bg-light d-flex align-items-center justify-content-center rounded" 
                                                     style="width: 60px; height: 60px;">
                                                    <i class="bi bi-joystick text-muted"></i>
                                                </div>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="d-flex flex-column">
                                                <strong>{{ $game->title }}</strong>
                                                <small class="text-muted">{!! Str::limit($game->short_description, 50) !!}</small>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex flex-wrap gap-1">
                                                @foreach($game->tags->take(3) as $tag)
                                                    <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary">{{ $tag->name }}</span>
                                                @endforeach
                                                @if($game->tags->count() > 3)
                                                    <span class="badge bg-light text-dark border">+{{ $game->tags->count() - 3 }}</span>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-pipa-red bg-opacity-10 text-pipa-red">
                                                <i class="bi bi-download me-1"></i> {{ $game->downloads_count }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <div class="d-flex justify-content-center gap-2">
                                                <a href="{{ route('games.show', $game) }}" class="btn btn-sm btn-outline-primary" 
                                                data-bs-toggle="tooltip" data-bs-title="Visualizar">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                
                                                <a href="{{ route('admin.games.edit', $game) }}" 
                                                class="btn btn-sm btn-outline-warning" data-bs-toggle="tooltip" 
                                                data-bs-title="Editar">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                
                                                <form action="{{ route('admin.games.destroy', $game) }}" method="POST" 
                                                    class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" 
                                                            data-bs-toggle="tooltip" data-bs-title="Excluir"
                                                            onclick="return confirm('Tem certeza que deseja excluir permanentemente este jogo? Esta ação não pode ser desfeita.')">
                                                        <i class="bi bi-trash-fill"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4">
                                            <div class="d-flex flex-column align-items-center">
                                                <i class="bi bi-joystick text-muted" style="font-size: 2.5rem;"></i>
                                                <p class="mt-3 mb-2 fw-bold">Nenhum jogo encontrado</p>
                                                <p class="text-muted mb-3">Não encontramos jogos correspondentes à sua busca</p>
                                                <a href="{{ route('admin.games.create') }}" class="btn btn-pipa-green btn-sm">
                                                    <i class="bi bi-plus-circle me-1"></i> Adicionar Jogo
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($games->hasPages())
                <div class="card-footer border-top py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="text-muted">
                            Mostrando {{ $games->firstItem() }} a {{ $games->lastItem() }} de {{ $games->total() }} resultados
                        </div>
                        <div>
                            {{ $games->links() }}
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
        
        // Toggle featured status
        document.querySelectorAll('.toggle-featured').forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                const gameId = this.dataset.id;
                const isFeatured = this.dataset.featured === '1';
                
                fetch(`/admin/games/${gameId}/toggle-featured`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        is_featured: !isFeatured
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if(data.success) {
                        location.reload();
                    }
                });
            });
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