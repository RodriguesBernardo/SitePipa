<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>{{ $game->title }} - PIPA IFRS</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            line-height: 1.5;
            color: #333;
            margin: 0;
            padding: 30px;
        }
        .header {
            margin-bottom: 30px;
            position: relative;
        }
        .logo {
            height: 60px;
            position: absolute;
            left: 0;
            top: 0;
        }
        .title-container {
            text-align: center;
            margin: 0 auto;
            max-width: 80%;
        }
        h1 {
            color: #d62b1f;
            font-size: 24px;
            margin: 0 0 10px;
            padding-top: 10px;
        }
        .subtitle {
            font-size: 14px;
            color: #666;
            margin-bottom: 20px;
        }
        .game-cover-container {
            display: flex;
            justify-content: center;
            margin: 20px auto;
            width: 100%;
        }
        .game-cover {
            max-height: 200px; /* Altura reduzida */
            width: auto;
            max-width: 100%;
            object-fit: contain;
            border: 1px solid #eee;
            padding: 5px;
            background-color: #f8f9fa;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            display: block; /* Garante centralização */
            margin: 0 auto; /* Centralização adicional */
        }
        .section {
            margin-bottom: 25px;
            page-break-inside: avoid;
        }
        .section-title {
            background-color: #f8f9fa;
            padding: 8px 12px;
            font-weight: bold;
            border-left: 4px solid #d62b1f;
            margin-bottom: 12px;
            font-size: 15px;
            color: #222;
        }
        .game-info {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 15px;
            margin-bottom: 25px;
            font-size: 13px;
        }
        .game-info-item {
            margin-bottom: 8px;
        }
        .game-info-item strong {
            display: inline-block;
            min-width: 120px;
            color: #555;
        }
        .content {
            font-size: 13px;
            text-align: justify;
            line-height: 1.6;
        }
        .content p {
            margin-bottom: 1rem;
        }
        .content ul,
        .content ol {
            margin-bottom: 1rem;
            padding-left: 2rem;
        }
        .content li {
            margin-bottom: 0.5rem;
        }
        .screenshots {
            margin-top: 25px;
        }
        .screenshot-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
            margin-top: 15px;
        }
        .screenshot {
            max-width: 100%;
            height: auto;
            border: 1px solid #ddd;
            padding: 5px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            border-radius: 4px;
        }
        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 11px;
            color: #666;
            border-top: 1px solid #eee;
            padding-top: 15px;
        }
        .header-content {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-bottom: 20px;
        }
        .header-text {
            text-align: center;
            margin-bottom: 20px;
        }
        .metadata {
            font-size: 12px;
            color: #555;
            margin-bottom: 5px;
        }
        .tags {
            display: flex;
            flex-wrap: wrap;
            gap: 5px;
            margin-top: 5px;
        }
        .tag {
            background-color: #f0f0f0;
            padding: 2px 8px;
            border-radius: 10px;
            font-size: 11px;
        }
        .featured-badge {
            position: absolute;
            top: 10px;
            left: 10px;
            background-color: #ffc107;
            color: #000;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: bold;
        }

        .game-cover-wrapper {
            position: relative;
            max-width: 250px; /* Tamanho reduzido */
            margin: 0 auto; /* Centralização */
        }
    </style>
</head>
<body>
    <div class="header">
        @if(file_exists($logoPath))
            <img src="{{ $logoPath }}" class="logo" alt="Logo PIPA">
        @endif
        
        <div class="header-content">
            <div class="header-text">
                <h1>{{ $game->title }}</h1>
                <div class="subtitle">Ficha Técnica do Jogo - PIPA IFRS</div>
<!--                 <div class="metadata">
                    Publicado em: {{ $game->created_at->format('d/m/Y') }} | 
                    Downloads: {{ $game->downloads_count }}
                </div> -->
            </div>
            
            @if($game->cover_image)
                <div class="game-cover-container">
                    <div style="position: relative;">
                        @if($game->is_featured)
                            <div class="featured-badge">
                                ★ Destaque
                            </div>
                        @endif
                        <img src="{{ storage_path('app/public/' . $game->cover_image) }}" class="game-cover" alt="Capa do jogo {{ $game->title }}">
                    </div>
                </div>
            @else
                <div class="game-cover-container">
                    <div style="height: 200px; width: 200px; background-color: #f8f9fa; display: flex; align-items: center; justify-content: center; border-radius: 8px;">
                        <span style="color: #ccc; font-size: 24px;">Sem capa</span>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <div class="game-info">
<!--         <div class="game-info-item">
            <strong>Resumo:</strong> {!! $game->short_description !!}
        </div> -->
        
        @if($game->tags->count() > 0)
        <div class="game-info-item">
            <strong>Categorias:</strong>
            <div class="tags">
                @foreach($game->tags as $tag)
                    <span class="tag">{{ $tag->name }}</span>
                @endforeach
            </div>
        </div>
        @endif
    </div>
    
    <div class="section">
        <div class="section-title">Regras do Jogo</div>
        <div class="content">{!! $game->long_description !!}</div>
    </div>
    
    @if($game->how_to_play)
    <div class="section">
        <div class="section-title">Instruções de Jogo</div>
        <div class="content">{!! $game->how_to_play !!}</div>
    </div>
    @endif
    
    @if($game->educational_objectives)
    <div class="section">
        <div class="section-title">Aprendizados</div>
        <div class="content">{!! $game->educational_objectives !!}</div>
    </div>
    @endif
    
    @if($game->screenshots->count() > 0)
    <div class="section screenshots">
        <div class="section-title">Imagens do Jogo</div>
        <div class="screenshot-grid">
            @foreach($game->screenshots as $screenshot)
                <img src="{{ Storage::url($screenshot->path) }}" class="screenshot" alt="Imagem do jogo {{ $loop->iteration }}">
            @endforeach
        </div>
    </div>
    @endif
    
<!--     <div class="footer">
        Gerado em {{ $currentDate }} | Plataforma PIPA IFRS - Instituto Federal do Rio Grande do Sul
    </div> -->
</body>
</html>