@extends('layouts.app')

@section('title', 'Gerenciamento de Usuários')

@section('content')
<div class="container-fluid py-4">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-0"><i class="bi bi-people me-2"></i> Gerenciamento de Usuários</h2>
                    <p class="text-muted mb-0">Administre os usuários do sistema</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.users.create') }}" class="btn btn-pipa-red">
                        <i class="bi bi-plus-circle me-1"></i> Novo Usuário
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Alertas -->
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase text-muted mb-2">Total de Usuários</h6>
                            <h3 class="mb-0">{{ $totalUsers }}</h3>
                        </div>
                        <div class="bg-pipa-red bg-opacity-10 p-3 rounded">
                            <i class="bi bi-people text-pipa-red" style="font-size: 1.5rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase text-muted mb-2">Administradores</h6>
                            <h3 class="mb-0">{{ $adminUsers }}</h3>
                        </div>
                        <div class="bg-warning bg-opacity-10 p-3 rounded">
                            <i class="bi bi-shield-check text-warning" style="font-size: 1.5rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase text-muted mb-2">Bolsistas</h6>
                            <h3 class="mb-0">{{ $bolsistaUsers }}</h3>
                        </div>
                        <div class=" bg-opacity-10 p-3 rounded">
                            <i class="bi bi-person-badge text-info" style="font-size: 1.5rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase text-muted mb-2">Aguardando Liberação</h6>
                            <h3 class="mb-0">{{ $blockedUsers }}</h3>
                        </div>
                        <div class="bg-danger bg-opacity-10 p-3 rounded">
                            <i class="bi bi-lock text-danger" style="font-size: 1.5rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters and Search -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.users.index') }}">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label for="search" class="form-label">Buscar</label>
                                <input type="text" name="search" class="form-control" placeholder="Nome ou email..." 
                                       value="{{ request('search') }}">
                            </div>
                            <div class="col-md-3">
                                <label for="status" class="form-label">Status</label>
                                <select name="status" class="form-select">
                                    <option value="">Todos os status</option>
                                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Ativos</option>
                                    <option value="blocked" {{ request('status') == 'blocked' ? 'selected' : '' }}>Bloqueados</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="role" class="form-label">Tipo de Usuário</label>
                                <select name="role" class="form-select">
                                    <option value="">Todos os tipos</option>
                                    <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Administradores</option>
                                    <option value="user" {{ request('role') == 'user' ? 'selected' : '' }}>Usuários Comuns</option>
                                    <option value="bolsista" {{ request('role') == 'bolsista' ? 'selected' : '' }}>Bolsistas</option>
                                </select>
                            </div>
                            <div class="col-md-3 d-flex align-items-end">
                                <button type="submit" class="btn btn-pipa-red w-100 me-2">
                                    <i class="bi bi-funnel me-1"></i> Filtrar
                                </button>
                                <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary w-100">
                                    <i class="bi bi-arrow-clockwise me-1"></i> Limpar
                                </a>
                            </div>
                        </div>
                    </form>
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
                        <h6 class="mb-0">Lista de Usuários</h6>
                        <div class="d-flex align-items-center">
                            <span class="text-muted me-3">
                                Mostrando {{ $users->count() }} de {{ $users->total() }} usuários
                            </span>
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th class="ps-4">Usuário</th>
                                    <th>Email</th>
                                    <th width="120">Status</th>
                                    <th width="120">Tipo</th>
                                    <th width="150">Registro</th>
                                    <th width="180" class="text-center">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($users as $user)
                                    @php
                                        $isBolsista = !empty($user->permissions) && $user->permissions !== '[]' && $user->permissions !== 'null';
                                    @endphp
                                    <tr class="{{ $user->is_blocked ? 'table-warning' : '' }} {{ $isBolsista ? '' : '' }}">
                                        <td class="ps-4">
                                            <div class="d-flex align-items-center">
                                                <div>
                                                    <strong>{{ $user->name }}</strong>
                                                    <div class="text-muted small">ID: {{ $user->id }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            {{ $user->email }}
                                            @if($user->email_verified_at)
                                                <span class="badge bg-success bg-opacity-10 text-success border border-success mt-1 d-block">Email verificado</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($user->is_blocked)
                                                <span class="badge bg-danger">
                                                    <i class="bi bi-lock me-1"></i> Bloqueado
                                                </span>
                                            @else
                                                <span class="badge bg-success">
                                                    <i class="bi bi-check-circle me-1"></i> Ativo
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($user->is_admin)
                                                <span class="badge bg-warning">
                                                    <i class="bi bi-shield me-1"></i> Admin
                                                </span>
                                            @elseif($isBolsista)
                                                <span class="badge bg-info">
                                                    <i class="bi bi-person-badge me-1"></i> Bolsista
                                                </span>
                                            @else
                                                <span class="badge bg-secondary">
                                                    <i class="bi bi-person me-1"></i> Usuário
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="d-flex flex-column">
                                                <span class="fw-semibold">{{ $user->created_at->format('d/m/Y') }}</span>
                                                <small class="text-muted">{{ $user->created_at->format('H:i') }}</small>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <div class="d-flex justify-content-center gap-2">
                                                <a href="{{ route('admin.users.edit', $user) }}" 
                                                   class="btn btn-sm btn-outline-warning" 
                                                   data-bs-toggle="tooltip" data-bs-title="Editar usuário">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                
                                                @if($user->id !== auth()->id())
                                                    <form action="{{ route('admin.users.toggle-admin', $user) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm {{ $user->is_admin ? 'btn-warning' : 'btn-outline-warning' }}" 
                                                                data-bs-toggle="tooltip" data-bs-title="{{ $user->is_admin ? 'Remover admin' : 'Tornar admin' }}"
                                                                onclick="return confirm('Tem certeza que deseja alterar o status de administrador?')">
                                                            <i class="bi bi-shield"></i>
                                                        </button>
                                                    </form>
                                                    
                                                    <form action="{{ route('admin.users.toggle-block', $user) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm {{ $user->is_blocked ? 'btn-success' : 'btn-outline-danger' }}" 
                                                                data-bs-toggle="tooltip" data-bs-title="{{ $user->is_blocked ? 'Desbloquear' : 'Bloquear' }}"
                                                                onclick="return confirm('Tem certeza que deseja {{ $user->is_blocked ? 'desbloquear' : 'bloquear' }} este usuário?')">
                                                            <i class="bi bi-{{ $user->is_blocked ? 'unlock' : 'lock' }}"></i>
                                                        </button>
                                                    </form>
                                                    
                                                    <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-outline-danger" 
                                                                data-bs-toggle="tooltip" data-bs-title="Excluir usuário"
                                                                onclick="return confirm('Tem certeza que deseja excluir permanentemente este usuário?')">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </form>
                                                @else
                                                    <span class="d-inline-block" tabindex="0" data-bs-toggle="tooltip" 
                                                          data-bs-title="Você não pode modificar seu próprio usuário">
                                                        <button class="btn btn-sm btn-outline-secondary" disabled>
                                                            <i class="bi bi-person"></i>
                                                        </button>
                                                    </span>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-5">
                                            <div class="d-flex flex-column align-items-center">
                                                <i class="bi bi-people text-muted" style="font-size: 3rem;"></i>
                                                <p class="mt-3 mb-2 fw-bold">Nenhum usuário encontrado</p>
                                                <p class="text-muted mb-4">Não encontramos usuários correspondentes aos seus filtros</p>
                                                <a href="{{ route('admin.users.create') }}" class="btn btn-pipa-red">
                                                    <i class="bi bi-plus-circle me-1"></i> Criar Primeiro Usuário
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($users->hasPages())
                <div class="card-footer border-top py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="text-muted">
                            Mostrando {{ $users->firstItem() }} a {{ $users->lastItem() }} de {{ $users->total() }} resultados
                        </div>
                        <div>
                            {{ $users->links() }}
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
        
        // Auto-submit filters when select changes
        document.querySelectorAll('select[name="status"], select[name="role"]').forEach(select => {
            select.addEventListener('change', function() {
                this.form.submit();
            });
        });
    });
</script>
@endsection

@section('styles')
<style>
    .card {
        border-radius: 0.75rem;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    
    .card:hover {
        transform: translateY(-2px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
    }
    
    .table th {
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.5px;
        color: #6c757d;
        border-bottom: 2px solid #e9ecef;
    }
    
    .table td {
        vertical-align: middle;
        padding: 1rem 0.75rem;
    }
    
    .avatar-sm {
        width: 40px;
        height: 40px;
    }
    
    .avatar-title {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 100%;
        height: 100%;
        font-weight: 600;
        font-size: 1.1rem;
    }
    
    .badge {
        font-weight: 500;
        padding: 0.5em 0.75em;
        font-size: 0.75em;
    }
    
    .btn-sm {
        padding: 0.375rem 0.5rem;
        font-size: 0.75rem;
        border-radius: 0.375rem;
    }
    
    .table-hover tbody tr:hover {
        background-color: rgba(184, 36, 36, 0.04);
    }
    
    .table-warning {
        --bs-table-bg: rgba(255, 193, 7, 0.1);
    }
    
    .table-info {
        --bs-table-bg: rgba(13, 202, 240, 0.1);
    }
    
    .pagination {
        margin-bottom: 0;
    }
</style>
@endsection