@extends('layouts.app')

@section('title', 'Editar Conteúdo de Ajuda')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-pipa-red">
                    <div class="d-flex justify-content-between align-items-center">
                        <h2 class="h5 mb-0">
                            <i class="bi bi-question-circle me-2"></i>Editar Conteúdo de Ajuda
                        </h2>
                            <a href="{{ route('admin.dashboard') }}" class="btn btn-sm btn-light">
                                <i class="bi bi-arrow-left me-1"></i> Voltar
                            </a>
                    </div>
                </div>
                
                <div class="card-body">
                    <form action="{{ route('admin.help.update') }}" method="POST" id="helpForm">
                        @csrf
                        @method('PUT')
                        
                        <!-- Abas de navegação -->
                        <ul class="nav nav-tabs mb-4" id="helpTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="coordinators-tab" data-bs-toggle="tab" data-bs-target="#coordinators" type="button" role="tab">
                                    <i class="bi bi-person-badge me-1"></i> Coordenadores
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="interns-tab" data-bs-toggle="tab" data-bs-target="#interns" type="button" role="tab">
                                    <i class="bi bi-people me-1"></i> Estagiários
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="machines-tab" data-bs-toggle="tab" data-bs-target="#machines" type="button" role="tab">
                                    <i class="bi bi-pc me-1"></i> Máquinas
                                </button>
                            </li>
                        </ul>
                        
                        <!-- Conteúdo das abas -->
                        <div class="tab-content" id="helpTabsContent">
                            <!-- Aba Coordenadores -->
                            <div class="tab-pane fade show active" id="coordinators" role="tabpanel" aria-labelledby="coordinators-tab">
                                <div class="mb-3">
                                    <label for="coordinators_content" class="form-label">
                                        Conteúdo para Coordenadores
                                    </label>
                                    <textarea class="form-control editor" id="coordinators_content" name="coordinators_content">{{ old('coordinators_content', $content->coordinators_content ?? '') }}</textarea>
                                </div>
                            </div>
                            
                            <!-- Aba Estagiários -->
                            <div class="tab-pane fade" id="interns" role="tabpanel" aria-labelledby="interns-tab">
                                <div class="mb-3">
                                    <label for="interns_content" class="form-label">
                                        Conteúdo para Estagiários
                                    </label>
                                    <textarea class="form-control editor" id="interns_content" name="interns_content">{{ old('interns_content', $content->interns_content ?? '') }}</textarea>
                                </div>
                            </div>
                            
                            <!-- Aba Máquinas -->
                            <div class="tab-pane fade" id="machines" role="tabpanel" aria-labelledby="machines-tab">
                                <div class="mb-3">
                                    <label for="machines_usage_content" class="form-label">
                                        Instruções de Uso das Máquinas
                                    </label>
                                    <textarea class="form-control editor" id="machines_usage_content" name="machines_usage_content">{{ old('machines_usage_content', $content->machines_usage_content ?? '') }}</textarea>
                                </div>
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-end mt-4">
                            <button type="submit" class="btn btn-pipa-green">
                                <i class="bi bi-save me-2"></i> Salvar Alterações
                            </button>
                        </div>
                    </form>
                </div>
            </div>
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

    // Função para converter texto simples para HTML
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
    document.getElementById('helpForm').addEventListener('submit', function(e) {
        // Salva o conteúdo de todos os editores
        tinymce.triggerSave();
        return true;
    });

    // Redimensiona o editor ao trocar de aba
    document.querySelectorAll('[data-bs-toggle="tab"]').forEach(tab => {
        tab.addEventListener('shown.bs.tab', function() {
            setTimeout(function() {
                tinymce.execCommand('mceAutoResize');
            }, 100);
        });
    });
});
</script>

<style>
/* Estilos para o TinyMCE */
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

/* Estilos para o conteúdo editável */
.tox .tox-edit-area__iframe {
    background-color: white !important;
}

/* Melhorias nas abas */
.nav-tabs .nav-link {
    color: #495057;
    font-weight: 500;
    border: none;
    border-bottom: 3px solid transparent;
    padding: 0.75rem 1.25rem;
    transition: all 0.2s;
}

.nav-tabs .nav-link:hover {
    border-bottom-color: #dee2e6;
}

.nav-tabs .nav-link.active {
    color: #d62b1f;
    border-bottom: 3px solid #d62b1f;
    background-color: transparent;
}

/* Botão de submit */
.btn-pipa-green {
    background-color: #28a745;
    border-color: #28a745;
    color: white;
    font-weight: 500;
    padding: 10px 24px;
}

.btn-pipa-green:hover {
    background-color: #218838;
    border-color: #1e7e34;
    color: white;
}

/* Responsivo */
@media (max-width: 768px) {
    .card-body {
        padding: 1rem;
    }
    
    .tox-tinymce {
        height: 300px !important;
    }
    
    .nav-tabs .nav-link {
        padding: 0.5rem 0.75rem;
        font-size: 0.875rem;
    }
}
</style>

@endsection