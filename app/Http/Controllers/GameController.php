<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\GameTag;
use App\Models\GameDownload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;

class GameController extends Controller
{
    public function __construct()
    {
        // Aplicar middleware apenas para métodos admin
        $this->middleware('auth')->only(['create', 'store', 'edit', 'update', 'destroy', 'restore', 'adminIndex', 'toggleFeatured', 'forceDelete']);
    }

    public function index(Request $request)
    {
        $query = Game::query();
        
        // Search
        if ($request->has('search')) {
            $query->search($request->search);
        }
        
        // Tag filter
        if ($request->has('tag')) {
            $query->whereHas('tags', function($q) use ($request) {
                $q->where('slug', $request->tag);
            });
        }
        
        // Featured filter
        if ($request->has('filter') && $request->filter == 'featured') {
            $query->featured();
        }
        
        // Sort filters
        if ($request->has('sort')) {
            switch ($request->sort) {
                case 'newest':
                    $query->latest();
                    break;
                case 'oldest':
                    $query->oldest();
                    break;
                case 'downloads':
                    $query->withCount('downloads')->orderBy('downloads_count', 'desc');
                    break;
            }
        } else {
            $query->latest(); // Default sorting
        }
        
        $games = $query->paginate(12);
        $tags = GameTag::withCount('games')->orderBy('name')->get();
        
        // Get featured games for the featured section
        $featuredGames = Game::featured()->latest()->take(3)->get();
        
        return view('games.index', compact('games', 'tags', 'featuredGames'));
    }

    public function show(Game $game)
    {
        // Carrega jogos relacionados que compartilham pelo menos uma tag
        $relatedGames = Game::whereHas('tags', function($q) use ($game) {
                $q->whereIn('game_tags.id', $game->tags->pluck('id'));
            })
            ->where('id', '!=', $game->id)
            ->inRandomOrder()
            ->limit(3)
            ->get();
        
        return view('games.show', compact('game', 'relatedGames'));
    }

    public function download(Game $game)
    {
        $filePath = str_starts_with($game->file_path, 'games/') 
            ? $game->file_path 
            : 'games/' . ltrim($game->file_path, '/');
        
        // Verifica se o arquivo existe no disco 'public'
        if (!Storage::disk('public')->exists($filePath)) {
            \Log::error("Arquivo não encontrado: {$filePath}");
            abort(404, "Arquivo não encontrado: {$filePath}");
        }
        
        $game->downloads()->create([
            'user_id' => auth()->id(),
            'ip_address' => request()->ip()
        ]);
        
        return Storage::disk('public')->download($filePath, Str::slug($game->title).'.zip');
    }

    public function rate(Request $request, Game $game)
    {
        $request->validate([
            'rating' => 'required|integer|between:1,5',
            'comment' => 'nullable|string|max:500',
        ]);
        
        return back()->with('success', 'Obrigado por avaliar este jogo!');
    }

    public function create()
    {
        if(!auth()->user()->is_admin && !auth()->user()->hasPermission('create_games')){
            abort(403, 'Você não tem permissão para criar jogos.');
        }
        $tags = GameTag::all();
        return view('admin.games.create', compact('tags'));
    }

