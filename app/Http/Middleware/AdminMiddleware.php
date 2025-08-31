<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        
        // Verifica se o usuário é administrador OU tem alguma permissão
        if (!$user->is_admin && empty($user->permissions)) {
            abort(403, 'Acesso não autorizado.');
        }

        return $next($request);
    }
}