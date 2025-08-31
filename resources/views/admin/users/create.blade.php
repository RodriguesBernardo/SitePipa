@extends('layouts.app')

@section('title', 'Criar Novo Usuário')

@section('content')
<div class="container-fluid px-4">
    <div class="row mb-4">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">Usuários</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Novo Usuário</li>
                </ol>
            </nav>
            <h1 class="mb-0">
                <i class="bi bi-person-plus me-2"></i>Criar Novo Usuário
            </h1>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body">
            <form action="{{ route('admin.users.store') }}" method="POST">
                @csrf

                <div class="row g-3">
                    <!-- Nome -->
                    <div class="col-md-6">
                        <label for="name" class="form-label">Nome *</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" 
                               id="name" name="name" value="{{ old('name') }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div class="col-md-6">
                        <label for="email" class="form-label">Email *</label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" 
                               id="email" name="email" value="{{ old('email') }}" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Senha -->
                    <div class="col-md-6">
                        <label for="password" class="form-label">Senha *</label>
                        <input type="password" class="form-control @error('password') is-invalid @enderror" 
                               id="password" name="password" required>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Confirmação de Senha -->
                    <div class="col-md-6">
                        <label for="password_confirmation" class="form-label">Confirmar Senha *</label>
                        <input type="password" class="form-control" 
                               id="password_confirmation" name="password_confirmation" required>
                    </div>

                    <!-- Permissões -->
                    <div class="col-12">
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" 
                                   id="is_admin" name="is_admin" value="1" {{ old('is_admin') ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_admin">Administrador (tem acesso total)</label>
                        </div>

                        <hr>
                        
                        <h5 class="mb-3">Permissões Específicas:</h5>
                        
                        <div class="row">
                            @foreach($permissions as $key => $label)
                            <div class="col-md-6 col-lg-4 mb-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" 
                                           id="permission_{{ $key }}" name="permissions[]" 
                                           value="{{ $key }}" {{ in_array($key, old('permissions', [])) ? 'checked' : '' }}>
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
                                <i class="bi bi-save me-2"></i> Criar Usuário
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection