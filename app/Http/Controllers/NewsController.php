<?php

namespace App\Http\Controllers;

use App\Models\News;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class NewsController extends Controller
{
    public function __construct()
    {
        // Aplicar middleware apenas para métodos admin
        $this->middleware('auth')->only(['adminIndex', 'create', 'store', 'edit', 'update', 'destroy', 'toggleFeatured']);
    }

    // Métodos públicos
    public function index()
    {
        $query = News::where('published', true)
            ->where(function($query) {
                $query->whereNull('published_at')
                    ->orWhere('published_at', '<=', now());
            });

        // Filtrar por destaques
        if (request()->has('filter') && request('filter') == 'featured') {
            $query->where('is_featured', true);
        }

        $featuredNews = News::where('is_featured', true)
            ->where('published', true)
            ->where(function($query) {
                $query->whereNull('published_at')
                    ->orWhere('published_at', '<=', now());
            })
            ->orderBy('published_at', 'desc')
            ->take(2)
            ->get();

        $news = $query->orderBy('published_at', 'desc')->paginate(6);

        return view('news.index', compact('featuredNews', 'news'));
    }

    // Método para listagem administrativa
    public function adminIndex()
    {
        if (!auth()->user()->is_admin && !auth()->user()->hasPermission('edit_news') && !auth()->user()->hasPermission('create_news')) {
            abort(403, 'Você não tem permissão para visualizar notícias.');
        }
        
        $news = News::latest()
            ->with('user')
            ->paginate(10);

        return view('admin.news.index', compact('news'));
    }

    public function show(News $news)
    {
        return view('news.show', compact('news'));
    }

    public function create()
    {
        if (!auth()->user()->is_admin && !auth()->user()->hasPermission('create_news')) {
            abort(403, 'Você não tem permissão para criar notícias.');
        }
        
        return view('admin.news.create');
    }

    public function store(Request $request)
    {
        if (!auth()->user()->is_admin && !auth()->user()->hasPermission('create_news')) {
            abort(403, 'Você não tem permissão para criar notícias.');
        }

        $validated = $request->validate([
            'title' => 'required|max:255',
            'excerpt' => 'nullable|string|max:300',
            'content' => 'required',
            'cover_image' => 'required|image|max:2048',
            'featured_image' => 'nullable|image|max:2048',
            'is_featured' => 'nullable|boolean',
            'published_at' => 'nullable|date',
            'status' => 'required|in:draft,published,scheduled',
        ]);

        // Lógica para published_at
        if ($request->status === 'published') {
            $validated['published_at'] = now();
        } elseif ($request->status === 'scheduled') {
            $validated['published_at'] = $request->published_at;
        } else {
            $validated['published_at'] = null;
        }

        if ($request->hasFile('cover_image')) {
            $validated['cover_image'] = $request->file('cover_image')->store('news/cover_images', 'public');
        }

        if ($request->hasFile('featured_image')) {
            $validated['featured_image'] = $request->file('featured_image')->store('news/featured_images', 'public');
        }

        $validated['user_id'] = Auth::id();
        $validated['is_featured'] = $request->has('is_featured');
        $validated['published'] = $request->status !== 'draft';

        News::create($validated);

        return redirect()->route('admin.news.index')
            ->with('success', 'Notícia criada com sucesso!');
    }

    public function edit(News $news)
    {
        if (!auth()->user()->is_admin && !auth()->user()->hasPermission('edit_news')) {
            abort(403, 'Você não tem permissão para editar notícias.');
        }
        
        return view('admin.news.edit', compact('news'));
    }

    public function update(Request $request, News $news)
    {
        if (!auth()->user()->is_admin && !auth()->user()->hasPermission('edit_news')) {
            abort(403, 'Você não tem permissão para editar notícias.');
        }

        $validated = $request->validate([
            'title' => 'required|max:255',
            'excerpt' => 'nullable|string|max:300',
            'content' => 'required',
            'cover_image' => 'nullable|image|max:2048',
            'featured_image' => 'nullable|image|max:2048',
            'is_featured' => 'required|boolean', // Alterado para required
            'published_at' => 'nullable|date',
            'status' => 'required|in:draft,published,scheduled',
        ]);

        // Lógica para atualizar published_at
        if ($request->status === 'published' && !$news->published_at) {
            $validated['published_at'] = now();
        } elseif ($request->status === 'published' && $news->published_at && $news->published_at->isFuture()) {
            $validated['published_at'] = now();
        } elseif ($request->status === 'scheduled') {
            $validated['published_at'] = $request->published_at;
        } elseif ($request->status === 'draft') {
            $validated['published_at'] = $news->published_at;
        }

        // Processar imagens
        if ($request->hasFile('cover_image')) {
            if ($news->cover_image) {
                Storage::disk('public')->delete($news->cover_image);
            }
            $validated['cover_image'] = $request->file('cover_image')->store('news/cover_images', 'public');
        }

        if ($request->hasFile('featured_image')) {
            if ($news->featured_image) {
                Storage::disk('public')->delete($news->featured_image);
            }
            $validated['featured_image'] = $request->file('featured_image')->store('news/featured_images', 'public');
        }

        $validated['published'] = $request->status !== 'draft';
        
        // Garantir que is_featured seja booleano
        $validated['is_featured'] = (bool)$request->is_featured;

        $news->update($validated);

        return redirect()->route('admin.news.index')
            ->with('success', 'Notícia atualizada com sucesso!');
    }

    public function destroy(News $news)
    {
        if (!auth()->user()->is_admin && !auth()->user()->hasPermission('edit_news')) {
            abort(403, 'Você não tem permissão para excluir notícias.');
        }

        if ($news->cover_image) {
            Storage::delete($news->cover_image);
        }

        if ($news->featured_image) {
            Storage::delete($news->featured_image);
        }

        $news->delete();

        return redirect()->route('admin.news.index')
            ->with('success', 'Notícia removida com sucesso!');
    }

    public function toggleFeatured(News $news)
    {
        if (!auth()->user()->is_admin && !auth()->user()->hasPermission('edit_news')) {
            abort(403, 'Você não tem permissão para destacar notícias.');
        }
        
        $news->update(['is_featured' => !$news->is_featured]);
        
        return back()->with('success', 'Status de destaque atualizado!');
    }
}