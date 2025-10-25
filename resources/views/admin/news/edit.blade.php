@extends('layouts.app')

@section('title', 'Editar Notícia')

@section('content')
<div class="container-fluid px-4">
    <div class="row mb-4">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.news.index') }}">Notícias</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Editar Notícia</li>
                </ol>
            </nav>
            <h1 class="mb-0">
                <i class="bi bi-pencil-square me-2"></i>Editar Notícia
            </h1>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body">
            <form action="{{ route('admin.news.update', $news) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="row g-3">
                    <!-- Título -->
                    <div class="col-12">
                        <label for="title" class="form-label">Título da Notícia *</label>
                        <input type="text" class="form-control @error('title') is-invalid @enderror" 
                               id="title" name="title" value="{{ old('title', $news->title) }}" required>
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Resumo -->
                    <div class="col-12">
                        <label for="excerpt" class="form-label">Resumo (opcional)</label>
                        <textarea class="form-control editor @error('excerpt') is-invalid @enderror" 
                                  id="excerpt" name="excerpt" rows="2">{{ old('excerpt', $news->excerpt) }}</textarea>
                        <small class="text-muted">Um breve resumo que aparecerá na listagem (máx. 255 caracteres)</small>
                        @error('excerpt')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Conteúdo -->
                    <div class="col-12">
                        <label for="content" class="form-label">Conteúdo Completo *</label>
                        <textarea class="form-control editor @error('content') is-invalid @enderror" 
                                  id="content" name="content" rows="8" required>{{ old('content', $news->content) }}</textarea>
                        @error('content')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Status de Publicação -->
                    <div class="col-md-4">
                        <label for="status" class="form-label">Status *</label>
                        <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                            @php
                                $currentStatus = $news->published ? 
                                    ($news->published_at && $news->published_at > now() ? 'scheduled' : 'published') : 
                                    'draft';
                            @endphp
                            <option value="draft" {{ old('status', $currentStatus) == 'draft' ? 'selected' : '' }}>Rascunho</option>
                            <option value="scheduled" {{ old('status', $currentStatus) == 'scheduled' ? 'selected' : '' }}>Agendado</option>
                            <option value="published" {{ old('status', $currentStatus) == 'published' ? 'selected' : '' }}>Publicado</option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Data de Publicação (condicional) -->
                    <div class="col-md-4" id="publishedAtContainer" style="{{ old('status', $currentStatus) != 'scheduled' ? 'display: none;' : '' }}">
                        <label for="published_at" class="form-label">Data de Publicação *</label>
                        <input type="datetime-local" class="form-control @error('published_at') is-invalid @enderror" 
                               id="published_at" name="published_at" 
                               value="{{ old('published_at', $news->published_at ? $news->published_at->format('Y-m-d') : now()->format('Y-m-d')) }}">
                        @error('published_at')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Destaque -->
                    <div class="col-md-4">
                        <input type="hidden" name="is_featured" value="0">
                        <div class="form-check form-switch mt-4 pt-2">
                            <input class="form-check-input" type="checkbox" 
                                id="is_featured" name="is_featured" value="1" 
                                {{ old('is_featured', $news->is_featured) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_featured">Marcar como Destaque</label>
                        </div>
                    </div>

                    <!-- Imagem de Capa -->
                    <div class="col-md-6">
                        <label for="cover_image" class="form-label">Imagem de Capa</label>
                        <input type="file" class="form-control @error('cover_image') is-invalid @enderror" 
                               id="cover_image" name="cover_image" accept="image/*">
                        <small class="text-muted">Deixe em branco para manter a imagem atual</small>
                        @error('cover_image')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        
                        @if($news->cover_image)
                        <div class="mt-3">
                            <p class="mb-1">Imagem atual:</p>
                            <img src="{{ Storage::url($news->cover_image) }}" 
                                 class="img-thumbnail" 
                                 style="max-height: 150px; object-fit: cover;" 
                                 alt="Capa atual">
                        </div>
                        @endif
                    </div>

                    <!-- Imagem Destacada -->
                    <div class="col-md-6">
                        <label for="featured_image" class="form-label">Imagem Secundária (opcional)</label>
                        <input type="file" class="form-control @error('featured_image') is-invalid @enderror" 
                               id="featured_image" name="featured_image" accept="image/*">
                        <small class="text-muted">Deixe em branco para manter a imagem atual</small>
                        @error('featured_image')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        
                        @if($news->featured_image)
                        <div class="mt-3">
                            <p class="mb-1">Imagem atual:</p>
                            <img src="{{ Storage::url($news->featured_image) }}" 
                                 class="img-thumbnail" 
                                 style="max-height: 150px; object-fit: cover;" 
                                 alt="Imagem destacada atual">
                        </div>
                        @endif
                    </div>

                    <!-- Pré-visualização de Imagens -->
                    <div class="col-12">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="image-preview mb-3">
                                    <h6>Pré-visualização da Nova Capa:</h6>
                                    <img id="coverPreview" src="#" alt="Pré-visualização da capa" 
                                         class="img-fluid rounded border" style="display: none; max-height: 200px; object-fit: cover;">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="image-preview mb-3">
                                    <h6>Pré-visualização da Nova Imagem Secundária:</h6>
                                    <img id="featuredPreview" src="#" alt="Pré-visualização da imagem secundária" 
                                         class="img-fluid rounded border" style="display: none; max-height: 200px; object-fit: cover;">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Botões -->
                    <div class="col-12">
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.news.index') }}" class="btn btn-outline-secondary">
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

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Bootstrap Icons -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">

<!-- TinyMCE -->
<script src="{{ asset('tinymce/tinymce.min.js') }}"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Configuração idêntica ao template de ajuda
    tinymce.init({
        selector: '.editor',
        plugins: 'advlist autolink lists link image charmap preview anchor pagebreak',
        toolbar_mode: 'floating',
        toolbar: 'undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image',
        menubar: false,
        statusbar: false,
        height: 400,
        automatic_uploads: false,
        convert_urls: false,
        entity_encoding: 'raw',
        forced_root_block: 'p',
        force_br_newlines: false,
        force_p_newlines: true,
        content_style: 'body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; font-size: 14px; line-height: 1.6; color: #212529; }',
        setup: function(editor) {
            editor.on('init', function() {
                const content = editor.getContent();
                if (content && !content.includes('<')) {
                    const formattedContent = formatPlainTextToHtml(content);
                    editor.setContent(formattedContent);
                }
            });
            
            editor.on('change', function() {
                editor.save();
            });
        }
    });

    // Função idêntica
    function formatPlainTextToHtml(text) {
        let html = text.replace(/\r\n|\r|\n/g, '</p><p>');
        html = '<p>' + html + '</p>';
        html = html.replace(/<p><\/p>/g, '');
        html = html.replace(/(^|\n)\*\s(.*?)(?=\n|$)/g, '$1<li>$2</li>');
        html = html.replace(/(^|\n)-\s(.*?)(?=\n|$)/g, '$1<li>$2</li>');
        html = html.replace(/(<li>.*?<\/li>)+/g, function(match) {
            return '<ul>' + match + '</ul>';
        });
        return html;
    }

    document.querySelector('form').addEventListener('submit', function(e) {
        tinymce.triggerSave();
        return true;
    });

    // Controle do campo de data de publicação
    document.getElementById('status').addEventListener('change', function() {
        const publishedAtContainer = document.getElementById('publishedAtContainer');
        if (this.value === 'scheduled') {
            publishedAtContainer.style.display = 'block';
        } else {
            publishedAtContainer.style.display = 'none';
        }
    });

    // Pré-visualização da imagem de capa
    const coverInput = document.getElementById('cover_image');
    const coverPreview = document.getElementById('coverPreview');
    
    if (coverInput) {
        coverInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    coverPreview.src = e.target.result;
                    coverPreview.style.display = 'block';
                }
                reader.readAsDataURL(file);
            }
        });
    }

    // Pré-visualização da imagem destacada
    const featuredInput = document.getElementById('featured_image');
    const featuredPreview = document.getElementById('featuredPreview');
    
    if (featuredInput) {
        featuredInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    featuredPreview.src = e.target.result;
                    featuredPreview.style.display = 'block';
                }
                reader.readAsDataURL(file);
            }
        });
    }
});

