@extends('layouts.app')

@section('title', 'Editar Jogo')

@section('content')
<div class="container">
    <h2 class="mb-4">Editar Jogo: {{ $game->title }}</h2>
    
    <!-- Exibir mensagens de erro gerais -->
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <h5 class="alert-heading">Erros encontrados:</h5>
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Exibir mensagens de sucesso/erro do session -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    
    <form action="{{ route('admin.games.update', $game) }}" method="POST" enctype="multipart/form-data" id="gameForm">
        @csrf
        @method('PUT')
        
        <div class="row">
            <div class="col-md-8">
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="title" class="form-label">Título</label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                   id="title" name="title" value="{{ old('title', $game->title) }}" required
                                   maxlength="255">
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                <span id="titleCounter">{{ strlen(old('title', $game->title)) }}</span>/255 caracteres
                            </small>
                        </div>
                        
                        <div class="mb-3">
                            <label for="short_description" class="form-label">Descrição Curta</label>
                            <textarea class="form-control editor @error('short_description') is-invalid @enderror" 
                                      id="short_description" name="short_description" rows="3" required
                                      maxlength="500">{{ old('short_description', $game->short_description) }}</textarea>
                            @error('short_description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                <span id="shortDescCounter">{{ strlen(old('short_description', $game->short_description)) }}</span>/255 caracteres
                            </small>
                        </div>
                        
                        <div class="mb-3">
                            <label for="long_description" class="form-label">Descrição Longa</label>
                            <textarea class="form-control editor @error('long_description') is-invalid @enderror" 
                                      id="long_description" name="long_description" rows="8" required
                                      maxlength="10000">{{ old('long_description', $game->long_description) }}</textarea>
                            @error('long_description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                <span id="longDescCounter">{{ strlen(old('long_description', $game->long_description)) }}</span>/10000 caracteres
                            </small>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="mb-3">
                            <p class="form-label">Imagem Atual:</p>
                            <img src="{{ Storage::url($game->cover_image) }}" class="img-fluid mb-2" alt="Capa atual" style="max-height: 150px;">
                            <label for="cover_image" class="form-label">Nova Imagem de Capa</label>
                            <input type="file" class="form-control @error('cover_image') is-invalid @enderror" 
                                   id="cover_image" name="cover_image" accept="image/*">
                            @error('cover_image')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                Formatos: JPEG, PNG, JPG, GIF. Máx: 5MB
                            </small>
                            <div id="coverPreview" class="mt-2 d-none">
                                <img src="" class="img-thumbnail" style="max-height: 150px;">
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Arquivo Atual</label>
                            <div class="mb-2 p-2 rounded">
                                <i class="bi bi-file-earmark-zip"></i> 
                                {{ basename($game->file_path) }}
                                <br>
                                <small class="text-muted">Última modificação: {{ $game->updated_at->format('d/m/Y H:i') }}</small>
                            </div>
                            <label for="file" class="form-label">Novo Arquivo do Jogo (ZIP/RAR)</label>
                            <input type="file" class="form-control @error('file') is-invalid @enderror" 
                                   id="file" name="file">
                            @error('file')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                Máximo: 100MB
                            </small>
                        </div>
                        
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="is_featured" 
                                   name="is_featured" value="1" {{ old('is_featured', $game->is_featured) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_featured">Destaque</label>
                        </div>
                    </div>
                </div>
                
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="card-title">Tags</h5>
                        @error('tags')
                            <div class="alert alert-danger">{{ $message }}</div>
                        @enderror
                        @foreach($tags as $tag)
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="tags[]" 
                                       value="{{ $tag->id }}" id="tag-{{ $tag->id }}"
                                       {{ in_array($tag->id, old('tags', $selectedTags)) ? 'checked' : '' }}>
                                <label class="form-check-label" for="tag-{{ $tag->id }}">
                                    {{ $tag->name }}
                                </label>
                            </div>
                        @endforeach
                    </div>
                </div>
                
                <button type="submit" class="btn btn-primary w-100" id="submitBtn">
                    <span class="spinner-border spinner-border-sm d-none" id="spinner"></span>
                    Atualizar Jogo
                </button>
                
                <a href="{{ route('admin.games.index') }}" class="btn btn-outline-secondary w-100 mt-2">
                    Cancelar
                </a>
            </div>
        </div>
    </form>
</div>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Bootstrap Icons -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">

<!-- TinyMCE -->
<script src="{{ asset('tinymce/tinymce.min.js') }}"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Contadores de caracteres
    function updateCharacterCounters() {
        $('#titleCounter').text($('#title').val().length);
        $('#shortDescCounter').text($('#short_description').val().length);
        $('#longDescCounter').text($('#long_description').val().length);
    }

    // Preview da nova imagem
    $('#cover_image').change(function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                $('#coverPreview').removeClass('d-none').find('img').attr('src', e.target.result);
            }
            reader.readAsDataURL(file);
        } else {
            $('#coverPreview').addClass('d-none');
        }
    });

    // Configuração do TinyMCE
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
                updateCharacterCounters();
            });
            
            editor.on('keyup', function() {
                updateCharacterCounters();
            });
            
            editor.on('change', function() {
                editor.save();
                updateCharacterCounters();
            });
        }
    });

    // Função para converter texto simples para HTML
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

    // Atualizar contadores ao digitar nos campos normais
    $('#title, #short_description, #long_description').on('input', updateCharacterCounters);

    // Inicializar contadores
    updateCharacterCounters();

    // Prevenir envio duplo do formulário
    $('#gameForm').on('submit', function() {
        $('#submitBtn').prop('disabled', true);
        $('#spinner').removeClass('d-none');
        tinymce.triggerSave();
        return true;
    });

    // Validar tamanho do arquivo antes do envio
    $('#gameForm').on('submit', function(e) {
        const coverFile = $('#cover_image')[0].files[0];
        const gameFile = $('#file')[0].files[0];
        
        if (coverFile && coverFile.size > 5 * 1024 * 1024) {
            e.preventDefault();
            alert('A imagem de capa não pode ter mais de 5MB');
            return false;
        }
        
        if (gameFile && gameFile.size > 100 * 1024 * 1024) {
            e.preventDefault();
            alert('O arquivo do jogo não pode ter mais de 100MB');
            return false;
        }
    });

    // Mostrar alerta se o usuário tentar sair da página com alterações não salvas
    let formChanged = false;
    $('#gameForm').on('change keyup', function() {
        formChanged = true;
    });

    window.addEventListener('beforeunload', function(e) {
        if (formChanged) {
            e.preventDefault();
            e.returnValue = 'Você tem alterações não salvas. Tem certeza que deseja sair?';
        }
    });

    $('#gameForm').on('submit', function() {
        formChanged = false;
    });
});
</script>

<style>
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

.invalid-feedback {
    display: block;
}

@media (max-width: 768px) {
    .tox-tinymce {
        height: 300px !important;
    }
}
</style>
@endsection