<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Game;
use App\Models\News;
use App\Models\User;
use App\Models\HelpContent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(\App\Http\Middleware\AdminMiddleware::class);
    }
    
    public function dashboard()
    {
        $user = auth()->user();
        
        // Inicializar todas as variáveis como null ou 0
        $gamesCount = 0;
        $newsCount = 0;
        $usersCount = 0;
        $activeGamesCount = 0;
        $deletedGamesCount = 0;
        $recentNewsCount = 0;
        $adminUsersCount = 0;
        $blockedUsersCount = 0;
        $recentGames = collect();
        $recentNews = collect();
        $recentUsers = collect();
        
        // Verificar permissões e carregar dados conforme necessário
        if ($user->is_admin || $user->hasPermission('edit_games') || $user->hasPermission('create_games')) {
            $gamesCount = Game::withTrashed()->count();
            $activeGamesCount = Game::whereNull('deleted_at')->count();
            $deletedGamesCount = Game::onlyTrashed()->count();
            $recentGames = Game::withTrashed()->orderBy('updated_at', 'desc')->take(5)->get();
        }
        
        if ($user->is_admin || $user->hasPermission('edit_news') || $user->hasPermission('create_news')) {
            $newsCount = News::count();
            $recentNewsCount = News::where('created_at', '>=', now()->subWeek())->count();
            $recentNews = News::orderBy('updated_at', 'desc')->take(5)->get();
        }
        
        if ($user->is_admin) {
            $usersCount = User::count();
            $adminUsersCount = User::where('is_admin', true)->count();
            $blockedUsersCount = User::where('is_blocked', true)->count();
            $recentUsers = User::orderBy('updated_at', 'desc')->take(5)->get();
        }
        
        return view('admin.dashboard', compact(
            'gamesCount', 
            'newsCount', 
            'usersCount',
            'activeGamesCount',
            'deletedGamesCount',
            'recentNewsCount',
            'adminUsersCount',
            'blockedUsersCount',
            'recentGames',
            'recentNews',
            'recentUsers',
            'user'
        ));
    }

    public function manageGames()
    {
        $games = Game::withTrashed()->latest()->paginate(10);
        return view('admin.games.index', compact('games'));
    }

    public function manageNews()
    {
        $news = News::latest()->paginate(10);
        return view('admin.news.index', compact('news'));
    }

    public function manageUsers()
    {
        $users = User::latest()->paginate(10);
        return view('admin.users.index', compact('users'));
    }

    public function createUser()
    {
        return view('admin.users.create');
    }

    public function storeUser(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'is_admin' => 'boolean',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'is_admin' => $request->is_admin ?? false,
        ]);

        // Alterado para admin.users.index
        return redirect()->route('admin.users.index')
            ->with('success', 'Usuário criado com sucesso!');
    }

    public function editUser(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function updateUser(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,'.$user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'is_admin' => 'boolean',
            'is_blocked' => 'boolean',
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'is_admin' => $request->is_admin ?? false,
            'is_blocked' => $request->is_blocked ?? false,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        // Alterado para admin.users.index
        return redirect()->route('admin.users.index')
            ->with('success', 'Usuário atualizado com sucesso!');
    }

    public function toggleAdmin(User $user)
    {
        \Log::info('Toggle Admin - Before', ['user_id' => $user->id, 'current_status' => $user->is_admin]);
        
        $newStatus = !$user->is_admin;
        $user->update(['is_admin' => $newStatus]);
        
        \Log::info('Toggle Admin - After', ['user_id' => $user->id, 'new_status' => $user->fresh()->is_admin]);
        
        return back()->with('success', 'Status de administrador alterado com sucesso!');
    }

    public function toggleBlock(User $user)
    {
        \Log::info('Toggle Block - Before', ['user_id' => $user->id, 'current_status' => $user->is_blocked]);
        
        $newStatus = !$user->is_blocked;
        $user->update(['is_blocked' => $newStatus]);
        
        \Log::info('Toggle Block - After', ['user_id' => $user->id, 'new_status' => $user->fresh()->is_blocked]);
        
        return back()->with('success', 'Status de bloqueio alterado com sucesso!');
    }

    public function destroyUser(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Você não pode excluir seu próprio usuário!');
        }

        $user->delete();
        
        return back()->with('success', 'Usuário removido com sucesso!');
    }

    public function editHelpContent()
    {
        $content = HelpContent::firstOrNew();
        return view('admin.help.edit', compact('content'));
    }

    public function updateHelpContent(Request $request)
    {
        $validated = $request->validate([
            'coordinators_content' => 'required',
            'interns_content' => 'required',
            'machines_usage_content' => 'required',
        ]);

        HelpContent::updateOrCreate(
            ['id' => 1],
            $validated
        );

        return redirect()->route('admin.help.edit')->with('success', 'Conteúdo de ajuda atualizado com sucesso!');
    }

    public function createGame()
    {
        return app(GameController::class)->create();
    }

    public function storeGame(Request $request)
    {
        return app(GameController::class)->store($request);
    }

    public function editGame(Game $game)
    {
        return app(GameController::class)->edit($game);
    }

    public function updateGame(Request $request, Game $game)
    {
        return app(GameController::class)->update($request, $game);
    }

    public function destroyGame(Game $game)
    {
        return app(GameController::class)->destroy($game);
    }

/*     public function restoreGame($id)   DESABILITADO 
    {
        return app(GameController::class)->restore($id);
    } */
}