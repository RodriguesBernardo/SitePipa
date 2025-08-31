<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\HelpContent;

class HelpController extends Controller
{
    public function __construct()
    {
        // Aplicar middleware apenas para métodos admin
        $this->middleware('auth')->only(['adminIndex', 'edit', 'update']);
    }

    // Método para a rota pública /help
    public function index()
    {
        $content = HelpContent::first();
        return view('help.index', compact('content'));
    }

    // Método para a rota admin/help (lista)
    public function adminIndex()
    {
        if (!auth()->user()->is_admin && !auth()->user()->hasPermission('edit_help')) {
            abort(403, 'Você não tem permissão para visualizar ajuda.');
        }
        
        $content = HelpContent::first();
        return view('admin.help.index', compact('content'));
    }

    // Método para edição
    public function edit()
    {
        if (!auth()->user()->is_admin && !auth()->user()->hasPermission('edit_help')) {
            abort(403, 'Você não tem permissão para editar ajuda.');
        }
        
        $content = HelpContent::firstOrNew();
        return view('admin.help.edit', compact('content'));
    }

    // Método para atualização
    public function update(Request $request)
    {
        if (!auth()->user()->is_admin && !auth()->user()->hasPermission('edit_help')) {
            abort(403, 'Você não tem permissão para editar ajuda.');
        }
        
        $validated = $request->validate([
            'coordinators_content' => 'nullable|string',
            'interns_content' => 'nullable|string',
            'machines_usage_content' => 'nullable|string',
        ]);

        $content = HelpContent::firstOrNew();
        $content->fill($validated);
        $content->save();

        return redirect()->route('admin.help.edit')
            ->with('success', 'Conteúdo de ajuda atualizado com sucesso!');
    }
}