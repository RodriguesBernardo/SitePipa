@extends('layouts.app')

@section('title', 'Adicionar Jogo')

@section('content')
<div class="container">
    <h2 class="mb-4">Adicionar Novo Jogo</h2>
    
    <form action="{{ route('admin.games.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        
        <div class="row">
            <div class="col-md-8">
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="title" class="form-label">Título</label>
                            <input type="text" class="form-control" id="title" name="title" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="short_description" class="form-label">Sobre o jogo</label>
                            <textarea class="form-control editor" id="short_description" name="short_description" rows="3" required></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label for="long_description" class="form-label">Regras</label>
                            <textarea class="form-control editor" id="long_description" name="long_description" rows="8" required></textarea>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="cover_image" class="form-label">Imagem de Capa</label>
                            <input type="file" class="form-control" id="cover_image" name="cover_image" accept="image/*" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="file" class="form-label">Arquivo do Jogo (ZIP/RAR)</label>
                            <input type="file" class="form-control" id="file" name="file" required>
                        </div>
                        
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="is_featured" name="is_featured" value="1" {{ old('is_featured', $game->is_featured ?? false) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_featured">Destaque</label>
                        </div>
                    </div>
                </div>
                
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="card-title">Tags</h5>
                        @foreach($tags as $tag)
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="tags[]" value="{{ $tag->id }}" id="tag-{{ $tag->id }}">
                                <label class="form-check-label" for="tag-{{ $tag->id }}">
                                    {{ $tag->name }}
                                </label>
                            </div>
                        @endforeach
                    </div>
                </div>
                
                <button type="submit" class="btn btn-primary w-100">Salvar Jogo</button>
            </div>
        </div>
    </form>
</div>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Bootstrap Icons -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">

<!-- TinyMCE Local (sem necessidade de chave API) -->
<script src="{{ asset('tinymce/tinymce.min.js') }}"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Configuração do TinyMCE (idêntica ao template de ajuda)
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
        // Remova a configuração de API key que estava causando o problema
        setup: function(editor) {
            // Converte conteúdo existente ao inicializar
            editor.on('init', function() {
                const content = editor.getContent();
                if (content && !content.includes('<')) {
                    const formattedContent = formatPlainTextToHtml(content);
                    editor.setContent(formattedContent);
                }
            });
            
            // Garante que o conteúdo seja salvo no textarea
            editor.on('change', function() {
                editor.save();
            });
        }
    });

    // Função para converter texto simples para HTML (idêntica)
    function formatPlainTextToHtml(text) {
        // Converte quebras de linha para parágrafos
        let html = text.replace(/\r\n|\r|\n/g, '</p><p>');
        html = '<p>' + html + '</p>';
        
        // Remove parágrafos vazios
        html = html.replace(/<p><\/p>/g, '');
        
        // Converte listas com marcadores
        html = html.replace(/(^|\n)\*\s(.*?)(?=\n|$)/g, '$1<li>$2</li>');
        html = html.replace(/(^|\n)-\s(.*?)(?=\n|$)/g, '$1<li>$2</li>');
        
        // Envolve listas em tags ul
        html = html.replace(/(<li>.*?<\/li>)+/g, function(match) {
            return '<ul>' + match + '</ul>';
        });
        
        return html;
    }

    // Ao enviar o formulário
    document.querySelector('form').addEventListener('submit', function(e) {
        // Salva o conteúdo de todos os editores
        tinymce.triggerSave();
        return true;
    });
});
</script>

<style>
/* Estilos idênticos ao template de ajuda */
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
</style>
@endsection