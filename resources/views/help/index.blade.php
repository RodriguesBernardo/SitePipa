@extends('layouts.app')

@section('title', 'Ajuda')

@section('content')
<div class="container py-5">
    <!-- Header Section -->
    <div class="row mb-5">
        <div class="col-12 text-center">
            <h1 class="display-4 font-weight-bold text-primary mb-3">Central de Ajuda</h1>
            <p class="lead text-muted">Encontre orientações e suporte para utilizar o PIPA IFRS</p>
            <div class="divider mx-auto bg-gradient-primary" style="width: 100px; height: 4px;"></div>
        </div>
    </div>

    <!-- Help Cards Section -->
    <div class="row">
        <div class="col-lg-4 mb-4">
            <div class="card help-card h-100 border-0 shadow-sm hover-shadow transition">
                <div class="card-header bg-primary text-white rounded-top">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-user-tie fa-2x mr-3"></i>
                        <h2 class="h5 mb-0">Coordenadores</h2>
                    </div>
                </div>
                <div class="card-body">
                    <div class="help-content">
                        {!! $content->coordinators_content ?? '<p class="text-muted">Nenhum conteúdo disponível no momento.</p>' !!}
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4 mb-4">
            <div class="card help-card h-100 border-0 shadow-sm hover-shadow transition">
                <div class="card-header bg-success text-white rounded-top">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-user-graduate fa-2x mr-3"></i>
                        <h2 class="h5 mb-0">Bolsistas</h2>
                    </div>
                </div>
                <div class="card-body">
                    <div class="help-content">
                        {!! $content->interns_content ?? '<p class="text-muted">Nenhum conteúdo disponível no momento.</p>' !!}
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4 mb-4">
            <div class="card help-card h-100 border-0 shadow-sm hover-shadow transition">
                <div class="card-header bg-info text-white rounded-top">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-cogs fa-2x mr-3"></i>
                        <h2 class="h5 mb-0">Uso das Máquinas</h2>
                    </div>
                </div>
                <div class="card-body">
                    <div class="help-content">
                        {!! $content->machines_usage_content ?? '<p class="text-muted">Nenhum conteúdo disponível no momento.</p>' !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    .help-card {
        transition: all 0.3s ease;
        border-radius: 0.5rem;
    }
    
    .help-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important;
    }
    
    .help-content {
        min-height: 200px;
    }
    
    .divider {
        background: linear-gradient(90deg, #007bff, #00bfff);
    }
    
    .hover-shadow {
        box-shadow: 0 5px 15px rgba(0,0,0,0.08);
    }
    
    .transition {
        transition: all 0.3s cubic-bezier(.25,.8,.25,1);
    }
</style>
@endsection