@extends('layouts.app')

@section('title', 'Editar Usuário')

@section('content')
<div class="container-fluid px-4">
    <div class="row mb-4">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">Usuários</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Editar Usuário</li>
                </ol>
            </nav>
            <h1 class="mb-0">
                <i class="bi bi-person-gear me-2"></i>Editar Usuário
            </h1>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body">
            <form action="{{ route('admin.users.update', $user) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row g-3">
                    <!-- Nome -->
                    <div class="col-md-6">
                        <label for="name" class="form-label">Nome *</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" 
                               id="name" name="name" value="{{ old('name', $user->name) }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div class="col-md-6">
                        <label for="email" class="form-label">Email *</label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" 
                               id="email" name="email" value="{{ old('email', $user->email) }}" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Senha (opcional) -->
                    <div class="col-md-6">
                        <label for="password" class="form-label">Nova Senha</label>
                        <input type="password" class="form-control @error('password') is-invalid @enderror" 
                               id="password" name="password">
                        <small class="text-muted">Deixe em branco para manter a senha atual</small>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Confirmação de Senha -->
                    <div class="col-md-6">
                        <label for="password_confirmation" class="form-label">Confirmar Nova Senha</label>
                        <input type="password" class="form-control" 
                               id="password_confirmation" name="password_confirmation">
                    </div>

                    <!-- Permissões -->
                    <div class="col-12">
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" 
                                   id="is_admin" name="is_admin" value="1" {{ old('is_admin', $user->is_admin) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_admin">Administrador (tem acesso total)</label>
                        </div>
                        
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" 
                                   id="is_blocked" name="is_blocked" value="1" {{ old('is_blocked', $user->is_blocked) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_blocked">Bloquear Usuário</label>
                        </div>

                        <hr>
                        
                        <h5 class="mb-3">Permissões Específicas:</h5>
                        
                        <div class="row">
                            @foreach($permissions as $key => $label)
                            <div class="col-md-6 col-lg-4 mb-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" 
                                           id="permission_{{ $key }}" name="permissions[]" 
                                           value="{{ $key }}" {{ in_array($key, old('permissions', $user->permissions ?? [])) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="permission_{{ $key }}">
                                        {{ $label }}
                                    </label>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Botões -->
                    <div class="col-12">
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left me-2"></i> Cancelar
                            </a>
                            <button type="submit" class="btn btn-pipa-red">
                                <i class="bi bi-save me-2"></i> Salvar Alterações
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection