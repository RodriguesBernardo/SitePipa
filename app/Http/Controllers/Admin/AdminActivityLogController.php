<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;

class AdminActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $logs = ActivityLog::with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(50);

        // Para o filtro de usuários
        $users = User::where('is_admin', true)
            ->orWhereHas('activityLogs')
            ->orderBy('name')
            ->get();

        return view('admin.logs.index', compact('logs', 'users'));
    }

    public function show(ActivityLog $log)
    {
        $log->load('user');
        
        return view('admin.logs.show', compact('log'));
    }

    public function filter(Request $request)
    {
        $query = ActivityLog::with('user');

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        if ($request->filled('model_type')) {
            $query->where('model_type', $request->model_type);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $logs = $query->orderBy('created_at', 'desc')->paginate(50);
        
        // Para o filtro de usuários
        $users = User::where('is_admin', true)
            ->orWhereHas('activityLogs')
            ->orderBy('name')
            ->get();

        return view('admin.logs.index', compact('logs', 'users'));
    }
}