document.getElementById('status').addEventListener('change', function() {
    const publishedAtContainer = document.getElementById('publishedAtContainer');
    const publishedAtInput = document.getElementById('published_at');
    
    if (this.value === 'scheduled') {
        publishedAtContainer.style.display = 'block';
    } else if (this.value === 'published') {
        publishedAtContainer.style.display = 'none';
        // Se mudar para publicado, define a data atual
        const now = new Date();
        const localDateTime = now.toISOString().slice(0, 16);
        publishedAtInput.value = localDateTime;
    } else {
        publishedAtContainer.style.display = 'none';
    }
});
</script>

<style>
/* Estilos idênticos */
.tox-tinymce {
    border-radius: 0.375rem !important;
    border: 1px solid #dee2e6 !important;
    margin-top: 0.5rem;
}

.tox .tox-toolbar__primary {
    background-color: #f8f9fa !important;
    border-bottom: 1px solid #dee2e6 !important;
    padding: 0.25rem !important;
}

.tox .tox-tbtn {
    border-radius: 0.25rem !important;
    margin: 0 2px !important;
}

.tox .tox-tbtn:hover {
    background-color: #e9ecef !important;
}

.tox .tox-tbtn--enabled {
    background-color: #d62b1f !important;
    color: white !important;
}

.tox .tox-edit-area__iframe {
    background-color: white !important;
}

@media (max-width: 768px) {
    .tox-tinymce {
        height: 300px !important;
    }
}

/* Estilos específicos do formulário */
.btn-pipa-red {
    background-color: #d62b1f;
    border-color: #d62b1f;
    color: white;
    font-weight: 500;
    padding: 10px 24px;
}

.btn-pipa-red:hover {
    background-color: #b8241a;
    border-color: #a52017;
    color: white;
}

.image-preview img {
    max-width: 100%;
    height: auto;
}

.img-thumbnail {
    padding: 0.25rem;
    background-color: #fff;
    border: 1px solid #dee2e6;
    border-radius: 0.25rem;
    max-width: 100%;
    height: auto;
}
</style>
@endsection