    public function store(Request $request)
    {
        if(!auth()->user()->is_admin && !auth()->user()->hasPermission('create_games')){
            abort(403, 'Você não tem permissão para criar jogos.');
        }

        \Log::info('Iniciando store do jogo', ['request' => $request->all()]);
        
        $request->validate([
            'title' => 'required|string|max:255',
            'short_description' => 'required|string|max:500', // Aumentado para 500 caracteres
            'long_description' => 'required|string|max:10000', // Adicionado limite de 10.000 caracteres
            'cover_image' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120', // Aumentado para 5MB
            'file' => 'required|file|mimes:zip,rar|max:102400', // Aumentado para 100MB
            'is_featured' => 'nullable|boolean',
            'tags' => 'array',
            'tags.*' => 'exists:game_tags,id',
        ], [
            'title.required' => 'O título é obrigatório',
            'title.max' => 'O título não pode ter mais de 255 caracteres',
            'short_description.required' => 'A descrição curta é obrigatória',
            'short_description.max' => 'A descrição curta não pode ter mais de 500 caracteres',
            'long_description.required' => 'As regras são obrigatórias',
            'long_description.max' => 'As regras não podem ter mais de 10.000 caracteres',
            'cover_image.required' => 'A imagem de capa é obrigatória',
            'cover_image.image' => 'O arquivo deve ser uma imagem',
            'cover_image.mimes' => 'A imagem deve ser JPEG, PNG, JPG ou GIF',
            'cover_image.max' => 'A imagem não pode ter mais de 5MB',
            'file.required' => 'O arquivo do jogo é obrigatório',
            'file.mimes' => 'O arquivo deve ser ZIP ou RAR',
            'file.max' => 'O arquivo não pode ter mais de 100MB',
        ]);

        try {
            // Upload cover image
            $coverPath = $request->file('cover_image')->store('games/covers', 'public');
            \Log::info('Capa armazenada', ['path' => $coverPath]);
            
            // Upload game file
            $filePath = $request->file('file')->store('games/files', 'public');
            \Log::info('Arquivo do jogo armazenado', ['path' => $filePath]);

            $game = Game::create([
                'title' => $request->title,
                'slug' => Str::slug($request->title),
                'short_description' => $request->short_description,
                'long_description' => $request->long_description,
                'cover_image' => $coverPath,
                'file_path' => $filePath,
                'is_featured' => $request->boolean('is_featured'),
            ]);
            \Log::info('Jogo criado no banco de dados', ['game_id' => $game->id]);

            // Sync tags
            if ($request->has('tags')) {
                $game->tags()->sync($request->tags);
                \Log::info('Tags sincronizadas', ['tags' => $request->tags]);
            }

            return redirect()->route('admin.games.index')
                ->with('success', 'Jogo criado com sucesso!');
                
        } catch (\Exception $e) {
            \Log::error('Erro ao criar jogo', ['error' => $e->getMessage()]);
            return back()->with('error', 'Erro ao criar jogo: ' . $e->getMessage())
                        ->withInput(); // Mantém os dados do formulário
        }
    }

    public function edit(Game $game)
    {
        if(!auth()->user()->is_admin && !auth()->user()->hasPermission('edit_games')){
            abort(403, 'Você não tem permissão para editar jogos.');
        }
        $tags = GameTag::all();
        $selectedTags = $game->tags->pluck('id')->toArray();
        return view('admin.games.edit', compact('game', 'tags', 'selectedTags'));
    }

    public function update(Request $request, Game $game)
    {
        if(!auth()->user()->is_admin && !auth()->user()->hasPermission('edit_games')){
            abort(403, 'Você não tem permissão para editar jogos.');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'short_description' => 'required|string|max:255',
            'long_description' => 'required|string',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'file' => 'nullable|file|mimes:zip,rar',
            'is_featured' => 'boolean',
            'tags' => 'array',
            'tags.*' => 'exists:game_tags,id',
        ]);

        $data = [
            'title' => $request->title,
            'slug' => Str::slug($request->title),
            'short_description' => $request->short_description,
            'long_description' => $request->long_description,
            'is_featured' => $request->boolean('is_featured'),
        ];

        // Update cover image if provided
        if ($request->hasFile('cover_image')) {
            Storage::disk('public')->delete($game->cover_image);
            $data['cover_image'] = $request->file('cover_image')->store('games/covers', 'public');
        }

        // Update game file if provided
        if ($request->hasFile('file')) {
            Storage::disk('public')->delete($game->file_path);
            $data['file_path'] = $request->file('file')->store('games/files', 'public');
        }

        $game->update($data);

        $validated['published'] = $request->status === 'published' || $request->status === 'scheduled';

        // Sync tags
        $game->tags()->sync($request->tags ?? []);

