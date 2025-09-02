<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    // Permissões personalizadas para o usuário 
    const PERMISSIONS = [
        'edit_games' => 'Editar Jogos',
        'create_games' => 'Criar Jogos',
        'edit_news' => 'Editar Notícias',
        'create_news' => 'Criar Notícias',
        'edit_help' => 'Editar Ajuda',
        'view_calendar' => 'Visualizar Calendário', // Nova permissão
        'create_calendar_events' => 'Criar Eventos no Calendário', // Nova permissão
    ];

    public function __construct()
    {
        // Middlewares já são aplicados nas rotas não precisa aqui
    }

    public function index(Request $request)
    {
        $query = User::query();
        
        // Filtro de busca
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%");
            });
        }
        
        // Filtro de status
        if ($request->has('status') && !empty($request->status)) {
            if ($request->status === 'active') {
                $query->where('is_blocked', false);
            } elseif ($request->status === 'blocked') {
                $query->where('is_blocked', true);
            }
        }
        
        // Filtro de tipo de usuário
        if ($request->has('role') && !empty($request->role)) {
            if ($request->role === 'admin') {
                $query->where('is_admin', true);
            } elseif ($request->role === 'user') {
                $query->where('is_admin', false);
            } elseif ($request->role === 'bolsista') {
                $query->where(function($q) {
                    $q->whereJsonLength('permissions', '>', 0)
                      ->orWhere('permissions', '!=', '[]')
                      ->orWhere('permissions', '!=', 'null');
                });
            }
        }
        
        $users = $query->latest()->paginate(10);
        
        // Estatísticas para os cards
        $totalUsers = User::count();
        $adminUsers = User::where('is_admin', true)->count();
        $activeUsers = User::where('is_blocked', false)->count();
        $blockedUsers = User::where('is_blocked', true)->count();
        
        $bolsistaUsers = User::where(function($q) {
            $q->whereJsonLength('permissions', '>', 0)
            ->orWhere('permissions', 'like', '%"%');
        })->count();
        
        return view('admin.users.index', compact(
            'users', 
            'totalUsers', 
            'adminUsers', 
            'activeUsers', 
            'blockedUsers',
            'bolsistaUsers'
        ));
    }

    public function create()
    {
        $permissions = self::PERMISSIONS;
        return view('admin.users.create', compact('permissions'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'is_admin' => 'boolean',
            'permissions' => 'array',
            'permissions.*' => 'in:' . implode(',', array_keys(self::PERMISSIONS)),
        ]);

        $validated['password'] = bcrypt($validated['password']);
        $validated['permissions'] = $request->permissions ?? [];

        User::create($validated);

        return redirect()->route('admin.users.index')->with('success', 'Usuário criado com sucesso!');
    }

    public function edit(User $user)
    {
        $permissions = self::PERMISSIONS;
        return view('admin.users.edit', compact('user', 'permissions'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,'.$user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'is_admin' => 'boolean',
            'is_blocked' => 'boolean',
            'permissions' => 'array',
            'permissions.*' => 'in:' . implode(',', array_keys(self::PERMISSIONS)),
        ]);

        if ($validated['password']) {
            $validated['password'] = bcrypt($validated['password']);
        } else {
            unset($validated['password']);
        }

        $validated['permissions'] = $request->permissions ?? [];

        $user->update($validated);

        return redirect()->route('admin.users.index')->with('success', 'Usuário atualizado com sucesso!');
    }

    public function toggleAdmin(User $user)
    {
        $user->update(['is_admin' => !$user->is_admin]);
        return back()->with('success', 'Status de administrador alterado com sucesso!');
    }

    public function toggleBlock(User $user)
    {
        $user->update(['is_blocked' => !$user->is_blocked]);

        $message = $user->is_blocked
            ? 'Usuário bloqueado com sucesso!'
            : 'Usuário liberado com sucesso!';

        return back()->with('success', 'Status de bloqueio alterado com sucesso!');
    }

    public function destroy(User $user)
    {
        $user->delete();
        return back()->with('success', 'Usuário removido com sucesso!');
    }

    public function restore($id)
    {
        User::withTrashed()->findOrFail($id)->restore();
        return back()->with('success', 'Usuário restaurado com sucesso!');
    }

    
}