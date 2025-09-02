<?php

namespace App\Http\Controllers;

use App\Models\CalendarEvent;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CalendarController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        // Verificar se usuário tem acesso ao calendário
        $user = Auth::user();
        if (!$user->is_admin && !$user->hasPermission('view_calendar')) {
            abort(403, 'Acesso não autorizado ao calendário.');
        }

        $users = User::where('is_admin', true)
            ->orWhere(function($query) {
                $query->whereJsonLength('permissions', '>', 0)
                      ->orWhere('permissions', 'like', '%"%');
            })
            ->get();

        return view('admin.calendar.index', compact('users'));
    }

    public function getEvents(Request $request)
    {
        $user = Auth::user();
        
        $events = CalendarEvent::where(function($query) use ($user) {
            $query->where('visibility', 'public')
                ->orWhere('user_id', $user->id)
                ->orWhereJsonContains('participants', (string) $user->id);
        })->get();

        return response()->json($events->map(function($event) use ($user) {
            // Verificar se o usuário atual pode editar o evento
            $canEdit = $event->user_id === $user->id || 
                    $user->is_admin || 
                    $user->hasPermission('edit_calendar_events');
            
            // Usar a cor salva no banco ou deixar vazio se não houver
            $color = $event->color ?: '';

            // Definir cores com base na visibilidade apenas se não houver cor específica
            $backgroundColor = $color ?: ($event->visibility === 'public' ? '' : '');
            $borderColor = $color ?: ($event->visibility === 'public' ? '' : '');
            
            return [
                'id' => $event->id,
                'title' => $event->title,
                'start' => $event->start_date->toIso8601String(),
                'end' => $event->end_date->toIso8601String(),
                'description' => $event->description,
                'visibility' => $event->visibility,
                'color' => $color,
                'user_id' => $event->user_id,
                'created_by' => $event->user->name,
                'participants' => $event->participants,
                'backgroundColor' => $backgroundColor,
                'borderColor' => $borderColor,
                'textColor' => $color ? '#ffffff' : '', // Texto branco apenas se houver cor de fundo
                'editable' => $canEdit,
                'extendedProps' => [
                    'color' => $color,
                    'visibility' => $event->visibility
                ]
            ];
        }));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        if (!$user->is_admin && !$user->hasPermission('create_calendar_events')) {
            return response()->json(['error' => 'Não autorizado'], 403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'visibility' => 'required|in:private,public',
            'color' => 'nullable|string|max:7', // Adicione max:7 para códigos hex (#000000)
            'participants' => 'nullable|array',
            'participants.*' => 'exists:users,id',
        ]);

        $validated['user_id'] = $user->id;
        $validated['participants'] = $request->participants ?? [];
        
        // Se a cor for uma string vazia, definir como null
        $validated['color'] = empty($request->color) ? null : $request->color;

        $event = CalendarEvent::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Evento criado com sucesso!',
            'event' => $event
        ]);
    }

    public function update(Request $request, CalendarEvent $event)
    {
        $user = Auth::user();
        
        // Verificar permissões para editar
        $canEdit = $event->user_id === $user->id || 
                $user->is_admin || 
                $user->hasPermission('edit_calendar_events');
                
        if (!$canEdit) {
            return response()->json(['error' => 'Não autorizado'], 403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'visibility' => 'required|in:private,public',
            'color' => 'nullable|string|max:7',
            'participants' => 'nullable|array',
            'participants.*' => 'exists:users,id',
        ]);

        // Se a cor for uma string vazia, definir como null
        $validated['color'] = empty($request->color) ? null : $request->color;

        $event->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Evento atualizado com sucesso!',
            'event' => $event
        ]);
    }

    public function destroy(CalendarEvent $event)
    {
        $user = Auth::user();
        
        // Verificar permissões para excluir
        $canDelete = $event->user_id === $user->id || 
                    $user->is_admin || 
                    $user->hasPermission('delete_calendar_events');
                    
        if (!$canDelete) {
            return response()->json(['error' => 'Não autorizado'], 403);
        }

        $event->delete();

        return response()->json(['message' => 'Evento deletado com sucesso']);
    }

    public function show(CalendarEvent $event)
    {
        $user = Auth::user();
        
        // Verificar se usuário pode visualizar o evento
        $canView = $event->visibility === 'public' || 
                  $event->user_id === $user->id || 
                  in_array($user->id, $event->participants ?? []) ||
                  $user->is_admin ||
                  $user->hasPermission('view_calendar_events');
                  
        if (!$canView) {
            return response()->json(['error' => 'Não autorizado'], 403);
        }

        // Carregar informações dos participantes
        $participants = [];
        if (!empty($event->participants)) {
            $participants = User::whereIn('id', $event->participants)->get(['id', 'name']);
        }

        return response()->json([
            'event' => $event,
            'participants' => $participants,
            'created_by' => $event->user->name
        ]);
    }
}