        return redirect()->route('admin.games.index')
            ->with('success', 'Jogo atualizado com sucesso!');
    }

    public function destroy(Game $game)
    {
        if (!auth()->user()->is_admin && !auth()->user()->hasPermission('edit_games')) {
            abort(403, 'Você não tem permissão para excluir jogos.');
        }
        
        // Delete files
        Storage::disk('public')->delete([$game->cover_image, $game->file_path]);
        
        // Delete screenshots if any
        foreach ($game->screenshots as $screenshot) {
            Storage::disk('public')->delete($screenshot->path);
            $screenshot->delete();
        }
        
        // Delete the game
        $game->delete();
        
        return back()->with('success', 'Jogo excluído com sucesso!');
    }

    public function restore($id)
    {
        if (!auth()->user()->is_admin && !auth()->user()->hasPermission('edit_games')) {
            abort(403, 'Você não tem permissão para restaurar jogos.');
        }
        
        $game = Game::withTrashed()->findOrFail($id);
        $game->restore();

        return back()->with('success', 'Jogo restaurado com sucesso!');
    }

    public function generatePdf(Game $game)
    {
        $logoPath = public_path('images/logo.png');
        
        if (!file_exists($logoPath)) {
            $logoPath = null;
        }
        
        $data = [
            'game' => $game,
            'logoPath' => $logoPath,
            'currentDate' => now()->format('d/m/Y'),
        ];
        
        $pdf = Pdf::loadView('games.pdf', $data)
            ->setPaper('a4', 'portrait')
            ->setOption('isPhpEnabled', true)
            ->setOption('isRemoteEnabled', true);
        
        return $pdf->download(Str::slug($game->title).'.pdf');
    }

    public function adminIndex(Request $request)
    {
        if (!auth()->user()->is_admin && !auth()->user()->hasPermission('edit_games') && !auth()->user()->hasPermission('create_games')) {
            abort(403, 'Você não tem permissão para visualizar jogos.');
        }

        $query = Game::withCount('downloads');
        
        // Filtros
        if ($request->has('featured') && in_array($request->featured, ['0', '1'])) {
            $query->where('is_featured', $request->featured);
        }
        
        // Busca
        if ($request->has('search')) {
            $query->search($request->search);
        }
        
        // Ordenação padrão
        $query->latest();
        
        $games = $query->paginate(15);
        
        // Estatísticas simplificadas
        $stats = [
            'total' => Game::count(),
            'featured' => Game::featured()->count(),
            'downloads' => GameDownload::count(),
        ];
        
        return view('admin.games.index', compact('games', 'stats'));
    }

    public function toggleFeatured(Request $request, Game $game)
    {
        if (!auth()->user()->is_admin && !auth()->user()->hasPermission('edit_games')) {
            abort(403, 'Você não tem permissão para destacar jogos.');
        }
        
        $request->validate([
            'is_featured' => 'required|boolean'
        ]);
        
        $game->update(['is_featured' => $request->is_featured]);
        
        return response()->json([
            'success' => true,
            'is_featured' => $game->is_featured,
            'message' => $game->is_featured ? 'Jogo destacado com sucesso!' : 'Jogo removido dos destaques!'
        ]);
    }

    public function forceDelete($id)
    {
        if (!auth()->user()->is_admin && !auth()->user()->hasPermission('edit_games')) {
            abort(403, 'Você não tem permissão para excluir permanentemente jogos.');
        }
        
        $game = Game::withTrashed()->findOrFail($id);
        
        // Delete files
        Storage::disk('public')->delete([$game->cover_image, $game->file_path]);
        
        // Delete screenshots if any
        foreach ($game->screenshots as $screenshot) {
            Storage::disk('public')->delete($screenshot->path);
            $screenshot->delete();
        }
        
        // Force delete game
        $game->forceDelete();
        
        return back()->with('success', 'Jogo excluído permanentemente com sucesso!');
    }

    protected function getStats()
    {
        return [
            'total' => Game::withTrashed()->count(),
            'active' => Game::count(),
            'trashed' => Game::onlyTrashed()->count(),
            'featured' => Game::featured()->count(),
            'downloads' => GameDownload::count(),
        ];
    }    
}